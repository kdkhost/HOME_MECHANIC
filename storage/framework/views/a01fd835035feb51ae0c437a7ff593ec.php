<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title>Instalação - HomeMechanic System</title>
    
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
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem 0;
        }
        
        .installer-container {
            max-width: 900px;
            margin: 0 auto;
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
        }
        
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            padding: 2rem;
            background: #f8f9fa;
            position: relative;
        }
        
        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .step.active .step-circle {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 20px rgba(255, 107, 0, 0.5);
            transform: scale(1.1);
        }
        
        .step.completed .step-circle {
            background: var(--success-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 600;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
        }
        
        .step.completed .step-label {
            color: var(--success-color);
        }
        
        .installer-body {
            padding: 3rem;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 0, 0.25);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        
        .btn-secondary {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
        }
        
        .progress-step {
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #dee2e6;
        }
        
        .progress-step.processing {
            border-left-color: var(--primary-color);
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        .progress-step.completed {
            border-left-color: var(--success-color);
        }
        
        .progress-step.error {
            border-left-color: #dc3545;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .success-animation {
            text-align: center;
            padding: 3rem;
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--success-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 4rem;
            animation: scaleIn 0.5s ease;
        }
        
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        
        .credentials-box {
            background: #f8f9fa;
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 107, 0, 0.3);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <!-- Header -->
            <div class="installer-header">
                <div class="logo">🔧</div>
                <h1 class="mb-2">HomeMechanic System</h1>
                <p class="mb-0">Instalação Guiada do Sistema</p>
            </div>
            
            <!-- Steps Indicator -->
            <div class="steps-indicator">
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Banco de Dados</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Administrador</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Empresa</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Instalação</div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-label">Concluído</div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="installer-body">
                <form id="installForm">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Step 1: Banco de Dados -->
                    <div class="step-content active" data-step="1">
                        <h3 class="mb-4"><i class="bi bi-database me-2"></i>Configuração do Banco de Dados</h3>
                        
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
                            <small class="text-muted">O banco de dados deve já existir</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db_user" class="form-label">Usuário</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="db_password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="db_password" name="db_password">
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="testDatabase()">
                                <i class="bi bi-check-circle me-1"></i>Testar Conexão
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                Próximo <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Administrador -->
                    <div class="step-content" data-step="2">
                        <h3 class="mb-4"><i class="bi bi-person-gear me-2"></i>Conta do Administrador</h3>
                        
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
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" required minlength="8">
                                    <small class="text-muted">Mínimo 8 caracteres</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="admin_password_confirmation" class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="prevStep(1)">
                                <i class="bi bi-arrow-left me-1"></i>Voltar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                Próximo <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Empresa -->
                    <div class="step-content" data-step="3">
                        <h3 class="mb-4"><i class="bi bi-building me-2"></i>Informações da Empresa</h3>
                        
                        <div class="form-group">
                            <label for="company_name" class="form-label">Nome da Empresa</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="HomeMechanic" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="company_description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="company_description" name="company_description" rows="3">Sistema de gestão para oficinas mecânicas especializadas em carros esportivos de luxo e tuning</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="system_url" class="form-label">URL do Sistema</label>
                            <input type="url" class="form-control" id="system_url" name="system_url" value="https://<?php echo e(request()->getHost()); ?>" required>
                            <small class="text-muted">Será detectada automaticamente se deixar em branco</small>
                        </div>
                        
                        <div class="form-check" style="margin-top: 20px;">
                            <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1" checked required>
                            <label class="form-check-label" for="terms_accepted">
                                Aceito os termos de uso e confirmo que os dados fornecidos estão corretos
                            </label>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="prevStep(2)">
                                <i class="bi bi-arrow-left me-1"></i>Voltar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="startInstallation()">
                                Iniciar Instalação <i class="bi bi-rocket ms-1"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 4: Instalação em Progresso -->
                    <div class="step-content" data-step="4">
                        <h3 class="mb-4"><i class="bi bi-gear-fill me-2"></i>Instalando Sistema</h3>
                        <p class="text-muted">Por favor, aguarde enquanto o sistema é instalado...</p>
                        
                        <div id="installationProgress">
                            <!-- Progress steps will be added here dynamically -->
                        </div>
                    </div>
                    
                    <!-- Step 5: Concluído -->
                    <div class="step-content" data-step="5">
                        <div class="success-animation">
                            <div class="success-icon">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <h2 class="mb-3">Instalação Concluída!</h2>
                            <p class="text-muted mb-4">O HomeMechanic foi instalado com sucesso</p>
                            
                            <div class="credentials-box">
                                <h5 class="mb-3"><i class="bi bi-key me-2"></i>Credenciais de Acesso</h5>
                                <div class="credential-item">
                                    <strong>Email:</strong>
                                    <span id="displayEmail">-</span>
                                </div>
                                <div class="credential-item">
                                    <strong>Senha:</strong>
                                    <span id="displayPassword">-</span>
                                </div>
                                <small class="text-danger d-block mt-2">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Guarde estas credenciais em local seguro!
                                </small>
                            </div>
                            
                            <div class="mt-4">
                                <a href="/" class="btn btn-secondary me-2">
                                    <i class="bi bi-house me-1"></i>Ir ao Site
                                </a>
                                <a href="/admin" class="btn btn-primary">
                                    <i class="bi bi-speedometer2 me-1"></i>Ir ao Painel Admin
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="<?php echo e(asset('js/installer-steps.js')); ?>?v=<?php echo e(time()); ?>"></script>
</body>
</html><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\installer\install-steps.blade.php ENDPATH**/ ?>