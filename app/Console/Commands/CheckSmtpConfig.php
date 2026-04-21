<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CheckSmtpConfig extends Command
{
    protected $signature = 'smtp:check';
    protected $description = 'Verificar configuracoes SMTP salvas no banco de dados';

    public function handle(): int
    {
        $this->info('=== Configuracoes SMTP no Banco de Dados ===');
        $this->newLine();

        $fields = [
            'mail_host'         => 'Servidor SMTP',
            'mail_port'         => 'Porta',
            'mail_username'     => 'Usuario',
            'mail_password'     => 'Senha',
            'mail_encryption'   => 'Criptografia',
            'mail_verify_peer'  => 'Verificar Peer',
            'mail_from_address' => 'E-mail Remetente',
            'mail_from_name'    => 'Nome Remetente',
            'mail_driver'       => 'Driver',
        ];

        foreach ($fields as $key => $label) {
            $value = Setting::get($key, '(nao definido)');
            if ($key === 'mail_password' && $value !== '(nao definido)' && !empty($value)) {
                $value = str_repeat('*', strlen($value)) . ' [' . strlen($value) . ' caracteres]';
            }
            $this->line("  <info>{$label}:</info> {$value}");
        }

        $this->newLine();

        // Verificar config efetiva do Laravel
        $this->info('=== Config Efetiva do Laravel ===');
        $this->newLine();
        $this->line('  mail.default: ' . config('mail.default'));
        $this->line('  mail.mailers.smtp.host: ' . config('mail.mailers.smtp.host'));
        $this->line('  mail.mailers.smtp.port: ' . config('mail.mailers.smtp.port'));
        $this->line('  mail.mailers.smtp.username: ' . config('mail.mailers.smtp.username'));
        $this->line('  mail.mailers.smtp.encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->line('  mail.from.address: ' . config('mail.from.address'));

        $this->newLine();

        // Verificar valores do .env
        $this->info('=== Valores no .env ===');
        $this->newLine();
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            foreach (['MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME'] as $key) {
                if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $envContent, $m)) {
                    $val = trim($m[1], '"');
                    if ($key === 'MAIL_PASSWORD' && !empty($val) && $val !== 'null') {
                        $val = str_repeat('*', strlen($val)) . ' [' . strlen($val) . ' caracteres]';
                    }
                    $this->line("  <info>{$key}:</info> {$val}");
                } else {
                    $this->line("  <info>{$key}:</info> <error>(nao encontrado)</error>");
                }
            }
        } else {
            $this->error('  Arquivo .env nao encontrado!');
        }

        $this->newLine();

        // Sincronizar .env com o banco
        $this->info('=== Sincronizando .env com o banco ===');
        $this->newLine();

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

        $envPath = base_path('.env');
        if (file_exists($envPath) && is_writable($envPath)) {
            $envContent = file_get_contents($envPath);
            $updated = 0;

            foreach ($envMap as $dbKey => $envKey) {
                $dbValue = Setting::get($dbKey);
                if ($dbValue === null || $dbValue === '') {
                    continue;
                }

                $envValue = $dbValue;
                if (preg_match('/[\s#"\'\\\\]/', $envValue) || $envValue === '') {
                    $envValue = '"' . addslashes($envValue) . '"';
                }

                $pattern = '/^' . preg_quote($envKey, '/') . '=.*/m';
                $replacement = $envKey . '=' . $envValue;

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                    $updated++;
                } else {
                    $envContent .= "\n" . $replacement;
                    $updated++;
                }
            }

            if ($updated > 0) {
                file_put_contents($envPath, $envContent);
                $this->line("  <info>{$updated} variaveis atualizadas no .env</info>");
                Artisan::call('config:clear');
                $this->line('  <info>Cache de config limpo</info>');
            } else {
                $this->line('  <comment>.env ja esta sincronizado com o banco</comment>');
            }
        } else {
            $this->error('  Arquivo .env nao encontrado ou sem permissao de escrita!');
        }

        $this->newLine();
        $this->comment('Agora teste o envio de e-mail pelo painel admin.');

        return self::SUCCESS;
    }
}
