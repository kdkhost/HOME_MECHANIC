@extends('layouts.frontend')
@section('title', 'HomeMechanic — Tuning & Performance de Luxo')
@section('description', 'Especialistas em tuning, performance e manutenção de supercars. Lamborghini, Ferrari, Porsche, McLaren e muito mais.')

@section('styles')
<style>
/* ── Hero ──────────────────────────────────────────────────── */
.hero {
    min-height: 100vh;
    position: relative;
    display: flex; align-items: center;
    overflow: hidden;
    background: var(--black);
}
.hero-bg {
    position: absolute; inset: 0;
    background:
        linear-gradient(to right, rgba(10,10,10,0.97) 40%, rgba(10,10,10,0.5) 100%),
        url('https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1600&q=80') center/cover no-repeat;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(255,107,0,0.12);
    border: 1px solid rgba(255,107,0,0.3);
    color: var(--orange); font-size: 0.75rem;
    font-weight: 600; letter-spacing: 2px; text-transform: uppercase;
    padding: 0.4rem 1rem; border-radius: 2px;
    margin-bottom: 1.5rem;
}
.hero-title {
    font-family: var(--font-head);
    font-size: clamp(3rem, 7vw, 6rem);
    font-weight: 700; line-height: 1;
    color: var(--white); margin-bottom: 1.5rem;
}
.hero-title .line-orange { color: var(--orange); display: block; }
.hero-sub {
    color: rgba(255,255,255,0.6);
    font-size: 1.05rem; line-height: 1.8;
    max-width: 480px; margin-bottom: 2.5rem;
}
.hero-stats {
    display: flex; gap: 2.5rem;
    margin-top: 3rem; padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.08);
}
.hero-stat-num {
    font-family: var(--font-head);
    font-size: 2.2rem; font-weight: 700;
    color: var(--orange); line-height: 1;
}
.hero-stat-label {
    font-size: 0.75rem; color: var(--gray);
    letter-spacing: 1px; text-transform: uppercase;
    margin-top: 0.25rem;
}
.hero-scroll {
    position: absolute; bottom: 2.5rem; left: 50%;
    transform: translateX(-50%);
    display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
    color: var(--gray); font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase;
    animation: bounce 2s infinite;
}
.hero-scroll i { font-size: 1.2rem; color: var(--orange); }
@keyframes bounce {
    0%,100% { transform: translateX(-50%) translateY(0); }
    50%      { transform: translateX(-50%) translateY(8px); }
}

/* ── Brands ────────────────────────────────────────────────── */
.brands-bar {
    background: var(--dark2);
    border-top: 1px solid rgba(255,107,0,0.1);
    border-bottom: 1px solid rgba(255,107,0,0.1);
    padding: 1.5rem 0;
    overflow: hidden;
    width: 100%;
}
.brands-track {
    display: flex; gap: 3rem; align-items: center;
    animation: marquee 20s linear infinite;
    white-space: nowrap;
}
.brands-track span {
    font-family: var(--font-head);
    font-size: 1rem; font-weight: 600;
    letter-spacing: 3px; text-transform: uppercase;
    color: rgba(255,255,255,0.2);
    flex-shrink: 0;
    transition: var(--transition);
}
.brands-track span:hover { color: var(--orange); }
@keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* ── About ─────────────────────────────────────────────────── */
.about-img-wrap {
    position: relative;
}
.about-img {
    width: 100%; height: 520px;
    object-fit: cover; border-radius: 4px;
    filter: brightness(0.85);
}
.about-badge {
    position: absolute; bottom: -1.5rem; right: -1.5rem;
    background: var(--orange);
    color: var(--black);
    padding: 1.5rem 2rem;
    border-radius: 4px;
    text-align: center;
}
.about-badge-num {
    font-family: var(--font-head);
    font-size: 3rem; font-weight: 700; line-height: 1;
}
.about-badge-text { font-size: 0.75rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
.about-feature {
    display: flex; align-items: flex-start; gap: 1rem;
    padding: 1.25rem;
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 4px;
    transition: var(--transition);
}
.about-feature:hover { border-color: rgba(255,107,0,0.3); }
.about-feature-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    background: rgba(255,107,0,0.1);
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: var(--orange); font-size: 1.2rem;
}
.about-feature-title { font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem; }
.about-feature-text  { color: var(--gray); font-size: 0.82rem; margin: 0; }

/* ── Services ──────────────────────────────────────────────── */
.services-section { background: var(--dark2); }
.service-card {
    background: var(--dark3);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 4px;
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
}
.service-card:hover {
    border-color: rgba(255,107,0,0.4);
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}
.service-img {
    width: 100%; height: 220px;
    object-fit: cover;
    filter: brightness(0.7);
    transition: var(--transition);
}
.service-card:hover .service-img { filter: brightness(0.9); transform: scale(1.03); }
.service-img-wrap { overflow: hidden; }
.service-body { padding: 1.5rem; }
.service-icon {
    width: 48px; height: 48px;
    background: rgba(255,107,0,0.1);
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: var(--orange); font-size: 1.3rem;
    margin-bottom: 1rem;
}
.service-title {
    font-family: var(--font-head);
    font-size: 1.3rem; font-weight: 700;
    color: var(--white); margin-bottom: 0.5rem;
}
.service-text { color: var(--gray); font-size: 0.88rem; line-height: 1.7; margin: 0; }
.service-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--orange); font-size: 0.8rem; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase;
    margin-top: 1.25rem;
    transition: var(--transition);
}
.service-link:hover { gap: 0.75rem; color: var(--orange-light); }

/* ── Gallery preview ───────────────────────────────────────── */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(2, 260px);
    gap: 8px;
    width: 100%;
    overflow: hidden;
}
@media (max-width: 767px) {
    .gallery-grid {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto;
    }
    .gallery-grid .gallery-item:first-child {
        grid-row: span 1;
        grid-column: span 2;
        height: 200px;
    }
    .gallery-grid .gallery-item { height: 160px; }
}
.gallery-item {
    overflow: hidden; border-radius: 4px;
    position: relative; cursor: pointer;
}
.gallery-item:first-child {
    grid-row: span 2;
}
.gallery-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease, filter 0.5s ease;
    filter: brightness(0.75);
}
.gallery-item:hover img { transform: scale(1.08); filter: brightness(0.9); }
.gallery-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);
    opacity: 0; transition: var(--transition);
    display: flex; align-items: flex-end; padding: 1rem;
}
.gallery-item:hover .gallery-overlay { opacity: 1; }
.gallery-overlay span {
    color: var(--white); font-size: 0.8rem;
    font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
}

/* ── Testimonials ──────────────────────────────────────────── */
.testimonials-section { background: var(--black); }
.testimonial-card {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px;
    padding: 2rem;
    height: 100%;
    transition: var(--transition);
}
.testimonial-card:hover { border-color: rgba(255,107,0,0.3); }
.testimonial-stars { color: var(--orange); font-size: 0.9rem; margin-bottom: 1rem; }
.testimonial-text {
    color: rgba(255,255,255,0.75);
    font-size: 0.92rem; line-height: 1.8;
    font-style: italic; margin-bottom: 1.5rem;
}
.testimonial-author { display: flex; align-items: center; gap: 0.75rem; }
.testimonial-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--orange); color: var(--black);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-head); font-size: 1.1rem; font-weight: 700;
    flex-shrink: 0;
}
.testimonial-name { font-weight: 600; font-size: 0.9rem; }
.testimonial-car  { color: var(--orange); font-size: 0.78rem; }

/* ── CTA ───────────────────────────────────────────────────── */
.cta-section {
    background:
        linear-gradient(rgba(10,10,10,0.88), rgba(10,10,10,0.88)),
        url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1600&q=80') center/cover no-repeat;
    padding: 6rem 0;
    text-align: center;
}
.cta-title {
    font-family: var(--font-head);
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 700; color: var(--white);
    margin-bottom: 1rem;
}
.cta-title span { color: var(--orange); }

/* ── Sponsors Carousel ──────────────────────────────────── */
.sponsors-section { background: var(--dark2); padding: 4rem 0; }
.sponsors-carousel {
    position: relative;
    overflow: hidden;
    mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}
.sponsors-track {
    display: flex;
    gap: 3rem;
    animation: scroll-carousel 30s linear infinite;
}
.sponsors-track:hover {
    animation-play-state: paused;
}
@keyframes scroll-carousel {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.sponsor-item {
    flex-shrink: 0;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    filter: grayscale(100%) brightness(0.7);
    transition: all 0.3s ease;
    padding: 0 1.5rem;
}
.sponsor-item:hover {
    filter: grayscale(0%) brightness(1);
    transform: scale(1.1);
}
.sponsor-item img {
    max-height: 60px;
    max-width: 150px;
    object-fit: contain;
}
/* Animate.css classes */
.animate__fadeIn { animation: fadeIn 1s; }
.animate__slideInUp { animation: slideInUp 1s; }
.animate__zoomIn { animation: zoomIn 1s; }
.animate__flipInX { animation: flipInX 1s; }
.animate__bounceIn { animation: bounceIn 1s; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes zoomIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
@keyframes flipInX { from { opacity: 0; transform: perspective(400px) rotateX(90deg); } to { opacity: 1; transform: perspective(400px) rotateX(0); } }
@keyframes bounceIn { 0% { opacity: 0; transform: scale(0.3); } 50% { transform: scale(1.05); } 70% { transform: scale(0.9); } 100% { opacity: 1; transform: scale(1); } }
</style>
@endsection

@section('content')

<!-- Hero -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container position-relative" style="padding-top:80px;">
        <div class="row">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="bi bi-diamond-fill" style="font-size:0.6rem;"></i>
                    {{ $siteSettings['hero_badge_text'] }}
                </div>
                <h1 class="hero-title">
                    {!! nl2br(e($siteSettings['hero_title'])) !!}
                </h1>
                <p class="hero-sub">
                    {{ $siteSettings['hero_subtitle'] }}
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('contact') }}" class="btn-orange">
                        <i class="bi bi-calendar-check"></i> Agendar Visita
                    </a>
                    <a href="{{ route('services') }}" class="btn-outline-orange">
                        <i class="bi bi-grid"></i> Nossos Serviços
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-num">{{ $siteSettings['hero_stat1_value'] }}</div>
                        <div class="hero-stat-label">{{ $siteSettings['hero_stat1_label'] }}</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">{{ $siteSettings['hero_stat2_value'] }}</div>
                        <div class="hero-stat-label">{{ $siteSettings['hero_stat2_label'] }}</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">{{ $siteSettings['hero_stat3_value'] }}</div>
                        <div class="hero-stat-label">{{ $siteSettings['hero_stat3_label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll">
        <span>Scroll</span>
        <i class="bi bi-chevron-down"></i>
    </div>
</section>

<!-- Brands marquee -->
<div class="brands-bar">
    <div class="brands-track">
        <span>Lamborghini</span><span>Ferrari</span><span>Porsche</span>
        <span>McLaren</span><span>Bugatti</span><span>Aston Martin</span>
        <span>Bentley</span><span>Rolls-Royce</span><span>Maserati</span>
        <span>Pagani</span><span>Koenigsegg</span><span>Rimac</span>
        <!-- duplicate for seamless loop -->
        <span>Lamborghini</span><span>Ferrari</span><span>Porsche</span>
        <span>McLaren</span><span>Bugatti</span><span>Aston Martin</span>
        <span>Bentley</span><span>Rolls-Royce</span><span>Maserati</span>
        <span>Pagani</span><span>Koenigsegg</span><span>Rimac</span>
    </div>
</div>

<!-- About -->
<section class="py-6" style="padding:6rem 0; background:var(--black);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="about-img-wrap me-lg-4">
                    <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80"
                         alt="Oficina HomeMechanic" class="about-img">
                    <div class="about-badge">
                        <div class="about-badge-num">{{ $siteSettings['about_years'] }}</div>
                        <div class="about-badge-text">{{ $siteSettings['about_subtitle'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="section-label">Sobre Nós</div>
                <h2 class="section-title mb-3">
                    {!! nl2br(e($siteSettings['about_title'])) !!}
                </h2>
                <div class="divider-orange"></div>
                <p class="section-sub mb-4">
                    {{ $siteSettings['about_text'] }}
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="about-feature">
                            <div class="about-feature-icon"><i class="bi bi-award-fill"></i></div>
                            <div>
                                <div class="about-feature-title">Certificação Internacional</div>
                                <p class="about-feature-text">Técnicos certificados pelas principais marcas premium</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-feature">
                            <div class="about-feature-icon"><i class="bi bi-cpu-fill"></i></div>
                            <div>
                                <div class="about-feature-title">Tecnologia de Ponta</div>
                                <p class="about-feature-text">Equipamentos de diagnóstico de última geração</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-feature">
                            <div class="about-feature-icon"><i class="bi bi-shield-check-fill"></i></div>
                            <div>
                                <div class="about-feature-title">Garantia Total</div>
                                <p class="about-feature-text">Todos os serviços com garantia documentada</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="about-feature">
                            <div class="about-feature-icon"><i class="bi bi-clock-fill"></i></div>
                            <div>
                                <div class="about-feature-title">Atendimento VIP</div>
                                <p class="about-feature-text">Serviço personalizado com acompanhamento em tempo real</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section class="services-section py-5" style="padding:5rem 0 !important;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="section-label">O Que Fazemos</div>
                <h2 class="section-title">Serviços <span>Premium</span></h2>
                <div class="divider-orange"></div>
            </div>
            <div class="col-lg-6 d-flex align-items-end justify-content-lg-end" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('services') }}" class="btn-outline-orange">
                    Ver Todos os Serviços <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="row g-4">
            @forelse($services as $i => $s)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                <div class="service-card">
                    <div class="service-img-wrap">
                        @php
                            $svcImg = $s->cover_image;
                            if ($svcImg && !str_starts_with($svcImg, 'http')) {
                                $svcImg = asset($svcImg);
                            }
                        @endphp
                        <img src="{{ $svcImg ?? 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' }}" alt="{{ $s->title }}" class="service-img">
                    </div>
                    <div class="service-body">
                        <div class="service-icon"><i class="{{ empty($s->icon) ? 'bi bi-tools' : $s->icon }}"></i></div>
                        <div class="service-title">{{ $s->title }}</div>
                        <p class="service-text">{{ Str::limit($s->description, 90) }}</p>
                        <a href="{{ route('services') }}" class="service-link">
                            Saiba mais <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Nenhum serviço cadastrado.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Gallery preview -->
<section style="padding:5rem 0; background:var(--black);">
    <div class="container">
        <div class="row mb-5 align-items-end">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="section-label">Nosso Trabalho</div>
                <h2 class="section-title">Galeria de <span>Projetos</span></h2>
                <div class="divider-orange"></div>
            </div>
            <div class="col-lg-6 text-lg-end" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('gallery') }}" class="btn-outline-orange">
                    Ver Galeria Completa <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="gallery-grid" data-aos="fade-up" data-aos-delay="150">
            @forelse($galleryPhotos as $photo)
            <div class="gallery-item">
                <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title }}">
                <div class="gallery-overlay"><span>{{ $photo->title }}</span></div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Nenhuma foto cadastrada.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section" style="padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label justify-content-center">Depoimentos</div>
            <h2 class="section-title">O Que Nossos <span>Clientes</span> Dizem</h2>
            <div class="divider-orange mx-auto"></div>
        </div>
        <div class="row g-4">
            @forelse($testimonials as $i => $t)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        @for($k=1; $k<=5; $k++)
                            <i class="{{ $k <= $t->rating ? 'bi bi-star-fill' : 'bi bi-star' }}"></i>
                        @endfor
                    </div>
                    <p class="testimonial-text">"{{ $t->content }}"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">{{ strtoupper(substr($t->name, 0, 2)) }}</div>
                        <div>
                            <div class="testimonial-name">{{ $t->name }}</div>
                            <div class="testimonial-car">{{ $t->role }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted">Nenhum depoimento cadastrado.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- Sponsors -->
@if(isset($sponsors) && $sponsors->count() > 0)
<section class="sponsors-section">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <div class="section-label justify-content-center">Parceiros</div>
            <h3 class="section-title" style="font-size:1.5rem;">Nossos <span>Patrocinadores</span></h3>
        </div>
        <div class="sponsors-carousel" data-aos="fade-up">
            <div class="sponsors-track">
                @foreach($sponsors as $sponsor)
                <a href="{{ $sponsor->website ?? '#' }}" class="sponsor-item {{ $sponsor->animation_class }}" style="animation-duration: {{ $sponsor->speed_value }};" target="_blank" rel="noopener">
                    @if($sponsor->logo)
                        <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}">
                    @else
                        <span style="color:var(--orange);font-weight:600;">{{ $sponsor->name }}</span>
                    @endif
                </a>
                @endforeach
                {{-- Duplicar para scroll infinito --}}
                @foreach($sponsors as $sponsor)
                <a href="{{ $sponsor->website ?? '#' }}" class="sponsor-item {{ $sponsor->animation_class }}" style="animation-duration: {{ $sponsor->speed_value }};" target="_blank" rel="noopener">
                    @if($sponsor->logo)
                        <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}">
                    @else
                        <span style="color:var(--orange);font-weight:600;">{{ $sponsor->name }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="cta-section" data-aos="fade-up">
    <div class="container">
        <div class="cta-title">{!! nl2br(e($siteSettings['cta_title'])) !!}</div>
        <p style="color:rgba(255,255,255,0.6); font-size:1.05rem; margin-bottom:2.5rem; max-width:500px; margin-left:auto; margin-right:auto;">
            {{ $siteSettings['cta_text'] }}
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ route('contact') }}" class="btn-orange">
                <i class="bi bi-calendar-check"></i> Agendar Agora
            </a>
            <a href="https://wa.me/5511999999999" class="btn-outline-orange" target="_blank">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
</section>

@endsection
