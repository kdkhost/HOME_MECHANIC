

<?php $__env->startSection('title', $service->title . ' - HomeMechanic'); ?>
<?php $__env->startSection('page-title', $service->title); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard.index')); ?>">Dashboard</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.services.index')); ?>">Serviços</a></li>
<li class="breadcrumb-item active"><?php echo e($service->title); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?php if($service->icon): ?>
                        <i class="bi <?php echo e($service->icon); ?> mr-2"></i>
                    <?php endif; ?>
                    <?php echo e($service->title); ?>

                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.services.edit', $service)); ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if($service->cover_image_url): ?>
                    <div class="mb-3">
                        <img src="<?php echo e($service->cover_image_url); ?>" class="img-fluid rounded" alt="<?php echo e($service->title); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <h5>Descrição</h5>
                    <p class="text-muted"><?php echo e($service->description); ?></p>
                </div>
                
                <?php if($service->content): ?>
                    <div class="mb-3">
                        <h5>Conteúdo Completo</h5>
                        <div class="content">
                            <?php echo nl2br(e($service->content)); ?>

                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <h5>Informações Técnicas</h5>
                    <dl class="row">
                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9"><code><?php echo e($service->slug); ?></code></dd>
                        
                        <dt class="col-sm-3">Ordem:</dt>
                        <dd class="col-sm-9"><?php echo e($service->sort_order); ?></dd>
                        
                        <dt class="col-sm-3">Criado em:</dt>
                        <dd class="col-sm-9"><?php echo e($service->created_at->format('d/m/Y H:i')); ?></dd>
                        
                        <dt class="col-sm-3">Atualizado em:</dt>
                        <dd class="col-sm-9"><?php echo e($service->updated_at->format('d/m/Y H:i')); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Status</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="font-weight-bold">Status Atual:</label>
                    <?php if($service->active): ?>
                        <span class="badge badge-success ml-2">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-secondary ml-2">Inativo</span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label class="font-weight-bold">Destaque:</label>
                    <?php if($service->featured): ?>
                        <span class="badge badge-warning ml-2">Em Destaque</span>
                    <?php else: ?>
                        <span class="badge badge-light ml-2">Sem Destaque</span>
                    <?php endif; ?>
                </div>
                
                <?php if($service->icon): ?>
                    <div class="mb-3">
                        <label class="font-weight-bold">Ícone:</label>
                        <div class="mt-1">
                            <i class="bi <?php echo e($service->icon); ?>" style="font-size: 2rem;"></i>
                            <code class="ml-2"><?php echo e($service->icon); ?></code>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ações Rápidas</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-<?php echo e($service->active ? 'warning' : 'success'); ?> btn-block" 
                            onclick="toggleActive(<?php echo e($service->id); ?>)">
                        <i class="bi bi-<?php echo e($service->active ? 'pause' : 'play'); ?>"></i>
                        <?php echo e($service->active ? 'Desativar' : 'Ativar'); ?>

                    </button>
                    
                    <button class="btn btn-outline-<?php echo e($service->featured ? 'secondary' : 'warning'); ?> btn-block" 
                            onclick="toggleFeatured(<?php echo e($service->id); ?>)">
                        <i class="bi bi-star<?php echo e($service->featured ? '-fill' : ''); ?>"></i>
                        <?php echo e($service->featured ? 'Remover Destaque' : 'Destacar'); ?>

                    </button>
                    
                    <a href="<?php echo e(route('admin.services.edit', $service)); ?>" class="btn btn-primary btn-block">
                        <i class="bi bi-pencil"></i> Editar Serviço
                    </a>
                    
                    <button class="btn btn-danger btn-block" onclick="deleteService(<?php echo e($service->id); ?>)">
                        <i class="bi bi-trash"></i> Excluir Serviço
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function toggleActive(id) {
    try {
        const response = await fetch(`<?php echo e(route('admin.services.index')); ?>/${id}/toggle-active`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            HMToast.success(data.message);
            
            setTimeout(() => location.reload(), 1500);
        } else {
            HMToast.error('Erro ao alterar status');
        }
    } catch (error) {
        console.error('Erro:', error);
        HMToast.error('Erro de conexão');
    }
}

async function toggleFeatured(id) {
    try {
        const response = await fetch(`<?php echo e(route('admin.services.index')); ?>/${id}/toggle-featured`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            HMToast.success(data.message);
            
            setTimeout(() => location.reload(), 1500);
        } else {
            HMToast.error('Erro ao alterar destaque');
        }
    } catch (error) {
        console.error('Erro:', error);
        HMToast.error('Erro de conexão');
    }
}

async function deleteService(id) {
    const result = await Swal.fire({
        title: 'Confirmar Exclusão',
        text: 'Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`<?php echo e(route('admin.services.index')); ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            HMToast.success(data.message);
            
            setTimeout(() => {
                window.location.href = '<?php echo e(route("admin.services.index")); ?>';
            }, 1500);
        } else {
            HMToast.error('Erro ao excluir serviço');
        }
    } catch (error) {
        console.error('Erro:', error);
        HMToast.error('Erro de conexão');
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\services\show.blade.php ENDPATH**/ ?>