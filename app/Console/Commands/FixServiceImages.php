<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixServiceImages extends Command
{
    protected $signature = 'services:fix-images';
    protected $description = 'Garante que todos os servicos tenham uma imagem de capa apropriada';

    public function handle(): int
    {
        $this->info('Verificando imagens dos servicos...');

        // Mapeamento de palavras-chave para imagens
        $imageMap = [
            'oleo' => 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800&h=500&fit=crop',
            'filtro' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=800&h=500&fit=crop',
            'freio' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop',
            'suspensao' => 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop',
            'diagnostico' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop',
            'eletronico' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
            'scanner' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop',
            'ar condicionado' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=500&fit=crop',
            'eletrica' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
            'motor' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop',
            'mecanica' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop',
            'retifica' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop',
            'funilaria' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=800&h=500&fit=crop',
            'pintura' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&h=500&fit=crop',
            'injecao' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=500&fit=crop',
            'bico' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=500&fit=crop',
            'cambio' => 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop',
            'transmissao' => 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop',
            'revisao' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=800&h=500&fit=crop',
            'tuning' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=500&fit=crop',
            'stage' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop',
            'remap' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop',
            'carbono' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop',
            'ceramica' => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop',
            'ppf' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop',
            'envelopamento' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=800&h=500&fit=crop',
            'som' => 'https://images.unsplash.com/photo-1541447270888-83e8494f9c08?w=800&h=500&fit=crop',
            'audio' => 'https://images.unsplash.com/photo-1541447270888-83e8494f9c08?w=800&h=500&fit=crop',
            'multimidia' => 'https://images.unsplash.com/photo-1562969289-4c36f93d40c0?w=800&h=500&fit=crop',
            'camera' => 'https://images.unsplash.com/photo-1562969289-4c36f93d40c0?w=800&h=500&fit=crop',
            'sensor' => 'https://images.unsplash.com/photo-1563720360172-67b8f3dce741?w=800&h=500&fit=crop',
            'led' => 'https://images.unsplash.com/photo-1563720360172-67b8f3dce741?w=800&h=500&fit=crop',
            'xenon' => 'https://images.unsplash.com/photo-1563720360172-67b8f3dce741?w=800&h=500&fit=crop',
            'bateria' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
            'partida' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
            'alternador' => 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop',
            'polimento' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop',
            'cristalizado' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop',
            'vitrificacao' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop',
            'higienizacao' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&h=500&fit=crop',
            'lavagem' => 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&h=500&fit=crop',
            'estetica' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop',
            'estetico' => 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop',
        ];

        // Imagem padrao
        $defaultImage = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop';

        $services = DB::table('services')->get();
        $updated = 0;

        foreach ($services as $service) {
            $title = strtolower($service->title);
            $description = strtolower($service->description);
            $content = strtolower(strip_tags($service->content ?? ''));

            // Se ja tem imagem, pular
            if (!empty($service->cover_image)) {
                continue;
            }

            // Encontrar imagem apropriada
            $image = null;
            foreach ($imageMap as $keyword => $imgUrl) {
                if (str_contains($title, $keyword) ||
                    str_contains($description, $keyword) ||
                    str_contains($content, $keyword)) {
                    $image = $imgUrl;
                    break;
                }
            }

            // Se nao encontrou, usar padrao
            if (!$image) {
                $image = $defaultImage;
            }

            // Atualizar no banco
            DB::table('services')->where('id', $service->id)->update([
                'cover_image' => $image,
                'updated_at' => now(),
            ]);

            $updated++;
            $this->info("Servico '{$service->title}' atualizado com imagem.");
        }

        $this->info("Total de servicos atualizados: {$updated}");
        $this->info('Concluido!');

        return self::SUCCESS;
    }
}
