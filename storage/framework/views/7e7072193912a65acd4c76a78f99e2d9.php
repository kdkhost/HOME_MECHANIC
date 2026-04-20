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
        
        .step.active .step-number {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
        }
        
        .step.completed .step-number {
            background: var(--success-color);
            color: white;
        }
        
        .step-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            color: #6c757d;
            transition: color 0.3s ease;
        }
        
        .step.active .step-title {
            color: var(--primary-color);
        }
        
        .step.completed .step-title {
            color: var(--success-color);
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
        
        .requirements-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .requirement-category {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
        }
        
        .category-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        
        .category-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            background: white;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        
        .requirement-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .requirement-item.success {
            border-color: var(--success-color);
            background: #f8fff9;
        }
        
        .requirement-item.error {
            border-color: var(--danger-color);
            background: #fff8f8;
        }
        
        .requirement-icon {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 24px;
            text-align: center;
        }
        
        .requirement-details {
            flex: 1;
        }
        
        .requirement-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark-color);
        }
        
        .requirement-status {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .system-info-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid #dee2e6;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-right: 0.5rem;
            min-width: 80px;
        }
        
        .info-value {
            color: var(--dark-color);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
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
        
        .status-summary {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .summary-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .help-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .help-title {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #856404;
            margin-bottom: 1rem;
        }
        
        .help-content {
            color: #856404;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                gap: 1rem;
            }
            
            .step-connector {
                display: none;
            }
            
            .requirements-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
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
                <p class="mb-0">Sistema de Gestão para Oficinas Especializadas</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-title">Requisitos</div>
                    <div class="step-connector"></div>
                </div>
                <div class="step">
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
                <?php if(session('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('info')): ?>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-info-circle me-2"></i>
                        <?php echo e(session('info')); ?>

                    </div>
                <?php endif; ?>

                <!-- Status Summary -->
                <div class="status-summary">
                    <div class="summary-item">
                        <div class="summary-number text-success"><?php echo e(collect($requirements['extensions'])->where('status', true)->count()); ?></div>
                        <div class="summary-label">Extensões OK</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number <?php echo e($requirements['php_version']['status'] ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($requirements['php_version']['status'] ? '✓' : '✗'); ?>

                        </div>
                        <div class="summary-label">PHP Version</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number <?php echo e(isset($requirements['permissions']) && collect($requirements['permissions'])->where('status', true)->count() === count($requirements['permissions']) ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e(isset($requirements['permissions']) ? collect($requirements['permissions'])->where('status', true)->count() : 0); ?>

                        </div>
                        <div class="summary-label">Permissões</div>
                    </div>
                </div>

                <!-- Requirements Grid -->
                <div class="requirements-grid">
                    <!-- PHP Requirements -->
                    <div class="requirement-category">
                        <div class="category-header">
                            <div class="category-icon">
                                <i class="bi bi-code-slash"></i>
                            </div>
                            <h5 class="category-title">Requisitos PHP</h5>
                        </div>
                        
                        <div class="requirement-item <?php echo e($requirements['php_version']['status'] ? 'success' : 'error'); ?>">
                            <div class="requirement-icon">
                                <i class="bi bi-<?php echo e($requirements['php_version']['status'] ? 'check-circle text-success' : 'x-circle text-danger'); ?>"></i>
                            </div>
                            <div class="requirement-details">
                                <div class="requirement-name"><?php echo e($requirements['php_version']['name']); ?></div>
                                <div class="requirement-status">
                                    Versão atual: <?php echo e($requirements['php_version']['current']); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PHP Extensions -->
                    <div class="requirement-category">
                        <div class="category-header">
                            <div class="category-icon">
                                <i class="bi bi-puzzle"></i>
                            </div>
                            <h5 class="category-title">Extensões PHP</h5>
                        </div>
                        
                        <?php $__currentLoopData = $requirements['extensions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extension => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="requirement-item <?php echo e($info['status'] ? 'success' : 'error'); ?>">
                                <div class="requirement-icon">
                                    <i class="bi bi-<?php echo e($info['status'] ? 'check-circle text-success' : 'x-circle text-danger'); ?>"></i>
                                </div>
                                <div class="requirement-details">
                                    <div class="requirement-name"><?php echo e($info['name']); ?></div>
                                    <div class="requirement-status">
                                        <?php echo e($info['status'] ? 'Instalada' : 'Não instalada'); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- Server Requirements -->
                    <div class="requirement-category">
                        <div class="category-header">
                            <div class="category-icon">
                                <i class="bi bi-server"></i>
                            </div>
                            <h5 class="category-title">Servidor Web</h5>
                        </div>
                        
                        <?php if(isset($requirements['web_server'])): ?>
                            <div class="requirement-item <?php echo e($requirements['web_server']['status'] ? 'success' : 'error'); ?>">
                                <div class="requirement-icon">
                                    <i class="bi bi-<?php echo e($requirements['web_server']['status'] ? 'check-circle text-success' : 'x-circle text-danger'); ?>"></i>
                                </div>
                                <div class="requirement-details">
                                    <div class="requirement-name"><?php echo e($requirements['web_server']['name']); ?></div>
                                    <div class="requirement-status">
                                        <?php echo e($requirements['web_server']['status'] ? $requirements['web_server']['current'] : 'Não detectado'); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($requirements['url_rewrite'])): ?>
                            <div class="requirement-item <?php echo e($requirements['url_rewrite']['status'] ? 'success' : 'error'); ?>">
                                <div class="requirement-icon">
                                    <i class="bi bi-<?php echo e($requirements['url_rewrite']['status'] ? 'check-circle text-success' : 'x-circle text-danger'); ?>"></i>
                                </div>
                                <div class="requirement-details">
                                    <div class="requirement-name"><?php echo e($requirements['url_rewrite']['name']); ?></div>
                                    <div class="requirement-status">
                                        <?php echo e($requirements['url_rewrite']['status'] ? 'Ativo' : 'Inativo ou não detectado'); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php endif; ?>

                    <!-- File Permissions -->
                    <?php if(isset($requirements['permissions'])): ?>
                        <div class="requirement-category">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h5 class="category-title">Permissões</h5>
                            </div>
                            
                            <?php $__currentLoopData = $requirements['permissions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="requirement-item <?php echo e($info['status'] ? 'success' : 'error'); ?>">
                                    <div class="requirement-icon">
                                        <i class="bi bi-<?php echo e($info['status'] ? 'check-circle text-success' : 'x-circle text-danger'); ?>"></i>
                                    </div>
                                    <div class="requirement-details">
                                        <div class="requirement-name"><?php echo e($info['name']); ?></div>
                                        <div class="requirement-status">
                                            <?php echo e($info['status'] ? 'Gravável' : 'Sem permissão de escrita'); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- PHP Limits -->
                    <?php if(isset($requirements['limits'])): ?>
                        <div class="requirement-category">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="bi bi-speedometer2"></i>
                                </div>
                                <h5 class="category-title">Limites PHP</h5>
                            </div>
                            
                            <?php $__currentLoopData = $requirements['limits']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $limit => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="requirement-item <?php echo e($info['status'] ? 'success' : 'warning'); ?>">
                                    <div class="requirement-icon">
                                        <i class="bi bi-<?php echo e($info['status'] ? 'check-circle text-success' : 'exclamation-triangle text-warning'); ?>"></i>
                                    </div>
                                    <div class="requirement-details">
                                        <div class="requirement-name"><?php echo e($info['name']); ?></div>
                                        <div class="requirement-status">
                                            Valor atual: <?php echo e($info['current']); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- System Information -->
                <div class="system-info-card">
                    <h6 class="mb-3 d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informações do Sistema
                    </h6>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">PHP:</span>
                            <span class="info-value"><?php echo e($systemInfo['php_version']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Laravel:</span>
                            <span class="info-value"><?php echo e($systemInfo['laravel_version']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Servidor:</span>
                            <span class="info-value"><?php echo e($systemInfo['server_software']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Memória:</span>
                            <span class="info-value"><?php echo e($systemInfo['memory_limit']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Upload:</span>
                            <span class="info-value"><?php echo e($systemInfo['upload_max_filesize']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">POST:</span>
                            <span class="info-value"><?php echo e($systemInfo['post_max_size']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="action-buttons">
                    <?php if($allRequirementsMet): ?>
                        <a href="<?php echo e(route('installer.create-steps')); ?>" class="btn-installer">
                            <i class="bi bi-arrow-right me-2"></i>
                            Continuar para Configuração
                        </a>
                    <?php else: ?>
                        <button onclick="location.reload()" class="btn-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Verificar Novamente
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Help Section -->
                <?php if(!$allRequirementsMet): ?>
                    <div class="help-section">
                        <div class="help-title">
                            <i class="bi bi-question-circle me-2"></i>
                            Precisa de Ajuda?
                        </div>
                        <div class="help-content">
                            <p class="mb-2">Se você encontrou problemas com os requisitos:</p>
                            <ul class="mb-0">
                                <li>Verifique se o PHP 8.4+ está instalado e ativo</li>
                                <li>Instale as extensões PHP necessárias via cPanel ou SSH</li>
                                <li>Configure permissões: <code>chmod -R 755 storage bootstrap/cache</code></li>
                                <li>Ative o mod_rewrite no Apache ou configure LiteSpeed</li>
                                <li>Entre em contato com seu provedor de hospedagem se necessário</li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\installer\requirements.blade.php ENDPATH**/ ?>