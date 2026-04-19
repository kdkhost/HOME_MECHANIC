<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login - HomeMechanic System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #FF6B00;
            --primary-dark: #E55A00;
            --dark-color: #0D0D0D;
            --sidebar-bg: #1A1A1A;
            --text-light: #6c757d;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            background: linear-gradient(135deg, var(--dark-color), var(--sidebar-bg));
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
            min-height: 100%;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .logo-container {
            position: relative;
            z-index: 2;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(255, 107, 0, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .login-right {
            padding: 3rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
            transform: translateY(-2px);
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group label {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            color: var(--text-light);
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 0.5rem;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -0.5rem;
            left: 0.75rem;
            font-size: 0.875rem;
            color: var(--primary-color);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 0, 0.3);
            color: white;
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
            accent-color: var(--primary-color);
        }

        .rate-limit-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .rate-limit-blocked {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        .session-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1050;
            display: none;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
            }
            
            .login-left {
                padding: 2rem;
            }
            
            .login-right {
                padding: 2rem;
            }
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .features-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .features-list li i {
            color: var(--primary-color);
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <!-- Session Warning -->
    <div id="sessionWarning" class="session-warning">
        <div class="d-flex align-items-center">
            <i class="bi bi-clock me-2"></i>
            <span>Sua sessão expirará em <span id="sessionTimer">--</span> minutos.</span>
            <button type="button" class="btn btn-sm btn-warning ms-3" onclick="renewSession()">
                Renovar
            </button>
        </div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="row g-0">
                <!-- Coluna Esquerda - Branding -->
                <div class="col-lg-5">
                    <div class="login-left">
                        <div class="logo-container">
                            <div class="logo">
                                <i class="bi bi-tools"></i>
                            </div>
                            <h2 class="h3 mb-3">HomeMechanic</h2>
                            <p class="mb-4">Sistema de gestão para oficinas especializadas em carros esportivos e tuning</p>
                            
                            <ul class="features-list">
                                <li>
                                    <i class="bi bi-shield-check"></i>
                                    <span>Sistema seguro e confiável</span>
                                </li>
                                <li>
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard completo</span>
                                </li>
                                <li>
                                    <i class="bi bi-tools"></i>
                                    <span>Gestão de serviços</span>
                                </li>
                                <li>
                                    <i class="bi bi-images"></i>
                                    <span>Galeria de trabalhos</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita - Formulário -->
                <div class="col-lg-7">
                    <div class="login-right">
                        <div class="text-center mb-4">
                            <h3 class="h4 mb-2">Bem-vindo de volta!</h3>
                            <p class="text-muted">Faça login para acessar o painel administrativo</p>
                        </div>

                        <!-- Rate Limit Info -->
                        <div id="rateLimitInfo" style="display: none;"></div>

                        <!-- Alerts -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Formulário de Login -->
                        <form id="loginForm" method="POST" action="{{ route('admin.login.submit') }}">
                            @csrf
                            
                            <div class="input-group">
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder=" " 
                                       required 
                                       autocomplete="email"
                                       value="{{ old('email') }}">
                                <label for="email">E-mail</label>
                            </div>

                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder=" " 
                                       required 
                                       autocomplete="current-password">
                                <label for="password">Senha</label>
                            </div>

                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember" value="1">
                                <label for="remember">Lembrar-me neste dispositivo</label>
                            </div>

                            <button type="submit" class="btn-login" id="loginBtn">
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Entrar no Sistema
                            </button>
                        </form>

                        <!-- Links Adicionais -->
                        <div class="text-center mt-4">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('admin.password.request') }}" class="text-muted small text-decoration-none">
                                        <i class="bi bi-question-circle me-1"></i>
                                        Esqueceu a senha?
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ url('/') }}" class="text-muted small text-decoration-none">
                                        <i class="bi bi-house me-1"></i>
                                        Voltar ao site
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Informações do Sistema -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                HomeMechanic System v1.0.0<br>
                                <i class="bi bi-shield-check text-success me-1"></i>
                                Conexão segura SSL
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const rateLimitInfo = document.getElementById('rateLimitInfo');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            /* ── HMToast Local (Bootstrap Icons) ──────────────── */
            const HMToast = {
                _cfg: {
                    success: { bg: 'linear-gradient(135deg,#16a34a,#15803d)', icon: 'bi-check-circle',   title: 'Sucesso'    },
                    error:   { bg: 'linear-gradient(135deg,#dc2626,#b91c1c)', icon: 'bi-x-circle',       title: 'Erro'       },
                    warning: { bg: 'linear-gradient(135deg,#d97706,#b45309)', icon: 'bi-exclamation-triangle', title: 'Atenção' },
                    info:    { bg: 'linear-gradient(135deg,#0891b2,#0e7490)', icon: 'bi-info-circle',    title: 'Informação' },
                },
                show(message, type = 'success', duration = 4000) {
                    const cfg = this._cfg[type] || this._cfg.info;
                    const html = `
                        <div class="d-flex align-items-center text-white" style="gap:12px; padding: 12px 15px;">
                            <div style="font-size: 1.5rem; display: flex; align-items: center;">
                                <i class="bi ${cfg.icon}"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.9rem; line-height: 1.2;">${cfg.title}</div>
                                <div style="font-size: 0.85rem; opacity: 0.9;">${message}</div>
                            </div>
                        </div>`;
                    Toastify({
                        node: (() => { const d = document.createElement('div'); d.innerHTML = html; return d.firstElementChild; })(),
                        duration, gravity: 'top', position: 'right', stopOnFocus: true,
                        style: { background: cfg.bg, padding: '0', borderRadius: '12px', boxShadow: '0 10px 15px -3px rgba(0,0,0,0.1)' },
                    }).showToast();
                },
                success(msg, dur) { this.show(msg, 'success', dur); },
                error(msg, dur)   { this.show(msg, 'error', dur || 6000); }
            };
            
            let rateLimitTimer;
            let sessionTimer;

            // Verificar rate limit ao carregar
            checkRateLimit();

            // Submissão do formulário
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const loading = loginBtn.querySelector('.loading');
                loading.classList.add('show');
                loginBtn.disabled = true;
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        HMToast.success(data.message);
                        
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        HMToast.error(data.message);
                        
                        // Atualizar informações de rate limit
                        checkRateLimit();
                        
                        // Mostrar tentativas restantes
                        if (data.attempts_left !== undefined) {
                            showRateLimitInfo(`Tentativas restantes: ${data.attempts_left}`, false);
                        }
                    }
                })
                .catch(error => {
                    HMToast.error('Erro de conexão. Tente novamente.');
                })
                .finally(() => {
                    loading.classList.remove('show');
                    loginBtn.disabled = false;
                });
            });

            // Verificar rate limit
            function checkRateLimit() {
                fetch('{{ route("admin.auth.rate-limit-info") }}', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.blocked) {
                        showRateLimitInfo(data.message, true);
                        startRateLimitCountdown(data.retry_after);
                    } else if (data.attempts > 0) {
                        showRateLimitInfo(`Tentativas restantes: ${data.attempts_left}`, false);
                    } else {
                        hideRateLimitInfo();
                    }
                })
                .catch(error => {
                    console.log('Erro ao verificar rate limit:', error);
                });
            }

            // Mostrar informações de rate limit
            function showRateLimitInfo(message, isBlocked) {
                rateLimitInfo.style.display = 'block';
                rateLimitInfo.className = 'rate-limit-info' + (isBlocked ? ' rate-limit-blocked' : '');
                rateLimitInfo.innerHTML = '<i class="bi bi-' + (isBlocked ? 'x-circle' : 'info-circle') + ' me-2"></i>' + message;
                
                if (isBlocked) {
                    loginBtn.disabled = true;
                }
            }

            // Ocultar informações de rate limit
            function hideRateLimitInfo() {
                rateLimitInfo.style.display = 'none';
                loginBtn.disabled = false;
            }

            // Countdown para rate limit
            function startRateLimitCountdown(seconds) {
                clearInterval(rateLimitTimer);
                
                rateLimitTimer = setInterval(() => {
                    seconds--;
                    
                    if (seconds <= 0) {
                        clearInterval(rateLimitTimer);
                        checkRateLimit();
                    } else {
                        showRateLimitInfo(`Bloqueado. Tente novamente em ${seconds} segundos.`, true);
                    }
                }, 1000);
            }

            // Verificar autenticação e sessão (se já logado)
            function checkAuthStatus() {
                fetch('{{ route("admin.auth.check") }}', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.authenticated) {
                        // Usuário já está logado, redirecionar
                        window.location.href = '{{ route("admin.dashboard.index") }}';
                    }
                })
                .catch(error => {
                    // Usuário não está logado, continuar normalmente
                });
            }

            // Verificar status ao carregar
            checkAuthStatus();

            // Animações dos inputs
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
                
                // Verificar se já tem valor ao carregar
                if (input.value) {
                    input.parentElement.classList.add('focused');
                }
            });
        });

        // Função global para renovar sessão
        function renewSession() {
            fetch('{{ route("admin.auth.renew-session") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('sessionWarning').style.display = 'none';
                    HMToast.success(data.message);
                }
            })
            .catch(error => {
                console.log('Erro ao renovar sessão:', error);
            });
        }
    </script>
</body>
</html>