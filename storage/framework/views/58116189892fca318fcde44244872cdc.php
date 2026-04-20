<?php $__env->startSection('title', 'Relatorio Analytics'); ?>
<?php $__env->startSection('page-title', 'Relatorio Analytics'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.analytics.index')); ?>">Analytics</a></li>
    <li class="breadcrumb-item active">Relatorio</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-file-alt"></i> Relatorio de <?php echo e($data['period']['start']); ?> a <?php echo e($data['period']['end']); ?></span>
        <div class="card-tools">
            <a href="<?php echo e(route('admin.analytics.index')); ?>" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-center p-3 border rounded">
                    <div style="font-size:1.8rem;font-weight:700;color:var(--hm-primary);"><?php echo e(number_format($data['summary']['total_visits'])); ?></div>
                    <div class="text-muted">Visitas Totais</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 border rounded">
                    <div style="font-size:1.8rem;font-weight:700;color:#28a745;"><?php echo e(number_format($data['summary']['unique_visits'])); ?></div>
                    <div class="text-muted">Visitas Unicas</div>
                </div>
            </div>
        </div>

        <?php if(count($data['summary']['top_pages'])): ?>
        <h6 class="fw-bold mt-4 mb-2">Paginas Mais Visitadas</h6>
        <table class="table table-sm table-bordered">
            <thead><tr><th>URL</th><th class="text-end" style="width:100px;">Visitas</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $data['summary']['top_pages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><td><?php echo e($page->url); ?></td><td class="text-end fw-bold"><?php echo e(number_format($page->visits)); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if(count($data['summary']['top_countries'])): ?>
        <h6 class="fw-bold mt-4 mb-2">Paises</h6>
        <table class="table table-sm table-bordered">
            <thead><tr><th>Pais</th><th class="text-end" style="width:100px;">Visitas</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $data['summary']['top_countries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><td><?php echo e($country->country); ?></td><td class="text-end fw-bold"><?php echo e(number_format($country->visits)); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\analytics\report.blade.php ENDPATH**/ ?>