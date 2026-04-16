<?php

namespace App\Modules\Installer\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PDO;
use PDOException;

class InstallerService
{
    /**
     * Helper para logging que funciona mesmo quando Laravel não está totalmente carregado
     */
    private function safeLog(string $level, string $message, array $context = []): void
    {
        try {
            // Tentar usar o Log do Laravel
            Log::{$level}($message, $context);
        } catch (Exception $e) {
            // Se falhar, usar error_log nativo do PHP
            $contextStr = !empty($context) ? ' - Context: ' . json_encode($context) : '';
            error_log("HomeMechanic [{$level}] {$message}{$contextStr}");
        }
    }
    /**
     * Verificar requisitos do sistema
     */
    public function checkRequirements(): array
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP 8.4+',
                'required' => true,
                'status' => version_compare(PHP_VERSION, '8.4.0', '>=') && version_compare(PHP_VERSION, '8.5.0', '<'),
                'current' => PHP_VERSION
            ],
            'extensions' => []
        ];

        // Extensões obrigatórias
        $requiredExtensions = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'mbstring' => 'Mbstring',
            'openssl' => 'OpenSSL',
            'tokenizer' => 'Tokenizer',
            'xml' => 'XML',
            'ctype' => 'Ctype',
            'json' => 'JSON',
            'bcmath' => 'BCMath',
            'fileinfo' => 'Fileinfo',
            'gd' => 'GD (para processamento de imagens)'
        ];

        foreach ($requiredExtensions as $extension => $name) {
            $requirements['extensions'][$extension] = [
                'name' => $name,
                'required' => true,
                'status' => extension_loaded($extension)
            ];
        }

        // Verificar servidor web (LiteSpeed/Apache)
        $requirements['web_server'] = [
            'name' => 'Servidor Web (LiteSpeed/Apache)',
            'required' => true,
            'status' => $this->checkWebServer(),
            'current' => $this->getWebServerInfo()
        ];

        // Verificar mod_rewrite ou LiteSpeed rewrite
        $requirements['url_rewrite'] = [
            'name' => 'URL Rewrite (mod_rewrite/LiteSpeed)',
            'required' => true,
            'status' => $this->checkUrlRewrite()
        ];

        // Verificar CloudLinux (opcional mas informativo)
        $requirements['cloudlinux'] = [
            'name' => 'CloudLinux (Detectado)',
            'required' => false,
            'status' => $this->checkCloudLinux(),
            'current' => $this->getCloudLinuxInfo()
        ];

        // Verificar Imunify360 (opcional mas informativo)
        $requirements['imunify360'] = [
            'name' => 'Imunify360 (Detectado)',
            'required' => false,
            'status' => $this->checkImunify360()
        ];

        // Verificar permissões de escrita
        $writableDirectories = [
            'storage' => storage_path(),
            'bootstrap_cache' => base_path('bootstrap/cache')
        ];

        foreach ($writableDirectories as $key => $path) {
            $requirements['permissions'][$key] = [
                'name' => "Escrita em {$path}",
                'required' => true,
                'status' => is_writable($path)
            ];
        }

        return $requirements;
    }

    /**
     * Testar conexão com banco de dados
     */
    public function testDatabaseConnection(array $config): array
    {
        try {
            // Validar parâmetros obrigatórios
            $requiredKeys = ['host', 'port', 'database', 'username', 'password'];
            foreach ($requiredKeys as $key) {
                if (!isset($config[$key])) {
                    return [
                        'success' => false,
                        'message' => "Parâmetro '{$key}' não fornecido na configuração do banco."
                    ];
                }
            }

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 10
            ]);

            // Testar uma query simples
            $pdo->query('SELECT 1');
            
            return [
                'success' => true,
                'message' => 'Conexão com banco de dados estabelecida com sucesso!'
            ];

        } catch (PDOException $e) {
            // Não expor credenciais na mensagem de erro
            $safeMessage = 'Erro na conexão com banco de dados. Verifique as configurações.';
            
            // Log detalhado para debug (sem credenciais) - usando safeLog
            $this->safeLog('error', 'Database connection failed', [
                'host' => $config['host'] ?? 'N/A',
                'port' => $config['port'] ?? 'N/A',
                'database' => $config['database'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $safeMessage
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro inesperado ao testar conexão: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Executar instalação completa
     */
    public function install(array $data): array
    {
        try {
            $this->safeLog('info', 'Iniciando instalação do HomeMechanic', [
                'admin_email' => $data['admin']['email'],
                'company_name' => $data['company']['name'] ?? 'HomeMechanic'
            ]);

            // 1. Detectar URL automaticamente se não fornecida
            $this->safeLog('info', 'Etapa 1: Preparando dados de instalação');
            $data = $this->prepareInstallationData($data);

            // 2. Criar arquivo .env
            $this->safeLog('info', 'Etapa 2: Criando arquivo .env');
            $this->createEnvFile($data);

            // 3. Limpar caches antes de gerar chave
            $this->safeLog('info', 'Etapa 3: Limpando caches');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            // 4. Gerar chave da aplicação
            $this->safeLog('info', 'Etapa 4: Gerando APP_KEY');
            $keyResult = Artisan::call('key:generate', ['--force' => true]);
            if ($keyResult !== 0) {
                throw new \Exception('Falha ao gerar APP_KEY');
            }

            // 5. Recarregar configuração após gerar chave
            $this->safeLog('info', 'Etapa 5: Recarregando configuração');
            Artisan::call('config:cache');

            // 6. Executar migrations
            $this->safeLog('info', 'Etapa 6: Executando migrations');
            $migrateResult = Artisan::call('migrate', ['--force' => true]);
            if ($migrateResult !== 0) {
                throw new \Exception('Falha ao executar migrations');
            }

            // 7. Executar seeders básicos
            $this->safeLog('info', 'Etapa 7: Executando seeders básicos');
            $this->runBasicSeeders($data);

            // 8. Criar usuário superadmin
            $this->safeLog('info', 'Etapa 8: Criando usuário superadmin');
            $this->createSuperAdminUser($data);

            // 9. Criar arquivo de instalação concluída
            $this->safeLog('info', 'Etapa 9: Criando arquivo de instalação concluída');
            $this->createInstalledFile($data);

            // 10. Otimizar aplicação
            $this->safeLog('info', 'Etapa 10: Otimizando aplicação');
            Artisan::call('config:cache');
            Artisan::call('route:cache');

            $this->safeLog('info', 'Instalação concluída com sucesso', [
                'admin_url' => $data['system']['url'] . '/admin/login',
                'admin_email' => $data['admin']['email']
            ]);

            return [
                'success' => true,
                'message' => 'Instalação concluída com sucesso!',
                'admin_url' => $data['system']['url'] . '/admin/login',
                'admin_email' => $data['admin']['email']
            ];

        } catch (\Exception $e) {
            // Log detalhado do erro
            $this->safeLog('error', 'Falha na instalação', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Limpar em caso de erro
            $this->cleanup();

            return [
                'success' => false,
                'message' => 'Erro durante a instalação: ' . $e->getMessage(),
                'details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ]
            ];
        }
    }

    /**
     * Verificar servidor web
     */
    private function checkWebServer(): bool
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
        
        // Detectar LiteSpeed ou Apache
        return (
            stripos($serverSoftware, 'litespeed') !== false ||
            stripos($serverSoftware, 'apache') !== false ||
            stripos($serverSoftware, 'nginx') !== false
        );
    }

    /**
     * Obter informações do servidor web
     */
    private function getWebServerInfo(): string
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido';
        
        if (stripos($serverSoftware, 'litespeed') !== false) {
            return 'LiteSpeed ' . $this->extractVersion($serverSoftware);
        }
        
        if (stripos($serverSoftware, 'apache') !== false) {
            return 'Apache ' . $this->extractVersion($serverSoftware);
        }
        
        return $serverSoftware;
    }

    /**
     * Verificar URL rewrite (mod_rewrite ou LiteSpeed)
     */
    private function checkUrlRewrite(): bool
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
        
        // Para LiteSpeed, assumir que rewrite está disponível
        if (stripos($serverSoftware, 'litespeed') !== false) {
            return true;
        }
        
        // Para Apache, verificar mod_rewrite
        return $this->checkModRewrite();
    }

    /**
     * Verificar CloudLinux
     */
    private function checkCloudLinux(): bool
    {
        // Verificar se está rodando em CloudLinux
        if (file_exists('/proc/lve/list')) {
            return true;
        }
        
        if (file_exists('/usr/bin/cloudlinux-selector')) {
            return true;
        }
        
        // Verificar variáveis de ambiente CloudLinux
        if (getenv('CLOUDLINUX_LVE_VERSION')) {
            return true;
        }
        
        return false;
    }

    /**
     * Obter informações do CloudLinux
     */
    private function getCloudLinuxInfo(): string
    {
        if (!$this->checkCloudLinux()) {
            return 'Não detectado';
        }
        
        // Tentar obter versão do CloudLinux
        if (file_exists('/etc/cloudlinux-release')) {
            $release = file_get_contents('/etc/cloudlinux-release');
            if (preg_match('/CloudLinux.*?(\d+\.\d+)/', $release, $matches)) {
                return 'CloudLinux ' . $matches[1];
            }
        }
        
        return 'CloudLinux (versão não detectada)';
    }

    /**
     * Verificar Imunify360
     */
    private function checkImunify360(): bool
    {
        // Verificar se Imunify360 está instalado
        if (file_exists('/opt/imunify360/imunify360-agent')) {
            return true;
        }
        
        if (file_exists('/usr/bin/imunify360-agent')) {
            return true;
        }
        
        // Verificar processo do Imunify360
        if (function_exists('shell_exec')) {
            $processes = shell_exec('ps aux | grep imunify360 | grep -v grep');
            if (!empty($processes)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extrair versão do software do servidor
     */
    private function extractVersion(string $serverSoftware): string
    {
        if (preg_match('/(\d+\.\d+(?:\.\d+)?)/', $serverSoftware, $matches)) {
            return $matches[1];
        }
        
        return '';
    }

    /**
     * Verificar se mod_rewrite está ativo
     */
    private function checkModRewrite(): bool
    {
        // Verificar se é Apache
        if (function_exists('apache_get_modules')) {
            return in_array('mod_rewrite', apache_get_modules());
        }

        // Verificar via variável de servidor
        if (isset($_SERVER['HTTP_MOD_REWRITE'])) {
            return $_SERVER['HTTP_MOD_REWRITE'] === 'On';
        }

        // Assumir que está ativo se não conseguir verificar
        return true;
    }

    /**
     * Preparar dados da instalação
     */
    private function prepareInstallationData(array $data): array
    {
        // Detectar URL automaticamente se não fornecida
        if (empty($data['system']['url'])) {
            $data['system']['url'] = $this->detectSystemUrl();
        }

        // Extrair domínio da URL
        $parsedUrl = parse_url($data['system']['url']);
        $data['system']['domain'] = $parsedUrl['host'] ?? 'localhost';

        // Definir nome da empresa se não fornecido
        if (empty($data['company']['name'])) {
            $data['company']['name'] = 'HomeMechanic';
        }

        // Definir descrição padrão se não fornecida
        if (empty($data['company']['description'])) {
            $data['company']['description'] = 'Sistema de gestão para oficinas mecânicas especializadas em carros esportivos de luxo e tuning';
        }

        return $data;
    }

    /**
     * Detectar URL do sistema automaticamente
     */
    private function detectSystemUrl(): string
    {
        $protocol = 'http';
        
        // Detectar HTTPS
        if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        ) {
            $protocol = 'https';
        }

        // Obter host
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

        // Obter caminho base (remover /install se presente)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Calcular path base
        $basePath = '';
        if ($scriptName) {
            $basePath = dirname($scriptName);
            if ($basePath === '/' || $basePath === '\\') {
                $basePath = '';
            }
        }

        // Remover /public se presente no path
        $basePath = str_replace('/public', '', $basePath);

        return $protocol . '://' . $host . $basePath;
    }
    /**
     * Criar arquivo .env
     */
    private function createEnvFile(array $data): void
    {
        $envContent = "APP_NAME=\"{$data['company']['name']}\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo
APP_URL={$data['system']['url']}

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$data['database']['host']}
DB_PORT={$data['database']['port']}
DB_DATABASE={$data['database']['name']}
DB_USERNAME={$data['database']['username']}
DB_PASSWORD={$data['database']['password']}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=\"noreply@{$data['system']['domain']}\"
MAIL_FROM_NAME=\"{$data['company']['name']}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME=\"{$data['company']['name']}\"
";

        File::put(base_path('.env'), $envContent);
    }

    /**
     * Executar seeders básicos
     */
    private function runBasicSeeders(array $data): void
    {
        try {
            $this->safeLog('info', 'Iniciando seeders básicos');

            // Configurações básicas do sistema
            $settings = [
                'site_name' => $data['company']['name'],
                'site_description' => $data['company']['description'],
                'site_url' => $data['system']['url'],
                'site_logo' => null,
                'site_favicon' => null,
                'maintenance_mode' => '0',
                'maintenance_message' => 'Sistema em manutenção. Voltaremos em breve.',
                'maintenance_eta' => null,
                'smtp_host' => '',
                'smtp_port' => '587',
                'smtp_username' => '',
                'smtp_password' => '',
                'smtp_encryption' => 'tls',
                'smtp_from_address' => "noreply@{$data['system']['domain']}",
                'smtp_from_name' => $data['company']['name'],
            ];

            $this->safeLog('info', 'Inserindo configurações básicas', ['count' => count($settings)]);

            // Verificar se tabela settings existe
            if (!Schema::hasTable('settings')) {
                throw new \Exception('Tabela settings não existe. Verifique se as migrations foram executadas.');
            }

            // Limpar configurações existentes (caso seja uma reinstalação)
            DB::table('settings')->truncate();

            foreach ($settings as $key => $value) {
                try {
                    DB::table('settings')->insert([
                        'key' => $key,
                        'value' => $value,
                        'group' => $this->getSettingGroup($key),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } catch (\Exception $e) {
                    $this->safeLog('error', 'Erro ao inserir configuração', [
                        'key' => $key,
                        'value' => $value,
                        'error' => $e->getMessage()
                    ]);
                    throw new \Exception("Falha ao inserir configuração '{$key}': " . $e->getMessage());
                }
            }

            $this->safeLog('info', 'Configurações básicas inseridas com sucesso');

            // Verificar se tabela maintenance_ips existe
            if (!Schema::hasTable('maintenance_ips')) {
                throw new \Exception('Tabela maintenance_ips não existe. Verifique se as migrations foram executadas.');
            }

            // Limpar IPs existentes
            DB::table('maintenance_ips')->truncate();

            // Adicionar IP do instalador à lista de IPs permitidos durante manutenção
            $installerIp = request()->ip();
            $this->safeLog('info', 'Adicionando IPs de manutenção', ['installer_ip' => $installerIp]);

            if ($installerIp && $installerIp !== '127.0.0.1') {
                try {
                    DB::table('maintenance_ips')->insert([
                        'ip_address' => $installerIp,
                        'label' => 'IP do Instalador',
                        'active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } catch (\Exception $e) {
                    $this->safeLog('warning', 'Erro ao inserir IP do instalador (não crítico)', [
                        'ip' => $installerIp,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Sempre adicionar localhost
            try {
                DB::table('maintenance_ips')->insert([
                    'ip_address' => '127.0.0.1',
                    'label' => 'Localhost',
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                $this->safeLog('warning', 'Erro ao inserir localhost (não crítico)', ['error' => $e->getMessage()]);
            }

            $this->safeLog('info', 'IPs de manutenção adicionados com sucesso');

        } catch (\Exception $e) {
            $this->safeLog('error', 'Erro ao executar seeders básicos', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw new \Exception('Falha ao executar seeders básicos: ' . $e->getMessage());
        }
    }

    /**
     * Obter grupo da configuração
     */
    private function getSettingGroup(string $key): string
    {
        if (str_starts_with($key, 'smtp_')) {
            return 'smtp';
        }
        
        if (str_starts_with($key, 'maintenance_')) {
            return 'maintenance';
        }
        
        return 'general';
    }

    /**
     * Criar usuário superadmin
     */
    private function createSuperAdminUser(array $data): void
    {
        try {
            $this->safeLog('info', 'Iniciando criação do usuário superadmin', [
                'name' => $data['admin']['name'],
                'email' => $data['admin']['email']
            ]);

            // Verificar se usuário já existe
            $existingUser = DB::table('users')->where('email', $data['admin']['email'])->first();
            if ($existingUser) {
                $this->safeLog('warning', 'Usuário com este email já existe', ['email' => $data['admin']['email']]);
                throw new \Exception('Já existe um usuário com este email: ' . $data['admin']['email']);
            }

            // Validar dados do admin
            if (empty($data['admin']['name']) || empty($data['admin']['email']) || empty($data['admin']['password'])) {
                throw new \Exception('Dados do administrador incompletos');
            }

            // Validar email
            if (!filter_var($data['admin']['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email do administrador inválido: ' . $data['admin']['email']);
            }

            // Validar senha
            if (strlen($data['admin']['password']) < 8) {
                throw new \Exception('Senha do administrador deve ter pelo menos 8 caracteres');
            }

            $this->safeLog('info', 'Dados do admin validados, criando usuário');

            // Criar usuário
            $userId = DB::table('users')->insertGetId([
                'name' => $data['admin']['name'],
                'email' => $data['admin']['email'],
                'password' => Hash::make($data['admin']['password']),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$userId) {
                throw new \Exception('Falha ao inserir usuário na base de dados');
            }

            $this->safeLog('info', 'Usuário criado com sucesso', ['user_id' => $userId]);

            // Registrar criação do usuário no audit log
            try {
                DB::table('audit_logs')->insert([
                    'user_id' => null, // Sistema
                    'action' => 'superadmin_created',
                    'model_type' => 'App\Models\User',
                    'model_id' => $userId,
                    'old_values' => json_encode([]),
                    'new_values' => json_encode([
                        'id' => $userId,
                        'name' => $data['admin']['name'],
                        'email' => $data['admin']['email'],
                        'role' => 'admin'
                    ]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now()
                ]);

                $this->safeLog('info', 'Audit log criado para usuário superadmin');
            } catch (\Exception $e) {
                $this->safeLog('warning', 'Falha ao criar audit log (não crítico)', ['error' => $e->getMessage()]);
                // Não falhar a instalação por causa do audit log
            }

            $this->safeLog('info', 'Usuário superadmin criado durante instalação', [
                'user_id' => $userId,
                'name' => $data['admin']['name'],
                'email' => $data['admin']['email'],
                'ip' => request()->ip()
            ]);

        } catch (\Exception $e) {
            $this->safeLog('error', 'Erro ao criar usuário superadmin', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'admin_data' => [
                    'name' => $data['admin']['name'] ?? 'N/A',
                    'email' => $data['admin']['email'] ?? 'N/A'
                ]
            ]);
            throw new \Exception('Falha ao criar usuário administrador: ' . $e->getMessage());
        }
    }

    /**
     * Criar arquivo de instalação concluída
     */
    private function createInstalledFile(array $data): void
    {
        $installationInfo = [
            'installed_at' => now()->toISOString(),
            'version' => '1.0.0',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'admin_email' => $data['admin']['email'],
            'system_url' => $data['system']['url'],
            'company_name' => $data['company']['name'],
            'installer_ip' => request()->ip(),
            'server_info' => [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'os' => PHP_OS,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ]
        ];

        File::put(storage_path('installed'), json_encode($installationInfo, JSON_PRETTY_PRINT));
    }

    /**
     * Limpar arquivos em caso de erro
     */
    private function cleanup(): void
    {
        try {
            $this->safeLog('info', 'Iniciando limpeza após erro na instalação');

            // Remover .env se foi criado
            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                File::delete($envPath);
                $this->safeLog('info', 'Arquivo .env removido');
            }

            // Remover arquivo de instalação se existe
            $installedPath = storage_path('installed');
            if (File::exists($installedPath)) {
                File::delete($installedPath);
                $this->safeLog('info', 'Arquivo installed removido');
            }

            // Limpar caches
            try {
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                $this->safeLog('info', 'Caches limpos');
            } catch (\Exception $e) {
                $this->safeLog('warning', 'Erro ao limpar caches durante cleanup', ['error' => $e->getMessage()]);
            }

            // Tentar fazer rollback das migrations (se possível)
            try {
                // Verificar se há migrations para fazer rollback
                $output = Artisan::output();
                Artisan::call('migrate:status');
                $status = Artisan::output();
                
                if (strpos($status, 'Ran') !== false) {
                    Artisan::call('migrate:rollback', ['--force' => true]);
                    $this->safeLog('info', 'Rollback de migrations executado');
                }
            } catch (\Exception $e) {
                $this->safeLog('warning', 'Erro ao fazer rollback das migrations', ['error' => $e->getMessage()]);
            }

            $this->safeLog('info', 'Limpeza concluída');

        } catch (\Exception $e) {
            $this->safeLog('error', 'Erro durante limpeza', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Verificar se sistema já está instalado
     */
    public function isInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }

    /**
     * Obter informações da instalação
     */
    public function getInstallationInfo(): ?array
    {
        $installedFile = storage_path('installed');
        
        if (!File::exists($installedFile)) {
            return null;
        }

        $content = File::get($installedFile);
        
        // Tentar decodificar JSON (novo formato)
        $info = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $info;
        }

        // Formato antigo (apenas data)
        return [
            'installed_at' => $content,
            'version' => 'Unknown',
            'format' => 'legacy'
        ];
    }

    /**
     * Obter informações do sistema
     */
    public function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ];
    }
}
