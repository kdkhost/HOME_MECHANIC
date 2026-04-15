@extends('layouts.frontend')

@section('title', 'HomeMechanic - Sistema de Gestão para Oficinas Especializadas')

@section('meta_description', 'Sistema completo de gestão para oficinas mecânicas especializadas em carros esportivos de luxo e tuning. Gerencie serviços, galeria, blog e muito mais.')

@section('content')
<!-- Hero Section -->
<section id="hero" class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">
                        <span class="text-primary">HomeMechanic</span><br>
                        Sistema de Gestão Completo
                    </h1>
                    <p class="hero-subtitle">
                        Plataforma profissional para oficinas especializadas em carros esportivos de luxo e tuning. 
                        Gerencie seus serviços, galeria de trabalhos, blog e muito mais.
                    </p>
                    <div class="hero-buttons">
                        <a href="/admin/login" class="btn btn-primary btn-lg me-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Acessar Sistema
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-info-circle me-2"></i>
                            Saiba Mais
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <div class="hero-card">
                        <i class="bi bi-tools hero-icon"></i>
                        <h3>Sistema Instalado</h3>
                        <p>Seu sistema HomeMechanic está funcionando perfeitamente!</p>
                        <div class="status-indicator">
                            <span class="status-dot"></span>
                            <span>Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Funcionalidades do Sistema</h2>
                <p class="section-subtitle">Tudo que você precisa para gerenciar sua oficina</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <h4>Dashboard Completo</h4>
                    <p>Painel administrativo com métricas e relatórios em tempo real</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h4>Gestão de Serviços</h4>
                    <p>Cadastre e gerencie todos os serviços oferecidos pela oficina</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-images"></i>
                    </div>
                    <h4>Galeria de Trabalhos</h4>
                    <p>Showcase dos trabalhos realizados com sistema de categorias</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <h4>Blog Integrado</h4>
                    <p>Sistema de blog completo com SEO otimizado</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- System Info Section -->
<section class="system-info-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h3>Sistema HomeMechanic v1.0.0</h3>
                <p class="text-muted">
                    Desenvolvido com Laravel 13 e PHP 8.4<br>
                    <i class="bi bi-shield-check text-success me-1"></i>
                    Sistema seguro e confiável
                </p>
                <div class="mt-4">
                    <a href="/admin/login" class="btn btn-primary">
                        <i class="bi bi-gear me-2"></i>
                        Acessar Painel Administrativo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .hero-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero-icon {
        font-size: 4rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1rem;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        background: #28a745;
        border-radius: 50%;
        margin-right: 0.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .features-section {
        background: #f8f9fa;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 1rem;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
    }

    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .feature-icon i {
        font-size: 2rem;
        color: white;
    }

    .feature-card h4 {
        color: var(--dark-color);
        margin-bottom: 1rem;
    }

    .feature-card p {
        color: #6c757d;
        margin: 0;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .hero-card {
            padding: 2rem;
            margin-top: 3rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animação de entrada para os cards de features
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Aplicar animação aos cards
        document.querySelectorAll('.feature-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    });
</script>
@endsection