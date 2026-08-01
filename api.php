<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/categorias.php';

$pdo = obter_pdo();
$acao = $_REQUEST['action'] ?? '';

function responder_json(array $dados, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados);
    exit;
}

function buscar_sala(PDO $pdo, string $codigo): ?array {
    $stmt = $pdo->prepare('SELECT * FROM salas WHERE codigo = ?');
    $stmt->execute([$codigo]);
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);
    return $sala ?: null;
}

function buscar_jogadores(PDO $pdo, string $codigo): array {
    $stmt = $pdo->prepare('SELECT * FROM jogadores WHERE sala_codigo = ? ORDER BY entrou_em ASC');
    $stmt->execute([$codigo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ===========================================================================
// AÇÕES DE LEITURA (GET) - usadas pelo JS para polling em tempo real
// ===========================================================================

if ($acao === 'estado_host') {
    $codigo = strtoupper(trim($_GET['sala'] ?? ''));
    $hostToken = $_GET['host_token'] ?? '';
    $sala = buscar_sala($pdo, $codigo);
    if (!$sala || !hash_equals($sala['host_token'], $hostToken)) {
        responder_json(['erro' => 'sala_nao_encontrada'], 404);
    }
    $jogadores = buscar_jogadores($pdo, $codigo);
    $totalVotos = 0;
    foreach ($jogadores as $j) {
        if ($j['voto_em'] !== null) $totalVotos++;
    }
    responder_json([
        'fase'           => $sala['fase'],
        'categoria'      => $sala['categoria'],
        'qtd_impostores' => (int) $sala['qtd_impostores'],
        'jogadores'      => array_map(fn($j) => ['id' => (int) $j['id'], 'nome' => $j['nome']], $jogadores),
        'total_jogadores'=> count($jogadores),
        'total_votos'    => $totalVotos,
    ]);
}

if ($acao === 'estado_jogador') {
    $codigo = strtoupper(trim($_GET['sala'] ?? ''));
    $token  = $_GET['token'] ?? '';
    $sala = buscar_sala($pdo, $codigo);
    if (!$sala) {
        responder_json(['erro' => 'sala_nao_encontrada'], 404);
    }
    $stmt = $pdo->prepare('SELECT * FROM jogadores WHERE sala_codigo = ? AND token = ?');
    $stmt->execute([$codigo, $token]);
    $eu = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$eu) {
        responder_json(['erro' => 'jogador_nao_encontrado'], 404);
    }

    $jogadores = buscar_jogadores($pdo, $codigo);
    $resposta = [
        'fase'        => $sala['fase'],
        'meu_nome'    => $eu['nome'],
        'meu_id'      => (int) $eu['id'],
        'sou_impostor'=> (bool) $eu['is_impostor'],
        'meu_voto'    => $eu['voto_em'] !== null ? (int) $eu['voto_em'] : null,
        'jogadores'   => array_map(fn($j) => ['id' => (int) $j['id'], 'nome' => $j['nome']], $jogadores),
    ];

    if (in_array($sala['fase'], ['revelar', 'discussao', 'votacao', 'resultado'], true)) {
        if ($eu['is_impostor']) {
            $resposta['dica'] = $sala['dica'];
        } else {
            $resposta['palavra'] = $sala['palavra'];
        }
    }

    if ($sala['fase'] === 'resultado') {
        $resposta['palavra_revelada'] = $sala['palavra'];
        $resposta['dica_revelada']    = $sala['dica'];
        $impostores = array_values(array_filter($jogadores, fn($j) => (int) $j['is_impostor'] === 1));
        $resposta['impostores'] = array_map(fn($j) => $j['nome'], $impostores);

        $contagem = [];
        foreach ($jogadores as $j) {
            if ($j['voto_em'] !== null) {
                $contagem[$j['voto_em']] = ($contagem[$j['voto_em']] ?? 0) + 1;
            }
        }
        $tabela = [];
        foreach ($jogadores as $j) {
            $tabela[] = ['nome' => $j['nome'], 'votos' => $contagem[$j['id']] ?? 0];
        }
        $resposta['tabela_votos'] = $tabela;
    }

    responder_json($resposta);
}

// ===========================================================================
// AÇÕES DE ESCRITA (POST) - redirecionam de volta para a página apropriada
// ===========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($acao === 'criar_sala') {
        limpar_salas_antigas($pdo);
        $codigo = gerar_codigo_sala($pdo);
        $hostToken = gerar_token();
        $agora = time();
        $stmt = $pdo->prepare('INSERT INTO salas (codigo, host_token, categoria, qtd_impostores, fase, criada_em, atualizada_em)
                                VALUES (?, ?, "todas", 1, "lobby", ?, ?)');
        $stmt->execute([$codigo, $hostToken, $agora, $agora]);
        header('Location: host.php?sala=' . urlencode($codigo) . '&host_token=' . urlencode($hostToken));
        exit;
    }

    if ($acao === 'entrar_sala') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $nome = trim($_POST['nome'] ?? '');
        $nome = mb_substr($nome, 0, 20);

        $sala = buscar_sala($pdo, $codigo);
        if (!$sala) {
            header('Location: index.php?erro=' . urlencode('Sala não encontrada. Confira o código.'));
            exit;
        }
        if ($sala['fase'] !== 'lobby') {
            header('Location: index.php?erro=' . urlencode('Essa sala já começou a partida.'));
            exit;
        }
        if ($nome === '') {
            header('Location: index.php?erro=' . urlencode('Digite um nome para entrar.') . '&sala=' . urlencode($codigo));
            exit;
        }

        // impede nomes duplicados na mesma sala
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM jogadores WHERE sala_codigo = ? AND LOWER(nome) = LOWER(?)');
        $stmt->execute([$codigo, $nome]);
        if ((int) $stmt->fetchColumn() > 0) {
            header('Location: index.php?erro=' . urlencode('Esse nome já está em uso nesta sala.') . '&sala=' . urlencode($codigo));
            exit;
        }

        $token = gerar_token();
        $stmt = $pdo->prepare('INSERT INTO jogadores (sala_codigo, nome, token, entrou_em) VALUES (?, ?, ?, ?)');
        $stmt->execute([$codigo, $nome, $token, time()]);
        tocar_atualizacao($pdo, $codigo);

        setcookie('impostor_token_' . $codigo, $token, time() + 6 * 3600, '/');
        header('Location: jogo.php?sala=' . urlencode($codigo) . '&token=' . urlencode($token));
        exit;
    }

    if ($acao === 'atualizar_config') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $hostToken = $_POST['host_token'] ?? '';
        $sala = buscar_sala($pdo, $codigo);
        if ($sala && hash_equals($sala['host_token'], $hostToken) && $sala['fase'] === 'lobby') {
            $categoria = $_POST['categoria'] ?? 'todas';
            $qtd = max(1, min(6, (int) ($_POST['qtd_impostores'] ?? 1)));
            $stmt = $pdo->prepare('UPDATE salas SET categoria = ?, qtd_impostores = ?, atualizada_em = ? WHERE codigo = ?');
            $stmt->execute([$categoria, $qtd, time(), $codigo]);
        }
        header('Location: host.php?sala=' . urlencode($codigo) . '&host_token=' . urlencode($hostToken));
        exit;
    }

    if ($acao === 'iniciar_jogo') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $hostToken = $_POST['host_token'] ?? '';
        $sala = buscar_sala($pdo, $codigo);
        if ($sala && hash_equals($sala['host_token'], $hostToken)) {
            $jogadores = buscar_jogadores($pdo, $codigo);
            if (count($jogadores) >= 3) {
                $qtdImpostores = (int) $sala['qtd_impostores'];
                $maxImpostores = max(1, count($jogadores) - 2);
                $qtdImpostores = min($qtdImpostores, $maxImpostores);

                $par = sortear_par($sala['categoria']);

                $indices = range(0, count($jogadores) - 1);
                shuffle($indices);
                $indicesImpostores = array_slice($indices, 0, $qtdImpostores);

                $pdo->beginTransaction();
                foreach ($jogadores as $i => $j) {
                    $ehImpostor = in_array($i, $indicesImpostores, true) ? 1 : 0;
                    $stmt = $pdo->prepare('UPDATE jogadores SET is_impostor = ?, voto_em = NULL WHERE id = ?');
                    $stmt->execute([$ehImpostor, $j['id']]);
                }
                $stmt = $pdo->prepare('UPDATE salas SET fase = "revelar", palavra = ?, dica = ?, atualizada_em = ? WHERE codigo = ?');
                $stmt->execute([$par['palavra'], $par['dica'], time(), $codigo]);
                $pdo->commit();
            }
        }
        header('Location: host.php?sala=' . urlencode($codigo) . '&host_token=' . urlencode($hostToken));
        exit;
    }

    if ($acao === 'avancar_fase') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $hostToken = $_POST['host_token'] ?? '';
        $para = $_POST['para'] ?? '';
        $sala = buscar_sala($pdo, $codigo);
        $permitidas = ['discussao', 'votacao', 'resultado'];
        if ($sala && hash_equals($sala['host_token'], $hostToken) && in_array($para, $permitidas, true)) {
            $stmt = $pdo->prepare('UPDATE salas SET fase = ?, atualizada_em = ? WHERE codigo = ?');
            $stmt->execute([$para, time(), $codigo]);
        }
        header('Location: host.php?sala=' . urlencode($codigo) . '&host_token=' . urlencode($hostToken));
        exit;
    }

    if ($acao === 'nova_rodada') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $hostToken = $_POST['host_token'] ?? '';
        $sala = buscar_sala($pdo, $codigo);
        if ($sala && hash_equals($sala['host_token'], $hostToken)) {
            $jogadores = buscar_jogadores($pdo, $codigo);
            if (count($jogadores) >= 3) {
                $qtdImpostores = (int) $sala['qtd_impostores'];
                $maxImpostores = max(1, count($jogadores) - 2);
                $qtdImpostores = min($qtdImpostores, $maxImpostores);

                $par = sortear_par($sala['categoria']);
                $indices = range(0, count($jogadores) - 1);
                shuffle($indices);
                $indicesImpostores = array_slice($indices, 0, $qtdImpostores);

                $pdo->beginTransaction();
                foreach ($jogadores as $i => $j) {
                    $ehImpostor = in_array($i, $indicesImpostores, true) ? 1 : 0;
                    $stmt = $pdo->prepare('UPDATE jogadores SET is_impostor = ?, voto_em = NULL WHERE id = ?');
                    $stmt->execute([$ehImpostor, $j['id']]);
                }
                $stmt = $pdo->prepare('UPDATE salas SET fase = "revelar", palavra = ?, dica = ?, atualizada_em = ? WHERE codigo = ?');
                $stmt->execute([$par['palavra'], $par['dica'], time(), $codigo]);
                $pdo->commit();
            }
        }
        header('Location: host.php?sala=' . urlencode($codigo) . '&host_token=' . urlencode($hostToken));
        exit;
    }

    if ($acao === 'voltar_lobby') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $hostToken = $_POST['host_token'] ?? '';
        $sala = buscar_sala($pdo, $codigo);
        if ($sala && hash_equals($sala['host_token'], $hostToken)) {
            $stmt = $pdo->prepare('UPDATE salas SET fase = "lobby", atualizada_em = ? WHERE codigo = ?');
            $stmt->execute([time(), $codigo]);
        }
        header('Location: host.php?sala=' . urlencode($codigo) . '&host_token=' . urlencode($hostToken));
        exit;
    }

    if ($acao === 'encerrar_sala') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $hostToken = $_POST['host_token'] ?? '';
        $sala = buscar_sala($pdo, $codigo);
        if ($sala && hash_equals($sala['host_token'], $hostToken)) {
            $pdo->prepare('DELETE FROM jogadores WHERE sala_codigo = ?')->execute([$codigo]);
            $pdo->prepare('DELETE FROM salas WHERE codigo = ?')->execute([$codigo]);
        }
        header('Location: index.php');
        exit;
    }

    if ($acao === 'votar') {
        $codigo = strtoupper(trim($_POST['sala'] ?? ''));
        $token = $_POST['token'] ?? '';
        $suspeito = (int) ($_POST['suspeito'] ?? -1);
        $sala = buscar_sala($pdo, $codigo);
        if ($sala && $sala['fase'] === 'votacao') {
            $stmt = $pdo->prepare('SELECT id FROM jogadores WHERE sala_codigo = ? AND token = ?');
            $stmt->execute([$codigo, $token]);
            $eu = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($eu && $suspeito !== (int) $eu['id']) {
                $stmt = $pdo->prepare('UPDATE jogadores SET voto_em = ? WHERE id = ? AND sala_codigo = ?');
                $stmt->execute([$suspeito, $eu['id'], $codigo]);
            }
        }
        header('Location: jogo.php?sala=' . urlencode($codigo) . '&token=' . urlencode($token));
        exit;
    }

    responder_json(['erro' => 'acao_invalida'], 400);
}

responder_json(['erro' => 'metodo_ou_acao_invalida'], 400);
