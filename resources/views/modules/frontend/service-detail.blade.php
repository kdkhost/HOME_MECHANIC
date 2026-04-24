@extends('layouts.frontend')
@section('title', $service->title . ' — HomeMechanic')
@section('description', $service->description ?? '')

@section('styles')
<style>
.svc-hero { min-height:55vh; display:flex; align-items:flex-end; padding-bottom:3rem; position:relative; overflow:hidden; padding-top:80px; }
.svc-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; filter:brightness(0.4); }
.svc-hero-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(10,10,10,0.97) 0%, rgba(10,10,10,0.3) 60%, transparent 100%); }
.svc-hero-content { position:relative; z-index:2; }
.svc-breadcrumb { display:flex; align-items:center; gap:0.5rem; font-size:0.78rem; color:rgba(255,255,255,0.5); margin-bottom:1.25rem; }
.svc-breadcrumb a { color:rgba(255,255,255,0.5); }
.svc-breadcrumb a:hover { color:var(--orange); }
.svc-content { font-size:1rem; line-height:1.9; color:rgba(255,255,255,0.82); }
.svc-content h2,.svc-content h3 { font-family:var(--font-head); color:var(--white); margin:2rem 0 1rem; font-weight:700; }
.svc-content h2 { font-size:1.8rem; border-left:4px solid var(--orange); padding-left:1rem; }
.svc-content h3 { font-size:1.4rem; color:var(--orange); }
.svc-content p { margin-bottom:1.25rem; }
.svc-content ul,.svc-content ol { padding-left:1.5rem; margin-bottom:1.25rem; }
.svc-content li { margin-bottom:0.5rem; }
.svc-content strong { color:var(--white); }
.svc-content blockquote { border-left:4px solid var(--orange); background:rgba(255,107,0,0.06); padding:1rem 1.5rem; margin:1.5rem 0; border-radius:0 6px 6px 0; font-style:italic; color:rgba(255,255,255,0.7); }
/* Contact menu */
.contact-options { position:absolute; bottom:calc(100% + 12px); left:0; right:0; background:#fff; border-radius:14px; padding:12px; box-shadow:0 10px 40px rgba(0,0,0,0.25); opacity:0; visibility:hidden; transform:translateY(8px) scale(0.97); transition:all 0.25s cubic-bezier(0.4,0,0.2,1); z-index:100; min-width:240px; }
.contact-options.open { opacity:1; visibility:visible; transform:translateY(0) scale(1); }
.contact-options::after { content:''; position:absolute; bottom:-8px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:8px solid transparent; border-right:8px solid transparent; border-top:8px solid #fff; }
.contact-opt { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px; color:#333; text-decoration:none; transition:all 0.2s; font-size:0.88rem; font-weight:500; }
.contact-opt:hover { background:#f5f5f5; color:var(--orange); }
.contact-opt .opt-icon { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.contact-opt.wa .opt-icon { background:#25D366; color:#fff; }
.contact-opt.em .opt-icon { background:var(--orange); color:#fff; }
.contact-opt.ph .opt-icon { background:#0891b2; color:#fff; }
.contact-opt.sm .opt-icon { background:#6366f1; color:#fff; }
.share-btn { display:inline-flex; align-items:center; gap:0.5rem; padding:0.55rem 1.1rem; border-radius:6px; font-size:0.8rem; font-weight:600; text-decoration:none !important; transition:all 0.2s; border:1px solid rgba(255,255,255,0.12); color:rgba(255,255,255,0.7); background:none; cursor:pointer; }
.share-btn:hover { border-color:var(--orange); color:var(--orange); }
.related-card { background:var(--dark2); border:1px solid rgba(255,255,255,0.06); border-radius:8px; overflow:hidden; transition:var(--transition); display:block; text-decoration:none !important; }
.related-card:hover { border-color:rgba(255,107,0,0.3); transform:translateY(-3px); }
.related-title { font-family:var(--font-head); font-size:0.95rem; font-weight:700; color:var(--white); line-height:1.3; transition:var(--transition); }
.related-card:hover .related-title { color:var(--orange); }
</style>
@endsection

@section('content')
@php
    $imgUrl = $service->cover_image;
    if (empty($imgUrl)) { $imgUrl = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1600&q=80'; }
    elseif (!str_starts_with($imgUrl, 'http')) { $imgUrl = '/' . ltrim($imgUrl, '/'); }
    $whatsapp = preg_replace('/\D/', '', $siteSettings['whatsapp'] ?? '5511999999999');
    $email    = $siteSettings['email'] ?? 'contato@homemechanic.com.br';
    $phone    = preg_replace('/\D/', '', $siteSettings['phone'] ?? '');
    $siteName = $siteSettings['site_name'] ?? 'HomeMechanic';
    $msgWa    = urlencode("Olá! Tenho interesse no serviço *{$service->title}* que vi no site {$siteName}. Poderia me dar mais informações?");
    $msgEmail = urlencode("Interesse no serviço: {$service->title}");
    $pageUrl  = url()->current();
@endphp

<div class="svc-hero">
    <div class="svc-hero-bg" style="background-image:url('{{ $imgUrl }}')"></div>
    <div class="svc-hero-overlay"></div>
    <div class="container svc-hero-content">
        <div class="svc-breadcrumb">
            <a href="{{ route('home') }}">Início</a>
            <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
            <a href="{{ route('services') }}">Serviços</a>
            <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
            <span>{{ \Illuminate\Support\Str::limit($service->title, 40) }}</span>
        </div>
        <div style="display:inline-flex;align-items:center;gap:0.6rem;background:rgba(255,107,0,0.15);border:1px solid rgba(255,107,0,0.3);color:var(--orange);font-size:0.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:3px;margin-bottom:1rem;">
            <i class="bi {{ $service->icon ?? 'bi-tools' }}"></i> Serviço Premium
        </div>
        <h1 style="font-family:var(--font-head);font-size:clamp(2rem,5vw,3.5rem);font-weight:700;color:#fff;line-height:1.1;margin-bottom:1rem;">{{ $service->title }}</h1>
        @if($service->description)
        <p style="font-size:1.05rem;color:rgba(255,255,255,0.7);max-width:600px;line-height:1.7;margin:0;">{{ $service->description }}</p>
        @endif
    </div>
</div>

<section style="padding:4rem 0; background:var(--black);">
    <div class="container">
        <div class="row g-5">

            <div class="col-lg-8">
                <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:2.5rem;">
                    <div class="svc-content">
                        @if($service->content)
                            {!! $service->content !!}
                        @else
                            <p>{{ $service->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Compartilhar --}}
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.06);">
                    <div style="font-size:0.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gray);margin-bottom:0.75rem;">Compartilhar</div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://wa.me/?text={{ urlencode($service->title . ' — ' . $pageUrl) }}" target="_blank" class="share-btn" style="border-color:#25D366;color:#25D366;"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($pageUrl) }}" target="_blank" class="share-btn" style="border-color:#1877F2;color:#1877F2;"><i class="bi bi-facebook"></i> Facebook</a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($service->title) }}&url={{ urlencode($pageUrl) }}" target="_blank" class="share-btn"><i class="bi bi-twitter-x"></i> Twitter</a>
                        <a href="{{ route('services') }}" class="share-btn"><i class="bi bi-grid-3x3-gap"></i> Ver todos</a>
                        <button onclick="navigator.clipboard.writeText(window.location.href).then(function(){alert('Link copiado!');})" class="share-btn"><i class="bi bi-link-45deg"></i> Copiar link</button>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('services') }}" class="btn-outline-orange" style="padding:0.7rem 1.5rem;font-size:0.8rem;">
                        <i class="bi bi-arrow-left"></i> Voltar aos Serviços
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div style="position:sticky;top:80px;">

                    {{-- CTA contato --}}
                    <div style="background:var(--dark2);border:1px solid rgba(255,107,0,0.2);border-radius:10px;padding:1.75rem;margin-bottom:1.5rem;">
                        <div style="font-family:var(--font-head);font-size:1.3rem;font-weight:700;color:var(--white);margin-bottom:0.4rem;">Interessado?</div>
                        <p style="font-size:0.85rem;color:var(--gray);margin-bottom:1.5rem;line-height:1.6;">Fale com nossa equipe e receba um orçamento personalizado para o seu veículo.</p>

                        <div style="position:relative;" id="contactFloat">
                            <div class="contact-options" id="contactOptions">
                                <div style="font-size:0.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#999;padding:0 8px 8px;border-bottom:1px solid #eee;margin-bottom:8px;">Como prefere falar conosco?</div>
                                <a href="https://wa.me/{{ $whatsapp }}?text={{ $msgWa }}" target="_blank" class="contact-opt wa">
                                    <div class="opt-icon"><i class="bi bi-whatsapp"></i></div>
                                    <div><div>WhatsApp</div><small style="color:#888;font-size:0.75rem;">Resposta imediata</small></div>
                                </a>
                                <a href="mailto:{{ $email }}?subject={{ $msgEmail }}" class="contact-opt em">
                                    <div class="opt-icon"><i class="bi bi-envelope-fill"></i></div>
                                    <div><div>E-mail</div><small style="color:#888;font-size:0.75rem;">{{ $email }}</small></div>
                                </a>
                                @if($phone)
                                <a href="tel:+{{ $phone }}" class="contact-opt ph">
                                    <div class="opt-icon"><i class="bi bi-telephone-fill"></i></div>
                                    <div><div>Ligar</div><small style="color:#888;font-size:0.75rem;">{{ $siteSettings['phone'] ?? '' }}</small></div>
                                </a>
                                <a href="sms:+{{ $phone }}?body={{ urlencode('Olá! Tenho interesse no serviço ' . $service->title) }}" class="contact-opt sm">
                                    <div class="opt-icon"><i class="bi bi-chat-dots-fill"></i></div>
                                    <div><div>SMS</div><small style="color:#888;font-size:0.75rem;">Mensagem de texto</small></div>
                                </a>
                                @endif
                                <a href="{{ route('contact') }}" class="contact-opt" style="border-top:1px solid #eee;margin-top:6px;padding-top:12px;">
                                    <div class="opt-icon" style="background:var(--orange);color:#fff;"><i class="bi bi-calendar-check-fill"></i></div>
                                    <div><div>Formulário</div><small style="color:#888;font-size:0.75rem;">Agendar visita</small></div>
                                </a>
                            </div>

                            <button onclick="toggleContact()" id="btnContact"
                                style="width:100%;background:linear-gradient(135deg,#E55A00,#FF6B00);color:#000;border:none;border-radius:8px;padding:1rem;font-weight:700;font-size:0.9rem;letter-spacing:1px;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.6rem;transition:all 0.3s;box-shadow:0 4px 20px rgba(255,107,0,0.4);">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span id="btnContactText">Falar sobre este serviço</span>
                                <i class="bi bi-chevron-up" id="btnContactChevron" style="transition:transform 0.3s;transform:rotate(180deg);"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Relacionados --}}
                    @if($related->count() > 0)
                    <div style="background:var(--dark2);border:1px solid rgba(255,255,255,0.06);border-radius:10px;overflow:hidden;">
                        <div style="padding:1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,0.06);">
                            <div style="font-size:0.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--orange);">Outros Serviços</div>
                        </div>
                        <div style="padding:1rem;display:flex;flex-direction:column;gap:0.75rem;">
                            @foreach($related as $r)
                            @php
                                $rImg = $r->cover_image;
                                if (empty($rImg)) { $rImg = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=300&q=70'; }
                                elseif (!str_starts_with($rImg, 'http')) { $rImg = '/' . ltrim($rImg, '/'); }
                            @endphp
                            <a href="{{ route('services.show', $r->slug) }}" class="related-card">
                                <div style="display:flex;gap:0.85rem;align-items:center;padding:0.5rem;">
                                    <img src="{{ $rImg }}" alt="{{ $r->title }}" style="width:64px;height:52px;object-fit:cover;border-radius:6px;flex-shrink:0;filter:brightness(0.8);">
                                    <div>
                                        <div class="related-title">{{ \Illuminate\Support\Str::limit($r->title, 40) }}</div>
                                        <div style="font-size:0.72rem;color:var(--gray);margin-top:0.2rem;">{{ \Illuminate\Support\Str::limit($r->description, 50) }}</div>
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
<script>
var contactOpen = false;
function toggleContact() {
    contactOpen = !contactOpen;
    document.getElementById('contactOptions').classList.toggle('open', contactOpen);
    document.getElementById('btnContactChevron').style.transform = contactOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    document.getElementById('btnContactText').textContent = contactOpen ? 'Fechar opções' : 'Falar sobre este serviço';
}
document.addEventListener('click', function(e) {
    if (contactOpen && !document.getElementById('contactFloat').contains(e.target)) {
        contactOpen = false;
        document.getElementById('contactOptions').classList.remove('open');
        document.getElementById('btnContactChevron').style.transform = 'rotate(180deg)';
        document.getElementById('btnContactText').textContent = 'Falar sobre este serviço';
    }
});
</script>
@endsection
