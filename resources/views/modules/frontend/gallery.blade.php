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
    background: var(--orange); color: var(--black); border-color: var(--orange);
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
.gallery-overlay-sub { color: var(--orange); font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; }
.gallery-zoom {
    position: absolute; top: 1rem; right: 1rem;
    width: 36px; height: 36px;
    background: rgba(255,107,0,0.9); color: var(--black);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: var(--transition);
}
.gallery-item:hover .gallery-zoom { opacity: 1; }
/* Lightbox */
.lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:9000; align-items:center; justify-content:center; }
.lightbox.open { display:flex; }
.lightbox img { max-width:90vw; max-height:90vh; border-radius:4px; }
.lightbox-close { position:absolute; top:1.5rem; right:1.5rem; color:var(--orange); font-size:2rem; cursor:pointer; }
</style>
@endsection

@section('content')
<div class="page-hero">
    <div class="container">
        <div class="section-label">Nosso Portfólio</div>
        <h1 class="section-title">Galeria de <span>Projetos</span></h1>
        <div class="divider-orange"></div>
        <p class="section-sub">Cada projeto conta uma história de paixão, precisão e performance.</p>
    </div>
</div>

<section style="padding:4rem 0; background:var(--black);">
    <div class="container">
        <div class="filter-bar" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">Todos</button>
            @foreach($categories as $cat)
                <button class="filter-btn" data-filter=".cat-{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        <div class="gallery-masonry" data-aos="fade-up" data-aos-delay="100">
            @forelse($photos as $p)
            <div class="gallery-item mix cat-{{ $p->category_id }}" onclick="openLightbox('{{ asset($p->image_path) }}')">
                <img src="{{ asset($p->image_path) }}" alt="{{ $p->title }}" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-title">{{ $p->title }}</div>
                    <div class="gallery-overlay-sub">{{ $p->category ? $p->category->name : '' }}</div>
                </div>
                <div class="gallery-zoom"><i class="bi bi-zoom-in"></i></div>
            </div>
            @empty
            <div class="col-12 text-center text-muted w-100">Nenhuma foto cadastrada.</div>
            @endforelse
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

// Filter buttons logic
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const filter = this.getAttribute('data-filter');
        document.querySelectorAll('.gallery-item').forEach(item => {
            if(filter === 'all' || item.classList.contains(filter.replace('.',''))) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
