<?php

namespace App\Modules\Settings\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Helpers\FileUploadHelper;

class SettingsController extends Controller
{
    // ── Defaults ───────────────────────────────────────────

    private array $generalDefaults = [
        'site_name'        => 'Home Mechanic',
        'site_description' => 'Oficina mecânica especializada em carros de luxo e tuning',
        'contact_email'    => 'contato@homemechanic.com.br',
        'contact_phone'    => '(11) 99999-9999',
        'whatsapp'         => '',
        // Redes sociais
        'social_instagram' => '',
        'social_facebook'  => '',
        'social_youtube'   => '',
        'social_twitter'   => '',
        'social_tiktok'    => '',
        'social_linkedin'  => '',
        // Endereço separado
        'address_cep'      => '',
        'address_street'   => '',
        'address_number'   => '',
        'address_complement'=> '',
        'address_district' => '',
        'address_city'     => '',
        'address_state'    => '',
        // Campo legado (mantido para compatibilidade)
        'address'          => '',
        'maintenance_mode' => '0',
        'maintenance_title' => 'Site em Manutenção',
        'maintenance_message' => 'Voltaremos em breve. Estamos realizando atualizações.',
        'maintenance_ips'  => '',
        'maintenance_timer'=> '',
        'maintenance_timer_action' => 'hide',
        'maintenance_bg_image' => '',
        'site_logo'        => '',
        'site_favicon'     => '',
        'analytics_enabled'=> '1',
        'timezone'         => 'America/Sao_Paulo',
        'language'         => 'pt_BR',
    ];

    private array $emailDefaults = [
        'mail_driver'        => 'smtp',
        'mail_host'          => 'smtp.gmail.com',
        'mail_port'          => '587',
        'mail_username'      => '',
        'mail_password'      => '',
        'mail_encryption'    => 'tls',
        'mail_verify_peer'   => '1',
        'mail_from_address'  => 'noreply@homemechanic.com.br',
        'mail_from_name'     => 'HomeMechanic',
    ];

    private array $seoDefaults = [
        'meta_title'         => 'HomeMechanic — Tuning & Performance de Luxo',
        'meta_description'   => 'Especialistas em tuning, performance e manutenção de carros de luxo.',
        'meta_keywords'      => 'tuning, carros de luxo, oficina, performance, lamborghini, ferrari',
        'google_analytics'   => '',
        'google_tag_manager' => '',
        'facebook_pixel'     => '',
    ];

    // ── Helpers ────────────────────────────────────────────

    /**
     * Ler configurações do banco, preenchendo com defaults onde não existir
     */
    private function readSettings(array $defaults, string $group): array
    {
        try {
            $saved = Setting::group($group);
            return array_merge($defaults, array_intersect_key($saved, $defaults));
        } catch (\Exception $e) {
            return $defaults;
        }
    }

    // ── Pages ──────────────────────────────────────────────

    public function index()
    {
        $settings = $this->readSettings($this->generalDefaults, 'general');
        return view('modules.settings.index', compact('settings'));
    }

    public function general()
    {
        $settings = $this->readSettings($this->generalDefaults, 'general');
        return view('modules.settings.index', compact('settings'));
    }

    public function seo()
    {
        $settings = $this->readSettings($this->seoDefaults, 'seo');
        return view('modules.settings.seo', compact('settings'));
    }

    public function email()
    {
        $settings = $this->readSettings($this->emailDefaults, 'email');
        // Indicar se a senha está preenchida (mas mostrar valor real no campo para funcionamento correto)
        if (!empty($settings['mail_password'])) {
            $settings['mail_password_set'] = true;
            $settings['mail_password_len'] = strlen($settings['mail_password']);
            // Manter a senha real no campo - bullets causam problemas no salvamento/teste
        }
        return view('modules.settings.email', compact('settings'));
    }

    public function backup()
    {
        $settings = $this->readSettings($this->generalDefaults, 'general');
        return view('modules.settings.backup', compact('settings'));
    }

    // ── Update ─────────────────────────────────────────────

    public function update(Request $request)
    {
        $section = $request->input('section', 'general');

        try {
            switch ($section) {
                case 'general':
                    $this->updateGeneral($request);
                    break;
                case 'email':
                    $this->updateEmail($request);
                    break;
                case 'seo':
                    $this->updateSeo($request);
                    break;
                case 'maintenance':
                    $this->updateMaintenance($request);
                    break;
                case 'system':
                    $this->updateSystem($request);
                    break;
                default:
                    return back()->with('error', 'Seção inválida.');
            }

            return back()->with('success', 'Configurações salvas com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações', [
                'section' => $section,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Erro ao salvar: ' . $e->getMessage())->withInput();
        }
    }

    private function updateGeneral(Request $request): void
    {
        $request->validate([
            'site_name'     => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'address_cep'   => 'nullable|string|max:10',
        ]);

        // Montar endereço completo para campo legado
        $parts = array_filter([
            $request->input('address_street'),
            $request->input('address_number') ? ', ' . $request->input('address_number') : '',
            $request->input('address_complement') ? ' - ' . $request->input('address_complement') : '',
            $request->input('address_district') ? ', ' . $request->input('address_district') : '',
            $request->input('address_city') ? ' — ' . $request->input('address_city') : '',
            $request->input('address_state') ? '/' . $request->input('address_state') : '',
        ]);

        Setting::setMany([
            'site_name'          => $request->input('site_name'),
            'site_description'   => $request->input('site_description'),
            'contact_email'      => $request->input('contact_email'),
            'contact_phone'      => $request->input('contact_phone'),
            'whatsapp'           => $request->input('whatsapp'),
            // Redes sociais
            'social_instagram'   => $request->input('social_instagram', ''),
            'social_facebook'    => $request->input('social_facebook', ''),
            'social_youtube'     => $request->input('social_youtube', ''),
            'social_twitter'     => $request->input('social_twitter', ''),
            'social_tiktok'      => $request->input('social_tiktok', ''),
            'social_linkedin'    => $request->input('social_linkedin', ''),
            'address_cep'        => preg_replace('/\D/', '', $request->input('address_cep', '')),
            'address_street'     => $request->input('address_street'),
            'address_number'     => $request->input('address_number'),
            'address_complement' => $request->input('address_complement'),
            'address_district'   => $request->input('address_district'),
            'address_city'       => $request->input('address_city'),
            'address_state'      => $request->input('address_state'),
            'address'            => implode('', $parts),
            'maintenance_mode'   => $request->boolean('maintenance_mode') ? '1' : '0',
            'maintenance_title'  => $request->input('maintenance_title', 'Site em Manutenção'),
            'maintenance_message'=> $request->input('maintenance_message', 'Voltaremos em breve.'),
            'maintenance_ips'    => $request->input('maintenance_ips', ''),
            'analytics_enabled'  => $request->boolean('analytics_enabled') ? '1' : '0',
            'timezone'           => $request->input('timezone', 'America/Sao_Paulo'),
            'language'           => $request->input('language', 'pt_BR'),
            // Integracao Google Places (avaliacoes)
            'google_place_id'        => $request->input('google_place_id', ''),
            'google_places_api_key'  => $request->input('google_places_api_key', ''),
        ], 'general');

        // Processar Logo e Favicon via FilePond (arquivo direto ou UUID)
        foreach (['site_logo', 'site_favicon'] as $field) {
            $resolved = FileUploadHelper::resolveFromRequest($request, $field, 'uploads/settings', Setting::get($field, ''));
            if ($resolved !== null) {
                Setting::set($field, $resolved, 'general');
            }
        }
    }

    private function updateEmail(Request $request): void
    {
        $request->validate([
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_from_address' => 'nullable|email|max:255',
        ]);

        $data = [
            'mail_driver'       => $request->input('mail_driver', 'smtp'),
            'mail_host'         => $request->input('mail_host'),
            'mail_port'         => $request->input('mail_port'),
            'mail_username'     => $request->input('mail_username'),
            'mail_encryption'   => $request->input('mail_encryption'),
            'mail_verify_peer'  => $request->boolean('mail_verify_peer') ? '1' : '0',
            'mail_from_address' => $request->input('mail_from_address'),
            'mail_from_name'    => $request->input('mail_from_name'),
        ];

        // Só atualiza a senha se foi preenchida
        if ($request->filled('mail_password')) {
            $data['mail_password'] = $request->input('mail_password');
        }

        Setting::setMany($data, 'email');

        // Sincronizar com .env para garantir que o Laravel use os valores corretos
        $this->syncMailToEnv($data);
    }

    /**
     * Sincronizar configuracoes de e-mail do banco para o arquivo .env
     */
    private function syncMailToEnv(array $data): void
    {
        try {
            $envPath = base_path('.env');
            if (!file_exists($envPath)) {
                return;
            }

            $envContent = file_get_contents($envPath);

            $envMap = [
                'mail_driver'       => 'MAIL_MAILER',
                'mail_host'         => 'MAIL_HOST',
                'mail_port'         => 'MAIL_PORT',
                'mail_username'     => 'MAIL_USERNAME',
                'mail_password'     => 'MAIL_PASSWORD',
                'mail_encryption'   => 'MAIL_ENCRYPTION',
                'mail_from_address' => 'MAIL_FROM_ADDRESS',
                'mail_from_name'   => 'MAIL_FROM_NAME',
            ];

            foreach ($envMap as $dbKey => $envKey) {
                if (!isset($data[$dbKey]) || $data[$dbKey] === null) {
                    continue;
                }

                $value = $data[$dbKey];

                // Formatar valor para .env (aspas se tiver espacos ou caracteres especiais)
                $envValue = $this->formatEnvValue($value);

                // Substituir ou adicionar a variavel no .env
                $pattern = '/^' . preg_quote($envKey, '/') . '=.*/m';
                $replacement = $envKey . '=' . $envValue;

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n" . $replacement;
                }
            }

            file_put_contents($envPath, $envContent);

            // Limpar cache de config para aplicar os novos valores do .env
            Artisan::call('config:clear');

            Log::info('Configuracoes SMTP sincronizadas com .env');
        } catch (\Exception $e) {
            Log::warning('Erro ao sincronizar SMTP com .env: ' . $e->getMessage());
        }
    }

    /**
     * Formatar valor para o arquivo .env
     */
    private function formatEnvValue(string $value): string
    {
        // Se tem espacos, aspas ou caracteres especiais, envolver em aspas duplas
        if (preg_match('/[\s#"\'\\\\]/', $value) || $value === '') {
            return '"' . addslashes($value) . '"';
        }
        return $value;
    }

    private function updateSeo(Request $request): void
    {
        Setting::setMany([
            'meta_title'         => $request->input('meta_title'),
            'meta_description'   => $request->input('meta_description'),
            'meta_keywords'      => $request->input('meta_keywords'),
            'google_analytics'   => $request->input('google_analytics'),
            'google_tag_manager' => $request->input('google_tag_manager'),
            'facebook_pixel'     => $request->input('facebook_pixel'),
        ], 'seo');
    }

    private function updateMaintenance(Request $request): void
    {
        $data = [
            'maintenance_mode'   => $request->boolean('maintenance_mode') ? '1' : '0',
            'maintenance_title'  => $request->input('maintenance_title', 'Site em Manutenção'),
            'maintenance_message'=> $request->input('maintenance_message', 'Voltaremos em breve.'),
            'maintenance_ips'    => $request->input('maintenance_ips', ''),
            'maintenance_timer'  => $request->input('maintenance_timer', ''),
            'maintenance_timer_action' => $request->input('maintenance_timer_action', 'hide'),
        ];

        $resolved = FileUploadHelper::resolveFromRequest(
            $request, 'maintenance_bg_image', 'uploads/maintenance', Setting::get('maintenance_bg_image', '')
        );
        if ($resolved !== null) {
            $data['maintenance_bg_image'] = $resolved;
        }

        Setting::setMany($data, 'general');
    }

    /**
     * Desativar modo de manutencao via AJAX (chamado quando timer expira)
     */
    public function disableMaintenance(Request $request)
    {
        // Verificar se manutencao esta ativa e se timer_action e 'disable'
        $maintenanceMode = Setting::get('maintenance_mode', '0');
        $timerAction = Setting::get('maintenance_timer_action', 'hide');
        $timer = Setting::get('maintenance_timer', '');

        if ($maintenanceMode !== '1' && $maintenanceMode !== 'true') {
            return response()->json(['success' => true, 'message' => 'Manutenção já está desativada.']);
        }

        if ($timerAction !== 'disable') {
            return response()->json(['success' => false, 'message' => 'Ação não permitida.'], 403);
        }

        // Verificar se o timer realmente expirou
        if ($timer) {
            $targetDate = \Carbon\Carbon::parse($timer);
            if (now()->lt($targetDate)) {
                return response()->json(['success' => false, 'message' => 'Temporizador ainda não expirou.'], 403);
            }
        }

        Setting::set('maintenance_mode', '0', 'general');
        Log::info('Manutenção desativada automaticamente pelo temporizador');

        return response()->json(['success' => true, 'message' => 'Manutenção desativada. O site está no ar!']);
    }

    /**
     * Atualizar configuracoes do sistema (ambiente, debug, fuso horario)
     */
    private function updateSystem(Request $request): void
    {
        $env = $request->input('app_env');
        $debug = $request->boolean('app_debug');
        $timezone = $request->input('app_timezone');

        $allowedEnv = ['local', 'production', 'staging'];
        $allowedTz = ['America/Sao_Paulo','America/Manaus','America/Belem','America/Fortaleza','America/Recife','America/Bahia','America/Cuiaba','America/Porto_Velho','America/Rio_Branco','UTC'];

        if (!in_array($env, $allowedEnv)) {
            throw new \Exception('Ambiente inválido.');
        }
        if (!in_array($timezone, $allowedTz)) {
            throw new \Exception('Fuso horário inválido.');
        }

        $this->setEnvValue('APP_ENV', $env);
        $this->setEnvValue('APP_DEBUG', $debug ? 'true' : 'false');
        $this->setEnvValue('APP_TIMEZONE', $timezone);

        // Atualizar config em runtime para refletir imediatamente
        config(['app.env' => $env]);
        config(['app.debug' => $debug]);
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        Log::info('Configurações do sistema atualizadas', [
            'env' => $env,
            'debug' => $debug ? 'true' : 'false',
            'timezone' => $timezone,
        ]);
    }

    /**
     * Modificar um valor no arquivo .env
     */
    private function setEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            throw new \Exception('Arquivo .env não encontrado.');
        }

        $content = file_get_contents($envPath);

        // Se a chave existe, substituir
        if (preg_match("/^{$key}=/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            // Se nao existe, adicionar no final
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($envPath, $content);
    }

    // ── Tarefas Agendadas (Cron) ──────────────────────────────

    /**
     * Listar tarefas agendadas do sistema
     */
    public function cronList()
    {
        // Forçar carregamento das rotas do console no contexto web (Laravel 11)
        if (file_exists(base_path('routes/console.php'))) {
            require_once base_path('routes/console.php');
        }

        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events = $schedule->events();

        $tasks = [];
        $disabled = json_decode(Setting::get('cron_disabled', '[]'), true) ?: [];

        foreach ($events as $event) {
            // Extrair comando completo (php artisan xxx)
            $fullCommand = $event->command ?? '';
            $command = $fullCommand;

            // Tentar extrair apenas o comando artisan de forma robusta (suporta caminhos absolutos e aspas)
            if (preg_match("/artisan['\"]?\s+(.+)$/", $command, $matches)) {
                $command = trim($matches[1], "'\" ");
            } elseif (str_starts_with($command, 'php artisan ')) {
                $command = substr($command, strlen('php artisan '));
            }

            if (empty($command)) {
                $command = $event->description ?? 'N/A';
            }

            // Nome amigavel
            $name = match(true) {
                str_contains($command, 'backup:run') => 'Backup Automático',
                str_contains($command, 'google:sync-reviews') => 'Sync Google Reviews',
                str_contains($command, 'schedule:run') => 'Executar Agendados',
                default => $command,
            };

            $expression = $event->expression;
            $cronHuman = $this->cronToHuman($expression);
            $nextRun = $event->nextRunDate();

            $tasks[] = [
                'id'          => md5($command),
                'command'     => $command,
                'name'        => $name,
                'expression'  => $expression,
                'human'       => $cronHuman,
                'timezone'    => $event->timezone ?? config('app.timezone'),
                'next_run'    => $nextRun ? $nextRun->format('d/m/Y H:i') : '—',
                'enabled'     => !in_array(md5($command), $disabled),
            ];
        }

        return response()->json(['success' => true, 'data' => $tasks]);
    }

    /**
     * Executar uma tarefa agendada manualmente
     */
    public function cronRun(Request $request)
    {
        $command = trim($request->input('command'), "'\" ");

        // Lista de comandos base permitidos (sem o prefixo php artisan)
        $allowed = ['backup:run', 'backup:run --type=all', 'backup:run --type=db', 'backup:run --type=files', 'google:sync-reviews', 'schedule:run'];

        // Verificar se o comando (ou parte dele) esta na lista permitida
        $isAllowed = false;
        foreach ($allowed as $a) {
            if ($command === $a || str_contains($command, $a)) {
                $isAllowed = true;
                $command = $a; // Normalizar para o comando curto
                break;
            }
        }

        if (!$isAllowed) {
            return response()->json(['success' => false, 'message' => "Comando '{$command}' não permitido."], 403);
        }

        // Carregar rotas do console se for schedule:run ou relacionado
        if (file_exists(base_path('routes/console.php'))) {
            require_once base_path('routes/console.php');
        }

        try {
            $exitCode = Artisan::call($command);
            $output = Artisan::output();

            if ($exitCode === 0) {
                return response()->json(['success' => true, 'message' => 'Tarefa executada com sucesso.', 'output' => trim($output)]);
            }

            return response()->json(['success' => false, 'message' => 'Erro na execução.', 'output' => trim($output)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Ativar/desativar uma tarefa agendada
     */
    public function cronToggle(Request $request)
    {
        $command = $request->input('command');
        $id = md5($command);

        $disabled = json_decode(Setting::get('cron_disabled', '[]'), true) ?: [];

        if (in_array($id, $disabled)) {
            // Reativar
            $disabled = array_values(array_diff($disabled, [$id]));
            $status = 'ativada';
        } else {
            // Desativar
            $disabled[] = $id;
            $status = 'desativada';
        }

        Setting::set('cron_disabled', json_encode($disabled), 'general');

        return response()->json(['success' => true, 'message' => "Tarefa {$status} com sucesso.", 'enabled' => !in_array($id, $disabled)]);
    }

    /**
     * Converter expressao cron para texto legivel
     */
    private function cronToHuman(string $expression): string
    {
        $map = [
            '* * * * *'  => 'A cada minuto',
            '0 3 * * *'  => 'Diariamente às 03:00',
            '0 6 * * 6'  => 'Sábados às 06:00',
            '0 * * * *'  => 'A cada hora',
            '0 0 * * *'  => 'Diariamente à meia-noite',
            '*/5 * * * *' => 'A cada 5 minutos',
            '*/15 * * * *' => 'A cada 15 minutos',
            '*/30 * * * *' => 'A cada 30 minutos',
        ];

        return $map[$expression] ?? $expression;
    }

    // ── Test SMTP ──────────────────────────────────────────

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            // Limpar cache de settings para garantir valores atualizados
            \Illuminate\Support\Facades\Cache::forget('settings_all');

            $host       = $request->input('mail_host',         Setting::get('mail_host',         'smtp.gmail.com'));
            $port       = $request->input('mail_port',         Setting::get('mail_port',         '587'));
            $username   = $request->input('mail_username',     Setting::get('mail_username',     ''));
            $password = $request->input('mail_password', '');

            // Se campo vazio, tentar usar senha do banco
            if (empty($password)) {
                $password = Setting::where('key', 'mail_password')->value('value') ?? '';
            }

            $encryption = $request->input('mail_encryption',   Setting::get('mail_encryption',   'tls'));
            $verifyPeer = $request->input('mail_verify_peer') !== null 
                ? $request->boolean('mail_verify_peer') 
                : (Setting::get('mail_verify_peer', '1') === '1');
            $fromAddr   = $request->input('mail_from_address', Setting::get('mail_from_address', 'noreply@homemechanic.com.br'));
            $fromName   = $request->input('mail_from_name',    Setting::get('mail_from_name',    'HomeMechanic'));

            // Diagnostico
            $diagnostic = [
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'encryption' => $encryption,
                'password_len' => strlen($password),
            ];

            if (empty($password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha SMTP vazia! Digite a senha no campo e envie o teste, ou salve as configurações primeiro.',
                    'diagnostic' => array_merge($diagnostic, ['password_status' => 'vazia']),
                ], 422);
            }

            // Sincronizar .env com o banco (garante que o .env esteja atualizado)
            $this->syncMailToEnv([
                'mail_driver'       => Setting::get('mail_driver', 'smtp'),
                'mail_host'         => $host,
                'mail_port'         => $port,
                'mail_username'     => $username,
                'mail_password'     => $password,
                'mail_encryption'   => $encryption,
                'mail_from_address' => $fromAddr,
                'mail_from_name'    => $fromName,
            ]);

            // Suporte a subject/body customizados (enviado da página de templates)
            $customSubject = $request->input('mail_subject');
            $customBody    = $request->input('mail_body');

            $mailConfig = [
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.transport'  => 'smtp',
                'mail.mailers.smtp.host'       => $host,
                'mail.mailers.smtp.port'       => (int) $port,
                'mail.mailers.smtp.username'   => $username,
                'mail.mailers.smtp.password'   => $password,
                'mail.mailers.smtp.encryption' => $encryption ?: null,
                'mail.mailers.smtp.timeout'    => 15,
                'mail.from.address'            => $fromAddr,
                'mail.from.name'               => $fromName,
            ];

            if (!$verifyPeer) {
                $mailConfig['mail.mailers.smtp.stream'] = [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ];
            }

            config($mailConfig);

            // Limpar instância do mailer para forçar recriação com novas config
            app()->forgetInstance('mail.manager');
            app()->forgetInstance('mailer');

            Log::info('Teste SMTP iniciado', [
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'encryption' => $encryption,
                'password_len' => strlen($password),
                'password_first' => substr($password, 0, 1),
                'password_last' => substr($password, -1),
                'from' => $fromAddr,
            ]);

            // Usar template personalizado do sistema (notification) para teste SMTP
            $siteName = Setting::get('site_name', 'HomeMechanic');
            $templateSubject = Setting::get('email_tpl_notification_subject', 'Teste de Configuracao — {{site_name}}');
            $templateBody = Setting::get('email_tpl_notification_body', $this->defaultTestEmailBody());

            $vars = [
                '{{titulo}}'       => 'Teste SMTP',
                '{{mensagem}}'     => "Se voce recebeu este e-mail, as configuracoes SMTP estao corretas!<br><br>Servidor: {$host}:{$port}<br>Criptografia: " . ($encryption ?: 'Nenhuma') . "<br>Remetente: {$fromName}",
                '{{acao_url}}'     => url('/admin/settings/email'),
                '{{acao_texto}}'   => 'Ver Configuracoes',
                '{{site_name}}'    => $siteName,
            ];

            $subjectParsed = str_replace(array_keys($vars), array_values($vars), $templateSubject);
            $bodyParsed    = str_replace(array_keys($vars), array_values($vars), $templateBody);

            $html = $this->wrapEmailInLayout($subjectParsed, $bodyParsed, $siteName);

            \Illuminate\Support\Facades\Mail::html(
                $html,
                fn($m) => $m->to($request->input('test_email'))
                            ->from($fromAddr, $fromName)
                            ->subject($subjectParsed)
            );

            return response()->json([
                'success' => true,
                'message' => "E-mail enviado para {$request->input('test_email')} com sucesso!",
            ]);

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            $diagnostic = [
                'host' => $host ?? 'N/A',
                'port' => $port ?? 'N/A',
                'username' => $username ?? 'N/A',
                'encryption' => $encryption ?? 'N/A',
                'password_len' => isset($password) ? strlen($password) : 0,
            ];

            // Sugestoes baseadas no tipo de erro
            $suggestions = [];
            if (str_contains($errorMsg, '535') || str_contains($errorMsg, 'Incorrect authentication')) {
                $suggestions[] = 'A senha SMTP está incorreta. Verifique no cPanel se a conta de e-mail existe e se a senha está certa.';
                $suggestions[] = 'No cPanel: Contas de E-mail → Gerenciar → Alterar Senha da conta noreply@homemechanic.com.br';
                $suggestions[] = 'Digite a senha NOVAMENTE no campo e clique em "Salvar" antes de testar.';
            }
            if (str_contains($errorMsg, 'Connection refused') || str_contains($errorMsg, 'Connection timed out')) {
                $suggestions[] = 'O servidor não está acessível na porta configurada. Verifique host e porta.';
                if (($encryption ?? '') === 'tls' && ($port ?? 0) == 465) {
                    $suggestions[] = 'Porta 465 requer criptografia SSL (não TLS). Altere para SSL.';
                }
            }
            if (str_contains($errorMsg, 'certificate') || str_contains($errorMsg, 'peer')) {
                $suggestions[] = 'Erro de certificado SSL. Tente desmarcar "Verificar Certificado SSL".';
            }

            Log::error('Erro no teste SMTP', [
                'error' => $errorMsg,
                'diagnostic' => $diagnostic,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Falha: ' . $errorMsg,
                'diagnostic' => $diagnostic,
                'suggestions' => $suggestions,
            ], 422);
        }
    }

    /**
     * Corpo padrao HTML para e-mail de teste SMTP (fallback se template nao configurado)
     */
    private function defaultTestEmailBody(): string
    {
        return <<<HTML
<p><strong>{{titulo}}</strong></p>
<p>{{mensagem}}</p>
<p style="text-align:center;margin:28px 0;">
  <a href="{{acao_url}}" style="display:inline-block;background:#FF6B00;color:#fff;text-decoration:none;padding:14px 32px;border-radius:6px;font-weight:700;font-size:0.95rem;">
    {{acao_texto}}
  </a>
</p>
<p>Atenciosamente,<br><strong>Equipe {{site_name}}</strong></p>
HTML;
    }

    /**
     * Envolver conteudo de e-mail no layout padrao do sistema
     */
    private function wrapEmailInLayout(string $subject, string $body, string $siteName): string
    {
        $primary = '#FF6B00';
        $year = date('Y');

        $isHtml = strip_tags($body) !== $body;
        $bodyContent = $isHtml ? $body : '<p>' . nl2br(e($body)) . '</p>';

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{$subject}</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; max-width: 100%; }
    a { color: {$primary}; }
    .email-wrapper { width: 100%; background-color: #f0f2f5; padding: 24px 16px; }
    .email-container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .email-header { background: {$primary}; padding: 28px 32px; text-align: center; }
    .email-header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
    .email-body { padding: 32px; color: #374151; font-size: 15px; line-height: 1.7; }
    .email-body p { margin: 0 0 16px; }
    .email-body p:last-child { margin-bottom: 0; }
    .email-footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
    .email-footer p { color: #94a3b8; font-size: 12px; margin: 0; line-height: 1.6; }
    @media only screen and (max-width: 600px) {
      .email-wrapper { padding: 12px 8px; }
      .email-header { padding: 20px 20px; }
      .email-header h1 { font-size: 18px; }
      .email-body { padding: 20px; font-size: 14px; }
      .email-footer { padding: 16px 20px; }
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="email-container">
      <div class="email-header">
        <h1>{$siteName}</h1>
      </div>
      <div class="email-body">
        {$bodyContent}
      </div>
      <div class="email-footer">
        <p>
          &copy; {$year} <strong>{$siteName}</strong>. Todos os direitos reservados.
        </p>
      </div>
    </div>
  </div>
</body>
</html>
HTML;
    }
}
