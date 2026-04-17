@extends('layouts.admin')
@section('title', 'Novo Usuário')
@section('page-title', 'Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Novo Usuário</li>
@endsection

@section('styles')
<style>
.pwd-strength { height: 4px; border-radius: 2px; margin-top: 6px; background: #e2e8f0; overflow: hidden; }
.pwd-strength-bar { height: 100%; border-radius: 2px; transition: all 0.3s; width: 0; }
.pwd-strength-label { font-size: 0.75rem; margin-top: 4px; font-weight: 600; }
.pwd-toggle { cursor: pointer; background: none; border: none; color: var(--hm-text-muted); padding: 0 10px; transition: color 0.2s; }
.pwd-toggle:hover { color: var(--hm-primary); }
.req { display: block; padding: 2px 0; }
.req.ok { color: #16a34a; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-user-plus me-2" style="color:var(--hm-primary);"></i>Novo Usuário
    </h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Dados pessoais --}}
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-id-card"></i> Dados Pessoais</span>
                </div>
                <div class="card-body">

                    {{-- Avatar upload --}}
                    <div class="d-flex align-items-center gap-4 mb-4 pb-3" style="border-bottom:1px solid var(--hm-border);">
                        <div style="position:relative;flex-shrink:0;">
                            <div id="avatarPreviewWrap"
                                 style="width:80px;height:80px;border-radius:14px;background:linear-gradient(135deg,var(--hm-primary),var(--hm-primary-dark));color:#fff;font-size:2rem;font-weight:700;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(255,107,0,0.3);overflow:hidden;cursor:pointer;"
                                 onclick="document.getElementById('avatarInput').click()" title="Clique para adicionar foto">
                                <span id="avatarInitials">?</span>
                                <img id="avatarPreview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;">
                            </div>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:0.88rem;margin-bottom:0.3rem;">Foto de Perfil</div>
                            <div style="font-size:0.8rem;color:var(--hm-text-muted);margin-bottom:0.6rem;">JPG, PNG ou WebP. Máx. 2MB.</div>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-camera"></i> Escolher Foto
                            </button>
                            <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp"
                                   class="d-none" onchange="previewAvatar(this)">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="nameInput"
                                       value="{{ old('name') }}" required placeholder="Nome completo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email"
                                           value="{{ old('email') }}" required placeholder="email@exemplo.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Função</label>
                                <select class="form-control" name="role">
                                    <option value="user">Usuário</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="phone" id="createPhone"
                                           value="{{ old('phone') }}" placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Bio / Observações</label>
                        <textarea class="form-control" name="bio" rows="2"
                                  placeholder="Breve descrição sobre o usuário..."
                                  maxlength="500">{{ old('bio') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Senha --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-lock"></i> Senha de Acesso</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Senha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password"
                                           id="password" required placeholder="Mínimo 8 caracteres"
                                           autocomplete="new-password">
                                    <button type="button" class="pwd-toggle input-group-text" onclick="togglePwd('password','eye1')">
                                        <i class="fas fa-eye" id="eye1"></i>
                                    </button>
                                </div>
                                <div class="pwd-strength"><div class="pwd-strength-bar" id="strengthBar"></div></div>
                                <div class="pwd-strength-label" id="strengthLabel"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirmar Senha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirmation"
                                           id="password_confirmation" required placeholder="Repita a senha"
                                           autocomplete="new-password">
                                    <button type="button" class="pwd-toggle input-group-text" onclick="togglePwd('password_confirmation','eye2')">
                                        <i class="fas fa-eye" id="eye2"></i>
                                    </button>
                                </div>
                                <div class="mt-1" id="matchMsg" style="font-size:0.75rem;display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <div style="background:#f8fafc;border-radius:8px;padding:0.85rem 1rem;font-size:0.8rem;color:var(--hm-text-muted);">
                        <div style="font-weight:700;margin-bottom:0.4rem;color:var(--hm-text);">
                            <i class="fas fa-shield-alt me-1" style="color:var(--hm-primary);"></i>
                            Requisitos de senha segura:
                        </div>
                        <div class="row g-1">
                            <div class="col-sm-6"><span id="req-len"   class="req">✗ Mínimo 8 caracteres</span></div>
                            <div class="col-sm-6"><span id="req-upper" class="req">✗ Letra maiúscula</span></div>
                            <div class="col-sm-6"><span id="req-lower" class="req">✗ Letra minúscula</span></div>
                            <div class="col-sm-6"><span id="req-num"   class="req">✗ Número</span></div>
                            <div class="col-sm-6"><span id="req-spec"  class="req">✗ Caractere especial (!@#$...)</span></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Criar Usuário
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </div>

        </div>

        {{-- Dicas --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-info-circle"></i> Informações</span>
                </div>
                <div class="card-body" style="font-size:0.85rem;color:var(--hm-text-muted);line-height:1.7;">
                    <p><i class="fas fa-shield-alt me-2" style="color:var(--hm-primary);"></i>
                    <strong>Administrador</strong> tem acesso total ao painel.</p>
                    <p><i class="fas fa-user me-2" style="color:#0891b2;"></i>
                    <strong>Usuário</strong> tem acesso limitado.</p>
                    <p><i class="fas fa-envelope me-2" style="color:#16a34a;"></i>
                    O e-mail será usado para login e notificações.</p>
                    <p class="mb-0"><i class="fas fa-key me-2" style="color:#d97706;"></i>
                    Use uma senha forte com letras, números e símbolos.</p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
// ── Avatar preview ────────────────────────────────────────
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        HMToast.error('A imagem deve ter no máximo 2MB.');
        input.value = '';
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('avatarInitials').style.display = 'none';
        var img = document.getElementById('avatarPreview');
        img.src = e.target.result;
        img.style.display = 'block';
        HMToast.success('Foto selecionada!', 2000);
    };
    reader.readAsDataURL(file);
}

// Atualizar iniciais ao digitar nome
document.getElementById('nameInput').addEventListener('input', function() {
    var name = this.value.trim();
    var initials = document.getElementById('avatarInitials');
    var preview  = document.getElementById('avatarPreview');
    if (initials && preview && preview.style.display === 'none') {
        var parts = name.split(' ').filter(Boolean);
        if (parts.length >= 2) {
            initials.textContent = (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
        } else if (parts.length === 1) {
            initials.textContent = parts[0].slice(0,2).toUpperCase();
        } else {
            initials.textContent = '?';
        }
    }
});

// ── Máscara telefone ──────────────────────────────────────
document.getElementById('createPhone').addEventListener('input', function() {
    var v = this.value.replace(/\D/g, '').slice(0, 11);
    this.value = v.length <= 10
        ? v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
        : v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
});

// ── Toggle senha ──────────────────────────────────────────
function togglePwd(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

document.getElementById('password').addEventListener('input', function() {
    var pwd = this.value;
    var checks = {
        len:   pwd.length >= 8,
        upper: /[A-Z]/.test(pwd),
        lower: /[a-z]/.test(pwd),
        num:   /[0-9]/.test(pwd),
        spec:  /[^A-Za-z0-9]/.test(pwd),
    };
    setReq('req-len',   checks.len,   '✓ Mínimo 8 caracteres',         '✗ Mínimo 8 caracteres');
    setReq('req-upper', checks.upper, '✓ Letra maiúscula',              '✗ Letra maiúscula');
    setReq('req-lower', checks.lower, '✓ Letra minúscula',              '✗ Letra minúscula');
    setReq('req-num',   checks.num,   '✓ Número',                       '✗ Número');
    setReq('req-spec',  checks.spec,  '✓ Caractere especial (!@#$...)', '✗ Caractere especial (!@#$...)');

    var score  = Object.values(checks).filter(Boolean).length;
    var colors = ['#e2e8f0','#dc2626','#d97706','#16a34a','#16a34a','#16a34a'];
    var labels = ['','Muito fraca','Fraca','Boa','Forte','Muito forte'];
    var widths = ['0%','20%','40%','60%','80%','100%'];

    document.getElementById('strengthBar').style.width      = pwd.length ? widths[score] : '0%';
    document.getElementById('strengthBar').style.background = colors[score];
    document.getElementById('strengthLabel').textContent    = pwd.length ? labels[score] : '';
    document.getElementById('strengthLabel').style.color    = colors[score];
    checkMatch();
});

document.getElementById('password_confirmation').addEventListener('input', checkMatch);

function setReq(id, ok, okText, failText) {
    var el = document.getElementById(id);
    el.textContent = ok ? okText : failText;
    el.className   = 'req' + (ok ? ' ok' : '');
}

function checkMatch() {
    var pwd  = document.getElementById('password').value;
    var conf = document.getElementById('password_confirmation').value;
    var msg  = document.getElementById('matchMsg');
    if (!conf) { msg.style.display = 'none'; return; }
    msg.innerHTML = pwd === conf
        ? '<span style="color:#16a34a;"><i class="fas fa-check-circle me-1"></i>Senhas coincidem!</span>'
        : '<span style="color:#dc2626;"><i class="fas fa-times-circle me-1"></i>Senhas não coincidem.</span>';
    msg.style.display = 'block';
}
</script>
@endsection
