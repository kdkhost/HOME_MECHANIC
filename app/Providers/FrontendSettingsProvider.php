<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendSettingsProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Compartilha configurações do admin em todas as views frontend.*
        View::composer('modules.frontend.*', function ($view) {
            try {
                $s = Setting::group('general');
                $view->with('siteSettings', [
                    'site_name'    => $s['site_name']    ?? 'HomeMechanic',
                    'site_desc'    => $s['site_description'] ?? 'Tuning & Performance de Luxo',
                    'phone'        => $s['contact_phone'] ?? '(11) 99999-9999',
                    'whatsapp'     => $s['whatsapp']      ?? '5511999999999',
                    'email'        => $s['contact_email'] ?? 'contato@homemechanic.com.br',
                    'address'      => trim(implode(', ', array_filter([
                        $s['address_street'] ?? '',
                        $s['address_number'] ?? '',
                        $s['address_district'] ?? '',
                        $s['address_city'] ?? '',
                        $s['address_state'] ?? '',
                    ]))),
                    'address_full' => $s['address'] ?? '',
                ]);
            } catch (\Exception $e) {
                $view->with('siteSettings', [
                    'site_name' => 'HomeMechanic', 'site_desc' => 'Tuning & Performance de Luxo',
                    'phone' => '(11) 99999-9999', 'whatsapp' => '5511999999999',
                    'email' => 'contato@homemechanic.com.br', 'address' => '', 'address_full' => '',
                ]);
            }
        });

        // Também compartilha no layout frontend
        View::composer('layouts.frontend', function ($view) {
            try {
                $s = Setting::group('general');
                $view->with('siteSettings', [
                    'site_name'    => $s['site_name']    ?? 'HomeMechanic',
                    'site_desc'    => $s['site_description'] ?? 'Tuning & Performance de Luxo',
                    'phone'        => $s['contact_phone'] ?? '(11) 99999-9999',
                    'whatsapp'     => $s['whatsapp']      ?? '5511999999999',
                    'email'        => $s['contact_email'] ?? 'contato@homemechanic.com.br',
                    'address'      => trim(implode(', ', array_filter([
                        $s['address_street'] ?? '',
                        $s['address_number'] ?? '',
                        $s['address_district'] ?? '',
                        $s['address_city'] ?? '',
                        $s['address_state'] ?? '',
                    ]))),
                    'address_full' => $s['address'] ?? '',
                ]);
            } catch (\Exception $e) {
                $view->with('siteSettings', [
                    'site_name' => 'HomeMechanic', 'site_desc' => 'Tuning & Performance de Luxo',
                    'phone' => '(11) 99999-9999', 'whatsapp' => '5511999999999',
                    'email' => 'contato@homemechanic.com.br', 'address' => '', 'address_full' => '',
                ]);
            }
        });
    }
}
