<?php
/**
 * Conexão com o banco SQLite compartilhado entre todos os dispositivos
 * (é isso que permite o jogo funcionar "online", com cada jogador em
 * seu próprio celular, parecido com o Kahoot).
 */

function obter_pdo(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $caminho = __DIR__ . '/game.db';
        $pdo = new PDO('sqlite:' . $caminho);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA journal_mode = WAL;');
        $pdo->exec('PRAGMA busy_timeout = 3000;');

        $pdo->exec('CREATE TABLE IF NOT EXISTS salas (
            codigo          TEXT PRIMARY KEY,
            host_token      TEXT NOT NULL,
            categoria       TEXT NOT NULL DEFAULT "todas",
            qtd_impostores  INTEGER NOT NULL DEFAULT 1,
            fase            TEXT NOT NULL DEFAULT "lobby",
            palavra         TEXT,
            dica            TEXT,
            criada_em       INTEGER NOT NULL,
            atualizada_em   INTEGER NOT NULL
        )');

        $pdo->exec('CREATE TABLE IF NOT EXISTS jogadores (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            sala_codigo     TEXT NOT NULL,
            nome            TEXT NOT NULL,
            token           TEXT NOT NULL,
            is_impostor     INTEGER NOT NULL DEFAULT 0,
            voto_em         INTEGER,
            entrou_em       INTEGER NOT NULL
        )');
    }
    return $pdo;
}

function gerar_codigo_sala(PDO $pdo): string {
    // Sem caracteres ambíguos (0/O, 1/I) para facilitar digitação no celular
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $codigo = '';
        for ($i = 0; $i < 4; $i++) {
            $codigo .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM salas WHERE codigo = ?');
        $stmt->execute([$codigo]);
    } while ((int) $stmt->fetchColumn() > 0);
    return $codigo;
}

function gerar_token(): string {
    return bin2hex(random_bytes(12));
}

function tocar_atualizacao(PDO $pdo, string $codigoSala): void {
    $stmt = $pdo->prepare('UPDATE salas SET atualizada_em = ? WHERE codigo = ?');
    $stmt->execute([time(), $codigoSala]);
}

/** Remove salas abandonadas há mais de 6 horas (limpeza simples, sem cron). */
function limpar_salas_antigas(PDO $pdo): void {
    $limite = time() - (6 * 3600);
    $stmt = $pdo->prepare('SELECT codigo FROM salas WHERE atualizada_em < ?');
    $stmt->execute([$limite]);
    $antigas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($antigas) {
        $in = implode(',', array_fill(0, count($antigas), '?'));
        $pdo->prepare("DELETE FROM jogadores WHERE sala_codigo IN ($in)")->execute($antigas);
        $pdo->prepare("DELETE FROM salas WHERE codigo IN ($in)")->execute($antigas);
    }
}
