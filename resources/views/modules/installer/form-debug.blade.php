<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Configuração - HomeMechanic System (Debug)</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #FF6B00;
            --primary-hover: #E55A00;
            --dark-color: #1A1A1A;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .installer-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .installer-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .installer-header {
            background: linear-gradient(135deg, var(--dark-color), #333);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 20px rgba(255, 107, 0, 0.3);
        }
        
        .debug-banner {
            background: #dc3545;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 0;
        }
        
        .installer-body {
            padding: 3rem;
        }
        
        .form-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-color);
        }
        
        .form-section h4 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
        }
        
        .btn-installer {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3);
        }
        
        .btn-installer:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.4);
        }
        
        .btn-test {
            background: #6c757d;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-test:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-content {
            text-align: center;
            color: white;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .debug-console {
            background: #1a1a1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="debug-banner">
        🐛 MODO DEBUG ATIVO - Versão com logs detalhados para diagnóstico
    </div>
    
    <div class="installer-container">
        <div class="installer-card">
            <!-- Header -->
            <div class="installer-header">
                <div class="logo">
                    🔧
                </div>
                <h1 class="mb-3">HomeMechanic System</h1>
                <p class="mb-0">Configuração e Instalação do Sistema</p>
                <small class="text-muted">Versão Debug com Logs Detalhados</small>
            </div>
            
            <!-- Body -->
            <div class="installer-body">
                <form id="installForm" method="POST" action="{{ route('installer.store') }}">
                    @csrf
                    
                    <!-- Seção Banco de Dados -->
                    <div class="form-section">
                        <h4><i class="bi bi-database me-2"></i>Configuração do Banco de Dados</h4>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="db_host" class="form-label">Host do Banco</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="127.0.0.1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="db_port" class="form-label">Porta</label>
                                    <input type="number" class="form-control" id="db_port" name="db_port" value="3306" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="db_name" class="form-label">Nome do Banco</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" placeholder="homemechanic_2026" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db_user" class="form-label">Usuário</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" placeholder="usuario_banco" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db_password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="db_password" name="db_password" placeholder="senha_banco">
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-test" id="testDbBtn">
                                <i class="bi bi-check-circle me-1"></i>Testar Conexão
                            </button>
                        </div>
                    </div>
                    
                    <!-- Seção Administrador -->
                    <div class="form-section">
                        <h4><i class="bi bi-person-gear me-2"></i>Conta do Administrador</h4>
                        
                        <div class="form-group">
                            <label for="admin_name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="admin_name" name="admin_name" value="Administrador" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="admin@homemechanic.com.br" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" value="admin123456" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password_confirmation" class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" value="admin123456" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção Empresa -->
                    <div class="form-section">
                        <h4><i class="bi bi-building me-2"></i>Informações da Empresa</h4>
                        
                        <div class="form-group">
                            <label for="company_name" class="form-label">Nome da Empresa</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="HomeMechanic">
                        </div>
                        
                        <div class="form-group">
                            <label for="company_description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="company_description" name="company_description" rows="3">Sistema de gestão para oficinas mecânicas especializadas em carros esportivos de luxo e tuning</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="system_url" class="form-label">URL do Sistema</label>
                            <input type="url" class="form-control" id="system_url" name="system_url" value="https://{{ request()->getHost() }}">
                        </div>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="text-center">
                        <a href="{{ route('installer.index') }}" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left me-2"></i>Voltar
                        </a>
                        
                        <button type="submit" class="btn-installer" id="installBtn">
                            <i class="bi bi-rocket me-2"></i>
                            Iniciar Instalação
                        </button>
                    </div>
                </form>
                
                <!-- Console de Debug -->
                <div class="debug-console" id="debugConsole">
                    <div><strong>[DEBUG] Console de logs em tempo real:</strong></div>
                    <div>Aguardando ações do usuário...</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h4>Instalando Sistema...</h4>
            <p>Por favor, aguarde. Este processo pode levar alguns minutos.</p>
        </div>
    </div>
    
    <!-- JavaScript com Debug -->
    <script src="{{ asset('js/installer-debug.js') }}"></script>
    
    <script>
        // Redirecionar logs do console para o debug console
        const debugConsole = document.getElementById('debugConsole');
        const originalLog = console.log;
        const originalError = console.error;
        
        function addToDebugConsole(message, type = 'log') {
            const timestamp = new Date().toLocaleTimeString();
            const color = type === 'error' ? '#ff6b6b' : type === 'warn' ? '#ffd93d' : '#00ff00';
            
            const logDiv = document.createElement('div');
            logDiv.style.color = color;
            logDiv.innerHTML = `[${timestamp}] ${message}`;
            
            debugConsole.appendChild(logDiv);
            debugConsole.scrollTop = debugConsole.scrollHeight;
            
            // Manter apenas as últimas 50 linhas
            while (debugConsole.children.length > 52) { // 50 + header + initial message
                debugConsole.removeChild(debugConsole.children[2]);
            }
        }
        
        console.log = function(...args) {
            originalLog.apply(console, args);
            addToDebugConsole(args.join(' '), 'log');
        };
        
        console.error = function(...args) {
            originalError.apply(console, args);
            addToDebugConsole(args.join(' '), 'error');
        };
        
        // Log inicial
        console.log('🚀 HomeMechanic Installer Debug Mode iniciado');
        console.log('📍 URL atual: ' + window.location.href);
        console.log('🌐 User Agent: ' + navigator.userAgent);
    </script>
</body>
</html>