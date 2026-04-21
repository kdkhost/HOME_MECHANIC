<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

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
        $this->comment('Execute "php artisan smtp:check" para verificar as configuracoes.');

        return self::SUCCESS;
    }
}
