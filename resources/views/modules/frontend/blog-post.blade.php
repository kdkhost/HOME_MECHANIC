@extends('layouts.frontend')
@section('title', $post->title . ' — HomeMechanic')
@section('description', $post->excerpt ?? '')

@section('styles')
<style>
.post-hero {
    min-height: 55vh; display: flex; align-items: flex-end; padding-bottom: 3rem;
    position: relative; overflow: hidden; padding-top: 80px;
}
.post-hero-bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    filter: brightness(0.45);
    transition: transform 8s ease;
}
.post-hero:hover .post-hero-bg { transform: scale(1.04); }
.post-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(10,10,10,0.97) 0%, rgba(10,10,10,0.3) 60%, transparent 100%);
}
.post-hero-content { position: relative; z-index: 2; }

/* Breadcrumb */
.post-breadcrumb {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.78rem; color: rgba(255,255,255,0.5);
    margin-bottom: 1.25rem;
}
.post-breadcrumb a { color: rgba(255,255,255,0.5); transition: color 0.2s; }
.post-breadcrumb a:hover { color: var(--orange); }
.post-breadcrumb i { font-size: 0.6rem; }

/* Post meta */
.post-meta {
    display: flex; flex-wrap: wrap; align-items: center; gap: 1.25rem;
    font-size: 0.82rem; color: rgba(255,255,255,0.55);
    margin-bottom: 1.25rem;
}
.post-meta i { color: var(--orange); margin-right: 0.3rem; }
.post-cat-badge {
    background: var(--orange); color: #000;
    font-size: 0.7rem; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 0.25rem 0.85rem; border-radius: 3px;
}

/* Content area */
.post-content-wrap {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 2.5rem;
}
.post-content {
    font-size: 1rem; line-height: 1.9;
    color: rgba(255,255,255,0.82);
}
.post-content h1,.post-content h2,.post-content h3,.post-content h4 {
    font-family: var(--font-head);
    color: var(--white); margin: 2rem 0 1rem;
    font-weight: 700;
}
.post-content h2 { font-size: 1.8rem; border-left: 3px solid var(--orange); padding-left: 1rem; }
.post-content h3 { font-size: 1.4rem; color: var(--orange); }
.post-content p  { margin-bottom: 1.25rem; }
.post-content a  { color: var(--orange); text-decoration: underline; }
.post-content a:hover { color: var(--orange-light); }
.post-content img { max-width: 100%; border-radius: 6px; margin: 1.5rem 0; }
.post-content blockquote {
    border-left: 4px solid var(--orange);
    background: rgba(255,107,0,0.06);
    padding: 1rem 1.5rem; margin: 1.5rem 0;
    border-radius: 0 6px 6px 0;
    font-style: italic; color: rgba(255,255,255,0.7);
}
.post-content ul, .post-content ol {
    padding-left: 1.5rem; margin-bottom: 1.25rem;
}
.post-content li { margin-bottom: 0.5rem; }
.post-content strong { color: var(--white); }
.post-content code {
    background: rgba(255,107,0,0.1); color: var(--orange);
    padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.88em;
}
.post-content pre {
    background: var(--dark3); padding: 1.25rem; border-radius: 6px;
    overflow-x: auto; margin: 1.5rem 0;
}
.post-content pre code { background: none; color: #e2e8f0; padding: 0; }
.post-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
.post-content th { background: rgba(255,107,0,0.15); color: var(--orange); padding: 0.75rem 1rem; text-align: left; }
.post-content td { padding: 0.65rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); }

/* Sidebar */
.post-sidebar { position: sticky; top: 80px; }

/* Related */
.related-card {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 6px; overflow: hidden;
    transition: var(--transition);
    display: flex; gap: 0; flex-direction: column;
    height: 100%;
}
.related-card:hover { border-color: rgba(255,107,0,0.3); transform: translateY(-3px); }
.related-img { width: 100%; height: 160px; object-fit: cover; filter: brightness(0.8); transition: var(--transition); }
.related-card:hover .related-img { filter: brightness(0.95); }
.related-body { padding: 1rem; flex: 1; }
.related-title {
    font-family: var(--font-head); font-size: 1rem; font-weight: 700;
    color: var(--white); line-height: 1.3; margin-bottom: 0.5rem;
    transition: var(--transition);
}
.related-card:hover .related-title { color: var(--orange); }
.related-date { font-size: 0.75rem; color: var(--gray); }

/* Share */
.share-btn {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.55rem 1.1rem; border-radius: 6px;
    font-size: 0.8rem; font-weight: 600;
    text-decoration: none !important; transition: var(--transition);
    border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.7);
}
.share-btn:hover { border-color: var(--orange); color: var(--orange); }
.share-btn.whatsapp:hover { border-color: #25D366; color: #25D366; }
.share-btn.twitter:hover  { border-color: #1DA1F2; color: #1DA1F2; }
</style>
@endsection

@section('content')

{{-- Hero com imagem de capa --}}
<div class="post-hero">
    <div class="post-hero-bg" style="background-image:url('{{ $post->cover_image ? asset($post->cover_image) : 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1600&q=80' }}')"></div>
    <div class="post-hero-overlay"></div>
    <div class="container post-hero-content">
        {{-- Breadcrumb --}}
        <div class="post-breadcrumb">
            <a href="{{ route('home') }}">Início</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('blog') }}">Blog</a>
            <i class="bi bi-chevron-right"></i>
            <span>{{ \Illuminate\Support\Str::limit($post->title, 40) }}</span>
        </div>

        {{-- Categoria --}}
        @if($post->category)
            <span class="post-cat-badge mb-3 d-inline-block">{{ $post->category->name }}</span>
        @endif

        {{-- Título --}}
        <h1 style="font-family:var(--font-head);font-size:clamp(1.8rem,4vw,3rem);font-weight:700;color:#fff;line-height:1.15;max-width:800px;margin-bottom:1.25rem;">
            {{ $post->title }}
        </h1>

        {{-- Meta --}}
        <div class="post-meta">
            @if($post->author)
            <span><i class="bi bi-person-fill"></i> {{ $post->author->name }}</span>
            @endif
            <span><i class="bi bi-calendar3"></i> {{ $post->published_at?->format('d \d\e F \d\e Y') }}</span>
            @php $words = str_word_count(strip_tags($post->content ?? '')); $mins = max(1, round($words/200)); @endphp
            <span><i class="bi bi-clock"></i> {{ $mins }} min de leitura</span>
        </div>
    </div>
</div>

{{-- Conteúdo --}}
<section style="padding:4rem 0; background:var(--black);">
    <div class="container">
        <div class="row g-5">

            {{-- Artigo --}}
            <div class="col-lg-8">
                {{-- Excerpt destaque --}}
                @if($post->excerpt)
                <div style="background:rgba(255,107,0,0.07);border-left:4px solid var(--orange);padding:1.25rem 1.5rem;border-radius:0 8px 8px 0;margin-bottom:2rem;font-size:1.05rem;color:rgba(255,255,255,0.8);font-style:italic;line-height:1.7;">
                    {{ $post->excerpt }}
                </div>
                @endif

                <div class="post-content-wrap">
                    <div class="post-content">
                        {!! $post->content !!}
                    </div>
                </div>

                {{-- Compartilhar --}}
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.06);">
                    <div style="font-size:0.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray);margin-bottom:0.75rem;">
                        Compartilhar
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . url()->current()) }}"
                           target="_blank" class="share-btn whatsapp">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}"
                           target="_blank" class="share-btn twitter">
                            <i class="bi bi-twitter-x"></i> Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank" class="share-btn">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('Link copiado!'))"
                                class="share-btn" style="background:none;cursor:pointer;">
                            <i class="bi bi-link-45deg"></i> Copiar link
                        </button>
                    </div>
                </div>

                {{-- Voltar --}}
                <div class="mt-4">
                    <a href="{{ route('blog') }}" class="btn-outline-orange" style="padding:0.7rem 1.5rem;font-size:0.8rem;">
                        <i class="bi bi-arrow-left"></i> Voltar ao Blog
                    </a>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="post-sidebar">

                    {{-- CTA --}}
                    <div style="background:linear-gradient(135deg,var(--orange-dark),var(--orange));border-radius:8px;padding:1.75rem;margin-bottom:1.5rem;text-align:center;">
                        <div style="font-family:var(--font-head);font-size:1.4rem;font-weight:700;color:#000;margin-bottom:0.5rem;">
                            Quer esse resultado?
                        </div>
                        <p style="font-size:0.85rem;color:rgba(0,0,0,0.7);margin-bottom:1.25rem;">
                            Fale com nossa equipe e solicite um orçamento personalizado.
                        </p>
                        <a href="{{ route('contact') }}" class="btn-orange" style="background:rgba(0,0,0,0.85) !important;color:#FF6B00 !important;width:100%;justify-content:center;border:none !important;">
                            <i class="bi bi-calendar-check-fill"></i> Solicitar Orçamento
                        </a>
                    </div>

                    {{-- Posts relacionados --}}
                    @if($related->count() > 0)
                    <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.06);border-radius:8px;overflow:hidden;">
                        <div style="padding:1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.06);">
                            <div style="font-size:0.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--orange);">
                                Leia Também
                            </div>
                        </div>
                        <div style="padding:1rem;">
                            @foreach($related as $r)
                            <a href="{{ route('blog.post', $r->slug) }}" style="display:flex;gap:0.85rem;align-items:flex-start;padding:0.75rem 0;border-bottom:1px solid rgba(255,255,255,0.05);text-decoration:none;" class="related-link">
                                <img src="{{ $r->cover_image ? asset($r->cover_image) : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=120&q=70' }}"
                                     alt="{{ $r->title }}"
                                     style="width:72px;height:56px;object-fit:cover;border-radius:5px;flex-shrink:0;filter:brightness(0.8);">
                                <div>
                                    <div style="font-size:0.85rem;font-weight:600;color:var(--white);line-height:1.3;margin-bottom:0.3rem;transition:color 0.2s;" class="related-link-title">
                                        {{ \Illuminate\Support\Str::limit($r->title, 55) }}
                                    </div>
                                    <div style="font-size:0.72rem;color:var(--gray);">
                                        {{ $r->published_at?->format('d M Y') }}
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<style>
.related-link:hover .related-link-title { color: var(--orange) !important; }
.related-link:last-child { border-bottom: none !important; }
</style>
@endsection
