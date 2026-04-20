
<?php $__env->startSection('title', 'Editar: ' . $user->name); ?>
<?php $__env->startSection('page-title', 'Usuários'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Usuários</a></li>
    <li class="breadcrumb-item active">Editar</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-user-edit me-2" style="color:var(--hm-primary);"></i>
        Editar: <?php echo e($user->name); ?>

        <?php if($user->id === auth()->id()): ?>
            <span class="badge badge-primary ms-2" style="font-size:0.7rem;vertical-align:middle;">Você</span>
        <?php endif; ?>
    </h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="<?php echo e(route('admin.users.update', $user->id)); ?>" id="userForm" enctype="multipart/form-data">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="row g-4">

    
    <div class="col-lg-3">

        
        <div class="card text-center mb-3">
            <div class="card-body py-4">
                <div class="mb-4">
                    <label class="form-label d-block mb-3" style="font-weight:600;">Foto de Perfil</label>
                    <?php if (isset($component)) { $__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filepond','data' => ['name' => 'avatar','value' => $user->avatar ? $user->avatar_url : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filepond'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'avatar','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->avatar ? $user->avatar_url : null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5)): ?>
<?php $attributes = $__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5; ?>
<?php unset($__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5)): ?>
<?php $component = $__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5; ?>
<?php unset($__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5); ?>
<?php endif; ?>
                    <input type="hidden" name="remove_avatar" id="removeAvatar" value="0">
                </div>

                <div style="font-weight:700;font-size:1.2rem;margin-bottom:0.25rem;"><?php echo e($user->name); ?></div>
                <div style="font-size:0.88rem;color:var(--hm-text-muted);"><?php echo e($user->email); ?></div>
                <div class="mt-2">
                    <?php if($user->role === 'admin'): ?>
                        <span class="badge badge-danger" style="font-size:0.75rem;"><i class="fas fa-shield-alt me-1"></i>Administrador</span>
                    <?php else: ?>
                        <span class="badge badge-info" style="font-size:0.75rem;"><i class="fas fa-user me-1"></i>Usuário</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle"></i> Informações</span>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <i class="fas fa-calendar-plus"></i>
                    <div>
                        <div class="lbl">Cadastrado em</div>
                        <div class="val"><?php echo e($user->created_at->format('d/m/Y H:i')); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <div class="lbl">E-mail verificado</div>
                        <div class="val">
                            <?php if($user->email_verified_at): ?>
                                <span style="color:#16a34a;"><?php echo e($user->email_verified_at->format('d/m/Y')); ?></span>
                            <?php else: ?>
                                <span style="color:#d97706;">Pendente</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-hashtag"></i>
                    <div>
                        <div class="lbl">ID do usuário</div>
                        <div class="val">#<?php echo e($user->id); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <div class="lbl">Última atualização</div>
                        <div class="val"><?php echo e($user->updated_at->diffForHumans()); ?></div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if($user->id !== auth()->id()): ?>
        <div class="card mt-3" style="border:1px solid #fecaca !important;">
            <div class="card-header" style="background:linear-gradient(135deg,#dc2626,#b91c1c) !important;">
                <span class="card-title"><i class="fas fa-exclamation-triangle"></i> Zona de Perigo</span>
            </div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--hm-text-muted);margin-bottom:0.75rem;">
                    Excluir este usuário é uma ação irreversível.
                </p>
                <form method="POST" action="<?php echo e(route('admin.users.destroy', $user->id)); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm w-100 btn-delete"
                            data-name="<?php echo e($user->name); ?>">
                        <i class="fas fa-trash"></i> Excluir Usuário
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>

    
    <div class="col-lg-9">

            <?php if($errors->any()): ?>
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0 mt-1"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>

            
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
                                       value="<?php echo e(old('name', $user->name)); ?>" required
                                       placeholder="Nome completo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email"
                                           value="<?php echo e(old('email', $user->email)); ?>" required
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
                                        <?php echo e($user->id === auth()->id() ? 'disabled' : ''); ?>>
                                    <option value="user"  <?php echo e($user->role === 'user'  ? 'selected' : ''); ?>>Usuário</option>
                                    <option value="admin" <?php echo e($user->role === 'admin' ? 'selected' : ''); ?>>Administrador</option>
                                </select>
                                <?php if($user->id === auth()->id()): ?>
                                    <input type="hidden" name="role" value="<?php echo e($user->role); ?>">
                                    <small class="form-text">Você não pode alterar sua própria função.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="phone" id="editPhone"
                                           value="<?php echo e(old('phone', $user->phone ?? '')); ?>"
                                           placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label>Bio / Observações</label>
                        <textarea class="form-control" name="bio" rows="2"
                                  placeholder="Breve descrição sobre o usuário..."
                                  maxlength="500"><?php echo e(old('bio', $user->bio ?? '')); ?></textarea>
                    </div>
                </div>
            </div>

            
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
                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
    </div>

    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<style>
.req { display: block; padding: 2px 0; }
.req.ok  { color: #16a34a; }
.req.ok::first-letter { content: '✓'; }
</style>
<script>
// ── Avatar preview (Obsoleto → Substituído por FilePond) ─────────────────
// Gerenciado agora via componente x-filepond


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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\users\edit.blade.php ENDPATH**/ ?>