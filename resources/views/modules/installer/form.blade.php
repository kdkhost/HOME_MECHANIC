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
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #FF6B00, #E55A00);
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
            width: 50%;
            transition: width 0.3s ease;
        }
        
        .section-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: #f8f9fa;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #FF6B00;
        }
        
        .section-icon {
            font-size: 1.5rem;
            color: #FF6B00;
            margin-right: 0.75rem;
        }
        
        .form-control:focus {
            border-color: #FF6B00;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
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
        
        .btn-test {
            background: #6c757d;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-size: 0.875rem;
        }
        
        .btn-test:hover {
            background: #5a6268;
            color: white;
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
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .password-strength-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
            width: 0%;
        }
        
        .strength-weak { background-color: #dc3545; }
        .strength-medium { background-color: #ffc107; }
        .strength-strong { background-color: #28a745; }
        
        .test-result {
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 5px;
            font-size: 0.875rem;
        }
        
        .test-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .test-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
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
                <p class="mb-0">Configuração da Instalação</p>
                
                <!-- Progress Bar -->
                <div class="progress-bar-custom mt-4">
                    <div class="progress-fill"></div>
                </div>
                <small>Passo 2 de 4: Configurando sistema</small>
            </div>

            <!-- Body -->
            <div class="installer-body">
                <form id="installForm" method="POST" action="{{ route('installer.store') }}">
                    @csrf
                    
                    <!-- Seção: Banco de Dados -->
                    <div class="section-card">
                        <div class="section-header">
                            <i class="bi bi-database section-icon"></i>
                            <h5 class="mb-0">Configuração do Banco de Dados</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <label for="db_host" class="form-label">Host do Banco</label>
                                <input type="text" class="form-control" id="db_host" name="db_host" 
                                       value="127.0.0.1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="db_port" class="form-label">Porta</label>
                                <input type="number" class="form-control" id="db_port" name="db_port" 
                                       value="3306" min="1" max="65535">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="db_name" class="form-label">Nome do Banco</label>
                                <input type="text" class="form-control" id="db_name" name="db_name" 
                                       placeholder="homemechanic" required>
                            </div>
                            <div class="col-md-6">
                                <label for="db_user" class="form-label">Usuário</label>
                                <input type="text" class="form-control" id="db_user" name="db_user" 
                                       placeholder="root" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <label for="db_password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="db_password" name="db_password" 
                                       placeholder="Deixe em branco se não houver senha">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-test w-100" id="testDbBtn">
                                    <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                    <i class="bi bi-database-check me-2"></i>
                                    Testar Conexão
                                </button>
                            </div>
                        </div>
                        
                        <div id="dbTestResult" class="test-result" style="display: none;"></div>
                    </div>

                    <!-- Seção: Administrador -->
                    <div class="section-card">
                        <div class="section-header">
                            <i class="bi bi-person-gear section-icon"></i>
                            <h5 class="mb-0">Conta do Administrador</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label for="admin_name" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="admin_name" name="admin_name" 
                                       placeholder="João Silva" required>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                       placeholder="admin@suaempresa.com.br" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="admin_password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                       placeholder="Mínimo 8 caracteres" required>
                                <div class="password-strength">
                                    <div class="password-strength-fill" id="passwordStrengthFill"></div>
                                </div>
                                <small class="text-muted" id="passwordStrengthText">Digite uma senha forte</small>
                            </div>
                            <div class="col-md-6">
                                <label for="admin_password_confirmation" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="admin_password_confirmation" 
                                       name="admin_password_confirmation" placeholder="Digite a senha novamente" required>
                            </div>
                        </div>
                    </div>

                    <!-- Seção: Empresa -->
                    <div class="section-card">
                        <div class="section-header">
                            <i class="bi bi-building section-icon"></i>
                            <h5 class="mb-0">Informações da Empresa</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <label for="company_name" class="form-label">Nome da Empresa</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       placeholder="Oficina Premium Motors" required>
                            </div>
                            <div class="col-md-4">
                                <label for="company_url" class="form-label">Site da Empresa</label>
                                <input type="url" class="form-control" id="company_url" name="company_url" 
                                       placeholder="https://suaempresa.com.br" required>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label for="company_description" class="form-label">Descrição (Opcional)</label>
                            <textarea class="form-control" id="company_description" name="company_description" 
                                      rows="3" placeholder="Breve descrição da sua empresa..."></textarea>
                        </div>
                    </div>

                    <!-- Termos de Uso -->
                    <div class="section-card">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" required>
                            <label class="form-check-label" for="terms_accepted">
                                Eu li e aceito os <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">termos de uso</a> 
                                e <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">política de privacidade</a>
                            </label>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="text-center">
                        <a href="{{ route('installer.index') }}" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left me-2"></i>
                            Voltar
                        </a>
                        <button type="submit" class="btn-installer" id="installBtn">
                            <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bi bi-download me-2"></i>
                            Instalar Sistema
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Termos de Uso -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Termos de Uso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Licença de Uso</h6>
                    <p>Este software é fornecido sob licença MIT. Você pode usar, modificar e distribuir conforme os termos da licença.</p>
                    
                    <h6>2. Responsabilidades</h6>
                    <p>O usuário é responsável pela configuração adequada, backup dos dados e segurança do sistema.</p>
                    
                    <h6>3. Suporte</h6>
                    <p>O suporte técnico é fornecido conforme disponibilidade e não há garantias de tempo de resposta.</p>
                    
                    <h6>4. Limitação de Responsabilidade</h6>
                    <p>O software é fornecido "como está" sem garantias de qualquer tipo.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Política de Privacidade -->
    <div class="modal fade" id="privacyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Política de Privacidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Coleta de Dados</h6>
                    <p>O sistema coleta apenas dados necessários para seu funcionamento, como informações de usuários e conteúdo.</p>
                    
                    <h6>Uso dos Dados</h6>
                    <p>Os dados são utilizados exclusivamente para o funcionamento do sistema e não são compartilhados com terceiros.</p>
                    
                    <h6>Segurança</h6>
                    <p>Implementamos medidas de segurança para proteger seus dados contra acesso não autorizado.</p>
                    
                    <h6>Cookies</h6>
                    <p>O sistema utiliza cookies para sessões de usuário e funcionalidades essenciais.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const installForm = document.getElementById('installForm');
            const testDbBtn = document.getElementById('testDbBtn');
            const installBtn = document.getElementById('installBtn');
            const passwordInput = document.getElementById('admin_password');
            const passwordConfirmInput = document.getElementById('admin_password_confirmation');
            
            // Configurar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Teste de conexão com banco
            testDbBtn.addEventListener('click', function() {
                const loading = this.querySelector('.loading');
                const resultDiv = document.getElementById('dbTestResult');
                
                loading.classList.add('show');
                this.disabled = true;
                
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('db_host', document.getElementById('db_host').value);
                formData.append('db_port', document.getElementById('db_port').value);
                formData.append('db_name', document.getElementById('db_name').value);
                formData.append('db_user', document.getElementById('db_user').value);
                formData.append('db_password', document.getElementById('db_password').value);
                
                fetch('{{ route("installer.test-database") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    resultDiv.style.display = 'block';
                    resultDiv.className = 'test-result ' + (data.success ? 'test-success' : 'test-error');
                    resultDiv.innerHTML = '<i class="bi bi-' + (data.success ? 'check-circle' : 'x-circle') + ' me-2"></i>' + data.message;
                })
                .catch(error => {
                    resultDiv.style.display = 'block';
                    resultDiv.className = 'test-result test-error';
                    resultDiv.innerHTML = '<i class="bi bi-x-circle me-2"></i>Erro na conexão. Verifique os dados.';
                })
                .finally(() => {
                    loading.classList.remove('show');
                    this.disabled = false;
                });
            });
            
            // Verificador de força da senha
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strengthFill = document.getElementById('passwordStrengthFill');
                const strengthText = document.getElementById('passwordStrengthText');
                
                let strength = 0;
                let text = '';
                
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                if (password.length === 0) {
                    strengthFill.style.width = '0%';
                    strengthFill.className = 'password-strength-fill';
                    text = 'Digite uma senha forte';
                } else if (strength < 3) {
                    strengthFill.style.width = '33%';
                    strengthFill.className = 'password-strength-fill strength-weak';
                    text = 'Senha fraca';
                } else if (strength < 5) {
                    strengthFill.style.width = '66%';
                    strengthFill.className = 'password-strength-fill strength-medium';
                    text = 'Senha média';
                } else {
                    strengthFill.style.width = '100%';
                    strengthFill.className = 'password-strength-fill strength-strong';
                    text = 'Senha forte';
                }
                
                strengthText.textContent = text;
            });
            
            // Validação de confirmação de senha
            passwordConfirmInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.setCustomValidity('As senhas não conferem');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            // Submissão do formulário
            installForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const loading = installBtn.querySelector('.loading');
                loading.classList.add('show');
                installBtn.disabled = true;
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#28a745"
                        }).showToast();
                        
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        Toastify({
                            text: data.message,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545"
                        }).showToast();
                        
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById(field);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const feedback = input.parentNode.querySelector('.invalid-feedback');
                                    if (feedback) {
                                        feedback.textContent = data.errors[field][0];
                                    }
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    Toastify({
                        text: 'Erro interno. Tente novamente.',
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                })
                .finally(() => {
                    loading.classList.remove('show');
                    installBtn.disabled = false;
                });
            });
            
            // Remover classe de erro ao digitar
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
</body>
</html>