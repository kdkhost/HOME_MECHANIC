<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContentSeeder extends Seeder
{
    /**
     * Insere conteudo de exemplo sem deletar dados existentes.
     * Seguro para rodar multiplas vezes (verifica slug/titulo duplicado).
     */
    public function run(): void
    {
        $this->seedServices();
        $this->seedBlogPosts();
        $this->seedTestimonials();
    }

    // ─── SERVICOS ──────────────────────────────────────────────
    private function seedServices(): void
    {
        $services = [
            [
                'title'       => 'Troca de Oleo e Filtros',
                'icon'        => 'fas fa-oil-can',
                'description' => 'Realizamos troca de oleo do motor, cambio, filtro de oleo, filtro de ar e filtro de combustivel com produtos de primeira linha.',
                'content'     => '<p>A troca de oleo e essencial para manter o motor do seu veiculo funcionando com eficiencia e prolongar sua vida util. Utilizamos oleos sinteticos e semissinteticos das melhores marcas do mercado.</p><h3>O que esta incluso</h3><ul><li>Oleo do motor (sintetico ou semissintetico)</li><li>Filtro de oleo novo</li><li>Verificacao do filtro de ar</li><li>Verificacao do filtro de combustivel</li><li>Inspecao visual de vazamentos</li></ul><p>Recomendamos a troca a cada 5.000 km ou 6 meses, o que vier primeiro.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800&h=500&fit=crop',
                'featured'    => true,
            ],
            [
                'title'       => 'Freios e Suspensao',
                'icon'        => 'fas fa-cogs',
                'description' => 'Manutencao completa do sistema de freios e suspensao: pastilhas, discos, amortecedores, molas e alinhamento.',
                'content'     => '<p>Seu sistema de freios e a peca mais importante para sua seguranca. Oferecemos diagnostico completo e substituicao de componentes com pecas originais ou de qualidade equivalente.</p><h3>Servicos</h3><ul><li>Troca de pastilhas e lonas</li><li>Retifica e troca de discos</li><li>Troca de amortecedores</li><li>Troca de molas e coifas</li><li>Alinhamento e balanceamento</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop',
                'featured'    => true,
            ],
            [
                'title'       => 'Diagnostico Eletronico',
                'icon'        => 'fas fa-laptop-code',
                'description' => 'Scanner automotivo de ultima geracao para leitura de falhas, reset de sensores e diagnostico completo da ECU.',
                'content'     => '<p>Contamos com equipamentos de diagnostico profissional que se comunicam diretamente com o modulo eletronico do seu veiculo. Identificamos falhas em sensores, atuadores e sistemas eletronicos com precisao.</p><h3>O que diagnosticamos</h3><ul><li>Motor e injecao eletronica</li><li>Transmissao automatica</li><li>ABS e controle de estabilidade</li><li>Airbags e sistemas de seguranca</li><li>Ar condicionado digital</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop',
                'featured'    => true,
            ],
            [
                'title'       => 'Ar Condicionado Automotivo',
                'icon'        => 'fas fa-snowflake',
                'description' => 'Recarga de gas, higienizacao do sistema, troca de filtro de cabine e reparo no compressor.',
                'content'     => '<p>Mantenha o conforto termico do seu veiculo com nosso servico completo de ar condicionado automotivo. Realizamos desde a simples recarga de gas ate reparos complexos no compressor.</p><h3>Servicos</h3><ul><li>Recarga de gas refrigerante R134a</li><li>Higienizacao do sistema com produto bactericida</li><li>Troca do filtro de cabine (filtro antipolen)</li><li>Verificacao de vazamentos</li><li>Reparo e troca do compressor</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=500&fit=crop',
                'featured'    => false,
            ],
            [
                'title'       => 'Eletrica Automotiva',
                'icon'        => 'fas fa-bolt',
                'description' => 'Reparo em alternador, motor de partida, farois, vidros eletricos e instalacao de acessorios.',
                'content'     => '<p>A parte eletrica do veiculo e responsavel por alimentar todos os componentes eletronicos. Problemas eletricos podem causar desde falhas no motor ate incendios.</p><h3>Servicos</h3><ul><li>Revisao do alternador e motor de partida</li><li>Troca e recarga de bateria</li><li>Reparo em vidros e travas eletricas</li><li>Instalacao de farois de LED e xenon</li><li>Instalacao de som, camera de re e sensores</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
                'featured'    => false,
            ],
            [
                'title'       => 'Motor e Mecanica Geral',
                'icon'        => 'fas fa-engine-warning',
                'description' => 'Retifica de motor, troca de correia dentada, junta do cabecote e reparos mecanicos completos.',
                'content'     => '<p>Nosso time de mecanicos especializados esta preparado para resolver qualquer problema mecanico do seu veiculo, desde ajustes simples ate retificas completas de motor.</p><h3>Servicos</h3><ul><li>Retifica parcial e completa do motor</li><li>Troca de correia dentada e acessorios</li><li>Troca de junta do cabecote</li><li>Regulagem de valvulas</li><li>Troca de embreagem</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop',
                'featured'    => true,
            ],
            [
                'title'       => 'Funilaria e Pintura',
                'icon'        => 'fas fa-paint-roller',
                'description' => 'Reparos em lataria, pintura automotiva, polimento tecnico e tratamento contra corrosao.',
                'content'     => '<p>Deixe seu veiculo com aparencia de novo! Nossa equipe de funilaria e pintura trabalha com tintas de alta qualidade e tecnologia de secagem em estufa.</p><h3>Servicos</h3><ul><li>Reparo de amassados e riscos</li><li>Pintura parcial e completa</li><li>Polimento tecnico cristalizado</li><li>Tratamento anticorrosivo</li><li>Envelopamento e plotagem</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=800&h=500&fit=crop',
                'featured'    => false,
            ],
            [
                'title'       => 'Injecao Eletronica',
                'icon'        => 'fas fa-microchip',
                'description' => 'Limpeza de bicos injetores, troca de sensores, regulagem de marcha lenta e manutencao do sistema de injecao.',
                'content'     => '<p>O sistema de injecao eletronica e o cerebro do motor do seu carro. Qualquer falha pode causar aumento no consumo, perda de potencia e emissoes excessivas.</p><h3>Servicos</h3><ul><li>Limpeza ultrassonica de bicos injetores</li><li>Troca de sensores (MAP, TPS, sonda lambda)</li><li>Regulagem de marcha lenta</li><li>Verificacao do corpo de borboleta</li><li>Analise de gases e emissoes</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=500&fit=crop',
                'featured'    => false,
            ],
            [
                'title'       => 'Cambio e Transmissao',
                'icon'        => 'fas fa-exchange-alt',
                'description' => 'Manutencao de cambio manual e automatico, troca de oleo do cambio e reparos na transmissao.',
                'content'     => '<p>Problemas no cambio podem comprometer a dirigibilidade e seguranca do veiculo. Oferecemos servicos especializados para cambios manuais, automatizados e automaticos.</p><h3>Servicos</h3><ul><li>Troca de oleo do cambio</li><li>Revisao do cambio manual</li><li>Manutencao do cambio automatico/CVT</li><li>Troca de kit de embreagem</li><li>Reparo em caixa de direcao</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop',
                'featured'    => false,
            ],
            [
                'title'       => 'Revisao Completa',
                'icon'        => 'fas fa-clipboard-check',
                'description' => 'Revisao de fabrica com checklist completo: motor, freios, suspensao, eletrica e fluidos.',
                'content'     => '<p>A revisao periodica e fundamental para manter seu veiculo em perfeito estado e preservar a garantia. Seguimos o checklist completo do fabricante.</p><h3>O que verificamos</h3><ul><li>Niveis de fluidos (oleo, arrefecimento, freio, direcao)</li><li>Estado de correias e mangueiras</li><li>Sistema de freios completo</li><li>Suspensao e direcao</li><li>Parte eletrica e iluminacao</li><li>Pneus e alinhamento</li></ul><p>Emitimos ordem de servico detalhada com fotos do que foi encontrado e do que foi realizado.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=800&h=500&fit=crop',
                'featured'    => true,
            ],
        ];

        foreach ($services as $i => $data) {
            $slug = Str::slug($data['title']);
            if (DB::table('services')->where('slug', $slug)->exists()) continue;

            DB::table('services')->insert([
                'title'       => $data['title'],
                'slug'        => $slug,
                'description' => $data['description'],
                'content'     => $data['content'],
                'icon'        => $data['icon'],
                'cover_image' => $data['cover_image'],
                'featured'    => $data['featured'],
                'sort_order'  => $i + 1,
                'active'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('Servicos inseridos com sucesso.');
    }

    // ─── BLOG POSTS ────────────────────────────────────────────
    private function seedBlogPosts(): void
    {
        $userId = DB::table('users')->value('id') ?? 1;

        $posts = [
            [
                'title'   => 'Quando trocar o oleo do motor? Guia completo',
                'excerpt' => 'Descubra o intervalo ideal para troca de oleo e os sinais de que esta na hora de fazer a manutencao.',
                'content' => '<p>A troca de oleo e uma das manutencoes mais importantes do seu veiculo. Um oleo velho ou contaminado pode causar desgaste prematuro do motor e ate danos irreversiveis.</p><h2>Intervalo recomendado</h2><p>Para a maioria dos veiculos modernos, o intervalo ideal e:</p><ul><li><strong>Oleo mineral:</strong> a cada 5.000 km ou 6 meses</li><li><strong>Oleo semissintetico:</strong> a cada 7.500 km ou 8 meses</li><li><strong>Oleo sintetico:</strong> a cada 10.000 km ou 12 meses</li></ul><h2>Sinais de que o oleo precisa ser trocado</h2><ul><li>Oleo escuro e com cheiro de queimado</li><li>Barulho excessivo no motor</li><li>Luz do oleo acesa no painel</li><li>Motor aquecendo mais que o normal</li></ul><p>Nao negligencie esta manutencao! Agende ja sua revisao conosco.</p>',
            ],
            [
                'title'   => '5 sinais de que os freios do seu carro precisam de atencao',
                'excerpt' => 'Aprenda a identificar quando os freios estao pedindo manutencao antes que vire um problema grave.',
                'content' => '<p>Os freios sao o principal item de seguranca do seu veiculo. Ignorar sinais de desgaste pode colocar voce e sua familia em risco.</p><h2>Sinais de alerta</h2><ol><li><strong>Ruido ao frear:</strong> chiados ou rangidos indicam pastilhas gastas</li><li><strong>Vibracao no pedal:</strong> pode indicar disco empenado</li><li><strong>Carro puxando para um lado:</strong> desgaste irregular ou pinca travada</li><li><strong>Pedal macio ou esponjoso:</strong> ar no sistema ou fluido velho</li><li><strong>Luz de freio no painel:</strong> verificacao imediata necessaria</li></ol><p>Se identificou algum destes sinais, nao espere! Traga seu veiculo para uma inspecao gratuita.</p>',
            ],
            [
                'title'   => 'Bateria automotiva: como aumentar a vida util',
                'excerpt' => 'Dicas praticas para fazer sua bateria durar mais e evitar surpresas desagradaveis.',
                'content' => '<p>A bateria e responsavel por dar partida no motor e alimentar todos os sistemas eletronicos quando o veiculo esta desligado. Uma bateria saudavel dura em media de 2 a 4 anos.</p><h2>Dicas para prolongar a vida util</h2><ul><li>Evite deixar farois ou som ligados com o motor desligado</li><li>Faca viagens mais longas periodicamente para recarregar a bateria</li><li>Mantenha os terminais limpos e sem oxidacao</li><li>Verifique o nivel de agua da bateria (se aplicavel)</li><li>Faca testes de carga a cada 6 meses</li></ul><h2>Quando trocar?</h2><p>Se o motor demora para pegar ou se os farois ficam fracos com o motor desligado, e hora de verificar a bateria.</p>',
            ],
            [
                'title'   => 'Pneus: como escolher o modelo ideal para seu carro',
                'excerpt' => 'Entenda as medidas, tipos e quando trocar os pneus do seu veiculo.',
                'content' => '<p>Os pneus sao o unico ponto de contato entre seu veiculo e o solo. Escolher o modelo correto e fundamental para seguranca e economia.</p><h2>Entendendo as medidas</h2><p>A medida 195/55 R16 significa:</p><ul><li><strong>195:</strong> largura do pneu em mm</li><li><strong>55:</strong> relacao entre altura e largura (%)</li><li><strong>R16:</strong> diametro da roda em polegadas</li></ul><h2>Quando trocar?</h2><p>Troque quando o indicador TWI estiver nivelado com a banda de rodagem, ou a cada 5 anos independente do desgaste.</p>',
            ],
            [
                'title'   => 'Manutencao preventiva: economia e seguranca',
                'excerpt' => 'Entenda porque a manutencao preventiva e o melhor investimento para seu veiculo.',
                'content' => '<p>Muitos motoristas so levam o carro a oficina quando algo quebra. Esse habito pode custar caro e colocar vidas em risco.</p><h2>Vantagens da manutencao preventiva</h2><ul><li><strong>Economia:</strong> prevenir e ate 5x mais barato que consertar</li><li><strong>Seguranca:</strong> componentes em dia significam menos riscos</li><li><strong>Valorizacao:</strong> veiculo com manutencao em dia vale mais na revenda</li><li><strong>Confiabilidade:</strong> menos chances de ficar na mao</li></ul><h2>Checklist basico</h2><p>A cada 10.000 km verifique: oleo, filtros, freios, pneus, correias e fluidos. Parece muito? Nos fazemos tudo em um unico dia!</p>',
            ],
            [
                'title'   => 'Ar condicionado automotivo: cuidados essenciais no verao',
                'excerpt' => 'Saiba como manter seu ar condicionado funcionando perfeitamente nos dias mais quentes.',
                'content' => '<p>Com as temperaturas subindo, o ar condicionado se torna indispensavel. Porem, um sistema mal cuidado pode causar problemas de saude e gastos desnecessarios.</p><h2>Cuidados essenciais</h2><ul><li>Troque o filtro de cabine a cada 10.000 km</li><li>Faca higienizacao do sistema a cada 6 meses</li><li>Verifique o nivel de gas refrigerante anualmente</li><li>Ligue o ar por pelo menos 10 minutos por semana, mesmo no inverno</li></ul><h2>Sinais de problema</h2><p>Mau cheiro, ar fraco ou barulhos estranhos indicam que esta na hora de trazer o veiculo para avaliacao.</p>',
            ],
            [
                'title'   => 'Correia dentada: o perigo silencioso',
                'excerpt' => 'Entenda porque a troca da correia dentada nao pode ser adiada e os riscos de ignorar.',
                'content' => '<p>A correia dentada e uma das pecas mais criticas do motor. Sua funcao e sincronizar o virabrequim com o comando de valvulas. Se ela romper, o prejuizo pode ser catastrofico.</p><h2>Intervalo de troca</h2><p>Geralmente entre 40.000 km e 60.000 km, dependendo do fabricante. Consulte o manual do seu veiculo.</p><h2>O que acontece se romper?</h2><p>Em motores interferentes (maioria dos carros nacionais), o rompimento causa colisao entre pistoes e valvulas, resultando em retifica do motor — um reparo que pode custar milhares de reais.</p><p><strong>Nao arrisque!</strong> Se esta proximo do intervalo, agende a troca agora.</p>',
            ],
            [
                'title'   => 'Economia de combustivel: 8 dicas que realmente funcionam',
                'excerpt' => 'Reduza seus gastos com combustivel sem comprometer o desempenho do veiculo.',
                'content' => '<p>Com o preco dos combustiveis cada vez mais alto, economizar faz toda a diferenca no orcamento mensal.</p><h2>Dicas comprovadas</h2><ol><li>Mantenha os pneus calibrados corretamente</li><li>Evite aceleracoes bruscas</li><li>Use a marcha adequada para cada velocidade</li><li>Desligue o ar condicionado em trajetos curtos</li><li>Faca revisoes periodicas do motor</li><li>Nao carregue peso desnecessario no porta-malas</li><li>Planeje rotas para evitar congestionamentos</li><li>Mantenha os filtros de ar e combustivel limpos</li></ol><p>Seguindo estas dicas, e possivel economizar ate 20% no consumo mensal!</p>',
            ],
            [
                'title'   => 'Suspensao: entenda os tipos e quando fazer manutencao',
                'excerpt' => 'Conheca os diferentes tipos de suspensao e aprenda a identificar problemas.',
                'content' => '<p>A suspensao e responsavel pelo conforto, estabilidade e seguranca na conducao. Um sistema desgastado aumenta a distancia de frenagem e compromete o controle do veiculo.</p><h2>Tipos de suspensao</h2><ul><li><strong>MacPherson:</strong> mais comum em carros populares</li><li><strong>Multilink:</strong> oferece melhor conforto e estabilidade</li><li><strong>Eixo rigido:</strong> comum em picapes e utilitarios</li></ul><h2>Sinais de desgaste</h2><ul><li>Barulhos ao passar em buracos</li><li>Carro inclinando nas curvas</li><li>Desgaste irregular dos pneus</li><li>Direcao pesada ou com folga</li></ul>',
            ],
            [
                'title'   => 'Como preparar seu carro para viagens longas',
                'excerpt' => 'Checklist completo para garantir uma viagem segura e sem imprevistos.',
                'content' => '<p>Antes de pegar a estrada, e fundamental verificar se seu veiculo esta em condicoes de encarar a viagem com seguranca.</p><h2>Checklist pre-viagem</h2><ul><li>Verificar nivel de oleo do motor</li><li>Conferir nivel do fluido de arrefecimento</li><li>Testar todos os farois e lanternas</li><li>Verificar estado e calibragem dos pneus (inclusive o estepe)</li><li>Conferir funcionamento dos freios</li><li>Verificar palhetas do limpador</li><li>Testar bateria</li><li>Levar triangulo, macaco e chave de roda</li></ul><p>Recomendamos fazer uma revisao completa pelo menos uma semana antes da viagem. Agende conosco!</p>',
            ],
        ];

        foreach ($posts as $i => $data) {
            $slug = Str::slug($data['title']);
            if (DB::table('posts')->where('slug', $slug)->exists()) continue;

            DB::table('posts')->insert([
                'user_id'      => $userId,
                'category_id'  => null,
                'title'        => $data['title'],
                'slug'         => $slug,
                'excerpt'      => $data['excerpt'],
                'content'      => $data['content'],
                'cover_image'  => null,
                'status'       => 'published',
                'featured'     => $i < 3,
                'published_at' => now()->subDays(10 - $i),
                'sort_order'   => $i + 1,
                'created_at'   => now()->subDays(10 - $i),
                'updated_at'   => now()->subDays(10 - $i),
            ]);
        }

        $this->command->info('Posts do blog inseridos com sucesso.');
    }

    // ─── DEPOIMENTOS ───────────────────────────────────────────
    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'name'    => 'Carlos Eduardo Silva',
                'role'    => 'Cliente desde 2022',
                'content' => 'Excelente atendimento! Levei meu carro para troca de oleo e aproveitaram para verificar outros itens. Encontraram um problema na correia que eu nem sabia. Super recomendo!',
                'rating'  => 5,
            ],
            [
                'name'    => 'Ana Paula Ferreira',
                'role'    => 'Proprietaria de HB20',
                'content' => 'Sempre fui muito receosa com oficinas mecanicas, mas aqui me senti segura desde o primeiro atendimento. Explicaram tudo com clareza e o preco foi justo. Voltarei sempre!',
                'rating'  => 5,
            ],
            [
                'name'    => 'Roberto Mendes',
                'role'    => 'Motorista de aplicativo',
                'content' => 'Como trabalho com meu carro todos os dias, preciso de uma oficina de confianca. Faco todas as revisoes aqui e nunca tive problema. O servico e rapido e de qualidade.',
                'rating'  => 5,
            ],
            [
                'name'    => 'Juliana Costa',
                'role'    => 'Cliente desde 2023',
                'content' => 'Meu ar condicionado estava com mau cheiro terrivel. Fizeram a higienizacao e troca do filtro em menos de uma hora. Ficou perfeito! Atendimento nota 10.',
                'rating'  => 5,
            ],
            [
                'name'    => 'Marcos Oliveira',
                'role'    => 'Proprietario de Hilux',
                'content' => 'Trouxe minha picape para revisao completa antes de uma viagem longa. Verificaram tudo com muita atencao e encontraram um problema na suspensao que poderia ter sido perigoso. Muito profissionais!',
                'rating'  => 5,
            ],
            [
                'name'    => 'Patricia Santos',
                'role'    => 'Cliente desde 2021',
                'content' => 'O diagnostico eletronico deles e muito preciso. Ja tinha levado meu carro em outras oficinas e ninguem resolvia. Aqui identificaram o problema na primeira tentativa. Recomendo demais!',
                'rating'  => 5,
            ],
            [
                'name'    => 'Fernando Almeida',
                'role'    => 'Proprietario de Corolla',
                'content' => 'Fiz a troca das pastilhas de freio e o alinhamento. Preco justo, pecas de qualidade e mao de obra impecavel. O carro ficou como novo! Com certeza vou indicar para amigos.',
                'rating'  => 4,
            ],
            [
                'name'    => 'Luciana Ribeiro',
                'role'    => 'Cliente recente',
                'content' => 'Primeira vez que trouxe meu carro aqui e fiquei impressionada com a organizacao e transparencia. Me mostraram fotos de tudo que precisava ser trocado antes de fazer o servico.',
                'rating'  => 5,
            ],
            [
                'name'    => 'Ricardo Barbosa',
                'role'    => 'Proprietario de Civic',
                'content' => 'A pintura do meu carro estava horrivel depois de um pequeno acidente. O trabalho de funilaria e pintura ficou perfeito, nem parece que teve dano algum. Equipe muito competente!',
                'rating'  => 5,
            ],
            [
                'name'    => 'Camila Pereira',
                'role'    => 'Cliente desde 2020',
                'content' => 'Sou cliente ha mais de 5 anos e nunca me decepcionaram. Sempre honesto, com precos transparentes e servico de primeira. E a melhor oficina da regiao sem duvida nenhuma!',
                'rating'  => 5,
            ],
        ];

        foreach ($testimonials as $i => $data) {
            // Evita duplicar pelo nome
            if (DB::table('testimonials')->where('name', $data['name'])->exists()) continue;

            DB::table('testimonials')->insert([
                'name'       => $data['name'],
                'role'       => $data['role'],
                'photo'      => null,
                'content'    => $data['content'],
                'rating'     => $data['rating'],
                'is_active'  => true,
                'sort_order' => $i + 1,
                'created_at' => now()->subDays(30 - ($i * 3)),
                'updated_at' => now()->subDays(30 - ($i * 3)),
            ]);
        }

        $this->command->info('Depoimentos inseridos com sucesso.');
    }
}
