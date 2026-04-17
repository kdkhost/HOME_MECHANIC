<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HomeMechanic — Tuning & Performance de Luxo')</title>
    <meta name="description" content="@yield('description', 'Especialistas em tuning, performance e manutenção de carros de luxo. Lamborghini, Ferrari, Porsche, McLaren e muito mais.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <!-- AOS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <style>
    /* ── Variables ─────────────────────────────────────────── */
    :root {
        --orange:       #FF6B00;
        --orange-light: #FF8C3A;
        --orange-dark:  #E55A00;
        --black:      #0A0A0A;
        --dark:       #111111;
        --dark2:      #1A1A1A;
        --dark3:      #222222;
        --gray:       #888888;
        --light:      #F5F5F5;
        --white:      #FFFFFF;
        --font-head:  'Rajdhani', sans-serif;
        --font-body:  'Inter', sans-serif;
        --transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ── Reset ─────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
        font-family: var(--font-body);
        background: var(--black);
        color: var(--white);
        overflow-x: hidden;
    }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; }

    /* ── Preloader ─────────────────────────────────────────── */
    #preloader {
        position: fixed; inset: 0;
        background: var(--black);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        z-index: 9999;
        transition: opacity 0.6s ease;
    }
    #preloader.out { opacity: 0; pointer-events: none; }
    .pre-logo {
        font-family: var(--font-head);
        font-size: 2.5rem; font-weight: 700;
        color: var(--orange); letter-spacing: 4px;
        margin-bottom: 2rem;
    }
    .pre-bar {
        width: 200px; height: 2px;
        background: var(--dark3); border-radius: 2px; overflow: hidden;
    }
    .pre-bar span {
        display: block; height: 100%;
        background: linear-gradient(90deg, var(--orange-dark), var(--orange-light));
        animation: preload 1.8s ease-in-out infinite;
    }
    @keyframes preload {
        0%   { width: 0%; margin-left: 0; }
        50%  { width: 70%; margin-left: 15%; }
        100% { width: 0%; margin-left: 100%; }
    }

    /* ── Scrollbar ─────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: var(--dark); }
    ::-webkit-scrollbar-thumb { background: var(--orange-dark); border-radius: 3px; }

    /* ── Navbar ────────────────────────────────────────────── */
    #navbar {
        position: fixed; top: 0; left: 0; right: 0;
        z-index: 1000;
        padding: 1.25rem 0;
        transition: var(--transition);
    }
    #navbar.scrolled {
        background: rgba(10,10,10,0.97);
        backdrop-filter: blur(12px);
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255,107,0,0.15);
    }
    .nav-brand {
        font-family: var(--font-head);
        font-size: 1.6rem; font-weight: 700;
        color: var(--white) !important;
        letter-spacing: 2px;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .nav-brand span { color: var(--orange); }
    .nav-brand .brand-icon {
        width: 36px; height: 36px;
        background: var(--orange);
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        color: var(--black); font-size: 1rem;
    }
    .navbar-nav .nav-link {
        color: rgba(255,255,255,0.75) !important;
        font-family: var(--font-body);
        font-size: 0.85rem; font-weight: 500;
        letter-spacing: 1px; text-transform: uppercase;
        padding: 0.5rem 1rem !important;
        transition: var(--transition);
        position: relative;
    }
    .navbar-nav .nav-link::after {
        content: '';
        position: absolute; bottom: 0; left: 1rem; right: 1rem;
        height: 1px; background: var(--orange);
        transform: scaleX(0); transition: var(--transition);
    }
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: var(--orange) !important;
    }
    .navbar-nav .nav-link:hover::after,
    .navbar-nav .nav-link.active::after { transform: scaleX(1); }
    .nav-cta {
        background: var(--orange) !important;
        color: var(--black) !important;
        border-radius: 4px !important;
        font-weight: 600 !important;
        padding: 0.5rem 1.25rem !important;
    }
    .nav-cta::after { display: none !important; }
    .nav-cta:hover { background: var(--orange-light) !important; }
    .navbar-toggler { border-color: rgba(255,107,0,0.4); }
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,107,0,0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* ── Buttons ───────────────────────────────────────────── */
    .btn-orange {
        background: linear-gradient(135deg, var(--orange-dark), var(--orange));
        color: var(--black);
        font-family: var(--font-body);
        font-weight: 600; font-size: 0.85rem;
        letter-spacing: 1.5px; text-transform: uppercase;
        padding: 0.85rem 2rem;
        border: none; border-radius: 4px;
        transition: var(--transition);
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-orange:hover {
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        color: var(--black);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(255,107,0,0.35);
    }
    .btn-outline-orange {
        background: transparent;
        color: var(--orange);
        border: 1px solid var(--orange);
        font-family: var(--font-body);
        font-weight: 600; font-size: 0.85rem;
        letter-spacing: 1.5px; text-transform: uppercase;
        padding: 0.85rem 2rem;
        border-radius: 4px;
        transition: var(--transition);
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-outline-orange:hover {
        background: var(--orange);
        color: var(--black);
        transform: translateY(-2px);
    }

    /* ── Section titles ────────────────────────────────────── */
    .section-label {
        font-family: var(--font-body);
        font-size: 0.72rem; font-weight: 600;
        letter-spacing: 4px; text-transform: uppercase;
        color: var(--orange);
        display: flex; align-items: center; gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .section-label::before {
        content: '';
        width: 30px; height: 1px;
        background: var(--orange);
    }
    .section-title {
        font-family: var(--font-head);
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 700; line-height: 1.1;
        color: var(--white);
    }
    .section-title span { color: var(--orange); }
    .section-sub {
        color: var(--gray);
        font-size: 1rem; line-height: 1.7;
        max-width: 560px;
    }
    .divider-orange {
        width: 60px; height: 2px;
        background: linear-gradient(90deg, var(--orange), transparent);
        margin: 1.5rem 0;
    }

    /* ── Footer ────────────────────────────────────────────── */
    footer {
        background: var(--dark);
        border-top: 1px solid rgba(255,107,0,0.12);
    }
    .footer-brand {
        font-family: var(--font-head);
        font-size: 1.8rem; font-weight: 700;
        color: var(--orange); letter-spacing: 2px;
    }
    .footer-text { color: var(--gray); font-size: 0.9rem; line-height: 1.7; }
    .footer-title {
        font-family: var(--font-body);
        font-size: 0.72rem; font-weight: 600;
        letter-spacing: 3px; text-transform: uppercase;
        color: var(--orange); margin-bottom: 1.25rem;
    }
    .footer-links { list-style: none; padding: 0; margin: 0; }
    .footer-links li { margin-bottom: 0.6rem; }
    .footer-links a {
        color: var(--gray); font-size: 0.88rem;
        transition: var(--transition);
    }
    .footer-links a:hover { color: var(--orange); padding-left: 4px; }
    .footer-contact li {
        display: flex; align-items: flex-start; gap: 0.75rem;
        color: var(--gray); font-size: 0.88rem;
        margin-bottom: 0.75rem;
    }
    .footer-contact li i { color: var(--orange); margin-top: 2px; flex-shrink: 0; }
    .social-links { display: flex; gap: 0.75rem; }
    .social-links a {
        width: 38px; height: 38px;
        border: 1px solid rgba(255,107,0,0.3);
        border-radius: 4px;
        display: flex; align-items: center; justify-content: center;
        color: var(--gray); font-size: 1rem;
        transition: var(--transition);
    }
    .social-links a:hover {
        background: var(--orange); color: var(--black);
        border-color: var(--orange);
    }
    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.06);
        padding: 1.25rem 0;
        color: rgba(255,255,255,0.3);
        font-size: 0.8rem;
    }

    /* ── Utilities ─────────────────────────────────────────── */
    .text-orange { color: var(--orange) !important; }
    .bg-dark2  { background: var(--dark2) !important; }
    .bg-dark3  { background: var(--dark3) !important; }
    </style>

    @yield('styles')
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="pre-logo">HOME<span style="color:#fff">MECHANIC</span></div>
    <div class="pre-bar"><span></span></div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg" id="navbar">
    <div class="container">
        <a class="navbar-brand nav-brand" href="{{ route('home') }}">
            <div class="brand-icon"><i class="bi bi-tools"></i></div>
            HOME<span>MECHANIC</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">Serviços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Galeria</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('blog') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="nav-link nav-cta" href="{{ route('contact') }}">Orçamento</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
@yield('content')

<!-- Footer -->
<footer class="pt-5 pb-0">
    <div class="container">
        <div class="row g-5 pb-5">
            <div class="col-lg-4">
                <div class="footer-brand mb-3">HOMEMECHANIC</div>
                <p class="footer-text mb-4">
                    Especialistas em tuning, performance e manutenção de carros de luxo. 
                    Mais de 15 anos transformando supercars em obras de arte mecânica.
                </p>
                <div class="social-links">
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                    <a href="#"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-title">Navegação</div>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Início</a></li>
                    <li><a href="{{ route('services') }}">Serviços</a></li>
                    <li><a href="{{ route('gallery') }}">Galeria</a></li>
                    <li><a href="{{ route('blog') }}">Blog</a></li>
                    <li><a href="{{ route('contact') }}">Contato</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-title">Serviços</div>
                <ul class="footer-links">
                    <li><a href="#">Tuning Motor</a></li>
                    <li><a href="#">Suspensão Sport</a></li>
                    <li><a href="#">Freios Performance</a></li>
                    <li><a href="#">Estética Premium</a></li>
                    <li><a href="#">Diagnóstico</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <div class="footer-title">Contato</div>
                <ul class="footer-contact list-unstyled">
                    <li><i class="bi bi-geo-alt-fill"></i> Av. das Supercars, 1500 — São Paulo, SP</li>
                    <li><i class="bi bi-telephone-fill"></i> (11) 99999-9999</li>
                    <li><i class="bi bi-envelope-fill"></i> contato@homemechanic.com.br</li>
                    <li><i class="bi bi-clock-fill"></i> Seg–Sex: 8h–18h &nbsp;|&nbsp; Sáb: 8h–13h</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} HomeMechanic. Todos os direitos reservados.</span>
            <span>Desenvolvido com <i class="bi bi-heart-fill text-gold"></i> para amantes de carros</span>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
    // Preloader
    window.addEventListener('load', () => {
        const pre = document.getElementById('preloader');
        pre.classList.add('out');
        setTimeout(() => pre.style.display = 'none', 700);
    });

    // Navbar scroll
    window.addEventListener('scroll', () => {
        document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 60);
    });

    // AOS
    AOS.init({ duration: 700, once: true, offset: 60 });
</script>

@yield('scripts')
</body>
</html>
