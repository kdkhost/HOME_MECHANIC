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
                'logo' => 'https://placehold.co/200x100/FF6B00/FFFFFF/png?text=CASTROL',
                'description' => 'Lubrificantes de alta performance',
                'animation' => 'fade',
                'speed' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Bosch',
                'website' => 'https://www.bosch.com.br',
                'logo' => 'https://placehold.co/200x100/009933/FFFFFF/png?text=BOSCH',
                'description' => 'Pecas e componentes automotivos',
                'animation' => 'slide',
                'speed' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Michelin',
                'website' => 'https://www.michelin.com.br',
                'logo' => 'https://placehold.co/200x100/0055A4/FFFFFF/png?text=MICHELIN',
                'description' => 'Pneus de alta performance',
                'animation' => 'zoom',
                'speed' => 'slow',
                'is_active' => true,
            ],
            [
                'name' => 'Continental',
                'website' => 'https://www.continental.com',
                'logo' => 'https://placehold.co/200x100/FFA500/000000/png?text=CONTINENTAL',
                'description' => 'Tecnologia automotiva',
                'animation' => 'flip',
                'speed' => 'normal',
                'is_active' => true,
            ],
            [
                'name' => 'Mobil 1',
                'website' => 'https://www.mobil.com.br',
                'logo' => 'https://placehold.co/200x100/CC0000/FFFFFF/png?text=MOBIL+1',
                'description' => 'Oleo lubrificante sintetico',
                'animation' => 'bounce',
                'speed' => 'fast',
                'is_active' => true,
            ],
            [
                'name' => 'Pirelli',
                'website' => 'https://www.pirelli.com',
                'logo' => 'https://placehold.co/200x100/FFCC00/000000/png?text=PIRELLI',
                'description' => 'Pneus esportivos',
                'animation' => 'fade',
                'speed' => 'fast',
                'is_active' => true,
            ],
            [
                'name' => 'Shell',
                'website' => 'https://www.shell.com.br',
                'logo' => 'https://placehold.co/200x100/FBCE07/000000/png?text=SHELL',
                'description' => 'Combustiveis e lubrificantes',
                'animation' => 'slide',
                'speed' => 'slow',
                'is_active' => true,
            ],
            [
                'name' => 'NGK',
                'website' => 'https://www.ngk.com.br',
                'logo' => 'https://placehold.co/200x100/003399/FFFFFF/png?text=NGK',
                'description' => 'Velas e componentes de ignicao',
                'animation' => 'zoom',
                'speed' => 'normal',
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
