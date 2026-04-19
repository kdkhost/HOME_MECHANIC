<?php

namespace Database\Seeders;

use App\Modules\Services\Models\Service;
use App\Modules\Gallery\Models\GalleryCategory;
use App\Modules\Gallery\Models\GalleryPhoto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PremiumContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpar dados existentes (opcional, mas recomendado para evitar duplicatas em testes)
        // Service::truncate();
        // GalleryCategory::truncate();
        // GalleryPhoto::truncate();

        // 2. Popular Serviços Premium
        $this->seedServices();

        // 3. Popular Galeria Premium
        $this->seedGallery();
    }

    private function seedServices()
    {
        $services = [
            [
                'title'       => 'Tuning de Motor',
                'description' => 'Reprogramação de ECU, upgrades de turbo, intercooler de alta performance e sistemas de injeção otimizados.',
                'icon'        => 'bi-speedometer2',
                'cover_image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
                'featured'    => true,
            ],
            [
                'title'       => 'Suspensão Sport',
                'description' => 'Kits de rebaixamento ajustáveis, amortecedores de competição e geometria de precisão.',
                'icon'        => 'bi-gear-wide-connected',
                'cover_image' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&q=80',
                'featured'    => true,
            ],
            [
                'title'       => 'Freios Performance',
                'description' => 'Sistemas de freio de alta performance para máxima segurança em altas velocidades.',
                'icon'        => 'bi-disc',
                'cover_image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80',
                'featured'    => false,
            ],
            [
                'title'       => 'Estética Premium',
                'description' => 'Envelopamento, polimento de alto brilho, proteção de pintura PPF e detalhamento completo.',
                'icon'        => 'bi-stars',
                'cover_image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80',
                'featured'    => true,
            ],
            [
                'title'       => 'Diagnóstico Digital',
                'description' => 'Leitura completa de todos os sistemas eletrônicos com equipamentos de última geração.',
                'icon'        => 'bi-cpu',
                'cover_image' => 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80',
                'featured'    => false,
            ],
            [
                'title'       => 'Manutenção Preventiva',
                'description' => 'Revisões completas seguindo os protocolos das fabricantes com peças originais.',
                'icon'        => 'bi-wrench-adjustable',
                'cover_image' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=80',
                'featured'    => false,
            ],
        ];

        foreach ($services as $s) {
            Service::updateOrCreate(['title' => $s['title']], [
                'slug'        => Str::slug($s['title']),
                'description' => $s['description'],
                'content'     => '<p>' . $s['description'] . '</p><p>Serviço realizado com equipamentos de ponta e equipe certificada.</p>',
                'icon'        => $s['icon'],
                'cover_image' => $s['cover_image'],
                'featured'    => $s['featured'],
                'active'      => true,
                'sort_order'  => null, // Boot handles this
            ]);
        }
    }

    private function seedGallery()
    {
        $categories = [
            ['name' => 'Performance & Tuning', 'desc' => 'Upgrades de motor e suspensão.'],
            ['name' => 'Estética & Proteção',   'desc' => 'Detalhamento, PPF e Cerâmica.'],
            ['name' => 'Manutenção Elite',     'desc' => 'Cuidado preventivo para supercars.'],
        ];

        foreach ($categories as $cat) {
            $category = GalleryCategory::updateOrCreate(['name' => $cat['name']], [
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['desc'],
                'active'      => true,
            ]);

            // Fotos para esta categoria
            $this->seedPhotos($category);
        }
    }

    private function seedPhotos($category)
    {
        $photos = [];
        
        if ($category->name === 'Performance & Tuning') {
            $photos = [
                ['title' => 'Tuning Porsche 911 GT3', 'url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200&q=90'],
                ['title' => 'Upgrade Ferrari 488',     'url' => 'https://images.unsplash.com/photo-1592198084033-aade902d1aae?w=1200&q=90'],
                ['title' => 'ECU Remapping McLaren',   'url' => 'https://images.unsplash.com/photo-1621135802920-133df287f89c?w=1200&q=90'],
            ];
        } elseif ($category->name === 'Estética & Proteção') {
            $photos = [
                ['title' => 'PPF Lamborghini Huracan','url' => 'https://images.unsplash.com/photo-1614200139390-e3229b1d310e?w=1200&q=90'],
                ['title' => 'Polimento Rolls Royce',     'url' => 'https://images.unsplash.com/photo-1632823471565-1ec2c68da408?w=1200&q=90'],
            ];
        } else {
            $photos = [
                ['title' => 'Diagnóstico Audi R8',       'url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1200&q=90'],
                ['title' => 'Revisão Porsche Carrera', 'url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1200&q=90'],
            ];
        }

        foreach ($photos as $p) {
            GalleryPhoto::updateOrCreate(['title' => $p['title']], [
                'category_id' => $category->id,
                'filename'    => $p['url'], // Usando URL como filename devido ao ajuste no model
                'description' => 'Serviço premium realizado na HomeMechanic.',
                'active'      => true,
            ]);
        }
    }
}
