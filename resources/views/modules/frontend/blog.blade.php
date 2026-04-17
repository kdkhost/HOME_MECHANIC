@extends('layouts.frontend')
@section('title', 'Blog — HomeMechanic Tuning & Performance')

@section('styles')
<style>
.page-hero {
    min-height: 45vh; display: flex; align-items: flex-end; padding-bottom: 4rem;
    background: linear-gradient(to bottom, rgba(10,10,10,0.5) 0%, rgba(10,10,10,0.95) 100%),
        url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1600&q=80') center/cover no-repeat;
    padding-top: 100px;
}
.blog-card {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; overflow: hidden;
    transition: var(--transition); height: 100%;
}
.blog-card:hover { border-color: rgba(201,168,76,0.35); transform: translateY(-4px); }
.blog-img { width:100%; height:220px; object-fit:cover; filter:brightness(0.8); transition:var(--transition); }
.blog-card:hover .blog-img { filter:brightness(0.95); }
.blog-img-wrap { overflow:hidden; }
.blog-body { padding: 1.5rem; }
.blog-cat {
    display: inline-block;
    background: rgba(201,168,76,0.12); color: var(--gold);
    font-size: 0.7rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;
    padding: 0.25rem 0.75rem; border-radius: 2px; margin-bottom: 0.75rem;
}
.blog-title {
    font-family: var(--font-head); font-size: 1.25rem; font-weight: 700;
    color: var(--white); margin-bottom: 0.75rem; line-height: 1.3;
    transition: var(--transition);
}
.blog-card:hover .blog-title { color: var(--gold); }
.blog-excerpt { color: var(--gray); font-size: 0.88rem; line-height: 1.7; margin-bottom: 1.25rem; }
.blog-meta { display:flex; align-items:center; gap:1rem; color:var(--gray); font-size:0.78rem; }
.blog-meta i { color: var(--gold); }
.blog-read {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--gold); font-size: 0.78rem; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase;
    transition: var(--transition);
}
.blog-read:hover { gap: 0.75rem; color: var(--gold-light); }
.blog-featured {
    background: var(--dark2);
    border: 1px solid rgba(201,168,76,0.2);
    border-radius: 4px; overflow: hidden;
    margin-bottom: 3rem;
}
.blog-featured-img { width:100%; height:420px; object-fit:cover; filter:brightness(0.75); }
.blog-featured-body { padding: 2.5rem; }
</style>
@endsection

@section('content')
<div class="page-hero">
    <div class="container">
        <div class="section-label">Conteúdo</div>
        <h1 class="section-title">Blog & <span>Notícias</span></h1>
        <div class="divider-gold"></div>
        <p class="section-sub">Dicas, novidades e bastidores do mundo dos supercars.</p>
    </div>
</div>

<section style="padding:4rem 0; background:var(--black);">
    <div class="container">

        <!-- Featured -->
        <div class="blog-featured" data-aos="fade-up">
            <div class="row g-0">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=900&q=80"
                         alt="Post em destaque" class="blog-featured-img" style="height:100%; min-height:320px;">
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="blog-featured-body">
                        <span class="blog-cat">Em Destaque</span>
                        <h2 class="blog-title" style="font-size:1.8rem;">
                            Lamborghini Huracán: Como Extraímos 850cv de um Motor Naturalmente Aspirado
                        </h2>
                        <p class="blog-excerpt">
                            Neste projeto exclusivo, nossa equipe desenvolveu um kit de performance completo 
                            para o Huracán EVO, elevando a potência de 640cv para impressionantes 850cv 
                            sem comprometer a confiabilidade do motor.
                        </p>
                        <div class="blog-meta mb-1.5">
                            <span><i class="bi bi-calendar3"></i> 15 Abr 2026</span>
                            <span><i class="bi bi-clock"></i> 8 min de leitura</span>
                        </div>
                        <a href="#" class="blog-read mt-3 d-inline-flex">
                            Ler Artigo Completo <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posts grid -->
        @php
        $posts = [
            ['img'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=600&q=80','cat'=>'Tuning','title'=>'Ferrari 488: Guia Completo de Upgrades de Performance','excerpt'=>'Tudo que você precisa saber sobre os melhores upgrades para o motor V8 biturbo da Ferrari 488.','date'=>'10 Abr 2026','read'=>'6 min'],
            ['img'=>'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&q=80','cat'=>'Suspensão','title'=>'Porsche 911: A Ciência por Trás da Suspensão Perfeita','excerpt'=>'Como a geometria de suspensão correta transforma completamente a dinâmica do 911 em pista.','date'=>'5 Abr 2026','read'=>'5 min'],
            ['img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80','cat'=>'Estética','title'=>'Envelopamento vs PPF: Qual a Melhor Proteção para Seu Supercar?','excerpt'=>'Comparamos as duas principais tecnologias de proteção de pintura disponíveis no mercado.','date'=>'1 Abr 2026','read'=>'4 min'],
            ['img'=>'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=600&q=80','cat'=>'Manutenção','title'=>'Os 10 Erros Mais Comuns na Manutenção de Supercars','excerpt'=>'Evite esses erros que podem custar caro e comprometer a performance do seu veículo.','date'=>'25 Mar 2026','read'=>'7 min'],
            ['img'=>'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=600&q=80','cat'=>'Tecnologia','title'=>'ECU Tuning: Como a Reprogramação Transforma Seu Motor','excerpt'=>'Entenda como a reprogramação eletrônica pode liberar potência escondida de fábrica.','date'=>'20 Mar 2026','read'=>'5 min'],
            ['img'=>'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&q=80','cat'=>'Freios','title'=>'Brembo Carbon Ceramic: Vale o Investimento?','excerpt'=>'Analisamos os freios de carbono cerâmico e quando eles fazem sentido para uso na rua.','date'=>'15 Mar 2026','read'=>'6 min'],
        ];
        @endphp

        <div class="row g-4">
            @foreach($posts as $i => $p)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                <div class="blog-card">
                    <div class="blog-img-wrap">
                        <img src="{{ $p['img'] }}" alt="{{ $p['title'] }}" class="blog-img" loading="lazy">
                    </div>
                    <div class="blog-body">
                        <span class="blog-cat">{{ $p['cat'] }}</span>
                        <div class="blog-title">{{ $p['title'] }}</div>
                        <p class="blog-excerpt">{{ $p['excerpt'] }}</p>
                        <div class="blog-meta mb-3">
                            <span><i class="bi bi-calendar3"></i> {{ $p['date'] }}</span>
                            <span><i class="bi bi-clock"></i> {{ $p['read'] }}</span>
                        </div>
                        <a href="#" class="blog-read">Ler mais <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
@endsection
