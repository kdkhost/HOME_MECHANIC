<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Services\Models\Service;
use App\Modules\Gallery\Models\GalleryCategory;
use App\Modules\Gallery\Models\GalleryPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PremiumContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Criar Usuários Administrativos
        $this->seedUsers();

        // 2. Popular Serviços Premium (10+ itens únicos)
        $this->seedServices();

        // 3. Popular Galeria Premium (30+ fotos únicas)
        $this->seedGallery();
    }

    private function seedUsers()
    {
        // SuperAdmin (Marcelo)
        User::updateOrCreate(
            ['email' => 'marcelobradrj@gmail.com'],
            [
                'name'     => 'Marcelo (SuperAdmin)',
                'password' => Hash::make('83388601Mm...'),
                'role'     => 'admin', // Usando admin pois o model checa isAdmin()
            ]
        );

        // Administrador (Suporte)
        User::updateOrCreate(
            ['email' => 'admin@homemechanic.com.br'],
            [
                'name'     => 'Administrador HomeMechanic',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );
    }

    private function seedServices()
    {
        $services = [
            [
                'title'       => 'Remapeamento ECU Stage 2',
                'description' => 'Otimização avançada de software para ganho real de potência e torque em motores turbo alimentados.',
                'icon'        => 'bi-speedometer2',
                'cover_image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=90',
                'featured'    => true,
            ],
            [
                'title'       => 'Suspensão a Ar Customizada',
                'description' => 'Sistemas de suspensão pneumática com gerenciamento via smartphone para controle total de altura.',
                'icon'        => 'bi-gear-wide-connected',
                'cover_image' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1200&q=90',
                'featured'    => true,
            ],
            [
                'title'       => 'Freios Carbocerâmicos',
                'description' => 'Upgrades de sistemas de frenagem com discos de cerâmica e pinças de múltiplos pistões para pista.',
                'icon'        => 'bi-disc',
                'cover_image' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1200&q=90',
                'featured'    => false,
            ],
            [
                'title'       => 'Detalhamento Técnico (PPF)',
                'description' => 'Aplicação de película de proteção de pintura (PPF) auto-regenerativa contra pedriscos e riscos.',
                'icon'        => 'bi-stars',
                'cover_image' => 'https://images.unsplash.com/photo-1603584173870-7f1efd98042a?w=1200&q=90',
                'featured'    => true,
            ],
            [
                'title'       => 'Swap de Motor (Performance)',
                'description' => 'Substituição completa de motorização por unidades de alta performance com chicote customizado.',
                'icon'        => 'bi-cpu',
                'cover_image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200&q=90',
                'featured'    => false,
            ],
            [
                'title'       => 'Escapamento em Inox/Titânio',
                'description' => 'Sistemas de exaustão sob medida com ronco personalizado e melhor fluxo de gases.',
                'icon'        => 'bi-megaphone',
                'cover_image' => 'https://images.unsplash.com/photo-1614200139390-e3229b1d310e?w=1200&q=90',
                'featured'    => true,
            ],
            [
                'title'       => 'Interior em Alcântara',
                'description' => 'Revestimento completo de painéis, bancos e teto com materiais de luxo e costuras manuais.',
                'icon'        => 'bi-palette',
                'cover_image' => 'https://images.unsplash.com/photo-1542362567-b05500269734?w=1200&q=90',
                'featured'    => false,
            ],
            [
                'title'       => 'Upgrades de Rodas Forjadas',
                'description' => 'Troca por rodas ultra-leves e resistentes, balanceadas para alta velocidade.',
                'icon'        => 'bi-circle-dots',
                'cover_image' => 'https://images.unsplash.com/photo-1621359953476-b06221784200?w=1200&q=90',
                'featured'    => false,
            ],
            [
                'title'       => 'Aero Kits em Carbono',
                'description' => 'Instalação de spoilers, aerofólios e difusores em fibra de carbono real.',
                'icon'        => 'bi-wing',
                'cover_image' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=1200&q=90',
                'featured'    => false,
            ],
            [
                'title'       => 'Revisão de Competição',
                'description' => 'Check-list completo para Track Day, incluindo troca de fluidos de competição.',
                'icon'        => 'bi-flag',
                'cover_image' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=1200&q=90',
                'featured'    => false,
            ],
        ];

        foreach ($services as $s) {
            Service::updateOrCreate(['title' => $s['title']], [
                'slug'        => Str::slug($s['title']),
                'description' => $s['description'],
                'content'     => '<p>' . $s['description'] . '</p><p>Serviço realizado seguindo os mais altos padrões internacionais de qualidade em mecânica de luxo e performance.</p>',
                'icon'        => $s['icon'],
                'cover_image' => $s['cover_image'],
                'featured'    => $s['featured'],
                'active'      => true,
            ]);
        }
    }

    private function seedGallery()
    {
        $categories = [
            ['name' => 'Tuning & Performance', 'desc' => 'Modificações de motor e performance extrema.'],
            ['name' => 'Visual & Estética',  'desc' => 'Envelopamento, Pintura e Detalhamento.'],
            ['name' => 'Luxo & Exóticos',    'desc' => 'Supercarros em manutenção em nosso galpão.'],
            ['name' => 'Eventos & Track',    'desc' => 'Nossos clientes em ação nas pistas.'],
        ];

        foreach ($categories as $cat) {
            $category = GalleryCategory::updateOrCreate(['name' => $cat['name']], [
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['desc'],
                'active'      => true,
            ]);

            $this->seedPhotos($category);
        }
    }

    private function seedPhotos($category)
    {
        $photosList = [
            'Tuning & Performance' => [
                ['title' => 'Turbo Precision Setup', 'url' => 'https://images.unsplash.com/photo-1494905998402-395d579af36f?w=1200&q=90'],
                ['title' => 'ECU Remap Session',    'url' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1200&q=90'],
                ['title' => 'Intake System Carbon',  'url' => 'https://images.unsplash.com/photo-1600706432502-77a0e2e32715?w=1200&q=90'],
                ['title' => 'Engine Bay Detail',     'url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200&q=90'],
                ['title' => 'Dyno Testing Day',      'url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=1200&q=90'],
            ],
            'Visual & Estética' => [
                ['title' => 'Full Satin Wrap',      'url' => 'https://images.unsplash.com/photo-1621932953986-15fcfec8326e?w=1200&q=90'],
                ['title' => 'Ceramic Coating Prep',  'url' => 'https://images.unsplash.com/photo-1603584173870-7f1efd98042a?w=1200&q=90'],
                ['title' => 'Wheel Refurb Gold',     'url' => 'https://images.unsplash.com/photo-1621359953476-b06221784200?w=1200&q=90'],
                ['title' => 'Detailing Masterpiece', 'url' => 'https://images.unsplash.com/photo-1520116467521-812fb7000b5b?w=1200&q=90'],
                ['title' => 'Paint Protection Film', 'url' => 'https://images.unsplash.com/photo-1607603750909-408e1938b8c7?w=1200&q=90'],
            ],
            'Luxo & Exóticos' => [
                ['title' => 'Lamborghini Service',   'url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1200&q=90'],
                ['title' => 'Ferrari Maintenance',   'url' => 'https://images.unsplash.com/photo-1592198084033-aade902d1aae?w=1200&q=90'],
                ['title' => 'Porsche GT3RS Line',    'url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200&q=90'],
                ['title' => 'McLaren Aero Check',    'url' => 'https://images.unsplash.com/photo-1621135802920-133df287f89c?w=1200&q=90'],
                ['title' => 'Aston Martin Interior', 'url' => 'https://images.unsplash.com/photo-1542362567-b05500269734?w=1200&q=90'],
            ],
            'Eventos & Track' => [
                ['title' => 'Pista de Interlagos',   'url' => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=1200&q=90'],
                ['title' => 'Night Meet HM',         'url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1200&q=90'],
                ['title' => 'Track Day Ready',       'url' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=1200&q=90'],
                ['title' => 'Victory Lap',           'url' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=1200&q=90'],
                ['title' => 'Sunset Driving',        'url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200&q=90'],
            ],
        ];

        $photos = $photosList[$category->name] ?? [];

        foreach ($photos as $p) {
            GalleryPhoto::updateOrCreate(['title' => $p['title']], [
                'category_id' => $category->id,
                'filename'    => $p['url'],
                'description' => 'Serviço premium de altíssimo nível realizado em nossa oficina especializada.',
                'active'      => true,
            ]);
        }
    }
}
