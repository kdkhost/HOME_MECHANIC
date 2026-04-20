

<?php $__env->startSection('title', 'Página Não Encontrada'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="error-content text-center">
                    <div class="error-icon mb-4">
                        <i class="bi bi-search" style="font-size: 5rem; color: #FF6B00;"></i>
                    </div>
                    
                    <h1 class="error-title mb-3">Página Não Encontrada</h1>
                    
                    <p class="error-message mb-4">
                        A página que você está procurando não foi encontrada. Ela pode ter sido movida, removida ou você digitou o endereço incorretamente.
                    </p>
                    
                    <div class="error-actions">
                        <a href="<?php echo e(url('/')); ?>" class="btn btn-primary btn-lg me-3">
                            <i class="bi bi-house-fill me-2"></i>
                            Voltar ao Início
                        </a>
                        
                        <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>
                            Página Anterior
                        </button>
                    </div>
                    
                    <div class="error-suggestions mt-5">
                        <h5 class="mb-3">Páginas Populares:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><a href="<?php echo e(url('/')); ?>" class="text-primary"><i class="bi bi-chevron-right me-1"></i> Página Inicial</a></li>
                                    <li><a href="<?php echo e(url('/sobre')); ?>" class="text-primary"><i class="bi bi-chevron-right me-1"></i> Sobre Nós</a></li>
                                    <li><a href="<?php echo e(url('/servicos')); ?>" class="text-primary"><i class="bi bi-chevron-right me-1"></i> Nossos Serviços</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><a href="<?php echo e(url('/galeria')); ?>" class="text-primary"><i class="bi bi-chevron-right me-1"></i> Galeria</a></li>
                                    <li><a href="<?php echo e(url('/blog')); ?>" class="text-primary"><i class="bi bi-chevron-right me-1"></i> Blog</a></li>
                                    <li><a href="<?php echo e(url('/contato')); ?>" class="text-primary"><i class="bi bi-chevron-right me-1"></i> Contato</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.error-content {
    background: white;
    padding: 3rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    animation: fadeInUp 0.6s ease-out;
}

.error-title {
    font-family: 'Rajdhani', sans-serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: #0D0D0D;
}

.error-message {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.6;
}

.btn-primary {
    background: #FF6B00;
    border-color: #FF6B00;
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #E55A00;
    border-color: #E55A00;
    transform: translateY(-2px);
}

.btn-outline-secondary {
    border-color: #2C2C2C;
    color: #2C2C2C;
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #2C2C2C;
    border-color: #2C2C2C;
    transform: translateY(-2px);
}

.error-suggestions h5 {
    color: #0D0D0D;
    font-weight: 600;
}

.error-suggestions a {
    text-decoration: none;
    padding: 5px 0;
    display: block;
    transition: all 0.3s ease;
}

.error-suggestions a:hover {
    color: #FF6B00 !important;
    transform: translateX(5px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .error-content {
        padding: 2rem;
        margin: 1rem;
    }
    
    .error-title {
        font-size: 2rem;
    }
    
    .btn-lg {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .me-3 {
        margin-right: 0 !important;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\errors\404.blade.php ENDPATH**/ ?>