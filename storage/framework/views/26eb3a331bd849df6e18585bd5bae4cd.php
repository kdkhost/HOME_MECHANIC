<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title>Redefinir Senha - HomeMechanic System</title>
    
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
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
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
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
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

        .btn-action {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 0, 0.3);
            color: white;
        }

        .loading { display: none; }
        .loading.show { display: inline-block; }

        @media (max-width: 768px) {
            .login-card { margin: 1rem; }
            .login-left { padding: 2rem; }
            .login-right { padding: 2rem; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="login-left">
                        <div class="logo">
                            <i class="bi bi-key"></i>
                        </div>
                        <h2 class="h3 mb-3">Nova Senha</h2>
                        <p class="mb-4">Escolha uma senha forte para manter sua conta protegida.</p>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="login-right">
                        <div class="text-center mb-4">
                            <h3 class="h4 mb-2">Redefinir Senha</h3>
                            <p class="text-muted">Crie sua nova senha de acesso</p>
                        </div>

                        <form id="resetForm" method="POST" action="<?php echo e(route('admin.password.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="token" value="<?php echo e($token); ?>">
                            
                            <div class="input-group">
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo e($email ?? old('email')); ?>" readonly required>
                                <label for="email">Seu E-mail</label>
                            </div>

                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder=" " required autofocus>
                                <label for="password">Nova Senha</label>
                            </div>

                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder=" " required>
                                <label for="password_confirmation">Confirmar Nova Senha</label>
                            </div>

                            <button type="submit" class="btn-action" id="submitBtn">
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="bi bi-check2-circle me-2"></i>
                                Alterar Minha Senha
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="<?php echo e(route('admin.login')); ?>" class="text-muted small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancelar e voltar para o login
                            </a>
                        </div>

                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">HomeMechanic System v1.0.0</small>
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
            const resetForm = document.getElementById('resetForm');
            const submitBtn = document.getElementById('submitBtn');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const HMToast = {
                _cfg: {
                    success: { bg: 'linear-gradient(135deg,#16a34a,#15803d)', icon: 'bi-check-circle', title: 'Sucesso' },
                    error: { bg: 'linear-gradient(135deg,#dc2626,#b91c1c)', icon: 'bi-x-circle', title: 'Erro' }
                },
                show(message, type = 'success') {
                    const cfg = this._cfg[type] || this._cfg.success;
                    const html = `<div class="d-flex align-items-center text-white" style="gap:12px; padding: 12px 15px;">
                        <i class="bi ${cfg.icon}" style="font-size: 1.5rem;"></i>
                        <div><b>${cfg.title}</b><br>${message}</div>
                    </div>`;
                    Toastify({
                        node: (() => { const d = document.createElement('div'); d.innerHTML = html; return d.firstElementChild; })(),
                        duration: 5000, gravity: 'top', position: 'right',
                        style: { background: cfg.bg, padding: '0', borderRadius: '12px' }
                    }).showToast();
                },
                success(msg) { this.show(msg, 'success'); },
                error(msg) { this.show(msg, 'error'); }
            };

            resetForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const loading = submitBtn.querySelector('.loading');
                loading.classList.add('show');
                submitBtn.disabled = true;

                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Senha Alterada!',
                            text: data.message,
                            confirmButtonColor: '#FF6B00'
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        HMToast.error(data.message);
                        if (data.errors) {
                            // Erros de validação detalhados se necessário
                        }
                    }
                })
                .catch(() => HMToast.error('Erro de conexão. Tente novamente.'))
                .finally(() => {
                    loading.classList.remove('show');
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
<?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\auth\reset-password.blade.php ENDPATH**/ ?>