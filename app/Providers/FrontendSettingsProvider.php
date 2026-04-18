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
                $f = Setting::group('frontend');
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
                    // Frontend Dinâmico
                    'hero_title'       => $f['hero_title'] ?? 'HOME MECHANIC',
                    'hero_subtitle'    => $f['hero_subtitle'] ?? 'Eleve a performance e o estilo do seu veículo. Especialistas em tuning, estética e manutenção premium para quem existe excelência.',
                    'hero_badge_text'  => $f['hero_badge_text'] ?? 'Bem-vindo à',
                    'hero_stat1_value' => $f['hero_stat1_value'] ?? '15+',
                    'hero_stat1_label' => $f['hero_stat1_label'] ?? 'Anos Mercado',
                    'hero_stat2_value' => $f['hero_stat2_value'] ?? '5K+',
                    'hero_stat2_label' => $f['hero_stat2_label'] ?? 'Projetos',
                    'hero_stat3_value' => $f['hero_stat3_value'] ?? '100%',
                    'hero_stat3_label' => $f['hero_stat3_label'] ?? 'Satisfação',
                    'about_title'      => $f['about_title'] ?? 'Nossa Missão é Superar Suas Expectativas',
                    'about_subtitle'   => $f['about_subtitle'] ?? 'Excelência Automotiva',
                    'about_years'      => $f['about_years'] ?? '15',
                    'about_text'       => $f['about_text'] ?? 'Com profissionais qualificados e tecnologia de ponta, oferecemos um serviço que vai além da manutenção. Nós transformamos o seu veículo.',
                    'cta_title'        => $f['cta_title'] ?? 'Pronto para Transformar seu Veículo?',
                    'cta_text'         => $f['cta_text'] ?? 'Traga seu projeto para a equipe mais qualificada do mercado.',
                ];
            } catch (\Exception $e) {
                return [
                    'site_name' => 'HomeMechanic', 'site_desc' => 'Tuning & Performance de Luxo',
                    'phone' => '(11) 99999-9999', 'whatsapp' => '5511999999999',
                    'email' => 'contato@homemechanic.com.br', 'address' => '', 'address_full' => '',
                    'social_instagram' => '', 'social_facebook' => '', 'social_youtube' => '',
                    'social_twitter' => '', 'social_tiktok' => '', 'social_linkedin' => '',
                    'hero_title' => 'HOME MECHANIC', 'hero_subtitle' => '', 'hero_badge_text' => '',
                    'hero_stat1_value' => '', 'hero_stat1_label' => '', 'hero_stat2_value' => '', 'hero_stat2_label' => '', 'hero_stat3_value' => '', 'hero_stat3_label' => '',
                    'about_title' => '', 'about_subtitle' => '', 'about_years' => '', 'about_text' => '', 'cta_title' => '', 'cta_text' => '',
                ];
            }
        };

        View::composer('modules.frontend.*', fn($v) => $v->with('siteSettings', $buildSettings()));
        View::composer('layouts.frontend',   fn($v) => $v->with('siteSettings', $buildSettings()));
    }
}
