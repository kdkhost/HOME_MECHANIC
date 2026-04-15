@extends('layouts.frontend')

@section('title', 'Erro Interno do Servidor')

@section('content')
<div class="error-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="error-content text-center">
                    <div class="error-icon mb-4">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 5rem; color: #FF6B00;"></i>
                    </div>
                    
                    <h1 class="error-title mb-3">Erro Interno</h1>
                    
                    <p class="error-message mb-4">
                        Ocorreu um erro interno no servidor. Nossa equipe foi notificada e está trabalhando para resolver o problema.
                    </p>
                    
                    <div class="error-actions">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg me-3">
                            <i class="bi bi-house-fill me-2"></i>
                            Voltar ao Início
                        </a>
                        
                        <button onclick="window.location.reload()" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Tentar Novamente
                        </button>
                    </div>
                    
                    <div class="error-help mt-5">
                        <p class="text-muted">
                            Se o problema persistir, entre em contato conosco através do 
                            <a href="{{ url('/contato') }}" class="text-primary">formulário de contato</a>.
                        </p>
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
@endsection