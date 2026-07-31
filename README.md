# Quem é o Impostor? — Versão Online

Jogo de festa multiplayer que roda no navegador. Cada jogador entra com o
próprio celular usando um código de sala (igual ao Kahoot) e recebe a
palavra secreta ou a dica (se for o impostor) só na sua tela.

## Como funciona

- **Anfitrião**: abre o site, clica em "Criar sala" e mostra o código
  gerado para o grupo (numa TV, notebook, ou até o próprio celular). É ele
  quem controla o andamento da partida (iniciar, avançar fases, nova rodada).
- **Jogadores**: cada um, no próprio celular, abre o mesmo site, digita o
  código da sala e o próprio nome. Não precisa passar o aparelho — cada um
  vê sua carta em sigilo na própria tela.
- **Categorias**: o anfitrião escolhe entre times de futebol, esportes,
  países, cidades, transporte, comidas, animais, profissões, lugares,
  filmes/séries, tecnologia, internet/apps, festas, ou "todas" misturadas.
- Suporta **qualquer número de jogadores** (mínimo 3) e permite configurar
  a **quantidade de impostores**.

## Requisitos para rodar

- PHP 8.0 ou superior
- Extensões: `pdo_sqlite` e `mbstring` (geralmente já vêm habilitadas na
  maioria das hospedagens PHP; em Ubuntu: `apt install php-sqlite3 php-mbstring`)
- Não precisa de MySQL nem de configuração extra: os dados ficam em um
  arquivo local `game.db` (criado automaticamente na primeira execução),
  então a pasta do projeto precisa ter permissão de escrita.

## Testar localmente

```bash
cd impostor-online
php -S localhost:8000
```

Depois acesse `http://localhost:8000` no navegador do computador para ser
o anfitrião, e em outro navegador/aba (ou celular na mesma rede, trocando
`localhost` pelo IP do computador) para entrar como jogador.

## Colocar de verdade "na internet"

Para qualquer pessoa entrar de qualquer lugar (não só na mesma rede local),
é preciso hospedar os arquivos em um servidor com PHP público, por exemplo:
um plano de hospedagem compartilhada, um VPS, ou serviços como Railway,
Render, Hostinger, etc. Basta enviar todos os arquivos desta pasta para lá
(via FTP/painel) e acessar pelo domínio/URL fornecido pelo serviço.

## Estrutura dos arquivos

- `index.php` — tela inicial (criar sala / entrar em sala)
- `host.php` — painel do anfitrião (tela principal/TV)
- `jogo.php` — tela de cada jogador (celular individual)
- `api.php` — todas as ações do jogo e os endpoints de atualização em tempo real
- `db.php` — conexão e criação automática do banco SQLite
- `categorias.php` — banco de palavras organizado por categoria
- `style.css` — visual do jogo
