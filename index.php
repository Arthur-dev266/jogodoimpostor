<?php
$erro = $_GET['erro'] ?? null;
$salaPreenchida = strtoupper(trim($_GET['sala'] ?? ''));
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Quem é o Impostor? - Online</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
    <h1>🕵️ Quem é o Impostor?</h1>
    <div class="subtitle">Jogue online com qualquer número de pessoas, cada uma no seu celular</div>

    <div class="card">
        <?php if ($erro): ?>
            <div class="error"><?= e($erro) ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button type="button" class="ativo" onclick="mostrarAba('entrar')">Entrar em uma sala</button>
            <button type="button" onclick="mostrarAba('criar')">Criar sala (anfitrião)</button>
        </div>

        <div id="aba-entrar" class="form-tab ativo">
            <form method="post" action="api.php">
                <input type="hidden" name="action" value="entrar_sala">
                <label>Código da sala</label>
                <input type="text" name="sala" class="pin" maxlength="4" placeholder="ABCD"
                       value="<?= e($salaPreenchida) ?>" required autocomplete="off">
                <label>Seu nome</label>
                <input type="text" name="nome" placeholder="Como te chamam?" maxlength="20" required autocomplete="off">
                <button class="btn" type="submit">Entrar na sala</button>
            </form>
        </div>

        <div id="aba-criar" class="form-tab">
            <p style="color:var(--muted); font-size:.9rem;">
                Crie uma sala e mostre o código para o grupo (numa TV, notebook ou celular do anfitrião).
                Cada jogador entra pelo próprio celular, informando esse código.
            </p>
            <form method="post" action="api.php">
                <input type="hidden" name="action" value="criar_sala">
                <button class="btn gold" type="submit">Criar nova sala</button>
            </form>
        </div>
    </div>
    <footer-note>O anfitrião controla o andamento da partida; cada jogador vê sua palavra ou dica só no próprio aparelho 🤫</footer-note>
</div>

<script>
function mostrarAba(nome){
    document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('ativo'));
    document.querySelectorAll('.form-tab').forEach(t => t.classList.remove('ativo'));
    event.currentTarget.classList.add('ativo');
    document.getElementById('aba-' + nome).classList.add('ativo');
}
</script>
</body>
</html>
