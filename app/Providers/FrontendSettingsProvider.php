<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendSettingsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $buildSettings = function () {
            try {
                $s = Setting::group('general');
                return [
                    'site_name'        => $s['site_name']        ?? 'HomeMechanic',
                    'site_desc'        => $s['site_description']  ?? 'Tuning & Performance de Luxo',
                    'phone'            => $s['contact_phone']     ?? '(11) 99999-9999',
                    'whatsapp'         => $s['whatsapp']          ?? '5511999999999',
                    'email'            => $s['contact_email']     ?? 'contato@homemechanic.com.br',
                    'address'          => trim(implode(', ', array_filter([
                        $s['address_street']   ?? '',
                        $s['address_number']   ?? '',
                        $s['address_district'] ?? '',
                        $s['address_city']     ?? '',
                        $s['address_state']    ?? '',
                    ]))),
                    'address_full'     => $s['address']           ?? '',
                    // Redes sociais
                    'social_instagram' => $s['social_instagram']  ?? '',
                    'social_facebook'  => $s['social_facebook']   ?? '',
                    'social_youtube'   => $s['social_youtube']    ?? '',
                    'social_twitter'   => $s['social_twitter']    ?? '',
                    'social_tiktok'    => $s['social_tiktok']     ?? '',
                    'social_linkedin'  => $s['social_linkedin']   ?? '',
                ];
            } catch (\Exception $e) {
                return [
                    'site_name' => 'HomeMechanic', 'site_desc' => 'Tuning & Performance de Luxo',
                    'phone' => '(11) 99999-9999', 'whatsapp' => '5511999999999',
                    'email' => 'contato@homemechanic.com.br', 'address' => '', 'address_full' => '',
                    'social_instagram' => '', 'social_facebook' => '', 'social_youtube' => '',
                    'social_twitter' => '', 'social_tiktok' => '', 'social_linkedin' => '',
                ];
            }
        };

        View::composer('modules.frontend.*', fn($v) => $v->with('siteSettings', $buildSettings()));
        View::composer('layouts.frontend',   fn($v) => $v->with('siteSettings', $buildSettings()));
    }
}
