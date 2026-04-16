<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Configuração - HomeMechanic System</title>
    
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
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2rem 0;
            padding: 0 2rem;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
            max-width: 200px;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            z-index: 2;
            position: relative;
        }
        
        .step.completed .step-number {
            background: var(--success-color);
            color: white;
        }
        
        .step.active .step-number {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        .step-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            color: #6c757d;
            transition: color 0.3s ease;
        }
        
        .step.completed .step-title {
            color: var(--success-color);
        }
        
        .step.active .step-title {
            color: var(--primary-color);
        }
        
        .step-connector {
            position: absolute;
            top: 25px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step.completed .step-connector {
            background: var(--success-color);
        }
        
        .step:last-child .step-connector {
            display: none;
        }
        
        .installer-body {
            padding: 2rem;
        }
        
        .config-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e9ecef;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .section-subtitle {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
        }
        
        .form-control.is-valid {
            border-color: var(--success-color);
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
        }
        
        .btn-test {
            background: #17a2b8;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-test:hover {
            background: #138496;
            transform: translateY(-1px);
        }
        
        .btn-installer {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
            font-size: 1.1rem;
        }
        
        .btn-installer:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.4);
        }
        
        .btn-installer:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
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
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                gap: 1rem;
            }
            
            .step-connector {
                display: none;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .config-section {
                padding: 1.5rem;
            }
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
                <p class="mb-0">Configuração do Sistema</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <div class="step-number"><i class="bi bi-check"></i></div>
                    <div class="step-title">Requisitos</div>
                    <div class="step-connector"></div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-title">Configuração</div>
                    <div class="step-connector"></div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-title">Instalação</div>
                    <div class="step-connector"></div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-title">Concluído</div>
                </div>
            </div>

            <!-- Body -->
            <div class="installer-body">
                <form id="installForm" method="POST" action="{{ route('installer.store') }}">
                    @csrf
                    
                    <!-- Database Configuration -->
                    <div class="config-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="bi bi-database"></i>
                            </div>
                            <div>
                                <h3 class="section-title">Configuração do Banco de Dados</h3>
                                <p class="section-subtitle">Configure a conexão com o banco de dados MySQL/MariaDB</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="db_host" class="form-label">
                                        <i class="bi bi-server"></i>
                                        Servidor do Banco
                                    </label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="127.0.0.1" required>
                                    <div class="form-text">Geralmente localhost ou 127.0.0.1</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="db_port" class="form-label">
                                        <i class="bi bi-plug"></i>
                                        Porta
                                    </label>
                                    <input type="number" class="form-control" id="db_port" name="db_port" 
                                           value="3306" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db_name" class="form-label">
                                        <i class="bi bi-database-fill"></i>
                                        Nome do Banco
                                    </label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" required>
                                    <div class="form-text">Nome do banco de dados criado no cPanel</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db_user" class="form-label">
                                        <i class="bi bi-person"></i>
                                        Usuário
                                    </label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="db_password" class="form-label">
                                        <i class="bi bi-key"></i>
                                        Senha
                                    </label>
                                    <input type="password" class="form-control" id="db_password" name="db_password">
                                    <div class="form-text">Deixe em branco se não houver senha</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-test w-100" onclick="testDatabase()">
                                        <i class="bi bi-wifi me-2"></i>
                                        Testar Conexão
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin User Configuration -->
                    <div class="config-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="bi bi-person-gear"></i>
                            </div>
                            <div>
                                <h3 class="section-title">Usuário Administrador</h3>
                                <p class="section-subtitle">Crie a conta do administrador principal do sistema</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_name" class="form-label">
                                        <i class="bi bi-person"></i>
                                        Nome Completo
                                    </label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name" 
                                           value="Administrador" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_email" class="form-label">
                                        <i class="bi bi-envelope"></i>
                                        E-mail
                                    </label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                    <div class="form-text">Este será seu login no sistema</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password" class="form-label">
                                        <i class="bi bi-lock"></i>
                                        Senha
                                    </label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                           minlength="8" required>
                                    <div class="form-text">Mínimo 8 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password_confirmation" class="form-label">
                                        <i class="bi bi-lock-fill"></i>
                                        Confirmar Senha
                                    </label>
                                    <input type="password" class="form-control" id="admin_password_confirmation" 
                                           name="admin_password_confirmation" minlength="8" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Configuration -->
                    <div class="config-section">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h3 class="section-title">Informações da Empresa</h3>
                                <p class="section-subtitle">Configure os dados da sua oficina mecânica</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="company_name" class="form-label">
                                        <i class="bi bi-shop"></i>
                                        Nome da Oficina
                                    </label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="HomeMechanic" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="system_url" class="form-label">
                                        <i class="bi bi-globe"></i>
                                        URL do Sistema
                                    </label>
                                    <input type="url" class="form-control" id="system_url" name="system_url" 
                                           value="{{ request()->getSchemeAndHttpHost() }}">
                                    <div class="form-text">Detectado automaticamente</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="company_description" class="form-label">
                                <i class="bi bi-card-text"></i>
                                Descrição da Empresa
                            </label>
                            <textarea class="form-control" id="company_description" name="company_description" 
                                      rows="3" placeholder="Descreva sua oficina e especialidades...">Oficina especializada em carros de luxo esportivos e tuning. Qualidade e excelência em cada serviço.</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('installer.index') }}" class="btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Voltar aos Requisitos
                        </a>
                        
                        <button type="submit" class="btn-installer" id="installBtn">
                            <i class="bi bi-rocket me-2"></i>
                            Iniciar Instalação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h5>Instalando Sistema...</h5>
            <p class="mb-0">Por favor, aguarde. Isso pode levar alguns minutos.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Test database connection
        async function testDatabase() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Testando...';
            
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_port', document.getElementById('db_port').value);
            formData.append('db_name', document.getElementById('db_name').value);
            formData.append('db_user', document.getElementById('db_user').value);
            formData.append('db_password', document.getElementById('db_password').value);
            
            try {
                const response = await fetch('{{ route("installer.test-database") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conexão Bem-sucedida!',
                        text: result.message,
                        confirmButtonColor: '#FF6B00'
                    });
                    
                    // Mark database fields as valid
                    ['db_host', 'db_port', 'db_name', 'db_user', 'db_password'].forEach(id => {
                        document.getElementById(id).classList.add('is-valid');
                        document.getElementById(id).classList.remove('is-invalid');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro na Conexão',
                        text: result.message,
                        confirmButtonColor: '#FF6B00'
                    });
                    
                    // Mark database fields as invalid
                    ['db_host', 'db_port', 'db_name', 'db_user', 'db_password'].forEach(id => {
                        document.getElementById(id).classList.add('is-invalid');
                        document.getElementById(id).classList.remove('is-valid');
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao testar conexão com banco de dados.',
                    confirmButtonColor: '#FF6B00'
                });
            }
            
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
        
        // Form validation
        document.getElementById('installForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate password confirmation
            const password = document.getElementById('admin_password').value;
            const confirmation = document.getElementById('admin_password_confirmation').value;
            
            if (password !== confirmation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Senhas não conferem',
                    text: 'A senha e confirmação devem ser iguais.',
                    confirmButtonColor: '#FF6B00'
                });
                return;
            }
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Confirmar Instalação',
                text: 'Tem certeza que deseja instalar o sistema com essas configurações?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF6B00',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, instalar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading overlay
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    
                    // Submit form
                    const formData = new FormData(this);
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Instalação Concluída!',
                                text: data.message,
                                confirmButtonColor: '#FF6B00',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = data.redirect || data.admin_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro na Instalação',
                                text: data.message,
                                confirmButtonColor: '#FF6B00'
                            });
                        }
                    })
                    .catch(error => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Ocorreu um erro durante a instalação.',
                            confirmButtonColor: '#FF6B00'
                        });
                    });
                }
            });
        });
        
        // Real-time password validation
        document.getElementById('admin_password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('admin_password').value;
            const confirmation = this.value;
            
            if (confirmation && password !== confirmation) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (confirmation) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>