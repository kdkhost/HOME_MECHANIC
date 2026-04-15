<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('meta_title', 'HomeMechanic - Especialistas em Carros de Luxo e Tuning')</title>
    <meta name="description" content="@yield('meta_description', 'HomeMechanic é especializada em manutenção e tuning de carros de luxo esportivos. Serviços de alta qualidade com tecnologia de ponta.')">
    <meta name="keywords" content="mecânica, carros de luxo, tuning, esportivos, manutenção automotiva">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('meta_title', 'HomeMechanic - Especialistas em Carros de Luxo e Tuning')">
    <meta property="og:description" content="@yield('meta_description', 'HomeMechanic é especializada em manutenção e tuning de carros de luxo esportivos.')">
    <meta property="og:image" content="@yield('og_image', asset('img/og-default.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])
    
    @yield('styles')
    
    <style>
        :root {
            --color-primary: #FF6B00;
            --color-dark: #0D0D0D;
            --color-graphite: #2C2C2C;
            --color-white: #FFFFFF;
            --color-primary-hover: #E55A00;
        }
        
        /* Preloader */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color-dark);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        #preloader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .preloader-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        .preloader-bar {
            width: 200px;
            height: 4px;
            background: var(--color-graphite);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .preloader-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--color-primary), var(--color-primary-hover));
            border-radius: 2px;
            animation: loading 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        @keyframes loading {
            0% { width: 0%; margin-left: 0%; }
            50% { width: 75%; margin-left: 12.5%; }
            100% { width: 0%; margin-left: 100%; }
        }
        
        /* Navbar Sticky */
        .navbar-sticky {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            background: rgba(13, 13, 13, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        /* Custom Colors */
        .bg-primary-custom {
            background-color: var(--color-primary) !important;
        }
        
        .text-primary-custom {
            color: var(--color-primary) !important;
        }
        
        .btn-primary-custom {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: var(--color-white);
        }
        
        .btn-primary-custom:hover {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
        }
        
        /* Scroll Animations */
        .scroll-animate {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .scroll-animate.animate {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader">
        <img src="{{ asset('img/logo.png') }}" alt="HomeMechanic" class="preloader-logo">
        <div class="preloader-bar">
            <span></span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/logo.png') }}" alt="HomeMechanic" height="40">
                <span class="ms-2 fw-bold">HomeMechanic</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sobre') ? 'active' : '' }}" href="{{ route('sobre') }}">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('servicos') ? 'active' : '' }}" href="{{ route('servicos') }}">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('galeria') ? 'active' : '' }}" href="{{ route('galeria') }}">Galeria</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contato') ? 'active' : '' }}" href="{{ route('contato') }}">Contato</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-primary-custom">HomeMechanic</h5>
                    <p>Especialistas em carros de luxo esportivos e tuning. Qualidade e excelência em cada serviço.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Links Rápidos</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white-50">Início</a></li>
                        <li><a href="{{ route('sobre') }}" class="text-white-50">Sobre</a></li>
                        <li><a href="{{ route('servicos') }}" class="text-white-50">Serviços</a></li>
                        <li><a href="{{ route('galeria') }}" class="text-white-50">Galeria</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Serviços</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Alinhamento</a></li>
                        <li><a href="#" class="text-white-50">Suspensão</a></li>
                        <li><a href="#" class="text-white-50">Tuning</a></li>
                        <li><a href="#" class="text-white-50">Diagnóstico</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Contato</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt me-2"></i>Rua das Oficinas, 123 - São Paulo, SP</li>
                        <li><i class="bi bi-telephone me-2"></i>(11) 99999-9999</li>
                        <li><i class="bi bi-envelope me-2"></i>contato@homemechanic.com.br</li>
                        <li><i class="bi bi-clock me-2"></i>Seg-Sex: 8h às 18h | Sáb: 8h às 12h</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} HomeMechanic. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('politica-privacidade') }}" class="text-white-50 me-3">Política de Privacidade</a>
                    <a href="#" class="text-white-50">Termos de Uso</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    @vite(['resources/js/app.js'])
    
    <script>
        // Ocultar preloader quando a página carregar
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.classList.add('hidden');
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }
        });

        // Navbar sticky effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-sticky');
            } else {
                navbar.classList.remove('navbar-sticky');
            }
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, observerOptions);

        // Observar elementos com classe scroll-animate
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.scroll-animate');
            animateElements.forEach(el => observer.observe(el));
        });

        // Configurar CSRF token para requisições AJAX
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios = window.axios || {};
            window.axios.defaults = window.axios.defaults || {};
            window.axios.defaults.headers = window.axios.defaults.headers || {};
            window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
    </script>
    
    @yield('scripts')
</body>
</html>