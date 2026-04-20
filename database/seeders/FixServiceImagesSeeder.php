<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixServiceImagesSeeder extends Seeder
{
    /**
     * Atualiza todos os servicos para garantir que tenham imagens de capa.
     */
    public function run(): void
    {
        $this->command->info('Atualizando imagens dos servicos...');

        // Mapeamento completo de servicos para imagens
        $serviceImages = [
            'troca-de-oleo' => 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800&h=500&fit=crop',
            'troca-de-oleo-e-filtros' => 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800&h=500&fit=crop',
            'freios-e-suspensao' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop',
            'diagnostico-eletronico' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop',
            'ar-condicionado-automotivo' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=500&fit=crop',
            'eletrica-automotiva' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
            'motor-e-mecanica-geral' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop',
            'funilaria-e-pintura' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=800&h=500&fit=crop',
            'injecao-eletronica' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=500&fit=crop',
            'cambio-e-transmissao' => 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop',
            'revisao-completa' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=800&h=500&fit=crop',
        ];

        // Imagem padrao para servicos nao mapeados
        $defaultImage = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop';

        $services = DB::table('services')->get();
        $updated = 0;

        foreach ($services as $service) {
            // Se ja tem imagem valida, pular
            if (!empty($service->cover_image)) {
                continue;
            }

            // Tentar encontrar pelo slug
            $image = $serviceImages[$service->slug] ?? null;

            // Se nao encontrou, usar padrao
            if (!$image) {
                $image = $defaultImage;
            }

            // Atualizar
            DB::table('services')->where('id', $service->id)->update([
                'cover_image' => $image,
                'updated_at' => now(),
            ]);

            $updated++;
            $this->command->info("✓ {$service->title} -> imagem adicionada");
        }

        $this->command->info("\nTotal: {$updated} servico(s) atualizado(s) com imagens.");
    }
}
