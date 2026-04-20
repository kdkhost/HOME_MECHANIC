

<?php $__env->startSection('title', 'Muitas Tentativas'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="error-content text-center">
                    <div class="error-icon mb-4">
                        <i class="bi bi-stopwatch" style="font-size: 5rem; color: #dc3545;"></i>
                    </div>
                    
                    <h1 class="error-title mb-3">Muitas Tentativas</h1>
                    
                    <p class="error-message mb-4">
                        Você fez muitas tentativas em pouco tempo. Por favor, aguarde alguns minutos antes de tentar novamente.
                    </p>
                    
                    <div class="countdown-container mb-4">
                        <div class="countdown-timer">
                            <span id="countdown">--:--</span>
                        </div>
                        <p class="text-muted">Tempo restante para nova tentativa</p>
                    </div>
                    
                    <div class="error-actions">
                        <button onclick="window.location.reload()" class="btn btn-primary btn-lg me-3">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Verificar Novamente
                        </button>
                        
                        <a href="<?php echo e(url('/')); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-house-fill me-2"></i>
                            Voltar ao Início
                        </a>
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

.countdown-container {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

.countdown-timer {
    font-size: 3rem;
    font-weight: 700;
    color: #dc3545;
    font-family: 'Courier New', monospace;
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
    
    .countdown-timer {
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simular countdown de 10 minutos (600 segundos)
    let timeLeft = 600;
    const countdownElement = document.getElementById('countdown');
    
    function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        countdownElement.textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft > 0) {
            timeLeft--;
        } else {
            countdownElement.textContent = '00:00';
            countdownElement.style.color = '#28a745';
        }
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\errors\429.blade.php ENDPATH**/ ?>