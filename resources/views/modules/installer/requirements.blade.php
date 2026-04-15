<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Instalação - HomeMechanic System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #FF6B00, #E55A00);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .installer-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .installer-header {
            background: linear-gradient(135deg, #1A1A1A, #333);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .installer-body {
            padding: 2rem;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid #dee2e6;
        }
        
        .requirement-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        
        .requirement-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .requirement-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            width: 30px;
            text-align: center;
        }
        
        .requirement-details {
            flex: 1;
        }
        
        .requirement-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .requirement-status {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .system-info {
            background: #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF6B00, #E55A00);
            width: 25%;
            transition: width 0.3s ease;
        }
        
        .btn-installer {
            background: linear-gradient(135deg, #FF6B00, #E55A00);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s ease;
        }
        
        .btn-installer:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-installer:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: #FF6B00;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <!-- Header -->
            <div class="installer-header">
                <div class="logo">
                    <i class="bi bi-tools"></i>
                </div>
                <h1 class="h3 mb-2">HomeMechanic System</h1>
                <p class="mb-0">Verificação de Requisitos do Sistema</p>
                
                <!-- Progress Bar -->
                <div class="progress-bar-custom mt-4">
                    <div class="progress-fill"></div>
                </div>
                <small>Passo 1 de 4: Verificando requisitos</small>
            </div>

            <!-- Body -->
            <div class="installer-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ session('info') }}
                    </div>
                @endif

                <!-- Requisitos PHP -->
                <h5 class="mb-3">
                    <i class="bi bi-code-slash text-primary me-2"></i>
                    Requisitos PHP
                </h5>

                <div class="requirement-item {{ $requirements['php_version']['status'] ? 'success' : 'error' }}">
                    <div class="requirement-icon">
                        <i class="bi bi-{{ $requirements['php_version']['status'] ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                    </div>
                    <div class="requirement-details">
                        <div class="requirement-name">{{ $requirements['php_version']['name'] }}</div>
                        <div class="requirement-status">
                            Versão atual: {{ $requirements['php_version']['current'] }}
                        </div>
                    </div>
                </div>

                <!-- Extensões PHP -->
                <h5 class="mb-3 mt-4">
                    <i class="bi bi-puzzle text-primary me-2"></i>
                    Extensões PHP
                </h5>

                @foreach($requirements['extensions'] as $extension => $info)
                    <div class="requirement-item {{ $info['status'] ? 'success' : 'error' }}">
                        <div class="requirement-icon">
                            <i class="bi bi-{{ $info['status'] ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                        </div>
                        <div class="requirement-details">
                            <div class="requirement-name">{{ $info['name'] }}</div>
                            <div class="requirement-status">
                                {{ $info['status'] ? 'Instalada' : 'Não instalada' }}
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Servidor Web -->
                <h5 class="mb-3 mt-4">
                    <i class="bi bi-server text-primary me-2"></i>
                    Servidor Web
                </h5>

                @if(isset($requirements['web_server']))
                    <div class="requirement-item {{ $requirements['web_server']['status'] ? 'success' : 'error' }}">
                        <div class="requirement-icon">
                            <i class="bi bi-{{ $requirements['web_server']['status'] ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                        </div>
                        <div class="requirement-details">
                            <div class="requirement-name">{{ $requirements['web_server']['name'] }}</div>
                            <div class="requirement-status">
                                {{ $requirements['web_server']['status'] ? $requirements['web_server']['current'] : 'Não detectado' }}
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($requirements['url_rewrite']))
                    <div class="requirement-item {{ $requirements['url_rewrite']['status'] ? 'success' : 'error' }}">
                        <div class="requirement-icon">
                            <i class="bi bi-{{ $requirements['url_rewrite']['status'] ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                        </div>
                        <div class="requirement-details">
                            <div class="requirement-name">{{ $requirements['url_rewrite']['name'] }}</div>
                            <div class="requirement-status">
                                {{ $requirements['url_rewrite']['status'] ? 'Ativo' : 'Inativo ou não detectado' }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ambiente de Hospedagem -->
                @if(isset($requirements['cloudlinux']) || isset($requirements['imunify360']))
                    <h5 class="mb-3 mt-4">
                        <i class="bi bi-cloud text-primary me-2"></i>
                        Ambiente de Hospedagem
                    </h5>

                    @if(isset($requirements['cloudlinux']))
                        <div class="requirement-item {{ $requirements['cloudlinux']['status'] ? 'success' : '' }}">
                            <div class="requirement-icon">
                                <i class="bi bi-{{ $requirements['cloudlinux']['status'] ? 'check-circle text-success' : 'info-circle text-info' }}"></i>
                            </div>
                            <div class="requirement-details">
                                <div class="requirement-name">{{ $requirements['cloudlinux']['name'] }}</div>
                                <div class="requirement-status">
                                    {{ $requirements['cloudlinux']['current'] }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($requirements['imunify360']))
                        <div class="requirement-item {{ $requirements['imunify360']['status'] ? 'success' : '' }}">
                            <div class="requirement-icon">
                                <i class="bi bi-{{ $requirements['imunify360']['status'] ? 'shield-check text-success' : 'shield text-info' }}"></i>
                            </div>
                            <div class="requirement-details">
                                <div class="requirement-name">{{ $requirements['imunify360']['name'] }}</div>
                                <div class="requirement-status">
                                    {{ $requirements['imunify360']['status'] ? 'Ativo' : 'Não detectado' }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Permissões -->
                @if(isset($requirements['permissions']))
                    <h5 class="mb-3 mt-4">
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        Permissões de Arquivo
                    </h5>

                    @foreach($requirements['permissions'] as $permission => $info)
                        <div class="requirement-item {{ $info['status'] ? 'success' : 'error' }}">
                            <div class="requirement-icon">
                                <i class="bi bi-{{ $info['status'] ? 'check-circle text-success' : 'x-circle text-danger' }}"></i>
                            </div>
                            <div class="requirement-details">
                                <div class="requirement-name">{{ $info['name'] }}</div>
                                <div class="requirement-status">
                                    {{ $info['status'] ? 'Gravável' : 'Sem permissão de escrita' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Informações do Sistema -->
                <div class="system-info">
                    <h6 class="mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informações do Sistema
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">PHP:</small> {{ $systemInfo['php_version'] }}<br>
                            <small class="text-muted">Laravel:</small> {{ $systemInfo['laravel_version'] }}<br>
                            <small class="text-muted">Servidor:</small> {{ $systemInfo['server_software'] }}
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Memória:</small> {{ $systemInfo['memory_limit'] }}<br>
                            <small class="text-muted">Upload máx:</small> {{ $systemInfo['upload_max_filesize'] }}<br>
                            <small class="text-muted">POST máx:</small> {{ $systemInfo['post_max_size'] }}
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="text-center mt-4">
                    @if($allRequirementsMet)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            Todos os requisitos foram atendidos! Você pode prosseguir com a instalação.
                        </div>
                        <a href="{{ route('installer.create') }}" class="btn-installer">
                            <i class="bi bi-arrow-right me-2"></i>
                            Continuar Instalação
                        </a>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Alguns requisitos não foram atendidos. Corrija-os antes de continuar.
                        </div>
                        <button onclick="location.reload()" class="btn-installer">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Verificar Novamente
                        </button>
                    @endif
                </div>

                <!-- Ajuda -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6><i class="bi bi-question-circle text-primary me-2"></i>Precisa de Ajuda?</h6>
                    <p class="mb-2 small">Se você encontrou problemas com os requisitos:</p>
                    <ul class="small mb-0">
                        <li>Verifique se o PHP 8.4+ está instalado</li>
                        <li>Instale as extensões PHP necessárias</li>
                        <li>Configure permissões de escrita nas pastas storage/ e bootstrap/cache/</li>
                        <li>Ative o mod_rewrite no Apache</li>
                        <li>Consulte a documentação de instalação</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>