<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->loadMailSettings();
    }

    /**
     * Carregar configurações de e-mail do banco de dados e aplicar ao config.
     */
    private function loadMailSettings(): void
    {
        try {
            // Só tenta carregar se a tabela de settings existir
            if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }

            $settings = \App\Models\Setting::group('email');
            
            if (!empty($settings['mail_host'])) {
                $config = [
                    'mail.mailers.smtp.host'       => $settings['mail_host'],
                    'mail.mailers.smtp.port'       => (int) ($settings['mail_port'] ?? 587),
                    'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? 'tls',
                    'mail.mailers.smtp.username'   => $settings['mail_username'] ?? null,
                    'mail.mailers.smtp.password'   => $settings['mail_password'] ?? null,
                    'mail.from.address'            => $settings['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name'               => $settings['mail_from_name'] ?? config('mail.from.name'),
                ];

                // Check for SSL verification bypass
                if (($settings['mail_verify_peer'] ?? '1') === '0') {
                    $config['mail.mailers.smtp.stream'] = [
                        'ssl' => [
                            'allow_self_signed' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ];
                }

                config($config);
            }
        } catch (\Exception $e) {
            // Silenciosamente falha para não quebrar a aplicação se o DB estiver fora
            \Illuminate\Support\Facades\Log::warning('Não foi possível carregar as configurações de e-mail do banco: ' . $e->getMessage());
        }
    }

    /**
     * Registrar Blade directives para formatação de dados.
     */
    private function registerBladeDirectives(): void
    {
        // @formatPhone('11999998888') → (11) 99999-8888
        Blade::directive('formatPhone', function ($expression) {
            return "<?php
                \$_v = preg_replace('/\D/', '', {$expression} ?? '');
                if (strlen(\$_v) <= 10) {
                    echo preg_replace('/(\d{2})(\d{4})(\d{4})/', '(\$1) \$2-\$3', \$_v);
                } else {
                    echo preg_replace('/(\d{2})(\d{5})(\d{4})/', '(\$1) \$2-\$3', \$_v);
                }
            ?>";
        });

        // @formatCpf('12345678901') → 123.456.789-01
        Blade::directive('formatCpf', function ($expression) {
            return "<?php
                \$_v = preg_replace('/\D/', '', {$expression} ?? '');
                echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '\$1.\$2.\$3-\$4', \$_v);
            ?>";
        });

        // @formatCnpj('12345678000195') → 12.345.678/0001-95
        Blade::directive('formatCnpj', function ($expression) {
            return "<?php
                \$_v = preg_replace('/\D/', '', {$expression} ?? '');
                echo preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '\$1.\$2.\$3/\$4-\$5', \$_v);
            ?>";
        });

        // @formatCpfCnpj — auto-detecta pelo tamanho
        Blade::directive('formatCpfCnpj', function ($expression) {
            return "<?php
                \$_v = preg_replace('/\D/', '', {$expression} ?? '');
                if (strlen(\$_v) <= 11) {
                    echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '\$1.\$2.\$3-\$4', \$_v);
                } else {
                    echo preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '\$1.\$2.\$3/\$4-\$5', \$_v);
                }
            ?>";
        });

        // @formatCep('01001000') → 01001-000
        Blade::directive('formatCep', function ($expression) {
            return "<?php
                \$_v = preg_replace('/\D/', '', {$expression} ?? '');
                echo preg_replace('/(\d{5})(\d{3})/', '\$1-\$2', \$_v);
            ?>";
        });

        // @formatDate('2026-04-18') → 18/04/2026
        Blade::directive('formatDate', function ($expression) {
            return "<?php
                \$_v = {$expression};
                if (\$_v) {
                    try {
                        echo \Carbon\Carbon::parse(\$_v)->format('d/m/Y');
                    } catch (\Exception \$e) {
                        echo \$_v;
                    }
                }
            ?>";
        });

        // @formatDateTime('2026-04-18 14:30:00') → 18/04/2026 14:30
        Blade::directive('formatDateTime', function ($expression) {
            return "<?php
                \$_v = {$expression};
                if (\$_v) {
                    try {
                        echo \Carbon\Carbon::parse(\$_v)->format('d/m/Y H:i');
                    } catch (\Exception \$e) {
                        echo \$_v;
                    }
                }
            ?>";
        });

        // @formatTime('14:30:00') → 14:30
        Blade::directive('formatTime', function ($expression) {
            return "<?php
                \$_v = {$expression};
                if (\$_v) {
                    try {
                        echo \Carbon\Carbon::parse(\$_v)->format('H:i');
                    } catch (\Exception \$e) {
                        echo \$_v;
                    }
                }
            ?>";
        });
    }
}
