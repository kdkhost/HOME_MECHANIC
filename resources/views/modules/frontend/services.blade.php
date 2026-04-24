@extends('layouts.frontend')
@section('title', 'Serviços — HomeMechanic Tuning & Performance')
@section('description', 'Tuning de motor, suspensão sport, estética premium e diagnóstico digital para supercars de luxo.')

@section('styles')
<style>
.page-hero {
    min-height: 50vh;
    display: flex; align-items: flex-end;
    padding-bottom: 4rem;
    background:
        linear-gradient(to bottom, rgba(10,10,10,0.6) 0%, rgba(10,10,10,0.95) 100%),
        url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=80') center/cover no-repeat;
    padding-top: 100px;
}
.service-full-card {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; overflow: hidden;
    transition: var(--transition);
}
.service-full-card:hover { border-color: rgba(255,107,0,0.35); }
.service-full-img {
    width: 100%; height: 300px;
    object-fit: cover; filter: brightness(0.8);
    transition: var(--transition);
}
.service-full-card:hover .service-full-img { filter: brightness(0.95); transform: scale(1.02); }
.service-full-img-wrap { overflow: hidden; }
.service-full-body { padding: 2rem; }
.service-full-icon {
    width: 52px; height: 52px;
    background: rgba(255,107,0,0.12);
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: var(--orange); font-size: 1.4rem;
    margin-bottom: 1.25rem;
}
.service-full-title {
    font-family: var(--font-head);
    font-size: 1.6rem; font-weight: 700;
    color: var(--white); margin-bottom: 0.75rem;
}
.service-full-text { color: var(--gray); font-size: 0.9rem; line-height: 1.8; }
.service-feature-list { list-style: none; padding: 0; margin: 1.25rem 0 0; }
.service-feature-list li {
    display: flex; align-items: center; gap: 0.6rem;
    color: rgba(255,255,255,0.75); font-size: 0.88rem;
    padding: 0.4rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.service-feature-list li:last-child { border-bottom: none; }
.service-feature-list li i { color: var(--orange); font-size: 0.75rem; }
.price-badge {
    display: inline-block;
    background: rgba(255,107,0,0.12);
    border: 1px solid rgba(255,107,0,0.3);
    color: var(--orange); font-size: 0.78rem;
    font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
    padding: 0.3rem 0.85rem; border-radius: 2px;
    margin-top: 1.25rem;
}
</style>
@endsection

@section('content')
<!-- Page Hero -->
<div class="page-hero">
    <div class="container">
        <div class="section-label">O Que Fazemos</div>
        <h1 class="section-title">Serviços <span>Premium</span></h1>
        <div class="divider-orange"></div>
        <p class="section-sub">Cada serviço é executado com precisão cirúrgica por especialistas certificados.</p>
    </div>
</div>

<!-- Services Grid -->
<section style="padding:5rem 0; background:var(--black);">
    <div class="container">
        @if($services->count() > 0)
        <div class="row g-4">
            @foreach($services as $i => $s)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                <a href="{{ route('services.show', $s->slug) }}" style="text-decoration:none;display:block;height:100%;">
                <div class="service-full-card h-100" style="cursor:pointer;transition:transform 0.3s,box-shadow 0.3s;">
                    @php
                        $imgUrl = $s->cover_image;
                        if (empty($imgUrl)) {
                            $titleLower = strtolower($s->title);
                            if (str_contains($titleLower, 'oleo') || str_contains($titleLower, 'filtro')) { $imgUrl = 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'freio') || str_contains($titleLower, 'suspensao')) { $imgUrl = 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'motor') || str_contains($titleLower, 'mecanica')) { $imgUrl = 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'tuning') || str_contains($titleLower, 'stage')) { $imgUrl = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'estetica') || str_contains($titleLower, 'polimento')) { $imgUrl = 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'funilaria') || str_contains($titleLower, 'pintura')) { $imgUrl = 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'cambio') || str_contains($titleLower, 'transmissao')) { $imgUrl = 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop'; }
                            elseif (str_contains($titleLower, 'revisao')) { $imgUrl = 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=800&h=500&fit=crop'; }
                            else { $imgUrl = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop'; }
                        } elseif (!str_starts_with($imgUrl, 'http')) {
                            $imgUrl = '/' . ltrim($imgUrl, '/');
                        }
                    @endphp
                    <div class="service-full-img-wrap">
                        <img src="{{ $imgUrl }}" alt="{{ $s->title }}" class="service-full-img" loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop'">
                    </div>
                    <div class="service-full-body">
                        <div class="service-full-icon"><i class="bi {{ $s->icon ?? 'bi-tools' }}"></i></div>
                        <div class="service-full-title">{{ $s->title }}</div>
                        <p class="service-full-text">{{ $s->description }}</p>
                        <div style="margin-top:1rem;display:inline-flex;align-items:center;gap:0.4rem;color:var(--orange);font-size:0.8rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;">
                            Ver detalhes <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>
                </a>
            </div>
            @endforeach
        </div>
                        if (empty($imgUrl)) {
                            // Imagens fallback por tipo de servico
                            $titleLower = strtolower($s->title);
                            if (str_contains($titleLower, 'oleo') || str_contains($titleLower, 'filtro')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'freio') || str_contains($titleLower, 'suspensao')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'diagnostico') || str_contains($titleLower, 'scanner')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'ar condicionado')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'eletrica') || str_contains($titleLower, 'bateria')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'motor') || str_contains($titleLower, 'mecanica')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'funilaria') || str_contains($titleLower, 'pintura')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'injecao')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'cambio') || str_contains($titleLower, 'transmissao')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1449130016994-a5ef73008ef5?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'revisao')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'tuning') || str_contains($titleLower, 'stage') || str_contains($titleLower, 'remap')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'carbono') || str_contains($titleLower, 'ceramico')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'estetica') || str_contains($titleLower, 'polimento') || str_contains($titleLower, 'vitrificacao')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1600712242805-5f7d787c568d?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'som') || str_contains($titleLower, 'audio') || str_contains($titleLower, 'multimidia')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1541447270888-83e8494f9c08?w=800&h=500&fit=crop';
                            } elseif (str_contains($titleLower, 'camera') || str_contains($titleLower, 'sensor') || str_contains($titleLower, 'led')) {
                                $imgUrl = 'https://images.unsplash.com/photo-1562969289-4c36f93d40c0?w=800&h=500&fit=crop';
                            } else {
                                $imgUrl = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop';
                            }
                        } elseif (!str_starts_with($imgUrl, 'http')) {
                            $imgUrl = '/' . ltrim($imgUrl, '/');
                        }
                    @endphp
                    <div class="service-full-img-wrap">
                        <img src="{{ $imgUrl }}" alt="{{ $s->title }}" class="service-full-img" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&h=500&fit=crop'">
                    </div>
                    <div class="service-full-body">
                        <div class="service-full-icon"><i class="bi {{ $s->icon ?? 'bi-tools' }}"></i></div>
                        <div class="service-full-title">{{ $s->title }}</div>
                        <p class="service-full-text">{{ $s->description }}</p>
                        @if($s->content)
                        <div class="service-full-text mt-2" style="font-size:0.85rem;">{!! \Illuminate\Support\Str::limit(strip_tags($s->content), 200) !!}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Fallback estático quando não há serviços cadastrados --}}
        @php
        $staticServices = [
            ['img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80','icon'=>'bi-speedometer2','title'=>'Tuning de Motor','text'=>'Reprogramação de ECU, upgrades de turbo, intercooler de alta performance e sistemas de injeção otimizados.'],
            ['img'=>'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&q=80','icon'=>'bi-gear-wide-connected','title'=>'Suspensão Sport','text'=>'Kits de rebaixamento ajustáveis, amortecedores de competição e geometria de precisão.'],
            ['img'=>'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80','icon'=>'bi-disc','title'=>'Freios Performance','text'=>'Sistemas de freio de alta performance para máxima segurança em altas velocidades.'],
            ['img'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80','icon'=>'bi-stars','title'=>'Estética Premium','text'=>'Envelopamento, polimento de alto brilho, proteção de pintura PPF e detalhamento completo.'],
            ['img'=>'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80','icon'=>'bi-cpu','title'=>'Diagnóstico Digital','text'=>'Leitura completa de todos os sistemas eletrônicos com equipamentos de última geração.'],
            ['img'=>'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=80','icon'=>'bi-wrench-adjustable','title'=>'Manutenção Preventiva','text'=>'Revisões completas seguindo os protocolos das fabricantes com peças originais.'],
        ];
        @endphp
        <div class="row g-4">
            @foreach($staticServices as $i => $s)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                <div class="service-full-card h-100">
                    <div class="service-full-img-wrap">
                        <img src="{{ $s['img'] }}" alt="{{ $s['title'] }}" class="service-full-img">
                    </div>
                    <div class="service-full-body">
                        <div class="service-full-icon"><i class="bi {{ $s['icon'] }}"></i></div>
                        <div class="service-full-title">{{ $s['title'] }}</div>
                        <p class="service-full-text">{{ $s['text'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="text-center mt-5" data-aos="fade-up">
            <p style="color:var(--gray); margin-bottom:1.5rem;">Precisa de um serviço personalizado? Entre em contato para um orçamento exclusivo.</p>
            <a href="{{ route('contact') }}" class="btn-orange">
                <i class="bi bi-calendar-check"></i> Solicitar Orçamento
            </a>
        </div>
    </div>
</section>
@endsection
