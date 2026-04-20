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
    html, body {
        overflow-x: hidden;
        max-width: 100%;
    }
    body {
        font-family: var(--font-body);
        background: var(--black);
        color: var(--white);
    }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; height: auto; }
    /* Previne qualquer elemento de causar scroll horizontal */
    section, .container, .container-fluid, footer, nav { max-width: 100%; }

    /* ── Floating Buttons ──────────────────────────────────── */
    .floating-buttons {
        position: fixed;
        bottom: 90px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    @media (min-width: 768px) {
        .floating-buttons {
            bottom: 30px;
            right: 30px;
        }
    }

    /* Botão Voltar ao Topo */
    .btn-float-top {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--orange);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        box-shadow: 0 4px 15px rgba(255, 107, 0, 0.4);
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
    }
    .btn-float-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .btn-float-top:hover {
        background: var(--orange-light);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(255, 107, 0, 0.5);
    }

    /* Botão de Contato Principal */
    .btn-float-contact {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
        transition: all 0.3s ease;
        position: relative;
    }
    .btn-float-contact:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(37, 211, 102, 0.5);
    }
    .btn-float-contact.pulse {
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0%, 100% { box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4); }
        50% { box-shadow: 0 4px 30px rgba(37, 211, 102, 0.7); }
    }

    /* Menu de Opções de Contato */
    .contact-menu {
        position: absolute;
        bottom: 70px;
        right: 0;
        background: white;
        border-radius: 16px;
        padding: 12px;
        min-width: 220px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px) scale(0.9);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .contact-menu.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }
    .contact-menu::after {
        content: '';
        position: absolute;
        bottom: -8px;
        right: 20px;
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid white;
    }
    .contact-menu-header {
        font-size: 0.75rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 8px 8px;
        border-bottom: 1px solid #eee;
        margin-bottom: 8px;
    }
    .contact-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .contact-option:hover {
        background: #f5f5f5;
        color: var(--orange);
    }
    .contact-option i {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .contact-option.whatsapp i {
        background: #25D366;
        color: white;
    }
    .contact-option.call i {
        background: var(--orange);
        color: white;
    }
    .contact-option.sms i {
        background: #17a2b8;
        color: white;
    }
    .contact-option span {
        font-size: 0.9rem;
        font-weight: 500;
    }
    .contact-option small {
        display: block;
        font-size: 0.75rem;
        color: #888;
        margin-top: 2px;
    }

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
        background: linear-gradient(135deg, var(--orange-dark), var(--orange)) !important;
        color: var(--black) !important;
        border-radius: 6px !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        letter-spacing: 1px !important;
        padding: 0.55rem 1.4rem !important;
        box-shadow: 0 4px 16px rgba(255,107,0,0.3) !important;
        transition: var(--transition) !important;
        outline: none !important;
        -webkit-tap-highlight-color: transparent;
    }
    .nav-cta::after { display: none !important; }
    .nav-cta:hover,
    .nav-cta:focus,
    .nav-cta:active,
    .nav-cta:focus-visible,
    .nav-cta:visited {
        background: linear-gradient(135deg, var(--orange), var(--orange-light)) !important;
        color: var(--black) !important;
        box-shadow: 0 6px 24px rgba(255,107,0,0.5) !important;
        outline: none !important;
        text-decoration: none !important;
    }
    .nav-cta:hover { transform: translateY(-1px); }
    .nav-cta:active { transform: translateY(0) scale(0.97); }
    .navbar-toggler { border-color: rgba(255,107,0,0.4); }
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,107,0,0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* ── Buttons — sem conflito Bootstrap ─────────────────── */
    /* Seletor de alta especificidade para vencer o Bootstrap */
    a.btn-orange, button.btn-orange, .btn-orange {
        background: linear-gradient(135deg, #E55A00, #FF6B00) !important;
        color: #0A0A0A !important;
        font-family: var(--font-body);
        font-weight: 700 !important;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 0.9rem 2.2rem;
        border: 2px solid transparent !important;
        border-radius: 6px;
        transition: all 0.3s ease;
        display: inline-flex !important;
        align-items: center;
        gap: 0.6rem;
        box-shadow: 0 4px 20px rgba(255,107,0,0.35) !important;
        text-decoration: none !important;
        cursor: pointer;
        outline: none !important;
        -webkit-tap-highlight-color: transparent;
        will-change: transform, box-shadow;
    }
    a.btn-orange:hover, button.btn-orange:hover, .btn-orange:hover {
        background: linear-gradient(135deg, #FF6B00, #FF8C3A) !important;
        color: #0A0A0A !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 28px rgba(255,107,0,0.55) !important;
        text-decoration: none !important;
        border-color: transparent !important;
    }
    a.btn-orange:active, button.btn-orange:active, .btn-orange:active,
    a.btn-orange:focus, button.btn-orange:focus, .btn-orange:focus,
    a.btn-orange:focus-visible, button.btn-orange:focus-visible {
        background: linear-gradient(135deg, #E55A00, #FF6B00) !important;
        color: #0A0A0A !important;
        transform: translateY(0) scale(0.98) !important;
        box-shadow: 0 4px 16px rgba(255,107,0,0.4) !important;
        text-decoration: none !important;
        outline: none !important;
        border-color: transparent !important;
    }

    a.btn-outline-orange, button.btn-outline-orange, .btn-outline-orange {
        background: transparent !important;
        color: #FF6B00 !important;
        border: 2px solid #FF6B00 !important;
        font-family: var(--font-body);
        font-weight: 700 !important;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: calc(0.9rem - 1px) 2.2rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        display: inline-flex !important;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none !important;
        cursor: pointer;
        outline: none !important;
        -webkit-tap-highlight-color: transparent;
        will-change: transform, background, color;
    }
    a.btn-outline-orange:hover, button.btn-outline-orange:hover, .btn-outline-orange:hover {
        background: #FF6B00 !important;
        color: #0A0A0A !important;
        border-color: #FF6B00 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(255,107,0,0.4) !important;
        text-decoration: none !important;
    }
    a.btn-outline-orange:active, button.btn-outline-orange:active, .btn-outline-orange:active,
    a.btn-outline-orange:focus, button.btn-outline-orange:focus, .btn-outline-orange:focus,
    a.btn-outline-orange:focus-visible, button.btn-outline-orange:focus-visible {
        background: #E55A00 !important;
        color: #0A0A0A !important;
        border-color: #E55A00 !important;
        transform: translateY(0) scale(0.98) !important;
        box-shadow: 0 4px 14px rgba(255,107,0,0.35) !important;
        text-decoration: none !important;
        outline: none !important;
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

    /* ── Mobile fixes — sem scroll horizontal ──────────────── */
    @media (max-width: 991px) {
        /* Containers não vazam */
        .container { padding-left: 1rem; padding-right: 1rem; }

        /* Hero */
        .hero { min-height: 100svh; }
        .hero-title { font-size: clamp(2.2rem, 8vw, 4rem); }
        .hero-stats { gap: 1.5rem; flex-wrap: wrap; }
        .hero-stat-num { font-size: 1.8rem; }

        /* Botões — não transbordam */
        .btn-orange, .btn-outline-orange {
            padding: 0.8rem 1.4rem;
            font-size: 0.8rem;
            letter-spacing: 1px;
            width: 100%;
            justify-content: center;
        }
        .d-flex.gap-3 { flex-direction: column; }

        /* About badge */
        .about-badge { right: 0; bottom: -1rem; }
        .about-img { height: 320px; }

        /* Section titles */
        .section-title { font-size: clamp(1.8rem, 6vw, 2.5rem); }

        /* Testimonials */
        .testimonial-card { padding: 1.5rem; }

        /* CTA section */
        .cta-section { padding: 4rem 0; }
        .cta-title { font-size: clamp(2rem, 7vw, 3rem); }
    }

    @media (max-width: 575px) {
        /* Botões lado a lado em telas muito pequenas */
        .d-flex.gap-3 { gap: 0.75rem !important; }
        .btn-orange, .btn-outline-orange { padding: 0.75rem 1.2rem; font-size: 0.78rem; }

        /* Hero stats em linha */
        .hero-stats { gap: 1rem; }
        .hero-stat-num { font-size: 1.5rem; }

        /* About */
        .about-img { height: 260px; }
        .about-badge { position: static; margin-top: 1rem; display: inline-block; }

        /* Footer */
        .footer-brand { font-size: 1.4rem; }
    }

    /* ── Mobile Drawer ─────────────────────────────────────── */
    /* Overlay */
    .drawer-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.75);
        backdrop-filter: blur(4px);
        z-index: 1998;
        opacity: 0; pointer-events: none;
        transition: opacity 0.35s ease;
    }
    .drawer-overlay.open { opacity: 1; pointer-events: all; }

    /* Drawer panel */
    .mobile-drawer {
        position: fixed; top: 0; left: 0; bottom: 0;
        width: 300px; max-width: 85vw;
        background: var(--dark);
        border-right: 1px solid rgba(255,107,0,0.15);
        z-index: 1999;
        transform: translateX(-100%);
        transition: transform 0.38s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column;
        overflow-y: auto;
    }
    .mobile-drawer.open { transform: translateX(0); }

    /* Drawer header */
    .drawer-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        background: var(--dark2);
        flex-shrink: 0;
    }
    .drawer-brand {
        font-family: var(--font-head);
        font-size: 1.4rem; font-weight: 700;
        letter-spacing: 2px; color: var(--white);
    }
    .drawer-brand span { color: var(--orange); }
    .drawer-close {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.06);
        border: none; border-radius: 6px;
        color: var(--white); font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: var(--transition);
    }
    .drawer-close:hover { background: rgba(255,107,0,0.2); color: var(--orange); }

    /* Drawer nav links */
    .drawer-nav { padding: 1rem 0; flex: 1; }
    .drawer-nav a {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.9rem 1.5rem;
        color: rgba(255,255,255,0.7);
        font-size: 0.95rem; font-weight: 500;
        letter-spacing: 0.5px;
        transition: var(--transition);
        border-left: 3px solid transparent;
        text-decoration: none;
    }
    .drawer-nav a i {
        width: 20px; text-align: center;
        color: var(--gray); font-size: 1rem;
        transition: var(--transition);
    }
    .drawer-nav a:hover,
    .drawer-nav a.active {
        color: var(--white);
        background: rgba(255,107,0,0.08);
        border-left-color: var(--orange);
    }
    .drawer-nav a:hover i,
    .drawer-nav a.active i { color: var(--orange); }
    .drawer-nav a.active { font-weight: 600; }

    /* Drawer CTA */
    .drawer-cta {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.06);
        flex-shrink: 0;
    }
    .drawer-cta a {
        display: flex; align-items: center; justify-content: center; gap: 0.6rem;
        background: linear-gradient(135deg, var(--orange-dark), var(--orange)) !important;
        color: var(--black) !important;
        font-weight: 700; font-size: 0.88rem;
        letter-spacing: 1.5px; text-transform: uppercase;
        padding: 0.9rem; border-radius: 6px;
        text-decoration: none !important;
        transition: var(--transition);
        outline: none !important;
        -webkit-tap-highlight-color: transparent;
    }
    .drawer-cta a:hover,
    .drawer-cta a:focus,
    .drawer-cta a:active,
    .drawer-cta a:visited {
        color: var(--black) !important;
        text-decoration: none !important;
        outline: none !important;
    }
    .drawer-cta a:hover { box-shadow: 0 6px 20px rgba(255,107,0,0.4); transform: translateY(-1px); }
    .drawer-cta a:active { transform: scale(0.98); }

    /* Drawer social */
    .drawer-social {
        padding: 1rem 1.5rem 1.5rem;
        display: flex; gap: 0.6rem;
        flex-shrink: 0;
    }
    .drawer-social a {
        width: 36px; height: 36px;
        border: 1px solid rgba(255,107,0,0.25);
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        color: var(--gray); font-size: 0.95rem;
        transition: var(--transition);
        text-decoration: none;
    }
    .drawer-social a:hover { background: var(--orange); color: var(--black); border-color: var(--orange); }

    /* Hamburguer button (mobile only) */
    .btn-drawer {
        display: none;
        background: none; border: none;
        color: var(--white); font-size: 1.5rem;
        padding: 0.25rem 0.5rem;
        cursor: pointer; transition: var(--transition);
    }
    .btn-drawer:hover { color: var(--orange); }
    @media (max-width: 991px) {
        .btn-drawer { display: flex; align-items: center; }
        .navbar-collapse { display: none !important; } /* esconde o collapse padrão */
    }

    /* ── Bottom Nav (mobile footer) ────────────────────────── */
    .bottom-nav {
        display: none;
        position: fixed; bottom: 0; left: 0; right: 0;
        background: rgba(17,17,17,0.97);
        backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255,107,0,0.15);
        z-index: 990;
        padding: 0.5rem 0 calc(0.5rem + env(safe-area-inset-bottom));
    }
    @media (max-width: 991px) { .bottom-nav { display: flex; } }

    .bottom-nav-inner {
        display: flex; width: 100%;
        align-items: stretch;
    }
    .bottom-nav-item {
        flex: 1;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 0.2rem;
        padding: 0.4rem 0.25rem;
        color: rgba(255,255,255,0.45);
        font-size: 0.62rem; font-weight: 600;
        letter-spacing: 0.5px; text-transform: uppercase;
        text-decoration: none;
        transition: var(--transition);
        position: relative;
    }
    .bottom-nav-item i { font-size: 1.25rem; line-height: 1; }
    .bottom-nav-item:hover,
    .bottom-nav-item.active { color: var(--orange); }
    .bottom-nav-item.active::before {
        content: '';
        position: absolute; top: 0; left: 25%; right: 25%;
        height: 2px; background: var(--orange);
        border-radius: 0 0 3px 3px;
    }
    /* CTA item especial */
    .bottom-nav-item.cta {
        color: var(--orange);
    }
    .bottom-nav-item.cta i {
        background: var(--orange);
        color: var(--black);
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 4px 16px rgba(255,107,0,0.4);
        margin-bottom: 0.1rem;
    }

    /* Espaço para o bottom nav não cobrir conteúdo */
    @media (max-width: 991px) {
        body { padding-bottom: 70px; }
    }
    </style>

    @yield('styles')
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="pre-logo">HOME<span style="color:#fff">MECHANIC</span></div>
    <div class="pre-bar"><span></span></div>
</div>

<!-- ── Drawer Overlay ──────────────────────────────────── -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

<!-- ── Mobile Drawer ──────────────────────────────────── -->
<div class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-header">
        <div class="drawer-brand">HOME<span>MECHANIC</span></div>
        <button class="drawer-close" onclick="closeDrawer()" aria-label="Fechar menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="drawer-nav">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-fill"></i> Início
        </a>
        <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">
            <i class="bi bi-tools"></i> Serviços
        </a>
        <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'active' : '' }}">
            <i class="bi bi-images"></i> Galeria
        </a>
        <a href="{{ route('blog') }}" class="{{ request()->routeIs('blog') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i> Blog
        </a>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
            <i class="bi bi-envelope-fill"></i> Contato
        </a>
    </nav>

    <div class="drawer-cta">
        <a href="{{ route('contact') }}">
            <i class="bi bi-calendar-check-fill"></i> Solicitar Orçamento
        </a>
    </div>

    <div class="drawer-social">
        <a href="{{ $siteSettings['social_instagram'] ?? '#' }}" {{ !empty($siteSettings['social_instagram']) ? 'target="_blank"' : '' }} aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="{{ $siteSettings['social_facebook'] ?? '#' }}" {{ !empty($siteSettings['social_facebook']) ? 'target="_blank"' : '' }} aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="{{ $siteSettings['social_youtube'] ?? '#' }}" {{ !empty($siteSettings['social_youtube']) ? 'target="_blank"' : '' }} aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        <a href="https://wa.me/{{ preg_replace('/\D/','',$siteSettings['whatsapp'] ?? '5511999999999') }}" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
    </div>
</div>

<!-- ── Navbar ──────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg" id="navbar">
    <div class="container">
        <a class="navbar-brand nav-brand" href="{{ route('home') }}">
            <div class="brand-icon"><i class="bi bi-tools"></i></div>
            HOME<span>MECHANIC</span>
        </a>

        {{-- Hamburguer mobile --}}
        <button class="btn-drawer" id="btnDrawer" onclick="openDrawer()" aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>

        {{-- Menu desktop --}}
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

<!-- ── Bottom Nav (mobile) ─────────────────────────────── -->
<nav class="bottom-nav" id="bottomNav">
    <div class="bottom-nav-inner">
        <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-fill"></i>
            <span>Início</span>
        </a>
        <a href="{{ route('services') }}" class="bottom-nav-item {{ request()->routeIs('services') ? 'active' : '' }}">
            <i class="bi bi-tools"></i>
            <span>Serviços</span>
        </a>
        <a href="{{ route('contact') }}" class="bottom-nav-item cta">
            <i class="bi bi-calendar-check-fill"></i>
            <span>Orçamento</span>
        </a>
        <a href="{{ route('gallery') }}" class="bottom-nav-item {{ request()->routeIs('gallery') ? 'active' : '' }}">
            <i class="bi bi-images"></i>
            <span>Galeria</span>
        </a>
        <a href="{{ route('blog') }}" class="bottom-nav-item {{ request()->routeIs('blog') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i>
            <span>Blog</span>
        </a>
    </div>
</nav>

<!-- Content -->
@yield('content')

{{-- Floating Buttons --}}
@php
    $whatsappNum = preg_replace('/\D/', '', $siteSettings['whatsapp'] ?? '');
    $phoneNum = preg_replace('/\D/', '', $siteSettings['contact_phone'] ?? $whatsappNum);
    $isMobile = preg_match('/Android|iPhone|iPad|iPod|Mobile/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
@endphp

<div class="floating-buttons">
    {{-- Botão Voltar ao Topo --}}
    <button class="btn-float-top" id="btnBackToTop" onclick="scrollToTop()" title="Voltar ao topo">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- Botão de Contato com Menu --}}
    <div style="position: relative;">
        <button class="btn-float-contact pulse" id="btnContact" onclick="toggleContactMenu()" title="Falar conosco">
            <i class="bi bi-chat-dots-fill"></i>
        </button>

        <div class="contact-menu" id="contactMenu">
            <div class="contact-menu-header">Falar com a HomeMechanic</div>

            @if(!empty($whatsappNum))
            <a href="https://wa.me/{{ $whatsappNum }}" class="contact-option whatsapp" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp"></i>
                <div>
                    <span>WhatsApp</span>
                    <small>Envie uma mensagem</small>
                </div>
            </a>
            @endif

            @if(!empty($phoneNum))
            <a href="tel:{{ $phoneNum }}" class="contact-option call">
                <i class="bi bi-telephone-fill"></i>
                <div>
                    <span>Ligar Agora</span>
                    <small>{{ $siteSettings['contact_phone'] ?? $siteSettings['whatsapp'] }}</small>
                </div>
            </a>

            @if($isMobile)
            <a href="sms:{{ $phoneNum }}" class="contact-option sms">
                <i class="bi bi-chat-text-fill"></i>
                <div>
                    <span>Enviar SMS</span>
                    <small>Mensagem de texto</small>
                </div>
            </a>
            @endif
            @endif

            <a href="{{ route('contact') }}" class="contact-option" style="border-top:1px solid #eee;margin-top:8px;padding-top:12px;">
                <i class="bi bi-envelope-fill" style="background:#FF6B00;color:white;"></i>
                <div>
                    <span>Formulário</span>
                    <small>Envie sua mensagem</small>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="pt-5 pb-0">
    <div class="container">
        <div class="row g-5 pb-5">
            <div class="col-lg-4">
                <div class="footer-brand mb-3">{{ strtoupper($siteSettings['site_name'] ?? 'HOMEMECHANIC') }}</div>
                <p class="footer-text mb-4">{{ $siteSettings['site_desc'] ?? 'Especialistas em tuning, performance e manutenção de carros de luxo.' }}</p>
                <div class="social-links">
                    @if(!empty($siteSettings['social_instagram']))
                        <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if(!empty($siteSettings['social_facebook']))
                        <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if(!empty($siteSettings['social_youtube']))
                        <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    @endif
                    @if(!empty($siteSettings['social_twitter']))
                        <a href="{{ $siteSettings['social_twitter'] }}" target="_blank" aria-label="Twitter/X"><i class="bi bi-twitter-x"></i></a>
                    @endif
                    @if(!empty($siteSettings['social_tiktok']))
                        <a href="{{ $siteSettings['social_tiktok'] }}" target="_blank" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                    @endif
                    @if(!empty($siteSettings['whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/\D/','',$siteSettings['whatsapp']) }}" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    @endif
                    {{-- Fallback se nenhuma rede configurada --}}
                    @if(empty($siteSettings['social_instagram']) && empty($siteSettings['social_facebook']) && empty($siteSettings['social_youtube']) && empty($siteSettings['whatsapp']))
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    @endif
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
                    <li><a href="{{ route('services') }}">Tuning Motor</a></li>
                    <li><a href="{{ route('services') }}">Suspensão Sport</a></li>
                    <li><a href="{{ route('services') }}">Freios Performance</a></li>
                    <li><a href="{{ route('services') }}">Estética Premium</a></li>
                    <li><a href="{{ route('services') }}">Diagnóstico</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <div class="footer-title">Contato</div>
                <ul class="footer-contact list-unstyled">
                    @if(!empty($siteSettings['address']))
                    <li><i class="bi bi-geo-alt-fill"></i> {{ $siteSettings['address'] }}</li>
                    @endif
                    @if(!empty($siteSettings['phone']))
                    <li>
                        <i class="bi bi-telephone-fill"></i>
                        <a href="tel:{{ preg_replace('/\D/','',$siteSettings['phone']) }}" style="color:inherit;">{{ $siteSettings['phone'] }}</a>
                    </li>
                    @endif
                    @if(!empty($siteSettings['whatsapp']))
                    <li>
                        <i class="bi bi-whatsapp"></i>
                        <a href="https://wa.me/{{ preg_replace('/\D/','',$siteSettings['whatsapp']) }}" target="_blank" style="color:inherit;">{{ $siteSettings['whatsapp'] }}</a>
                    </li>
                    @endif
                    @if(!empty($siteSettings['email']))
                    <li>
                        <i class="bi bi-envelope-fill"></i>
                        <a href="mailto:{{ $siteSettings['email'] }}" style="color:inherit;">{{ $siteSettings['email'] }}</a>
                    </li>
                    @endif
                    <li><i class="bi bi-clock-fill"></i> Seg–Sex: 8h–18h &nbsp;|&nbsp; Sáb: 8h–13h</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'HomeMechanic' }}. Todos os direitos reservados.</span>
            <span>Desenvolvido com <i class="bi bi-heart-fill text-orange pulse"></i> por: Eth! Estratégias</span>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="{{ asset('js/hm-masks.js') }}"></script>

<script>
    // ── Preloader ─────────────────────────────────────────
    window.addEventListener('load', () => {
        const pre = document.getElementById('preloader');
        pre.classList.add('out');
        setTimeout(() => pre.style.display = 'none', 700);
    });

    // ── Navbar scroll ─────────────────────────────────────
    window.addEventListener('scroll', () => {
        document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 60);
    });

    // ── AOS ───────────────────────────────────────────────
    AOS.init({ duration: 700, once: true, offset: 60 });

    // ── Mobile Drawer ─────────────────────────────────────
    function openDrawer() {
        document.getElementById('mobileDrawer').classList.add('open');
        document.getElementById('drawerOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        document.getElementById('mobileDrawer').classList.remove('open');
        document.getElementById('drawerOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    // Fechar com ESC
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

    // Fechar drawer ao clicar em link interno
    document.querySelectorAll('.drawer-nav a, .drawer-cta a').forEach(a => {
        a.addEventListener('click', () => {
            if (a.getAttribute('href') && !a.getAttribute('href').startsWith('#')) {
                closeDrawer();
            }
        });
    });

    // ── Swipe para fechar drawer ──────────────────────────
    let touchStartX = 0;
    document.getElementById('mobileDrawer').addEventListener('touchstart', e => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    document.getElementById('mobileDrawer').addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (diff > 60) closeDrawer(); // swipe left → fechar
    }, { passive: true });

    // ── Botão Voltar ao Topo ───────────────────────────────
    const btnBackToTop = document.getElementById('btnBackToTop');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            btnBackToTop.classList.add('visible');
        } else {
            btnBackToTop.classList.remove('visible');
        }
    });
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ── Menu de Contato Flutuante ───────────────────────────
    const btnContact = document.getElementById('btnContact');
    const contactMenu = document.getElementById('contactMenu');
    let menuOpen = false;

    function toggleContactMenu() {
        menuOpen = !menuOpen;
        contactMenu.classList.toggle('open', menuOpen);
        btnContact.classList.toggle('pulse', !menuOpen);
    }

    // Fechar menu ao clicar fora
    document.addEventListener('click', (e) => {
        if (!btnContact.contains(e.target) && !contactMenu.contains(e.target)) {
            menuOpen = false;
            contactMenu.classList.remove('open');
            btnContact.classList.add('pulse');
        }
    });

    // Fechar menu com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menuOpen) {
            menuOpen = false;
            contactMenu.classList.remove('open');
            btnContact.classList.add('pulse');
        }
    });
</script>

@yield('scripts')
</body>
</html>
