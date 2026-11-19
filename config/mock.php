<?php

return [
    'store' => [
        'name' => 'Alê Bordados Kids',
        'tagline' => 'Bordados personalizados com carinho',
        'cart_count' => 2,
        'email' => 'loja@email.com',
        'whatsapp' => '(XX) XXXXX-XXXX',
        'address' => 'Atendimento online para todo o Brasil',
    ],

    'social' => [
        'facebook' => 'https://facebook.com',
        'instagram' => 'https://instagram.com',
        'tiktok' => 'https://tiktok.com',
    ],

    'payments' => [
        'Pix',
        'Cartão de crédito',
        'Cartão de débito',
        'Boleto',
    ],

    'own_piece' => [
        'imagem' => 'images/body.png',
        'alt' => 'Peça infantil com bordado personalizado',
    
        'ajuste' => 'contain',
    ],

    'hero' => [
        'eyebrow' => 'Bordados personalizados',
        'title' => 'Carinho em cada ponto,',
        'highlight' => 'feito para o seu bebê',
        'description' => 'Enxovais, roupas e toalhas infantis com bordados delicados e personalizados para eternizar os primeiros momentos.',
    ],


    'hero_slides' => [
        [
            'imagem' => 'images/hero_banner.png',
            'alt' => 'Banner de bordado com ursinha Sara Maria',
            'ajuste' => 'cover',
            'posicao' => 'center center',
            'eyebrow' => 'Bordados personalizados',
            'title' => 'Carinho em cada ponto,',
            'highlight' => 'feito para o seu bebê',
            'description' => 'Enxovais, roupas e toalhas infantis com bordados delicados e personalizados para eternizar os primeiros momentos.',
            'acao' => 'Ver coleção',
            'link' => '#produtos',
        ],
        [
            'imagem' => 'images/maria_flor.png',
            'alt' => 'Kit de fraldas bordadas para enxoval',
            'ajuste' => 'cover',
            'posicao' => 'center 40%',
            'eyebrow' => 'Enxoval completo',
            'title' => 'Tudo prontinho para a chegada,',
            'highlight' => 'com muito carinho',
            'description' => 'Kits e conjuntos bordados com acabamento delicado para receber o bebê com todo o amor.',
            'acao' => 'Conhecer os kits',
            'link' => '#produtos',
        ],
        [
            'imagem' => 'images/agata.png',
            'alt' => 'Body infantil com bordado personalizado',
            'ajuste' => 'cover',
            'posicao' => 'center 30%',
            'eyebrow' => 'Peças únicas',
            'title' => 'O nome do seu bebê,',
            'highlight' => 'bordado à mão',
            'description' => 'Escolha a cor, a fonte e o detalhe: cada peça é feita especialmente para contar a sua história.',
            'acao' => 'Ver personalizações',
            'link' => '#produtos',
        ],
    ],

    'rating' => [
        'nota' => 4.5,
        'total' => 23,
    ],

    'testimonials' => [
        [
            'nome' => 'Maria Clara',
            'nota' => 5,
            'texto' => 'A fraldinha ficou ainda mais linda com o bordado. Acabamento impecável e atendimento muito carinhoso!',
        ],
        [
            'nome' => 'Bruna Silva',
            'nota' => 5,
            'texto' => 'Enviei a foto da peça e recebi uma sugestão perfeita. O bordado superou tudo o que eu imaginava.',
        ],
        [
            'nome' => 'João Santana',
            'nota' => 4.5,
            'texto' => 'Qualidade excelente e entrega dentro do prazo. O enxoval do nosso bebê ficou completo e especial.',
        ],
        [
            'nome' => 'Julia Garcia',
            'nota' => 5,
            'texto' => 'Cada detalhe foi feito com muito cuidado. Com certeza voltarei a comprar para presentear.',
        ],
        [
            'nome' => 'Lívia Costa',
            'nota' => 4.5,
            'texto' => 'As toalhinhas bordadas são delicadas e macias. Ficaram perfeitas para o quarto do bebê.',
        ],
        [
            'nome' => 'Camila Rocha',
            'nota' => 5,
            'texto' => 'O body personalizado ficou encantador. A qualidade do bordado e o carinho no atendimento fizeram toda a diferença.',
        ],
        [
            'nome' => 'Fernanda Alves',
            'nota' => 5,
            'texto' => 'Recebi o kit fralda dentro do prazo e muito bem embalado. As peças são ainda mais bonitas ao vivo.',
        ],
        [
            'nome' => 'Patrícia Lima',
            'nota' => 4.5,
            'texto' => 'A toalha de banho com capuz ficou perfeita para o enxoval. Bordado delicado e acabamento impecável.',
        ],
        [
            'nome' => 'Rafael Martins',
            'nota' => 5,
            'texto' => 'Presenteei minha sobrinha com uma fraldinha bordada e ela amou. Voltarei a comprar com certeza.',
        ],
        [
            'nome' => 'Beatriz Souza',
            'nota' => 5,
            'texto' => 'Atendimento rápido, peças lindas e bordados cheios de personalidade. Recomendo de olhos fechados.',
        ],
    ],


    'personalizacao' => [
        'cores_produto' => [
            'Branco',
            'Rosa',
            'Azul',
            'Amarelo',
            'Verde',
            'Bege',
        ],
        'cores_linha' => [
            'Branco',
            'Rosa',
            'Azul',
            'Dourado',
            'Prata',
            'Verde',
            'Vermelho',
            'Preto',
        ],
        'fontes' => [
            'Clássica',
            'Cursiva',
            'Redonda',
            'Elegante',
            'Infantil',
        ],
        'cor_padrao' => 'Branco',
    ],

    'admin' => [
       
        'nav' => [
            [
                'label' => 'Início',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
            ],
            [
                'label' => 'Cadastrar Produto',
                'route' => 'admin.produtos.create',
                'active' => ['admin.produtos.create'],
            ],
            [
                'label' => 'Gerenciar produtos',
                'route' => 'admin.produtos.index',
                'active' => ['admin.produtos.index', 'admin.produtos.edit'],
            ],
            [
                'label' => 'Gerenciar pedidos',
                'route' => 'admin.pedidos.index',
                'active' => ['admin.pedidos.index'],
            ],
        ],

        'months' => [
            1 => 'janeiro',
            2 => 'fevereiro',
            3 => 'março',
            4 => 'abril',
            5 => 'maio',
            6 => 'junho',
            7 => 'julho',
            8 => 'agosto',
            9 => 'setembro',
            10 => 'outubro',
            11 => 'novembro',
            12 => 'dezembro',
        ],

        'months_short' => ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'],

        'weekdays' => ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'],

        'dashboard' => [
            'metrics' => [
                ['icon' => 'box', 'label' => 'Em produção:', 'value' => 14],
                ['icon' => 'cart', 'label' => 'Pedidos novos:', 'value' => 7],
                ['icon' => 'document', 'label' => 'Orçamentos pendentes:', 'value' => 3],
                ['icon' => 'card', 'label' => 'Pagamentos pendentes:', 'value' => 4],
            ],
            'revenue' => [
                'title' => 'Faturamento:',
                'labels' => ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'],
                'values' => [8200, 12000, 9800, 11200, 10400, 12600, 13800, 12900, 14600, 13200, 15800, 17200],
                'min' => 4000,
                'max' => 18000,
                'format' => 'currency',
                'color' => 'var(--cor-rosa)',
                'fill' => true,
            ],
            'financial' => [
                'years' => [2025, 2026],
                'year_factors' => [2025 => 0.88, 2026 => 1.0],
                'months' => [
                    1 => ['revenue' => 28900, 'completed' => 15, 'sold' => 34],
                    2 => ['revenue' => 32450, 'completed' => 18, 'sold' => 42],
                    3 => ['revenue' => 30100, 'completed' => 16, 'sold' => 38],
                    4 => ['revenue' => 33200, 'completed' => 19, 'sold' => 44],
                    5 => ['revenue' => 31800, 'completed' => 17, 'sold' => 40],
                    6 => ['revenue' => 35200, 'completed' => 20, 'sold' => 47],
                    7 => ['revenue' => 36600, 'completed' => 21, 'sold' => 49],
                    8 => ['revenue' => 34100, 'completed' => 19, 'sold' => 45],
                    9 => ['revenue' => 37800, 'completed' => 22, 'sold' => 52],
                    10 => ['revenue' => 35900, 'completed' => 21, 'sold' => 50],
                    11 => ['revenue' => 39100, 'completed' => 23, 'sold' => 55],
                    12 => ['revenue' => 41200, 'completed' => 25, 'sold' => 58],
                ],
            ],
            'pendings' => [
                ['title' => 'Solicitação #038 - Bruna', 'description' => 'Aguardando análise'],
                ['title' => 'Solicitação #034 - Ana', 'description' => 'Enviada a 2h'],
                ['title' => 'Pedido #125 - João', 'description' => 'O prazo é daqui 5 dias'],
                ['title' => 'Pedido #115 - José', 'description' => 'Pagamento pendente'],
            ],
            'chat' => [
                'contacts' => [
                    ['name' => 'Maria Clara', 'unread' => 2, 'tag' => 'Pedido #125', 'message' => 'Olá! Gostaria de saber se o bordado do enxoval fica pronto até sexta.'],
                    ['name' => 'Bruna Silva', 'unread' => 1, 'tag' => 'Orçamento', 'message' => 'Olá, enviei as fotos da peça, gostaria de saber o valor!'],
                    ['name' => 'João Santana', 'unread' => 0, 'tag' => 'Entrega', 'message' => 'Bom dia! Qual é a previsão de entrega do pedido?'],
                    ['name' => 'Julia Garcia', 'unread' => 0, 'tag' => 'Personalização', 'message' => 'Oi! A toalha pode ser bordada com o nome em azul?'],
                ],
            ],
        ],

        'orders' => [
            'chart' => [
                'title' => 'Pedidos e entregas',
                'labels' => ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'],
                'series' => [
                    ['label' => 'pedidos', 'color' => 'var(--cor-rosa)', 'values' => [12, 14, 15, 18, 16, 20, 19, 22, 21, 24, 23, 26]],
                    ['label' => 'entregas', 'color' => 'var(--cor-rosa-escuro)', 'values' => [11, 13, 15, 17, 16, 18, 19, 20, 21, 22, 23, 24]],
                ],
                'min' => 0,
                'max' => 28,
                'format' => 'number',
            ],
            'deadline_days' => 15,
        ],

        'order_stages' => [
            'labels' => [
                'cadastrado' => 'pedido cadastrado',
                'finalizado' => 'pedido finalizado',
                'pagamento' => 'pagamento efetuado',
                'entregue' => 'pedido entregue',
            ],
            'by_status' => [
                'novo' => ['cadastrado'],
                'analise' => ['cadastrado'],
                'orcamento' => ['cadastrado'],
                'pagamento' => ['cadastrado'],
                'producao' => ['cadastrado'],
                'finalizado' => ['cadastrado', 'finalizado', 'pagamento'],
                'entregue' => ['cadastrado', 'finalizado', 'pagamento', 'entregue'],
                'cancelado' => ['cadastrado'],
            ],
        ],
    ],

    'status' => [
        'novo' => [
            'label' => 'Novo',
            'class' => 'badge-novo',
        ],
        'analise' => [
            'label' => 'Em análise',
            'class' => 'badge-novo',
        ],
        'orcamento' => [
            'label' => 'Orçamento pendente',
            'class' => 'badge-orcamento',
        ],
        'pagamento' => [
            'label' => 'Pagamento pendente',
            'class' => 'badge-pagamento',
        ],
        'producao' => [
            'label' => 'Em produção',
            'class' => 'badge-producao',
        ],
        'finalizado' => [
            'label' => 'Finalizado',
            'class' => 'badge-finalizado',
        ],
        'entregue' => [
            'label' => 'Entregue',
            'class' => 'badge-finalizado',
        ],
        'cancelado' => [
            'label' => 'Cancelado',
            'class' => 'badge-cancelado',
        ],
    ],
];
