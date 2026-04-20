
<?php $__env->startSection('title', 'Templates de E-mail'); ?>
<?php $__env->startSection('page-title', 'Configurações'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.settings.email')); ?>">E-mail</a></li>
    <li class="breadcrumb-item active">Templates</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope-open-text me-2" style="color:var(--hm-primary);"></i>Templates de E-mail</h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.settings.email')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row g-4">
    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex gap-3 align-items-start">
                <div style="width:48px;height:48px;background:var(--hm-primary-light);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-envelope" style="color:var(--hm-primary);font-size:1.2rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div style="font-weight:700;font-size:0.95rem;color:var(--hm-text);margin-bottom:0.3rem;"><?php echo e($tpl['name']); ?></div>
                    <p style="font-size:0.84rem;color:var(--hm-text-muted);margin-bottom:0.75rem;"><?php echo e($tpl['desc']); ?></p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <?php $__currentLoopData = $tpl['vars']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <code style="background:#f1f5f9;color:var(--hm-primary);padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;"><?php echo e($var); ?></code>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <a href="<?php echo e(route('admin.settings.email.templates.edit', $slug)); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-pencil-alt"></i> Editar Template
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\settings\email-templates.blade.php ENDPATH**/ ?>