<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            [
                'name' => 'Castrol',
                'website' => 'https://www.castrol.com',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/96/Castrol_logo.svg/320px-Castrol_logo.svg.png',
                'description' => 'Lubrificantes de alta performance',
                'animation' => 'fade',
                'speed' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Bosch',
                'website' => 'https://www.bosch.com.br',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Bosch_logo.svg/320px-Bosch_logo.svg.png',
                'description' => 'Pecas e componentes automotivos',
                'animation' => 'slide',
                'speed' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Michelin',
                'website' => 'https://www.michelin.com.br',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Michelin_Logo.svg/320px-Michelin_Logo.svg.png',
                'description' => 'Pneus de alta performance',
                'animation' => 'zoom',
                'speed' => 'slow',
                'is_active' => true,
            ],
            [
                'name' => 'Continental',
                'website' => 'https://www.continental.com',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Continental_AG_logo.svg/320px-Continental_AG_logo.svg.png',
                'description' => 'Tecnologia automotiva',
                'animation' => 'flip',
                'speed' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Mobil 1',
                'website' => 'https://www.mobil.com.br',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Mobil_1_logo.svg/320px-Mobil_1_logo.svg.png',
                'description' => 'Oleo lubrificante sintetico',
                'animation' => 'bounce',
                'speed' => 'fast',
                'is_active' => true,
            ],
        ];

        foreach ($sponsors as $i => $data) {
            $slug = Str::slug($data['name']);
            
            $exists = DB::table('sponsors')->where('slug', $slug)->exists();
            if ($exists) continue;

            DB::table('sponsors')->insert([
                'name' => $data['name'],
                'slug' => $slug,
                'website' => $data['website'],
                'logo' => $data['logo'],
                'description' => $data['description'],
                'animation' => $data['animation'],
                'speed' => $data['speed'],
                'is_active' => $data['is_active'],
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Patrocinadores criados com sucesso!');
    }
}
