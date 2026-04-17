@extends('layouts.frontend')
@section('title', 'Galeria — HomeMechanic Tuning & Performance')

@section('styles')
<style>
.page-hero {
    min-height: 45vh; display: flex; align-items: flex-end; padding-bottom: 4rem;
    background: linear-gradient(to bottom, rgba(10,10,10,0.5) 0%, rgba(10,10,10,0.95) 100%),
        url('https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1600&q=80') center/cover no-repeat;
    padding-top: 100px;
}
.filter-bar {
    display: flex; flex-wrap: wrap; gap: 0.5rem;
    margin-bottom: 2.5rem;
}
.filter-btn {
    background: var(--dark2); border: 1px solid rgba(255,255,255,0.08);
    color: var(--gray); font-size: 0.78rem; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    padding: 0.5rem 1.25rem; border-radius: 2px;
    cursor: pointer; transition: var(--transition);
}
.filter-btn:hover, .filter-btn.active {
    background: var(--gold); color: var(--black); border-color: var(--gold);
}
.gallery-masonry {
    columns: 3; column-gap: 8px;
}
@media(max-width:768px) { .gallery-masonry { columns: 2; } }
@media(max-width:480px) { .gallery-masonry { columns: 1; } }
.gallery-item {
    break-inside: avoid; margin-bottom: 8px;
    position: relative; overflow: hidden; border-radius: 4px;
    cursor: pointer;
}
.gallery-item img {
    width: 100%; display: block;
    filter: brightness(0.8);
    transition: transform 0.5s ease, filter 0.5s ease;
}
.gallery-item:hover img { transform: scale(1.05); filter: brightness(1); }
.gallery-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 50%);
    opacity: 0; transition: var(--transition);
    display: flex; flex-direction: column;
    justify-content: flex-end; padding: 1.25rem;
}
.gallery-item:hover .gallery-overlay { opacity: 1; }
.gallery-overlay-title {
    font-family: var(--font-head); font-size: 1.1rem; font-weight: 700;
    color: var(--white); margin-bottom: 0.2rem;
}
.gallery-overlay-sub { color: var(--gold); font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; }
.gallery-zoom {
    position: absolute; top: 1rem; right: 1rem;
    width: 36px; height: 36px;
    background: rgba(201,168,76,0.9); color: var(--black);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: var(--transition);
}
.gallery-item:hover .gallery-zoom { opacity: 1; }
/* Lightbox */
.lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:9000; align-items:center; justify-content:center; }
.lightbox.open { display:flex; }
.lightbox img { max-width:90vw; max-height:90vh; border-radius:4px; }
.lightbox-close { position:absolute; top:1.5rem; right:1.5rem; color:var(--gold); font-size:2rem; cursor:pointer; }
</style>
@endsection

@section('content')
<div class="page-hero">
    <div class="container">
        <div class="section-label">Nosso Portfólio</div>
        <h1 class="section-title">Galeria de <span>Projetos</span></h1>
        <div class="divider-gold"></div>
        <p class="section-sub">Cada projeto conta uma história de paixão, precisão e performance.</p>
    </div>
</div>

<section style="padding:4rem 0; background:var(--black);">
    <div class="container">
        <div class="filter-bar" data-aos="fade-up">
            <button class="filter-btn active">Todos</button>
            <button class="filter-btn">Tuning</button>
            <button class="filter-btn">Suspensão</button>
            <button class="filter-btn">Estética</button>
            <button class="filter-btn">Freios</button>
        </div>

        @php
        $photos = [
            ['url'=>'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=80','title'=>'Lamborghini Huracán','sub'=>'Tuning Completo'],
            ['url'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80','title'=>'Ferrari 488 GTB','sub'=>'Estética Premium'],
            ['url'=>'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80','title'=>'Porsche 911 GT3','sub'=>'Suspensão Sport'],
            ['url'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80','title'=>'McLaren 720S','sub'=>'Tuning Motor'],
            ['url'=>'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&q=80','title'=>'Aston Martin DB11','sub'=>'Freios Brembo'],
            ['url'=>'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80','title'=>'Bugatti Chiron','sub'=>'Diagnóstico'],
            ['url'=>'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=800&q=80','title'=>'BMW M4 Competition','sub'=>'Tuning ECU'],
            ['url'=>'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&q=80','title'=>'Mercedes AMG GT','sub'=>'Estética'],
            ['url'=>'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=800&q=80','title'=>'Audi R8 V10','sub'=>'Performance'],
            ['url'=>'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=800&q=80','title'=>'Nissan GT-R','sub'=>'Tuning Completo'],
            ['url'=>'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&q=80','title'=>'Porsche Cayenne','sub'=>'Suspensão'],
            ['url'=>'https://images.unsplash.com/photo-1553440569-bcc63803a83d?w=800&q=80','title'=>'Ferrari F8','sub'=>'Estética Premium'],
        ];
        @endphp

        <div class="gallery-masonry" data-aos="fade-up" data-aos-delay="100">
            @foreach($photos as $p)
            <div class="gallery-item" onclick="openLightbox('{{ $p['url'] }}')">
                <img src="{{ $p['url'] }}" alt="{{ $p['title'] }}" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title">{{ $p['title'] }}</div>
                    <div class="gallery-overlay-sub">{{ $p['sub'] }}</div>
                </div>
                <div class="gallery-zoom"><i class="bi bi-zoom-in"></i></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close"><i class="bi bi-x-lg"></i></span>
    <img id="lightbox-img" src="" alt="">
</div>
@endsection

@section('scripts')
<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeLightbox(); });

// Filter buttons (visual only)
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endsection
