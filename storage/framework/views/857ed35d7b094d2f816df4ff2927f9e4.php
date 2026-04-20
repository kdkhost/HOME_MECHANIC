

<?php $__env->startSection('title', 'Editar Serviço - HomeMechanic'); ?>
<?php $__env->startSection('page-title', 'Editar Serviço'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard.index')); ?>">Dashboard</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.services.index')); ?>">Serviços</a></li>
<li class="breadcrumb-item active">Editar: <?php echo e($service->title); ?></li>
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
                    <i class="bi bi-pencil mr-2"></i>
                    Editar Serviço: <?php echo e($service->title); ?>

                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <form id="serviceForm" action="<?php echo e(route('admin.services.update', $service)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="card-body">
                    <?php echo $__env->make('modules.services._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Atualizar Serviço
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
    
    // Preencher formulário com dados do serviço
    const service = <?php echo json_encode($service, 15, 512) ?>;
    
    $('#title').val(service.title);
    $('#slug').val(service.slug);
    $('#description').val(service.description);
    $('#content').val(service.content);
    $('#icon').val(service.icon);
    $('#featured').prop('checked', service.featured);
    $('#sort_order').val(service.sort_order);
    $('#active').prop('checked', service.active);
    
    if (service.icon) {
        $('#iconPreview').attr('class', `bi ${service.icon}`);
    }
    
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
                HMToast.error(data.message || 'Erro ao atualizar serviço');
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\services\edit.blade.php ENDPATH**/ ?>