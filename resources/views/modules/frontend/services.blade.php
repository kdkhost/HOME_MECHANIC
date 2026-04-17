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
        @php
        $services = [
            [
                'img'   => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
                'icon'  => 'bi-speedometer2',
                'title' => 'Tuning de Motor',
                'text'  => 'Extraímos o máximo potencial do seu motor com reprogramação de ECU, upgrades de turbo, intercooler de alta performance e sistemas de injeção otimizados. Cada projeto é único e desenvolvido especificamente para o seu veículo.',
                'items' => ['Reprogramação de ECU/TCU','Upgrade de Turbo e Intercooler','Sistemas de Injeção Performance','Escape Esportivo Titanium','Filtros de Alta Vazão'],
                'badge' => 'A partir de R$ 3.500',
            ],
            [
                'img'   => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&q=80',
                'icon'  => 'bi-gear-wide-connected',
                'title' => 'Suspensão Sport',
                'text'  => 'Kits de rebaixamento ajustáveis, amortecedores de competição e geometria de precisão para máximo controle e resposta. Transformamos a dinâmica do seu supercar para pista ou estrada.',
                'items' => ['Coilovers Ajustáveis','Barras Estabilizadoras Reforçadas','Geometria de Precisão','Buchas Poliuretano','Rodas Forjadas Leves'],
                'badge' => 'A partir de R$ 5.000',
            ],
            [
                'img'   => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80',
                'icon'  => 'bi-disc',
                'title' => 'Freios Performance',
                'text'  => 'Sistemas de freio de alta performance para máxima segurança em altas velocidades. Discos ventilados, pastilhas de competição e fluido de freio de corrida.',
                'items' => ['Discos Ventilados Brembo','Pastilhas de Competição','Fluido de Freio Racing','Linhas de Freio Inox','Calibres Monobloco'],
                'badge' => 'A partir de R$ 4.200',
            ],
            [
                'img'   => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80',
                'icon'  => 'bi-stars',
                'title' => 'Estética Premium',
                'text'  => 'Envelopamento em vinil de alta qualidade, polimento de alto brilho, proteção de pintura PPF e detalhamento completo. Seu supercar sempre impecável.',
                'items' => ['Envelopamento Completo','Polimento Espelho','Proteção PPF 10 anos','Ceramic Coating','Detalhamento Interior'],
                'badge' => 'A partir de R$ 2.800',
            ],
            [
                'img'   => 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80',
                'icon'  => 'bi-cpu',
                'title' => 'Diagnóstico Digital',
                'text'  => 'Leitura completa de todos os sistemas eletrônicos com equipamentos de última geração. Identificamos qualquer problema antes que se torne crítico.',
                'items' => ['Scanner Multimarca','Leitura de Todos os Módulos','Relatório Detalhado','Calibração de Sensores','Atualização de Firmware'],
                'badge' => 'A partir de R$ 350',
            ],
            [
                'img'   => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=80',
                'icon'  => 'bi-wrench-adjustable',
                'title' => 'Manutenção Preventiva',
                'text'  => 'Revisões completas seguindo os protocolos das fabricantes. Utilizamos apenas peças originais ou de primeira linha para garantir a longevidade do seu investimento.',
                'items' => ['Revisão Completa','Troca de Fluidos Premium','Filtros Originais','Inspeção de 150 Pontos','Relatório Fotográfico'],
                'badge' => 'A partir de R$ 1.200',
            ],
        ];
        @endphp

        <div class="row g-4">
            @foreach($services as $i => $s)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                <div class="service-full-card h-100">
                    <div class="service-full-img-wrap">
                        <img src="{{ $s['img'] }}" alt="{{ $s['title'] }}" class="service-full-img">
                    </div>
                    <div class="service-full-body">
                        <div class="service-full-icon"><i class="bi {{ $s['icon'] }}"></i></div>
                        <div class="service-full-title">{{ $s['title'] }}</div>
                        <p class="service-full-text">{{ $s['text'] }}</p>
                        <ul class="service-feature-list">
                            @foreach($s['items'] as $item)
                            <li><i class="bi bi-check-circle-fill"></i> {{ $item }}</li>
                            @endforeach
                        </ul>
                        <div class="price-badge">{{ $s['badge'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <p style="color:var(--gray); margin-bottom:1.5rem;">Precisa de um serviço personalizado? Entre em contato para um orçamento exclusivo.</p>
            <a href="{{ route('contact') }}" class="btn-orange">
                <i class="bi bi-calendar-check"></i> Solicitar Orçamento
            </a>
        </div>
    </div>
</section>
@endsection
