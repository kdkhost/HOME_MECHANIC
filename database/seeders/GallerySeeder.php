<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GallerySeeder extends Seeder
{
    /**
     * Popula a galeria com categorias e fotos reais.
     * Seguro para rodar multiplas vezes (verifica duplicados).
     */
    public function run(): void
    {
        $this->seedGalleryCategories();
        $this->seedGalleryPhotos();
    }

    private function seedGalleryCategories(): void
    {
        $categories = [
            ['name' => 'Servicos Realizados', 'description' => 'Trabalhos concluidos em nossa oficina', 'sort_order' => 1],
            ['name' => 'Troca de Oleo', 'description' => 'Servico de troca de oleo e filtros', 'sort_order' => 2],
            ['name' => 'Freios e Suspensao', 'description' => 'Manutencao do sistema de freios', 'sort_order' => 3],
            ['name' => 'Funilaria e Pintura', 'description' => 'Reparos de lataria e pintura', 'sort_order' => 4],
            ['name' => 'Diagnostico', 'description' => 'Diagnostico eletronico e mecanico', 'sort_order' => 5],
            ['name' => 'Instalacoes', 'description' => 'Instalacao de acessorios e som', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            $exists = DB::table('gallery_categories')->where('slug', $slug)->first();
            if (!$exists) {
                DB::table('gallery_categories')->insert([
                    'name'        => $cat['name'],
                    'slug'        => $slug,
                    'description' => $cat['description'],
                    'sort_order'  => $cat['sort_order'],
                    'active'      => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        $this->command->info('Categorias da galeria criadas com sucesso.');
    }

    private function seedGalleryPhotos(): void
    {
        // Buscar IDs das categorias
        $categories = DB::table('gallery_categories')->pluck('id', 'slug');

        $photos = [
            // Servicos Realizados
            [
                'category' => 'servicos-realizados',
                'title'    => 'Troca de oleo completa',
                'filename' => 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'servicos-realizados',
                'title'    => 'Revisao de freios',
                'filename' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'servicos-realizados',
                'title'    => 'Diagnostico eletronico',
                'filename' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'servicos-realizados',
                'title'    => 'Troca de pneus',
                'filename' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'servicos-realizados',
                'title'    => 'Ar condicionado',
                'filename' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=1200&h=800&fit=crop',
            ],
            // Troca de Oleo
            [
                'category' => 'troca-de-oleo',
                'title'    => 'Drenagem de oleo usado',
                'filename' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'troca-de-oleo',
                'title'    => 'Filtro de oleo novo',
                'filename' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'troca-de-oleo',
                'title'    => 'Oleo sintetico premium',
                'filename' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=1200&h=800&fit=crop',
            ],
            // Freios e Suspensao
            [
                'category' => 'freios-e-suspensao',
                'title'    => 'Disco de freio novo',
                'filename' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'freios-e-suspensao',
                'title'    => 'Pastilhas de freio',
                'filename' => 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'freios-e-suspensao',
                'title'    => 'Amortecedor dianteiro',
                'filename' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=1200&h=800&fit=crop',
            ],
            // Funilaria e Pintura
            [
                'category' => 'funilaria-e-pintura',
                'title'    => 'Carro na cabine de pintura',
                'filename' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'funilaria-e-pintura',
                'title'    => 'Preparacao para pintura',
                'filename' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'funilaria-e-pintura',
                'title'    => 'Polimento cristalizado',
                'filename' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=1200&h=800&fit=crop',
            ],
            // Diagnostico
            [
                'category' => 'diagnostico',
                'title'    => 'Scanner automotivo',
                'filename' => 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'diagnostico',
                'title'    => 'Analise de injecao',
                'filename' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=1200&h=800&fit=crop',
            ],
            // Instalacoes
            [
                'category' => 'instalacoes',
                'title'    => 'Instalacao de som',
                'filename' => 'https://images.unsplash.com/photo-1541447270888-83e8494f9c08?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'instalacoes',
                'title'    => 'Camera de re',
                'filename' => 'https://images.unsplash.com/photo-1562969289-4c36f93d40c0?w=1200&h=800&fit=crop',
            ],
            [
                'category' => 'instalacoes',
                'title'    => 'LEDs e farois',
                'filename' => 'https://images.unsplash.com/photo-1563720360172-67b8f3dce741?w=1200&h=800&fit=crop',
            ],
        ];

        foreach ($photos as $i => $photo) {
            $categoryId = $categories[$photo['category']] ?? null;
            if (!$categoryId) continue;

            // Verifica se ja existe foto com mesmo titulo na mesma categoria
            $exists = DB::table('gallery_photos')
                ->where('title', $photo['title'])
                ->where('category_id', $categoryId)
                ->exists();

            if ($exists) continue;

            $maxOrder = DB::table('gallery_photos')
                ->where('category_id', $categoryId)
                ->max('sort_order') ?? 0;

            DB::table('gallery_photos')->insert([
                'category_id' => $categoryId,
                'title'       => $photo['title'],
                'filename'    => $photo['filename'],
                'thumbnail'   => null,
                'description' => null,
                'sort_order'  => $maxOrder + 1,
                'active'      => true,
                'created_at'  => now()->subDays(rand(1, 60)),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('Fotos da galeria inseridas com sucesso.');
    }
}
