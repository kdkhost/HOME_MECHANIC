@extends('layouts.frontend')
@section('title', 'Contato — HomeMechanic Tuning & Performance')

@section('styles')
<style>
.page-hero {
    min-height: 45vh; display: flex; align-items: flex-end; padding-bottom: 4rem;
    background: linear-gradient(to bottom, rgba(10,10,10,0.5) 0%, rgba(10,10,10,0.95) 100%),
        url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1600&q=80') center/cover no-repeat;
    padding-top: 100px;
}
.contact-info-card {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; padding: 2rem;
    height: 100%; transition: var(--transition);
}
.contact-info-card:hover { border-color: rgba(201,168,76,0.3); }
.contact-info-icon {
    width: 52px; height: 52px;
    background: rgba(201,168,76,0.1);
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: var(--gold); font-size: 1.4rem;
    margin-bottom: 1.25rem;
}
.contact-info-title { font-weight: 600; font-size: 0.9rem; color: var(--white); margin-bottom: 0.5rem; }
.contact-info-text { color: var(--gray); font-size: 0.88rem; line-height: 1.7; margin: 0; }
.contact-form-wrap {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; padding: 2.5rem;
}
.form-label-custom {
    font-size: 0.75rem; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(255,255,255,0.6); margin-bottom: 0.5rem;
}
.form-control-custom {
    background: var(--dark3) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    color: var(--white) !important;
    border-radius: 4px !important;
    padding: 0.85rem 1rem !important;
    font-size: 0.9rem !important;
    transition: var(--transition) !important;
}
.form-control-custom:focus {
    border-color: rgba(201,168,76,0.5) !important;
    box-shadow: 0 0 0 3px rgba(201,168,76,0.1) !important;
    outline: none !important;
}
.form-control-custom::placeholder { color: rgba(255,255,255,0.25) !important; }
.map-wrap {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; overflow: hidden;
    height: 300px;
    display: flex; align-items: center; justify-content: center;
}
.map-placeholder {
    text-align: center; color: var(--gray);
}
.map-placeholder i { font-size: 3rem; color: var(--gold); margin-bottom: 1rem; display: block; }
</style>
@endsection

@section('content')
<div class="page-hero">
    <div class="container">
        <div class="section-label">Fale Conosco</div>
        <h1 class="section-title">Entre em <span>Contato</span></h1>
        <div class="divider-gold"></div>
        <p class="section-sub">Agende uma visita ou solicite um orçamento sem compromisso.</p>
    </div>
</div>

<section style="padding:5rem 0; background:var(--black);">
    <div class="container">

        <!-- Info cards -->
        <div class="row g-4 mb-5">
            @php
            $infos = [
                ['icon'=>'bi-geo-alt-fill','title'=>'Endereço','text'=>'Av. das Supercars, 1500<br>Jardim Europa — São Paulo, SP<br>CEP: 01452-000'],
                ['icon'=>'bi-telephone-fill','title'=>'Telefone & WhatsApp','text'=>'(11) 99999-9999<br>(11) 3333-4444<br>Seg–Sex: 8h–18h'],
                ['icon'=>'bi-envelope-fill','title'=>'E-mail','text'=>'contato@homemechanic.com.br<br>orcamento@homemechanic.com.br'],
                ['icon'=>'bi-clock-fill','title'=>'Horário de Funcionamento','text'=>'Segunda a Sexta: 8h às 18h<br>Sábado: 8h às 13h<br>Domingo: Fechado'],
            ];
            @endphp
            @foreach($infos as $i => $info)
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="contact-info-card">
                    <div class="contact-info-icon"><i class="bi {{ $info['icon'] }}"></i></div>
                    <div class="contact-info-title">{{ $info['title'] }}</div>
                    <p class="contact-info-text">{!! $info['text'] !!}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-5">
            <!-- Form -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="contact-form-wrap">
                    <h3 style="font-family:var(--font-head); font-size:1.8rem; font-weight:700; margin-bottom:0.5rem;">
                        Solicite um <span style="color:var(--gold);">Orçamento</span>
                    </h3>
                    <p style="color:var(--gray); font-size:0.88rem; margin-bottom:2rem;">
                        Preencha o formulário e nossa equipe entrará em contato em até 2 horas úteis.
                    </p>

                    @if(session('success'))
                        <div style="background:rgba(40,167,69,0.15); border:1px solid rgba(40,167,69,0.3); color:#75b798; padding:1rem; border-radius:4px; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem;">
                            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label-custom">Nome Completo *</label>
                                <input type="text" name="name" class="form-control form-control-custom"
                                       placeholder="Seu nome" value="{{ old('name') }}" required>
                                @error('name')<div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-custom">E-mail *</label>
                                <input type="email" name="email" class="form-control form-control-custom"
                                       placeholder="seu@email.com" value="{{ old('email') }}" required>
                                @error('email')<div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-custom">Telefone / WhatsApp</label>
                                <input type="text" name="phone" class="form-control form-control-custom"
                                       placeholder="(11) 99999-9999" value="{{ old('phone') }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-custom">Assunto *</label>
                                <select name="subject" class="form-control form-control-custom" required>
                                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Selecione...</option>
                                    <option value="Orçamento Tuning" {{ old('subject')=='Orçamento Tuning'?'selected':'' }}>Orçamento — Tuning</option>
                                    <option value="Orçamento Suspensão" {{ old('subject')=='Orçamento Suspensão'?'selected':'' }}>Orçamento — Suspensão</option>
                                    <option value="Orçamento Estética" {{ old('subject')=='Orçamento Estética'?'selected':'' }}>Orçamento — Estética</option>
                                    <option value="Orçamento Freios" {{ old('subject')=='Orçamento Freios'?'selected':'' }}>Orçamento — Freios</option>
                                    <option value="Manutenção" {{ old('subject')=='Manutenção'?'selected':'' }}>Manutenção Preventiva</option>
                                    <option value="Outro" {{ old('subject')=='Outro'?'selected':'' }}>Outro Assunto</option>
                                </select>
                                @error('subject')<div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Mensagem *</label>
                                <textarea name="message" class="form-control form-control-custom" rows="5"
                                          placeholder="Descreva seu veículo e o serviço desejado..." required>{{ old('message') }}</textarea>
                                @error('message')<div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-gold w-100" style="justify-content:center;">
                                    <i class="bi bi-send-fill"></i> Enviar Mensagem
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Map + WhatsApp -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="map-wrap mb-4">
                    <div class="map-placeholder">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div style="font-family:var(--font-head); font-size:1.1rem; font-weight:600; color:var(--white); margin-bottom:0.5rem;">
                            Av. das Supercars, 1500
                        </div>
                        <div style="font-size:0.85rem;">Jardim Europa — São Paulo, SP</div>
                    </div>
                </div>

                <div style="background:var(--dark2); border:1px solid rgba(255,255,255,0.06); border-radius:4px; padding:2rem;">
                    <h4 style="font-family:var(--font-head); font-size:1.3rem; font-weight:700; margin-bottom:0.5rem;">
                        Atendimento <span style="color:var(--gold);">Imediato</span>
                    </h4>
                    <p style="color:var(--gray); font-size:0.88rem; margin-bottom:1.5rem;">
                        Para respostas rápidas, fale diretamente com nossa equipe pelo WhatsApp.
                    </p>
                    <a href="https://wa.me/5511999999999" target="_blank" class="btn-gold w-100" style="justify-content:center; background:linear-gradient(135deg,#128C7E,#25D366);">
                        <i class="bi bi-whatsapp"></i> Chamar no WhatsApp
                    </a>
                    <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,0.06);">
                        <div style="font-size:0.75rem; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:var(--gold); margin-bottom:0.75rem;">
                            Redes Sociais
                        </div>
                        <div class="social-links">
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
