@extends('layouts.admin')
@section('title', 'reCAPTCHA / Segurança')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">reCAPTCHA</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-shield-alt me-2" style="color:var(--hm-primary);"></i>
        reCAPTCHA v3 / Segurança
    </h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'recaptcha'])

    <div class="col-md-9">

        {{-- Status atual --}}
        <div class="card mb-3">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle"></i> Status Atual</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    @if($settings['recaptcha_enabled'] === '1' && !empty($settings['recaptcha_site_key']))
                        <span class="badge badge-success" style="font-size:0.85rem;padding:0.5rem 1rem;">
                            <i class="fas fa-check-circle me-1"></i> reCAPTCHA Ativo
                        </span>
                        <span style="font-size:0.85rem;color:var(--hm-text-muted);">
                            Formulário de contato protegido contra bots.
                        </span>
                    @elseif($settings['recaptcha_enabled'] === '1' && empty($settings['recaptcha_site_key']))
                        <span class="badge badge-warning" style="font-size:0.85rem;padding:0.5rem 1rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i> Habilitado mas sem chaves
                        </span>
                        <span style="font-size:0.85rem;color:var(--hm-text-muted);">
                            Configure as chaves abaixo para ativar a proteção.
                        </span>
                    @else
                        <span class="badge badge-secondary" style="font-size:0.85rem;padding:0.5rem 1rem;">
                            <i class="fas fa-times-circle me-1"></i> reCAPTCHA Desativado
                        </span>
                        <span style="font-size:0.85rem;color:var(--hm-text-muted);">
                            Formulário sem proteção anti-bot.
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Formulário --}}
        <form method="POST" action="{{ route('admin.settings.recaptcha.update') }}">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fab fa-google"></i> Google reCAPTCHA v3</span>
                </div>
                <div class="card-body">

                    {{-- Ativar/desativar --}}
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="recaptcha_enabled"
                                   name="recaptcha_enabled" value="1"
                                   {{ $settings['recaptcha_enabled'] === '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="recaptcha_enabled">
                                Habilitar reCAPTCHA v3 no formulário de contato
                            </label>
                        </div>
                        <small class="form-text">
                            Quando ativo, cada envio do formulário é verificado silenciosamente pelo Google.
                            Usuários reais não precisam resolver nenhum desafio.
                        </small>
                    </div>

                    <hr style="border-color:var(--hm-border);margin:1.5rem 0;">

                    {{-- Site Key --}}
                    <div class="form-group">
                        <label>
                            Chave do Site (Site Key)
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="text" class="form-control" name="recaptcha_site_key"
                                   id="recaptcha_site_key"
                                   value="{{ $settings['recaptcha_site_key'] }}"
                                   placeholder="6Lc...">
                        </div>
                        <small class="form-text">Usada no frontend (JavaScript). Pode ser pública.</small>
                    </div>

                    {{-- Secret Key --}}
                    <div class="form-group">
                        <label>
                            Chave Secreta (Secret Key)
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="recaptcha_secret"
                                   id="recaptcha_secret"
                                   value="{{ $settings['recaptcha_secret'] }}"
                                   placeholder="6Lc..."
                                   autocomplete="new-password">
                            <button type="button" class="btn btn-secondary" onclick="toggleSecret()">
                                <i class="fas fa-eye" id="secretEye"></i>
                            </button>
                        </div>
                        <small class="form-text">Usada no backend para verificar tokens. Mantenha em segredo.</small>
                    </div>

                    {{-- Score threshold --}}
                    <div class="form-group mb-0">
                        <label>
                            Score Mínimo
                            <span id="thresholdVal"
                                  style="background:var(--hm-primary);color:#fff;padding:0.15rem 0.6rem;border-radius:20px;font-size:0.78rem;margin-left:0.5rem;">
                                {{ $settings['recaptcha_threshold'] }}
                            </span>
                        </label>
                        <input type="range" class="form-range" name="recaptcha_threshold"
                               id="recaptcha_threshold"
                               min="0" max="1" step="0.1"
                               value="{{ $settings['recaptcha_threshold'] }}"
                               style="accent-color:var(--hm-primary);">
                        <div class="d-flex justify-content-between" style="font-size:0.75rem;color:var(--hm-text-muted);margin-top:0.25rem;">
                            <span>0.0 — Aceita tudo</span>
                            <span>0.5 — Recomendado</span>
                            <span>1.0 — Apenas humanos</span>
                        </div>
                        <small class="form-text">
                            O reCAPTCHA v3 retorna um score de 0.0 (bot) a 1.0 (humano).
                            Envios com score abaixo deste valor serão bloqueados.
                        </small>
                    </div>

                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>

        {{-- Como obter as chaves --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-question-circle"></i> Como Obter as Chaves</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div style="background:#f8fafc;border-radius:8px;padding:1.25rem;border-left:3px solid var(--hm-primary);">
                            <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.75rem;">
                                <i class="fab fa-google text-danger me-2"></i>
                                Passo a passo para criar as chaves
                            </div>
                            <ol style="font-size:0.85rem;color:var(--hm-text-muted);line-height:2;margin:0;padding-left:1.25rem;">
                                <li>Acesse <a href="https://www.google.com/recaptcha/admin/create" target="_blank" style="color:var(--hm-primary);">google.com/recaptcha/admin/create</a></li>
                                <li>Faça login com sua conta Google</li>
                                <li>Em <strong>Tipo</strong>, selecione <strong>reCAPTCHA v3</strong></li>
                                <li>Em <strong>Domínios</strong>, adicione seu domínio (ex: <code>homemechanic.com.br</code>)</li>
                                <li>Aceite os termos e clique em <strong>Enviar</strong></li>
                                <li>Copie a <strong>Chave do Site</strong> e a <strong>Chave Secreta</strong> para os campos acima</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#f0fdf4;border-radius:8px;padding:1rem;border-left:3px solid #16a34a;">
                            <div style="font-weight:700;font-size:0.85rem;color:#166534;margin-bottom:0.4rem;">
                                <i class="fas fa-check-circle me-1"></i> Score alto (0.7 – 1.0)
                            </div>
                            <div style="font-size:0.82rem;color:#166534;">
                                Usuário humano. Formulário enviado normalmente.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#fef2f2;border-radius:8px;padding:1rem;border-left:3px solid #dc2626;">
                            <div style="font-weight:700;font-size:0.85rem;color:#991b1b;margin-bottom:0.4rem;">
                                <i class="fas fa-times-circle me-1"></i> Score baixo (0.0 – 0.4)
                            </div>
                            <div style="font-size:0.82rem;color:#991b1b;">
                                Possível bot. Envio bloqueado silenciosamente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
// Toggle secret key visibility
function toggleSecret() {
    var input = document.getElementById('recaptcha_secret');
    var icon  = document.getElementById('secretEye');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

// Score slider label
document.getElementById('recaptcha_threshold').addEventListener('input', function() {
    document.getElementById('thresholdVal').textContent = parseFloat(this.value).toFixed(1);
});
</script>
@endsection
