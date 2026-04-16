<?php

namespace App\Modules\Installer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Installer\Requests\InstallRequest;
use App\Modules\Installer\Services\InstallerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstallerController extends Controller
{
    private InstallerService $installerService;

    public function __construct(InstallerService $installerService)
    {
        $this->installerService = $installerService;
    }

    /**
     * Exibir página de verificação de requisitos
     */
    public function index()
    {
        // Verificar se já está instalado
        if ($this->installerService->isInstalled()) {
            return redirect('/')->with('info', 'Sistema já está instalado.');
        }

        $requirements = $this->installerService->checkRequirements();
        $systemInfo = $this->installerService->getSystemInfo();

        // Verificar se todos os requisitos foram atendidos
        $allRequirementsMet = $this->checkAllRequirements($requirements);

        return view('modules.installer.requirements', [
            'requirements' => $requirements,
            'systemInfo' => $systemInfo,
            'allRequirementsMet' => $allRequirementsMet
        ]);
    }

    /**
     * Exibir formulário de configuração
     */
    public function create()
    {
        // Verificar se já está instalado
        if ($this->installerService->isInstalled()) {
            return redirect('/')->with('info', 'Sistema já está instalado.');
        }

        // Verificar requisitos novamente
        $requirements = $this->installerService->checkRequirements();
        if (!$this->checkAllRequirements($requirements)) {
            return redirect()->route('installer.index')
                ->with('error', 'Alguns requisitos não foram atendidos. Corrija-os antes de continuar.');
        }

        return view('modules.installer.form');
    }

    /**
     * Processar instalação
     */
    public function store(InstallRequest $request)
    {
        try {
            // Verificar se já está instalado
            if ($this->installerService->isInstalled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistema já está instalado.'
                ], 400);
            }

            // Testar conexão com banco de dados
            $dbConfig = [
                'host' => $request->input('db_host'),
                'port' => $request->input('db_port', 3306),
                'database' => $request->input('db_name'),
                'username' => $request->input('db_user'),
                'password' => $request->input('db_password', '')
            ];

            $connectionTest = $this->installerService->testDatabaseConnection($dbConfig);
            
            if (!$connectionTest['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $connectionTest['message'],
                    'step' => 'database'
                ], 400);
            }

            // Preparar dados para instalação
            $installData = [
                'database' => $dbConfig,
                'admin' => [
                    'name' => $request->input('admin_name'),
                    'email' => $request->input('admin_email'),
                    'password' => $request->input('admin_password')
                ],
                'company' => [
                    'name' => $request->input('company_name') ?: 'HomeMechanic',
                    'description' => $request->input('company_description')
                ],
                'system' => [
                    'url' => $request->input('system_url') // Será detectado automaticamente se vazio
                ]
            ];

            // Executar instalação
            $result = $this->installerService->install($installData);

            if ($result['success']) {
                Log::info('Sistema instalado com sucesso', [
                    'admin_email' => $installData['admin']['email'],
                    'company_name' => $installData['company']['name'],
                    'system_url' => $result['admin_url'] ?? 'N/A'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'admin_url' => $result['admin_url'] ?? route('admin.login'),
                    'admin_email' => $result['admin_email'] ?? $installData['admin']['email'],
                    'redirect' => $result['admin_url'] ?? route('admin.login')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'details' => $result['details'] ?? null
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação durante instalação', [
                'errors' => $e->errors(),
                'input' => $request->except(['admin_password', 'admin_password_confirmation', 'db_password'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Dados de entrada inválidos.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erro durante instalação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'input' => $request->except(['admin_password', 'admin_password_confirmation', 'db_password'])
            ]);

            // Retornar erro mais específico baseado no tipo de exceção
            $errorMessage = 'Erro interno durante a instalação.';
            $errorDetails = null;

            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $errorMessage = 'Erro de banco de dados durante a instalação.';
                $errorDetails = 'Verifique as configurações do banco de dados e permissões.';
            } elseif (strpos($e->getMessage(), 'file_put_contents') !== false) {
                $errorMessage = 'Erro de permissão de arquivo durante a instalação.';
                $errorDetails = 'Verifique as permissões dos diretórios storage/ e bootstrap/cache/.';
            } elseif (strpos($e->getMessage(), 'Class') !== false && strpos($e->getMessage(), 'not found') !== false) {
                $errorMessage = 'Erro de classe não encontrada.';
                $errorDetails = 'Possível problema com autoloader ou dependências.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'details' => $errorDetails,
                'debug_info' => config('app.debug') ? [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * Testar conexão com banco de dados via AJAX
     */
    public function testDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_password' => 'nullable|string'
        ]);

        $dbConfig = [
            'host' => $request->input('db_host'),
            'port' => $request->input('db_port', 3306),
            'database' => $request->input('db_name'),
            'username' => $request->input('db_user'),
            'password' => $request->input('db_password', '')
        ];

        $result = $this->installerService->testDatabaseConnection($dbConfig);

        return response()->json($result);
    }

    /**
     * Verificar se todos os requisitos foram atendidos
     */
    private function checkAllRequirements(array $requirements): bool
    {
        // Verificar versão do PHP
        if (!$requirements['php_version']['status']) {
            return false;
        }

        // Verificar extensões
        foreach ($requirements['extensions'] as $extension) {
            if ($extension['required'] && !$extension['status']) {
                return false;
            }
        }

        // Verificar URL rewrite (mod_rewrite ou LiteSpeed)
        if (isset($requirements['url_rewrite']) && !$requirements['url_rewrite']['status']) {
            return false;
        }

        // Verificar servidor web
        if (isset($requirements['web_server']) && !$requirements['web_server']['status']) {
            return false;
        }

        // Verificar permissões
        if (isset($requirements['permissions'])) {
            foreach ($requirements['permissions'] as $permission) {
                if ($permission['required'] && !$permission['status']) {
                    return false;
                }
            }
        }

        return true;
    }
}