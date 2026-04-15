<?php

namespace App\Modules\Installer\Services;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use PDO;
use PDOException;

class InstallerService
{
    /**
     * Verificar requisitos do sistema
     */
    public function checkRequirements(): array
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP 8.4+',
                'required' => true,
                'status' => version_compare(PHP_VERSION, '8.4.0', '>='),
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

        // Verificar mod_rewrite (Apache)
        $requirements['mod_rewrite'] = [
            'name' => 'Apache mod_rewrite',
            'required' => true,
            'status' => $this->checkModRewrite()
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
            
            // Log detalhado para debug (sem credenciais)
            \Log::error('Database connection failed', [
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $safeMessage
            ];
        }
    }

    /**
     * Executar instalação completa
     */
    public function install(array $data): array
    {
        try {
            // 1. Detectar URL automaticamente se não fornecida
            $data = $this->prepareInstallationData($data);

            // 2. Criar arquivo .env
            $this->createEnvFile($data);

            // 3. Gerar chave da aplicação
            Artisan::call('key:generate', ['--force' => true]);

            // 4. Executar migrations
            Artisan::call('migrate', ['--force' => true]);

            // 5. Executar seeders básicos
            $this->runBasicSeeders($data);

            // 6. Criar usuário superadmin
            $this->createSuperAdminUser($data);

            // 7. Criar arquivo de instalação concluída
            $this->createInstalledFile($data);

            // 8. Otimizar aplicação
            Artisan::call('config:cache');
            Artisan::call('route:cache');

            return [
                'success' => true,
                'message' => 'Instalação concluída com sucesso!',
                'admin_url' => $data['system']['url'] . '/admin',
                'admin_email' => $data['admin']['email']
            ];

        } catch (Exception $e) {
            // Limpar em caso de erro
            $this->cleanup();

            \Log::error('Installation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erro durante a instalação: ' . $e->getMessage()
            ];
        }
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

        foreach ($settings as $key => $value) {
            DB::table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'group' => $this->getSettingGroup($key),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Adicionar IP do instalador à lista de IPs permitidos durante manutenção
        $installerIp = request()->ip();
        if ($installerIp && $installerIp !== '127.0.0.1') {
            DB::table('maintenance_ips')->insert([
                'ip_address' => $installerIp,
                'label' => 'IP do Instalador',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Sempre adicionar localhost
        DB::table('maintenance_ips')->insert([
            'ip_address' => '127.0.0.1',
            'label' => 'Localhost',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
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
        $userId = DB::table('users')->insertGetId([
            'name' => $data['admin']['name'],
            'email' => $data['admin']['email'],
            'password' => Hash::make($data['admin']['password']),
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Registrar criação do usuário no audit log
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

        \Log::info('Usuário superadmin criado durante instalação', [
            'user_id' => $userId,
            'name' => $data['admin']['name'],
            'email' => $data['admin']['email'],
            'ip' => request()->ip()
        ]);
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
        // Remover .env se foi criado
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            File::delete($envPath);
        }

        // Remover arquivo de instalação se existe
        $installedPath = storage_path('installed');
        if (File::exists($installedPath)) {
            File::delete($installedPath);
        }

        // Limpar caches
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('cache:clear');
        } catch (Exception $e) {
            // Ignorar erros de limpeza
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