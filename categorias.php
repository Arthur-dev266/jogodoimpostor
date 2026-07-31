<?php
/**
 * Banco de palavras organizado por categorias. Cada par tem a palavra
 * secreta (vista pela maioria) e a dica (vista pelo impostor).
 */
function obter_categorias(): array {
    return [
        'futebol' => [
            'label' => '⚽ Times de Futebol',
            'palavras' => [
                ['palavra' => 'Flamengo',         'dica' => 'Time de futebol do Rio de Janeiro'],
                ['palavra' => 'Corinthians',      'dica' => 'Time de futebol de São Paulo'],
                ['palavra' => 'Palmeiras',        'dica' => 'Time de futebol paulista'],
                ['palavra' => 'São Paulo',        'dica' => 'Time de futebol paulista'],
                ['palavra' => 'Santos',           'dica' => 'Time de futebol do litoral paulista'],
                ['palavra' => 'Grêmio',           'dica' => 'Time de futebol gaúcho'],
                ['palavra' => 'Internacional',    'dica' => 'Time de futebol gaúcho'],
                ['palavra' => 'Cruzeiro',         'dica' => 'Time de futebol mineiro'],
                ['palavra' => 'Atlético Mineiro', 'dica' => 'Time de futebol mineiro'],
                ['palavra' => 'Vasco da Gama',    'dica' => 'Time de futebol carioca'],
                ['palavra' => 'Botafogo',         'dica' => 'Time de futebol carioca'],
                ['palavra' => 'Fluminense',       'dica' => 'Time de futebol carioca'],
                ['palavra' => 'Real Madrid',      'dica' => 'Time de futebol espanhol'],
                ['palavra' => 'Barcelona',        'dica' => 'Time de futebol espanhol'],
                ['palavra' => 'Manchester United','dica' => 'Time de futebol inglês'],
                ['palavra' => 'Liverpool',        'dica' => 'Time de futebol inglês'],
                ['palavra' => 'Chelsea',          'dica' => 'Time de futebol inglês'],
                ['palavra' => 'Boca Juniors',     'dica' => 'Time de futebol argentino'],
                ['palavra' => 'River Plate',      'dica' => 'Time de futebol argentino'],
                ['palavra' => 'Bayern de Munique','dica' => 'Time de futebol alemão'],
                ['palavra' => 'Juventus',         'dica' => 'Time de futebol italiano'],
                ['palavra' => 'Seleção Brasileira','dica' => 'Time de futebol nacional'],
            ],
        ],
        'esportes' => [
            'label' => '🏀 Esportes',
            'palavras' => [
                ['palavra' => 'Basquete',   'dica' => 'Esporte jogado com cesta'],
                ['palavra' => 'Vôlei',      'dica' => 'Esporte jogado com rede'],
                ['palavra' => 'Tênis',      'dica' => 'Esporte jogado com raquete'],
                ['palavra' => 'Natação',    'dica' => 'Esporte praticado na água'],
                ['palavra' => 'Surfe',      'dica' => 'Esporte praticado em ondas'],
                ['palavra' => 'Skate',      'dica' => 'Esporte radical sobre rodinhas'],
                ['palavra' => 'Boxe',       'dica' => 'Esporte de luta com socos'],
                ['palavra' => 'Judô',       'dica' => 'Esporte de luta japonês'],
                ['palavra' => 'Corrida',    'dica' => 'Esporte de velocidade a pé'],
                ['palavra' => 'Ciclismo',   'dica' => 'Esporte praticado com bicicleta'],
                ['palavra' => 'Golfe',      'dica' => 'Esporte jogado com taco e bolinha'],
                ['palavra' => 'Handebol',   'dica' => 'Esporte jogado com as mãos e gol'],
                ['palavra' => 'Xadrez',     'dica' => 'Jogo de tabuleiro estratégico'],
                ['palavra' => 'Fórmula 1',  'dica' => 'Esporte de corrida de carros'],
                ['palavra' => 'Olimpíadas', 'dica' => 'Grande evento esportivo mundial'],
            ],
        ],
        'paises' => [
            'label' => '🌍 Países',
            'palavras' => [
                ['palavra' => 'Brasil',         'dica' => 'País da América do Sul'],
                ['palavra' => 'Argentina',      'dica' => 'País da América do Sul'],
                ['palavra' => 'Chile',          'dica' => 'País da América do Sul'],
                ['palavra' => 'Estados Unidos', 'dica' => 'País da América do Norte'],
                ['palavra' => 'México',         'dica' => 'País da América do Norte'],
                ['palavra' => 'Canadá',         'dica' => 'País da América do Norte'],
                ['palavra' => 'Portugal',       'dica' => 'País da Europa'],
                ['palavra' => 'Espanha',        'dica' => 'País da Europa'],
                ['palavra' => 'França',         'dica' => 'País da Europa'],
                ['palavra' => 'Itália',         'dica' => 'País da Europa'],
                ['palavra' => 'Alemanha',       'dica' => 'País da Europa'],
                ['palavra' => 'Japão',          'dica' => 'País da Ásia'],
                ['palavra' => 'China',          'dica' => 'País da Ásia'],
                ['palavra' => 'Índia',          'dica' => 'País da Ásia'],
                ['palavra' => 'Egito',          'dica' => 'País da África'],
                ['palavra' => 'Austrália',      'dica' => 'País/continente da Oceania'],
            ],
        ],
        'cidades' => [
            'label' => '🏙️ Cidades',
            'palavras' => [
                ['palavra' => 'Rio de Janeiro', 'dica' => 'Cidade brasileira litorânea'],
                ['palavra' => 'São Paulo',      'dica' => 'Maior cidade do Brasil'],
                ['palavra' => 'Nova York',      'dica' => 'Cidade famosa dos Estados Unidos'],
                ['palavra' => 'Paris',          'dica' => 'Capital de um país europeu'],
                ['palavra' => 'Londres',        'dica' => 'Capital de um país europeu'],
                ['palavra' => 'Tóquio',         'dica' => 'Capital de um país asiático'],
                ['palavra' => 'Roma',           'dica' => 'Cidade histórica da Europa'],
                ['palavra' => 'Buenos Aires',   'dica' => 'Capital de um país sul-americano'],
                ['palavra' => 'Lisboa',         'dica' => 'Capital de um país europeu'],
                ['palavra' => 'Miami',          'dica' => 'Cidade litorânea dos Estados Unidos'],
                ['palavra' => 'Dubai',          'dica' => 'Cidade luxuosa do Oriente Médio'],
                ['palavra' => 'Amsterdã',       'dica' => 'Cidade europeia cheia de canais'],
            ],
        ],
        'transporte' => [
            'label' => '🚗 Meios de Transporte',
            'palavras' => [
                ['palavra' => 'Carro',       'dica' => 'Meio de transporte terrestre'],
                ['palavra' => 'Ônibus',      'dica' => 'Meio de transporte terrestre'],
                ['palavra' => 'Moto',        'dica' => 'Meio de transporte terrestre'],
                ['palavra' => 'Bicicleta',   'dica' => 'Meio de transporte terrestre'],
                ['palavra' => 'Trem',        'dica' => 'Meio de transporte sobre trilhos'],
                ['palavra' => 'Metrô',       'dica' => 'Meio de transporte sobre trilhos'],
                ['palavra' => 'Avião',       'dica' => 'Meio de transporte aéreo'],
                ['palavra' => 'Helicóptero', 'dica' => 'Meio de transporte aéreo'],
                ['palavra' => 'Navio',       'dica' => 'Meio de transporte aquático'],
                ['palavra' => 'Barco',       'dica' => 'Meio de transporte aquático'],
                ['palavra' => 'Foguete',     'dica' => 'Meio de transporte que vai ao espaço'],
                ['palavra' => 'Patinete',    'dica' => 'Meio de transporte urbano pequeno'],
            ],
        ],
        'comida' => [
            'label' => '🍕 Comidas',
            'palavras' => [
                ['palavra' => 'Pizza',         'dica' => 'Comida italiana'],
                ['palavra' => 'Lasanha',       'dica' => 'Comida italiana'],
                ['palavra' => 'Sushi',         'dica' => 'Comida japonesa'],
                ['palavra' => 'Taco',          'dica' => 'Comida mexicana'],
                ['palavra' => 'Hambúrguer',    'dica' => 'Comida rápida (fast food)'],
                ['palavra' => 'Feijoada',      'dica' => 'Comida típica brasileira'],
                ['palavra' => 'Churrasco',     'dica' => 'Comida típica brasileira'],
                ['palavra' => 'Coxinha',       'dica' => 'Salgado brasileiro'],
                ['palavra' => 'Pão de queijo', 'dica' => 'Salgado mineiro'],
                ['palavra' => 'Brigadeiro',    'dica' => 'Doce brasileiro'],
                ['palavra' => 'Chocolate',     'dica' => 'Doce feito de cacau'],
                ['palavra' => 'Sorvete',       'dica' => 'Sobremesa gelada'],
                ['palavra' => 'Pastel',        'dica' => 'Salgado frito brasileiro'],
            ],
        ],
        'animais' => [
            'label' => '🐾 Animais',
            'palavras' => [
                ['palavra' => 'Cachorro',   'dica' => 'Animal de estimação'],
                ['palavra' => 'Gato',       'dica' => 'Animal de estimação'],
                ['palavra' => 'Elefante',   'dica' => 'Animal grande e cinza'],
                ['palavra' => 'Leão',       'dica' => 'Animal chamado de "rei da selva"'],
                ['palavra' => 'Girafa',     'dica' => 'Animal de pescoço muito comprido'],
                ['palavra' => 'Tubarão',    'dica' => 'Animal marinho perigoso'],
                ['palavra' => 'Águia',      'dica' => 'Ave de rapina'],
                ['palavra' => 'Cavalo',     'dica' => 'Animal usado para cavalgar'],
                ['palavra' => 'Camelo',     'dica' => 'Animal do deserto'],
                ['palavra' => 'Pinguim',    'dica' => 'Ave que não voa e vive no frio'],
                ['palavra' => 'Urso',       'dica' => 'Animal grande e peludo'],
                ['palavra' => 'Macaco',     'dica' => 'Animal que vive em árvores'],
                ['palavra' => 'Dinossauro', 'dica' => 'Animal pré-histórico'],
            ],
        ],
        'profissoes' => [
            'label' => '👩‍⚕️ Profissões',
            'palavras' => [
                ['palavra' => 'Médico',     'dica' => 'Profissão da área da saúde'],
                ['palavra' => 'Dentista',   'dica' => 'Profissão da área da saúde'],
                ['palavra' => 'Professor',  'dica' => 'Profissão da área da educação'],
                ['palavra' => 'Policial',   'dica' => 'Profissão que mantém a ordem'],
                ['palavra' => 'Bombeiro',   'dica' => 'Profissão que apaga incêndios'],
                ['palavra' => 'Advogado',   'dica' => 'Profissão da área jurídica'],
                ['palavra' => 'Engenheiro', 'dica' => 'Profissão que projeta construções'],
                ['palavra' => 'Cozinheiro', 'dica' => 'Profissão que prepara comida'],
                ['palavra' => 'Piloto',     'dica' => 'Profissão que conduz aviões'],
                ['palavra' => 'Cantor',     'dica' => 'Profissão artística musical'],
                ['palavra' => 'Astronauta', 'dica' => 'Profissão que viaja ao espaço'],
                ['palavra' => 'Jornalista', 'dica' => 'Profissão que informa notícias'],
            ],
        ],
        'lugares' => [
            'label' => '📍 Lugares',
            'palavras' => [
                ['palavra' => 'Praia',      'dica' => 'Lugar com areia e mar'],
                ['palavra' => 'Escola',     'dica' => 'Lugar de ensino'],
                ['palavra' => 'Hospital',   'dica' => 'Lugar onde se tratam doenças'],
                ['palavra' => 'Aeroporto',  'dica' => 'Lugar de onde saem aviões'],
                ['palavra' => 'Biblioteca', 'dica' => 'Lugar cheio de livros'],
                ['palavra' => 'Shopping',   'dica' => 'Lugar cheio de lojas'],
                ['palavra' => 'Cinema',     'dica' => 'Lugar para assistir filmes'],
                ['palavra' => 'Parque',     'dica' => 'Lugar ao ar livre para lazer'],
                ['palavra' => 'Estádio',    'dica' => 'Lugar de jogos esportivos'],
                ['palavra' => 'Zoológico',  'dica' => 'Lugar com animais em exibição'],
                ['palavra' => 'Museu',      'dica' => 'Lugar com obras e exposições'],
                ['palavra' => 'Padaria',    'dica' => 'Lugar que vende pão'],
            ],
        ],
        'filmes' => [
            'label' => '🎬 Filmes e Séries',
            'palavras' => [
                ['palavra' => 'Titanic',       'dica' => 'Filme de romance e naufrágio'],
                ['palavra' => 'Tubarão',       'dica' => 'Filme clássico de suspense no mar'],
                ['palavra' => 'Jurassic Park', 'dica' => 'Filme com dinossauros'],
                ['palavra' => 'Matrix',        'dica' => 'Filme de ficção científica'],
                ['palavra' => 'Frozen',        'dica' => 'Animação com duas irmãs'],
                ['palavra' => 'Shrek',         'dica' => 'Animação sobre um ogro'],
                ['palavra' => 'Avatar',        'dica' => 'Filme de ficção científica em outro planeta'],
                ['palavra' => 'Vingadores',    'dica' => 'Filme de super-heróis'],
                ['palavra' => 'Friends',       'dica' => 'Série de comédia sobre amigos'],
                ['palavra' => 'Stranger Things','dica' => 'Série de mistério e sobrenatural'],
            ],
        ],
        'tecnologia' => [
            'label' => '📱 Tecnologia',
            'palavras' => [
                ['palavra' => 'Celular',        'dica' => 'Aparelho eletrônico portátil'],
                ['palavra' => 'Computador',     'dica' => 'Aparelho eletrônico de trabalho'],
                ['palavra' => 'Geladeira',      'dica' => 'Eletrodoméstico da cozinha'],
                ['palavra' => 'Robô',           'dica' => 'Máquina que imita ações humanas'],
                ['palavra' => 'Fone de ouvido', 'dica' => 'Acessório para ouvir música'],
                ['palavra' => 'Câmera',         'dica' => 'Aparelho para tirar fotos'],
                ['palavra' => 'Impressora',     'dica' => 'Aparelho que imprime documentos'],
                ['palavra' => 'Controle remoto','dica' => 'Aparelho que controla a TV'],
            ],
        ],
        'internet' => [
            'label' => '💻 Internet e Apps',
            'palavras' => [
                ['palavra' => 'Instagram', 'dica' => 'Rede social de fotos e vídeos'],
                ['palavra' => 'TikTok',    'dica' => 'Rede social de vídeos curtos'],
                ['palavra' => 'YouTube',   'dica' => 'Plataforma de vídeos'],
                ['palavra' => 'WhatsApp',  'dica' => 'Aplicativo de mensagens'],
                ['palavra' => 'Netflix',   'dica' => 'Serviço de streaming'],
                ['palavra' => 'Google',    'dica' => 'Site de buscas'],
                ['palavra' => 'Spotify',   'dica' => 'Serviço de streaming de música'],
                ['palavra' => 'Discord',   'dica' => 'Aplicativo de chat para comunidades'],
                ['palavra' => 'LinkedIn',  'dica' => 'Rede social profissional'],
                ['palavra' => 'Wi-Fi',     'dica' => 'Tecnologia de internet sem fio'],
            ],
        ],
        'festas' => [
            'label' => '🎉 Festas e Comemorações',
            'palavras' => [
                ['palavra' => 'Carnaval',     'dica' => 'Festa popular brasileira'],
                ['palavra' => 'Halloween',    'dica' => 'Festa de fantasias'],
                ['palavra' => 'Casamento',    'dica' => 'Cerimônia de união entre pessoas'],
                ['palavra' => 'Aniversário',  'dica' => 'Comemoração anual de uma pessoa'],
                ['palavra' => 'Réveillon',    'dica' => 'Festa de virada de ano'],
                ['palavra' => 'Copa do Mundo','dica' => 'Grande competição esportiva mundial'],
            ],
        ],
    ];
}

function obter_label_categoria(string $chave): string {
    if ($chave === 'todas') return '🎲 Todas as categorias (misturado)';
    $categorias = obter_categorias();
    return $categorias[$chave]['label'] ?? $chave;
}

/** Sorteia um par palavra/dica de acordo com a categoria escolhida ('todas' mistura tudo). */
function sortear_par(string $categoriaChave): array {
    $categorias = obter_categorias();
    if ($categoriaChave === 'todas' || !isset($categorias[$categoriaChave])) {
        $pool = [];
        foreach ($categorias as $cat) {
            foreach ($cat['palavras'] as $par) {
                $pool[] = $par;
            }
        }
    } else {
        $pool = $categorias[$categoriaChave]['palavras'];
    }
    return $pool[array_rand($pool)];
}
