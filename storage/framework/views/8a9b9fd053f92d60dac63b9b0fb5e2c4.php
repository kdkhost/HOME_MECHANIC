
<?php $__env->startSection('title', 'Novo Usuário'); ?>
<?php $__env->startSection('page-title', 'Usuários'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Usuários</a></li>
    <li class="breadcrumb-item active">Novo Usuário</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.pwd-strength { height: 4px; border-radius: 2px; margin-top: 6px; background: #e2e8f0; overflow: hidden; }
.pwd-strength-bar { height: 100%; border-radius: 2px; transition: all 0.3s; width: 0; }
.pwd-strength-label { font-size: 0.75rem; margin-top: 4px; font-weight: 600; }
.pwd-toggle { cursor: pointer; background: none; border: none; color: var(--hm-text-muted); padding: 0 10px; transition: color 0.2s; }
.pwd-toggle:hover { color: var(--hm-primary); }
.req { display: block; padding: 2px 0; }
.req.ok { color: #16a34a; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-user-plus me-2" style="color:var(--hm-primary);"></i>Novo Usuário
    </h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="<?php echo e(route('admin.users.store')); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0 mt-1"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">

            
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-id-card"></i> Dados Pessoais</span>
                </div>
                <div class="card-body">

                    <div class="row align-items-center mb-4 pb-3" style="border-bottom:1px solid var(--hm-border);">
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold">Foto de Perfil</label>
                            <small class="d-block text-muted mb-2">JPG, PNG ou WebP. Máx. 2MB.</small>
                        </div>
                        <div class="col-md-9">
                            <?php if (isset($component)) { $__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filepond','data' => ['name' => 'avatar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filepond'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'avatar']); ?>
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
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="nameInput"
                                       value="<?php echo e(old('name')); ?>" required placeholder="Nome completo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email"
                                           value="<?php echo e(old('email')); ?>" required placeholder="email@exemplo.com">
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
                                           value="<?php echo e(old('phone')); ?>" placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Bio / Observações</label>
                        <textarea class="form-control" name="bio" rows="2"
                                  placeholder="Breve descrição sobre o usuário..."
                                  maxlength="500"><?php echo e(old('bio')); ?></textarea>
                    </div>
                </div>
            </div>

            
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
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </div>

        </div>

        
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// ── Avatar preview (Obsoleto → Substituído por FilePond) ─────────────────
// O componente x-filepond gerencia o upload e preview automaticamente.


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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\users\create.blade.php ENDPATH**/ ?>