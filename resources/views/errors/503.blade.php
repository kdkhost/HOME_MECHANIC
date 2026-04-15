@extends('layouts.frontend')

@section('title', 'Sistema em Manutenção')

@section('content')
<div class="maintenance-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="maintenance-content text-center">
                    <div class="maintenance-icon mb-4">
                        <i class="bi bi-tools" style="font-size: 6rem; color: #FF6B00;"></i>
                    </div>
                    
                    <h1 class="maintenance-title mb-3">Sistema em Manutenção</h1>
                    
                    <p class="maintenance-message mb-4">
                        Estamos realizando melhorias no sistema para oferecer uma experiência ainda melhor. 
                        Voltaremos em breve com novidades!
                    </p>
                    
                    <div class="maintenance-info mb-5">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-card">
                                    <i class="bi bi-clock-fill mb-2"></i>
                                    <h5>Previsão de Retorno</h5>
                                    <p class="mb-0">Em algumas horas</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-card">
                                    <i class="bi bi-gear-fill mb-2"></i>
                                    <h5>Tipo de Manutenção</h5>
                                    <p class="mb-0">Melhorias do Sistema</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="maintenance-contact">
                        <h5 class="mb-3">Precisa de Atendimento Urgente?</h5>
                        <div class="contact-options">
                            <a href="tel:+5511999999999" class="btn btn-primary btn-lg me-3 mb-2">
                                <i class="bi bi-telephone-fill me-2"></i>
                                (11) 99999-9999
                            </a>
                            
                            <a href="mailto:contato@homemechanic.com.br" class="btn btn-outline-secondary btn-lg mb-2">
                                <i class="bi bi-envelope-fill me-2"></i>
                                E-mail
                            </a>
                        </div>
                    </div>
                    
                    <div class="maintenance-footer mt-5">
                        <p class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Seus dados estão seguros e serão preservados durante a manutenção.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Animated background -->
    <div class="maintenance-bg">
        <div class="gear gear-1">
            <i class="bi bi-gear-fill"></i>
        </div>
        <div class="gear gear-2">
            <i class="bi bi-gear-wide-connected"></i>
        </div>
        <div class="gear gear-3">
            <i class="bi bi-gear-fill"></i>
        </div>
    </div>
</div>

<style>
.maintenance-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #0D0D0D 0%, #2C2C2C 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.maintenance-content {
    background: rgba(255, 255, 255, 0.95);
    color: #0D0D0D;
    padding: 4rem;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    animation: fadeInUp 0.8s ease-out;
    position: relative;
    z-index: 10;
}

.maintenance-title {
    font-family: 'Rajdhani', sans-serif;
    font-size: 3rem;
    font-weight: 700;
    color: #0D0D0D;
    margin-bottom: 1rem;
}

.maintenance-message {
    font-size: 1.2rem;
    color: #6c757d;
    line-height: 1.6;
}

.info-card {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.info-card i {
    font-size: 2rem;
    color: #FF6B00;
}

.info-card h5 {
    color: #0D0D0D;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.btn-primary {
    background: #FF6B00;
    border-color: #FF6B00;
    padding: 15px 30px;
    font-weight: 600;
    border-radius: 10px;
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
    padding: 15px 30px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #2C2C2C;
    border-color: #2C2C2C;
    transform: translateY(-2px);
}

.maintenance-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.gear {
    position: absolute;
    color: rgba(255, 107, 0, 0.1);
    animation: rotate 20s linear infinite;
}

.gear-1 {
    top: 10%;
    left: 10%;
    font-size: 4rem;
    animation-duration: 15s;
}

.gear-2 {
    top: 20%;
    right: 15%;
    font-size: 6rem;
    animation-duration: 25s;
    animation-direction: reverse;
}

.gear-3 {
    bottom: 15%;
    left: 20%;
    font-size: 5rem;
    animation-duration: 18s;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .maintenance-content {
        padding: 2rem;
        margin: 1rem;
    }
    
    .maintenance-title {
        font-size: 2.5rem;
    }
    
    .btn-lg {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .me-3 {
        margin-right: 0 !important;
    }
    
    .gear {
        display: none;
    }
}
</style>
@endsection