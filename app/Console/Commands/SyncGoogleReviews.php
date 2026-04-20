<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncGoogleReviews extends Command
{
    protected $signature = 'google:sync-reviews';
    protected $description = 'Sincroniza avaliacoes do Google Places para a tabela de depoimentos';

    public function handle(): int
    {
        $apiKey  = Setting::get('google_places_api_key');
        $placeId = Setting::get('google_place_id');

        if (!$apiKey || !$placeId) {
            $this->error('Configure google_places_api_key e google_place_id em Configuracoes > Integracao.');
            return self::FAILURE;
        }

        $this->info('Buscando avaliacoes do Google Places...');

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields'   => 'reviews',
                'language' => 'pt-BR',
                'key'      => $apiKey,
            ]);

            if (!$response->successful()) {
                $this->error('Erro na API do Google: HTTP ' . $response->status());
                return self::FAILURE;
            }

            $data = $response->json();

            if (($data['status'] ?? '') !== 'OK') {
                $this->error('Google retornou status: ' . ($data['status'] ?? 'desconhecido'));
                if (isset($data['error_message'])) {
                    $this->error($data['error_message']);
                }
                return self::FAILURE;
            }

            $reviews = $data['result']['reviews'] ?? [];

            if (empty($reviews)) {
                $this->warn('Nenhuma avaliacao encontrada.');
                return self::SUCCESS;
            }

            $imported = 0;
            $skipped  = 0;

            foreach ($reviews as $review) {
                $name = $review['author_name'] ?? 'Anonimo';
                $text = $review['text'] ?? '';

                if (empty($text)) {
                    $skipped++;
                    continue;
                }

                // Evita duplicar pelo nome + conteudo
                $exists = DB::table('testimonials')
                    ->where('name', $name)
                    ->where('content', $text)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $maxOrder = DB::table('testimonials')->max('sort_order') ?? 0;

                DB::table('testimonials')->insert([
                    'name'       => $name,
                    'role'       => 'Avaliacao Google',
                    'photo'      => $review['profile_photo_url'] ?? null,
                    'content'    => $text,
                    'rating'     => min(5, max(1, $review['rating'] ?? 5)),
                    'is_active'  => true,
                    'sort_order' => $maxOrder + 1,
                    'created_at' => isset($review['time'])
                        ? \Carbon\Carbon::createFromTimestamp($review['time'])
                        : now(),
                    'updated_at' => now(),
                ]);

                $imported++;
            }

            $this->info("Concluido: {$imported} importados, {$skipped} ignorados (duplicados ou vazios).");
            Log::info('Google Reviews sincronizadas', ['imported' => $imported, 'skipped' => $skipped]);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            Log::error('Erro ao sincronizar Google Reviews', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
