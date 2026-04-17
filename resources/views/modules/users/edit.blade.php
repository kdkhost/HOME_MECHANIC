@extends('layouts.admin')
@section('title', 'Editar: ' . $user->name)
@section('page-title', 'Usuários')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('styles')
<style>
/* Avatar */
.user-avatar-wrap { position: relative; display: inline-block; cursor: pointer; }
.user-avatar-wrap:hover .avatar-overlay { opacity: 1; }
.avatar-overlay {
    position: absolute; inset: 0; border-radius: 16px;
    background: rgba(0,0,0,0.45); display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 4px;
    opacity: 0; transition: opacity 0.2s; color: #fff; font-size: 0.72rem; font-weight: 600;
}
.avatar-overlay i { font-size: 1.2rem; }
.user-avatar {
    width: 96px; height: 96px; border-radius: 16px;
    background: linear-gradient(135deg, var(--hm-primary), var(--hm-primary-dark));
    color: #fff; font-size: 2.5rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(255,107,0,0.3);
}
.user-avatar-img {
    width: 96px; height: 96px; border-radius: 16px;
    object-fit: cover; box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

/* Força da senha */
.pwd-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: all 0.3s; background: #e2e8f0; overflow: hidden; }
.pwd-strength-bar { height: 100%; border-radius: 2px; transition: all 0.3s; width: 0; }
.pwd-strength-label { font-size: 0.75rem; margin-top: 4px; font-weight: 600; }

/* Toggle senha */
.pwd-toggle {
    cursor: pointer; background: none; border: none;
    color: var(--hm-text-muted); padding: 0 10px;
    transition: color 0.2s;
}
.pwd-toggle:hover { color: var(--hm-primary); }

/* Info card */
.info-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.65rem 0; border-bottom: 1px solid var(--hm-border);
    font-size: 0.85rem;
}
.info-item:last-child { border-bottom: none; }
.info-item i { width: 18px; text-align: center; color: var(--hm-primary); flex-shrink: 0; }
.info-item .lbl { color: var(--hm-text-muted); font-size: 0.75rem; }
.info-item .val { font-weight: 600; color: var(--hm-text); }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-user-edit me-2" style="color:var(--hm-primary);"></i>
        Editar: {{ $user->name }}
        @if($user->id === auth()->id())
            <span class="badge badge-primary ms-2" style="font-size:0.7rem;vertical-align:middle;">Você</span>
        @endif
    </h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- ── Coluna lateral: perfil ──────────────────────── --}}
    <div class="col-lg-3">

        {{-- Card de perfil --}}
        <div class="card text-center mb-3">
            <div class="card-body py-4">
                <div class="user-avatar-wrap mb-3 mx-auto" onclick="document.getElementById('avatarInput').click()" title="Clique para alterar foto">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" class="user-avatar-img mx-auto d-block" id="avatarPreview" alt="Avatar">
                    @else
                        <div class="user-avatar mx-auto" id="avatarInitials">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <img src="" class="user-avatar-img mx-auto d-block" id="avatarPreview" alt="Avatar" style="display:none!important;">
                    @endif
                    <div class="avatar-overlay" style="border-radius:16px;">
                        <i class="fas fa-camera"></i>
                        <span>Alterar foto</span>
                    </div>
                </div>

                {{-- Input oculto --}}
                <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp"
                       class="d-none" onchange="previewAvatar(this)">
                <input type="hidden" name="remove_avatar" id="removeAvatar" value="0">

                {{-- Botões de avatar --}}
                <div class="d-flex justify-content-center gap-2 mb-3" id="avatarBtns">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i> Foto
                    </button>
                    @if($user->avatar)
                    <button type="button" class="btn btn-danger btn-sm" id="btnRemoveAvatar" onclick="removeAvatar()">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </div>

                <div style="font-weight:700;font-size:1rem;">{{ $user->name }}</div>
                <div style="font-size:0.82rem;color:var(--hm-text-muted);">{{ $user->email }}</div>
                <div class="mt-2">
                    @if($user->role === 'admin')
                        <span class="badge badge-danger"><i class="fas fa-shield-alt me-1"></i>Administrador</span>
                    @else
                        <span class="badge badge-info"><i class="fas fa-user me-1"></i>Usuário</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Informações --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle"></i> Informações</span>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <i class="fas fa-calendar-plus"></i>
                    <div>
                        <div class="lbl">Cadastrado em</div>
                        <div class="val">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <div class="lbl">E-mail verificado</div>
                        <div class="val">
                            @if($user->email_verified_at)
                                <span style="color:#16a34a;">{{ $user->email_verified_at->format('d/m/Y') }}</span>
                            @else
                                <span style="color:#d97706;">Pendente</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-hashtag"></i>
                    <div>
                        <div class="lbl">ID do usuário</div>
                        <div class="val">#{{ $user->id }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <div class="lbl">Última atualização</div>
                        <div class="val">{{ $user->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ações perigosas --}}
        @if($user->id !== auth()->id())
        <div class="card mt-3" style="border:1px solid #fecaca !important;">
            <div class="card-header" style="background:linear-gradient(135deg,#dc2626,#b91c1c) !important;">
                <span class="card-title"><i class="fas fa-exclamation-triangle"></i> Zona de Perigo</span>
            </div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--hm-text-muted);margin-bottom:0.75rem;">
                    Excluir este usuário é uma ação irreversível.
                </p>
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100 btn-delete"
                            data-name="{{ $user->name }}">
                        <i class="fas fa-trash"></i> Excluir Usuário
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>

    {{-- ── Formulário principal ────────────────────────── --}}
    <div class="col-lg-9">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="userForm" enctype="multipart/form-data">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Dados pessoais --}}
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-id-card"></i> Dados Pessoais</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                       value="{{ old('name', $user->name) }}" required
                                       placeholder="Nome completo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email"
                                           value="{{ old('email', $user->email) }}" required
                                           placeholder="email@exemplo.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Função</label>
                                <select class="form-control" name="role"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>Usuário</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @if($user->id === auth()->id())
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <small class="form-text">Você não pode alterar sua própria função.</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="phone" id="editPhone"
                                           value="{{ old('phone', $user->phone ?? '') }}"
                                           placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label>Bio / Observações</label>
                        <textarea class="form-control" name="bio" rows="2"
                                  placeholder="Breve descrição sobre o usuário..."
                                  maxlength="500">{{ old('bio', $user->bio ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Segurança --}}
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-lock"></i> Segurança</span>
                    <div class="card-tools">
                        <span style="font-size:0.75rem;color:rgba(255,255,255,0.75);">Deixe em branco para manter a senha atual</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nova Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password"
                                           id="password" placeholder="Mínimo 8 caracteres"
                                           autocomplete="new-password">
                                    <button type="button" class="pwd-toggle input-group-text" onclick="togglePwd('password','eyeIcon1')">
                                        <i class="fas fa-eye" id="eyeIcon1"></i>
                                    </button>
                                </div>
                                <div class="pwd-strength"><div class="pwd-strength-bar" id="strengthBar"></div></div>
                                <div class="pwd-strength-label" id="strengthLabel"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirmar Nova Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirmation"
                                           id="password_confirmation" placeholder="Repita a nova senha"
                                           autocomplete="new-password">
                                    <button type="button" class="pwd-toggle input-group-text" onclick="togglePwd('password_confirmation','eyeIcon2')">
                                        <i class="fas fa-eye" id="eyeIcon2"></i>
                                    </button>
                                </div>
                                <div class="mt-1" id="matchMsg" style="font-size:0.75rem;display:none;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Dicas de senha --}}
                    <div style="background:#f8fafc;border-radius:8px;padding:0.85rem 1rem;font-size:0.8rem;color:var(--hm-text-muted);">
                        <div style="font-weight:700;margin-bottom:0.4rem;color:var(--hm-text);">
                            <i class="fas fa-shield-alt me-1" style="color:var(--hm-primary);"></i>
                            Requisitos de senha segura:
                        </div>
                        <div class="row g-1">
                            <div class="col-sm-6"><span id="req-len"  class="req">✗ Mínimo 8 caracteres</span></div>
                            <div class="col-sm-6"><span id="req-upper" class="req">✗ Letra maiúscula</span></div>
                            <div class="col-sm-6"><span id="req-lower" class="req">✗ Letra minúscula</span></div>
                            <div class="col-sm-6"><span id="req-num"  class="req">✗ Número</span></div>
                            <div class="col-sm-6"><span id="req-spec" class="req">✗ Caractere especial (!@#$...)</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-transparent border-0 px-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<style>
.req { display: block; padding: 2px 0; }
.req.ok  { color: #16a34a; }
.req.ok::first-letter { content: '✓'; }
</style>
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
        // Esconder iniciais, mostrar imagem
        var initials = document.getElementById('avatarInitials');
        var preview  = document.getElementById('avatarPreview');
        if (initials) initials.style.display = 'none';
        if (preview) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            preview.style.removeProperty('display');
        }
        document.getElementById('removeAvatar').value = '0';
        HMToast.success('Foto selecionada. Salve para confirmar.', 3000);
    };
    reader.readAsDataURL(file);
}

function removeAvatar() {
    Swal.fire({
        title: 'Remover foto?',
        text: 'A foto de perfil será removida ao salvar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Remover',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (!r.isConfirmed) return;
        // Mostrar iniciais, esconder imagem
        var initials = document.getElementById('avatarInitials');
        var preview  = document.getElementById('avatarPreview');
        if (initials) initials.style.display = 'flex';
        if (preview)  preview.style.display  = 'none';
        document.getElementById('removeAvatar').value = '1';
        document.getElementById('avatarInput').value  = '';
        // Esconder botão remover
        var btn = document.getElementById('btnRemoveAvatar');
        if (btn) btn.style.display = 'none';
        HMToast.info('Foto marcada para remoção. Salve para confirmar.', 3000);
    });
}

// ── Máscara telefone ──────────────────────────────────────
var phoneEl = document.getElementById('editPhone');
if (phoneEl) {
    phoneEl.addEventListener('input', function() {
        var v = this.value.replace(/\D/g, '').slice(0, 11);
        this.value = v.length <= 10
            ? v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
            : v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    });
}

// ── Toggle senha ──────────────────────────────────────────
function togglePwd(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// ── Força da senha ────────────────────────────────────────
document.getElementById('password').addEventListener('input', function() {
    var pwd = this.value;
    var bar = document.getElementById('strengthBar');
    var lbl = document.getElementById('strengthLabel');

    var checks = {
        len:   pwd.length >= 8,
        upper: /[A-Z]/.test(pwd),
        lower: /[a-z]/.test(pwd),
        num:   /[0-9]/.test(pwd),
        spec:  /[^A-Za-z0-9]/.test(pwd),
    };

    // Atualizar requisitos
    setReq('req-len',   checks.len,   '✓ Mínimo 8 caracteres',       '✗ Mínimo 8 caracteres');
    setReq('req-upper', checks.upper, '✓ Letra maiúscula',            '✗ Letra maiúscula');
    setReq('req-lower', checks.lower, '✓ Letra minúscula',            '✗ Letra minúscula');
    setReq('req-num',   checks.num,   '✓ Número',                     '✗ Número');
    setReq('req-spec',  checks.spec,  '✓ Caractere especial (!@#$...)', '✗ Caractere especial (!@#$...)');

    var score = Object.values(checks).filter(Boolean).length;

    var colors = ['#e2e8f0', '#dc2626', '#d97706', '#16a34a', '#16a34a', '#16a34a'];
    var labels = ['',        'Muito fraca', 'Fraca', 'Boa', 'Forte', 'Muito forte'];
    var widths = ['0%',      '20%',         '40%',   '60%', '80%',   '100%'];

    bar.style.width      = pwd.length ? widths[score] : '0%';
    bar.style.background = colors[score];
    lbl.textContent      = pwd.length ? labels[score] : '';
    lbl.style.color      = colors[score];

    checkMatch();
});

function setReq(id, ok, okText, failText) {
    var el = document.getElementById(id);
    el.textContent = ok ? okText : failText;
    el.className   = 'req' + (ok ? ' ok' : '');
}

// ── Verificar se senhas coincidem ─────────────────────────
document.getElementById('password_confirmation').addEventListener('input', checkMatch);

function checkMatch() {
    var pwd  = document.getElementById('password').value;
    var conf = document.getElementById('password_confirmation').value;
    var msg  = document.getElementById('matchMsg');

    if (!conf) { msg.style.display = 'none'; return; }

    if (pwd === conf) {
        msg.innerHTML = '<span style="color:#16a34a;"><i class="fas fa-check-circle me-1"></i>Senhas coincidem!</span>';
    } else {
        msg.innerHTML = '<span style="color:#dc2626;"><i class="fas fa-times-circle me-1"></i>Senhas não coincidem.</span>';
    }
    msg.style.display = 'block';
}
</script>
@endsection
