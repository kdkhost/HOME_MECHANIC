<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Geral
            ['key' => 'site_name',         'value' => 'HomeMechanic',                                          'group' => 'general'],
            ['key' => 'site_description',  'value' => 'Oficina mecânica especializada em carros de luxo e tuning', 'group' => 'general'],
            ['key' => 'contact_email',     'value' => 'contato@homemechanic.com.br',                           'group' => 'general'],
            ['key' => 'contact_phone',     'value' => '(11) 99999-9999',                                       'group' => 'general'],
            ['key' => 'address',           'value' => 'Av. das Supercars, 1500 — São Paulo, SP',               'group' => 'general'],
            ['key' => 'maintenance_mode',  'value' => '0',                                                     'group' => 'general'],
            ['key' => 'analytics_enabled', 'value' => '1',                                                     'group' => 'general'],
            ['key' => 'timezone',          'value' => 'America/Sao_Paulo',                                     'group' => 'general'],
            ['key' => 'language',          'value' => 'pt_BR',                                                 'group' => 'general'],

            // Email
            ['key' => 'mail_driver',       'value' => 'smtp',                          'group' => 'email'],
            ['key' => 'mail_host',         'value' => 'smtp.gmail.com',                'group' => 'email'],
            ['key' => 'mail_port',         'value' => '587',                           'group' => 'email'],
            ['key' => 'mail_username',     'value' => '',                              'group' => 'email'],
            ['key' => 'mail_password',     'value' => '',                              'group' => 'email'],
            ['key' => 'mail_encryption',   'value' => 'tls',                           'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@homemechanic.com.br',   'group' => 'email'],
            ['key' => 'mail_from_name',    'value' => 'HomeMechanic',                  'group' => 'email'],

            // SEO
            ['key' => 'meta_title',         'value' => 'HomeMechanic — Tuning & Performance de Luxo',                          'group' => 'seo'],
            ['key' => 'meta_description',   'value' => 'Especialistas em tuning, performance e manutenção de carros de luxo.', 'group' => 'seo'],
            ['key' => 'meta_keywords',      'value' => 'tuning, carros de luxo, oficina, performance, lamborghini, ferrari',   'group' => 'seo'],
            ['key' => 'google_analytics',   'value' => '',                                                                     'group' => 'seo'],
            ['key' => 'google_tag_manager', 'value' => '',                                                                     'group' => 'seo'],
            ['key' => 'facebook_pixel',     'value' => '',                                                                     'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
