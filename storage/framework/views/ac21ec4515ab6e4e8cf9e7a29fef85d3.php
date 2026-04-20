

<?php $__env->startSection('title', 'Novo Serviço - HomeMechanic'); ?>
<?php $__env->startSection('page-title', 'Novo Serviço'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard.index')); ?>">Dashboard</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.services.index')); ?>">Serviços</a></li>
<li class="breadcrumb-item active">Novo Serviço</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-plus mr-2"></i>
                    Criar Novo Serviço
                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <form id="serviceForm" action="<?php echo e(route('admin.services.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <?php echo $__env->make('modules.services._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Criar Serviço
                    </button>
                    <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-secondary ml-2">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
$(document).ready(function() {
    
    $('#serviceForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                HMToast.success(data.message);
                
                setTimeout(() => {
                    window.location.href = '<?php echo e(route("admin.services.index")); ?>';
                }, 1500);
            } else {
                HMToast.error(data.message || 'Erro ao criar serviço');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            HMToast.error('Erro de conexão');
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\services\create.blade.php ENDPATH**/ ?>