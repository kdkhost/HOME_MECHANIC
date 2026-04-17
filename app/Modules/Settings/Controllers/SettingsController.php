<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Página principal de configurações
     */
    public function index()
    {
        $settings = [
            'site_name' => 'HomeMechanic',
            'site_description' => 'Oficina mecânica especializada',
            'contact_email' => 'contato@homemechanic.com.br',
            'contact_phone' => '(11) 99999-9999',
            'address' => 'Rua das Oficinas, 123 - São Paulo/SP',
            'maintenance_mode' => false,
            'analytics_enabled' => true
        ];

        return view('modules.settings.index', compact('settings'));
    }

    /**
     * Configurações gerais
     */
    public function general()
    {
        $settings = [
            'site_name' => 'HomeMechanic',
            'site_description' => 'Oficina mecânica especializada',
            'contact_email' => 'contato@homemechanic.com.br',
            'contact_phone' => '(11) 99999-9999',
            'address' => 'Rua das Oficinas, 123 - São Paulo/SP',
            'timezone' => 'America/Sao_Paulo',
            'language' => 'pt_BR'
        ];

        return view('modules.settings.general', compact('settings'));
    }

    /**
     * Configurações de SEO
     */
    public function seo()
    {
        $settings = [
            'meta_title' => 'HomeMechanic - Oficina Mecânica',
            'meta_description' => 'Oficina mecânica especializada em manutenção automotiva',
            'meta_keywords' => 'oficina, mecânica, carros, manutenção',
            'google_analytics' => '',
            'google_tag_manager' => '',
            'facebook_pixel' => ''
        ];

        return view('modules.settings.seo', compact('settings'));
    }

    /**
     * Configurações de email
     */
    public function email()
    {
        $settings = [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => '587',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@homemechanic.com.br',
            'mail_from_name' => 'HomeMechanic'
        ];

        return view('modules.settings.email', compact('settings'));
    }

    /**
     * Backup e manutenção
     */
    public function backup()
    {
        return view('modules.settings.backup');
    }

    /**
     * Atualizar configurações
     */
    public function update(Request $request)
    {
        // TODO: Implementar salvamento das configurações
        
        return redirect()->back()
            ->with('success', 'Configurações atualizadas com sucesso!');
    }
}