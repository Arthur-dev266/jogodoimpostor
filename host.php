<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/categorias.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$pdo = obter_pdo();
$codigo = strtoupper(trim($_GET['sala'] ?? ''));
$hostToken = $_GET['host_token'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM salas WHERE codigo = ?');
$stmt->execute([$codigo]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sala || !hash_equals($sala['host_token'], $hostToken)) {
    ?>
    <!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sala não encontrada</title><link rel="stylesheet" href="style.css"></head>
    <body><div class="wrap"><div class="card">
        <div class="error">Sala não encontrada ou você não é o anfitrião dela.</div>
        <a class="btn" href="index.php">Voltar ao início</a>
    </div></div></body></html>
    <?php
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM jogadores WHERE sala_codigo = ? ORDER BY entrou_em ASC');
$stmt->execute([$codigo]);
$jogadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalJogadores = count($jogadores);
$categorias = obter_categorias();
$fase = $sala['fase'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Sala <?= e($codigo) ?> - Anfitrião</title>
<link rel="stylesheet" href="style.css">
</head>
<body data-fase="<?= e($fase) ?>" data-sala="<?= e($codigo) ?>" data-host-token="<?= e($hostToken) ?>">
<div class="wrap">
<h1>🕵️ Quem é o Impostor?</h1>

<?php if ($fase === 'lobby'): ?>

    <div class="card">
        <div class="room-code-label">Código da sala — digam para o grupo:</div>
        <div class="room-code"><?= e($codigo) ?></div>
        <p class="hint">Cada jogador acessa este site pelo próprio celular e entra com esse código.</p>

        <form method="post" action="api.php" style="margin-top:20px;">
            <input type="hidden" name="action" value="atualizar_config">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">

            <label>Categoria de palavras</label>
            <select name="categoria" onchange="this.form.submit()">
                <option value="todas" <?= $sala['categoria'] === 'todas' ? 'selected' : '' ?>>🎲 Todas (misturado)</option>
                <?php foreach ($categorias as $chave => $cat): ?>
                    <option value="<?= e($chave) ?>" <?= $sala['categoria'] === $chave ? 'selected' : '' ?>>
                        <?= e($cat['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Quantidade de impostores</label>
            <select name="qtd_impostores" onchange="this.form.submit()">
                <?php for ($n = 1; $n <= 6; $n++): ?>
                    <option value="<?= $n ?>" <?= (int)$sala['qtd_impostores'] === $n ? 'selected' : '' ?>><?= $n ?> impostor<?= $n>1?'es':'' ?></option>
                <?php endfor; ?>
            </select>
            <noscript><button class="btn secondary" type="submit">Salvar configuração</button></noscript>
        </form>

        <label style="margin-top:20px;">Jogadores conectados <span class="badge-count" id="contador-jogadores"><?= $totalJogadores ?></span></label>
        <ul class="players-plain" id="lista-jogadores">
            <?php foreach ($jogadores as $j): ?>
                <li><span class="dot"></span><?= e($j['nome']) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php if ($totalJogadores === 0): ?>
            <p class="hint" id="msg-vazio">Aguardando jogadores entrarem...</p>
        <?php endif; ?>

        <form method="post" action="api.php">
            <input type="hidden" name="action" value="iniciar_jogo">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn" type="submit" id="btn-iniciar" <?= $totalJogadores < 3 ? 'disabled' : '' ?>>
                <?= $totalJogadores < 3 ? 'Aguardando pelo menos 3 jogadores...' : 'Iniciar jogo' ?>
            </button>
        </form>
        <form method="post" action="api.php" style="margin-top:8px;">
            <input type="hidden" name="action" value="encerrar_sala">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn secondary" type="submit">Encerrar sala</button>
        </form>
    </div>

<?php elseif ($fase === 'revelar'): ?>

    <div class="card">
        <h2 style="text-align:center;">📱 Todos estão vendo sua carta</h2>
        <p class="instruction">Peça para cada jogador olhar em silêncio a palavra ou dica no próprio celular.
        Quando todos estiverem prontos, avancem para a discussão.</p>
        <ul class="players-plain">
            <?php foreach ($jogadores as $j): ?>
                <li><span class="dot"></span><?= e($j['nome']) ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="post" action="api.php">
            <input type="hidden" name="action" value="avancar_fase">
            <input type="hidden" name="para" value="discussao">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn" type="submit">Todos viram, iniciar discussão</button>
        </form>
    </div>

<?php elseif ($fase === 'discussao'): ?>

    <div class="card">
        <h2 style="text-align:center;">💬 Rodada de discussão</h2>
        <p class="instruction">Cada jogador diz, na sua vez, uma palavra relacionada ao tema — sem falar a palavra secreta.
        Observem quem parece perdido: pode ser o impostor!</p>
        <ul class="players-plain">
            <?php foreach ($jogadores as $j): ?>
                <li><span class="dot"></span><?= e($j['nome']) ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="post" action="api.php">
            <input type="hidden" name="action" value="avancar_fase">
            <input type="hidden" name="para" value="votacao">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn" type="submit">Encerrar discussão, iniciar votação</button>
        </form>
    </div>

<?php elseif ($fase === 'votacao'):
    $totalVotos = 0;
    foreach ($jogadores as $j) if ($j['voto_em'] !== null) $totalVotos++;
    $pct = $totalJogadores > 0 ? round(($totalVotos / $totalJogadores) * 100) : 0;
?>

    <div class="card">
        <h2 style="text-align:center;">🗳️ Votação em andamento</h2>
        <p class="instruction">Cada jogador vota, no próprio celular, em quem acha que é o impostor.</p>
        <div class="progress-bar"><div class="progress-bar-fill" id="barra-votos" style="width:<?= $pct ?>%"></div></div>
        <p class="hint" id="texto-votos"><?= $totalVotos ?> de <?= $totalJogadores ?> jogadores já votaram</p>
        <form method="post" action="api.php">
            <input type="hidden" name="action" value="avancar_fase">
            <input type="hidden" name="para" value="resultado">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn" type="submit">Ver resultado agora</button>
        </form>
    </div>

<?php elseif ($fase === 'resultado'):
    $contagem = [];
    foreach ($jogadores as $j) {
        if ($j['voto_em'] !== null) $contagem[$j['voto_em']] = ($contagem[$j['voto_em']] ?? 0) + 1;
    }
    arsort($contagem);
    $maisVotadoId = array_key_first($contagem);
    $impostores = array_filter($jogadores, fn($j) => (int) $j['is_impostor'] === 1);
    $nomesImpostores = array_map(fn($j) => $j['nome'], $impostores);
    $impostorIds = array_map(fn($j) => (int) $j['id'], $impostores);
    $acertouMaioria = $maisVotadoId !== null && in_array((int) $maisVotadoId, $impostorIds, true);
?>

    <div class="card">
        <h2 style="text-align:center;">🎉 Resultado da rodada</h2>

        <?php if ($maisVotadoId === null): ?>
            <div class="result-banner errou">Ninguém votou nesta rodada.</div>
        <?php else: ?>
            <?php
                $nomeMaisVotado = '';
                foreach ($jogadores as $j) if ((int)$j['id'] === (int)$maisVotadoId) $nomeMaisVotado = $j['nome'];
            ?>
            <div class="result-banner <?= $acertouMaioria ? 'acertou' : 'errou' ?>">
                <?= $acertouMaioria
                    ? '✅ O grupo acertou! ' . e($nomeMaisVotado) . ' era impostor.'
                    : '❌ O grupo errou! ' . e($nomeMaisVotado) . ' era inocente.' ?>
            </div>
        <?php endif; ?>

        <div class="reveal-info">
            <b>Palavra secreta:</b> <?= e($sala['palavra']) ?><br>
            <b>Dica do(s) impostor(es):</b> <?= e($sala['dica']) ?><br>
            <b><?= count($nomesImpostores) > 1 ? 'Impostores' : 'Impostor' ?>:</b> <?= e(implode(', ', $nomesImpostores)) ?>
        </div>

        <label>Votos recebidos</label>
        <ul class="players-plain">
            <?php foreach ($jogadores as $j): ?>
                <li><span class="dot"></span><?= e($j['nome']) ?> <span class="badge-count"><?= $contagem[$j['id']] ?? 0 ?></span></li>
            <?php endforeach; ?>
        </ul>

        <form method="post" action="api.php">
            <input type="hidden" name="action" value="nova_rodada">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn gold" type="submit">🔁 Nova rodada (mesmos jogadores)</button>
        </form>
        <form method="post" action="api.php">
            <input type="hidden" name="action" value="encerrar_sala">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="host_token" value="<?= e($hostToken) ?>">
            <button class="btn secondary" type="submit">Encerrar sala</button>
        </form>
    </div>

<?php endif; ?>
</div>

<script>
const fase = document.body.dataset.fase;
const sala = document.body.dataset.sala;
const hostToken = document.body.dataset.hostToken;

async function atualizar() {
    try {
        const resp = await fetch(`api.php?action=estado_host&sala=${sala}&host_token=${hostToken}`);
        if (!resp.ok) return;
        const dados = await resp.json();
        if (dados.erro) return;

        if (dados.fase !== fase) {
            location.reload();
            return;
        }

        if (fase === 'lobby') {
            const lista = document.getElementById('lista-jogadores');
            const contador = document.getElementById('contador-jogadores');
            const btn = document.getElementById('btn-iniciar');
            if (lista) {
                lista.innerHTML = dados.jogadores.map(j =>
                    `<li><span class="dot"></span>${j.nome.replace(/</g,'&lt;')}</li>`
                ).join('');
            }
            if (contador) contador.textContent = dados.total_jogadores;
            const msgVazio = document.getElementById('msg-vazio');
            if (msgVazio && dados.total_jogadores > 0) msgVazio.style.display = 'none';
            if (btn) {
                if (dados.total_jogadores >= 3) {
                    btn.disabled = false;
                    btn.textContent = 'Iniciar jogo';
                } else {
                    btn.disabled = true;
                    btn.textContent = 'Aguardando pelo menos 3 jogadores...';
                }
            }
        }

        if (fase === 'votacao') {
            const barra = document.getElementById('barra-votos');
            const texto = document.getElementById('texto-votos');
            const pct = dados.total_jogadores > 0 ? Math.round((dados.total_votos / dados.total_jogadores) * 100) : 0;
            if (barra) barra.style.width = pct + '%';
            if (texto) texto.textContent = `${dados.total_votos} de ${dados.total_jogadores} jogadores já votaram`;
        }
    } catch (e) { /* ignora falhas de rede pontuais */ }
}

setInterval(atualizar, 2000);
</script>
</body>
</html>
