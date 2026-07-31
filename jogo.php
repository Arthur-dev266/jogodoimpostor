<?php
require_once __DIR__ . '/db.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$pdo = obter_pdo();
$codigo = strtoupper(trim($_GET['sala'] ?? ''));
$token = $_GET['token'] ?? ($_COOKIE['impostor_token_' . $codigo] ?? '');

$stmt = $pdo->prepare('SELECT * FROM salas WHERE codigo = ?');
$stmt->execute([$codigo]);
$sala = $stmt->fetch(PDO::FETCH_ASSOC);

$eu = null;
if ($sala) {
    $stmt = $pdo->prepare('SELECT * FROM jogadores WHERE sala_codigo = ? AND token = ?');
    $stmt->execute([$codigo, $token]);
    $eu = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$sala || !$eu) {
    ?>
    <!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sala não encontrada</title><link rel="stylesheet" href="style.css"></head>
    <body><div class="wrap"><div class="card">
        <div class="error">Você não está nesta sala. Entre novamente com o código.</div>
        <a class="btn" href="index.php">Voltar ao início</a>
    </div></div></body></html>
    <?php
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM jogadores WHERE sala_codigo = ? ORDER BY entrou_em ASC');
$stmt->execute([$codigo]);
$jogadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
$fase = $sala['fase'];
$souImpostor = (bool) $eu['is_impostor'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Quem é o Impostor?</title>
<link rel="stylesheet" href="style.css">
</head>
<body data-fase="<?= e($fase) ?>" data-sala="<?= e($codigo) ?>" data-token="<?= e($token) ?>">
<div class="wrap">
<h1>🕵️ Quem é o Impostor?</h1>

<?php if ($fase === 'lobby'): ?>

    <div class="card">
        <div class="turn-name">Olá, <?= e($eu['nome']) ?>! 👋</div>
        <p class="instruction">Você entrou na sala <b><?= e($codigo) ?></b>.<br>Aguardando o anfitrião iniciar o jogo...</p>
        <div style="text-align:center; margin-top:10px;" class="loader-dots"><span></span><span></span><span></span></div>
    </div>

<?php elseif ($fase === 'revelar'): ?>

    <div class="card">
        <div class="turn-name"><?= e($eu['nome']) ?></div>
        <div id="area-carta">
            <div class="secret-card oculta" id="carta" onclick="revelarCarta()">
                <div class="tag">Toque para revelar</div>
                <div class="valor">🔒</div>
            </div>
        </div>
        <p class="instruction" id="dica-instrucao">Só você deve ver esta tela. Toque na carta com cuidado.</p>
    </div>

    <script>
    function revelarCarta(){
        const carta = document.getElementById('carta');
        <?php if ($souImpostor): ?>
            carta.className = 'secret-card impostor';
            carta.innerHTML = '<div class="tag">Você é o IMPOSTOR</div><div class="valor">Dica: <?= e($sala['dica']) ?></div>';
        <?php else: ?>
            carta.className = 'secret-card normal';
            carta.innerHTML = '<div class="tag">Palavra secreta</div><div class="valor"><?= e($sala['palavra']) ?></div>';
        <?php endif; ?>
        carta.onclick = null;
        document.getElementById('dica-instrucao').textContent =
            <?= $souImpostor ? "'Tente descobrir a palavra e se disfarçar entre os demais!'" : "'Memorize e não fale a palavra em voz alta!'" ?>;
    }
    </script>

<?php elseif ($fase === 'discussao'): ?>

    <div class="card">
        <h2 style="text-align:center;">💬 Hora da discussão</h2>
        <?php if ($souImpostor): ?>
            <p class="instruction">Lembre-se: você é o <b>impostor</b>. Disfarce-se e tente adivinhar a palavra pelas dicas dos outros!</p>
        <?php else: ?>
            <p class="instruction">Diga uma palavra relacionada ao tema, sem revelar a palavra secreta.</p>
        <?php endif; ?>
        <p class="hint">Aguardando o anfitrião iniciar a votação...</p>
        <div style="text-align:center;" class="loader-dots"><span></span><span></span><span></span></div>
    </div>

<?php elseif ($fase === 'votacao'): ?>

    <div class="card" id="area-votacao">
        <h2 style="text-align:center;">🗳️ Quem é o impostor?</h2>
        <p class="instruction">Toque no nome de quem você suspeita:</p>
        <form method="post" action="api.php" id="form-votar">
            <input type="hidden" name="action" value="votar">
            <input type="hidden" name="sala" value="<?= e($codigo) ?>">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <?php foreach ($jogadores as $j): if ((int)$j['id'] === (int)$eu['id']) continue; ?>
                <button class="vote-btn <?= (int)$eu['voto_em'] === (int)$j['id'] ? 'votado' : '' ?>"
                        type="submit" name="suspeito" value="<?= (int)$j['id'] ?>">
                    <?= e($j['nome']) ?>
                </button>
            <?php endforeach; ?>
        </form>
        <?php if ($eu['voto_em'] !== null): ?>
            <p class="hint">Voto registrado! Você pode trocar tocando em outro nome.</p>
        <?php endif; ?>
    </div>

<?php elseif ($fase === 'resultado'):
    $contagem = [];
    foreach ($jogadores as $j) if ($j['voto_em'] !== null) $contagem[$j['voto_em']] = ($contagem[$j['voto_em']] ?? 0) + 1;
    arsort($contagem);
    $maisVotadoId = array_key_first($contagem);
    $impostores = array_filter($jogadores, fn($j) => (int) $j['is_impostor'] === 1);
    $nomesImpostores = array_map(fn($j) => $j['nome'], $impostores);
?>

    <div class="card">
        <h2 style="text-align:center;">🎉 Resultado</h2>
        <div class="reveal-info">
            <b>Palavra secreta:</b> <?= e($sala['palavra']) ?><br>
            <b>Dica do(s) impostor(es):</b> <?= e($sala['dica']) ?><br>
            <b><?= count($nomesImpostores) > 1 ? 'Impostores' : 'Impostor' ?>:</b> <?= e(implode(', ', $nomesImpostores)) ?>
        </div>
        <p class="hint">Aguardando o anfitrião iniciar uma nova rodada...</p>
        <div style="text-align:center;" class="loader-dots"><span></span><span></span><span></span></div>
    </div>

<?php endif; ?>
</div>

<script>
const fase = document.body.dataset.fase;
const sala = document.body.dataset.sala;
const token = document.body.dataset.token;

async function checarAtualizacao() {
    try {
        const resp = await fetch(`api.php?action=estado_jogador&sala=${sala}&token=${token}`);
        if (!resp.ok) return;
        const dados = await resp.json();
        if (dados.erro) return;
        if (dados.fase !== fase) {
            location.reload();
        }
    } catch (e) { /* ignora falhas de rede pontuais */ }
}
setInterval(checarAtualizacao, 2000);
</script>
</body>
</html>
