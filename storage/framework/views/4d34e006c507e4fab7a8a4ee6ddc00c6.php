<?php $__env->startSection('title', 'Depoimentos'); ?>
<?php $__env->startSection('page-title', 'Depoimentos'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Depoimentos</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listagem de Depoimentos</h3>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="fas fa-plus"></i> Novo Depoimento
        </button>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-valign-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Avaliação</th>
                    <th>Depoimento</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="sortable">
                <?php $__empty_1 = true; $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr data-id="<?php echo e($t->id); ?>">
                    <td>
                        <i class="fas fa-grip-vertical text-muted me-2" style="cursor: grab;"></i>
                        <strong><?php echo e($t->name); ?></strong>
                        <?php if($t->role): ?><div class="small text-muted"><?php echo e($t->role); ?></div><?php endif; ?>
                    </td>
                    <td>
                        <div class="text-warning">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="<?php echo e($i <= $t->rating ? 'fas' : 'far'); ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td><small><?php echo e(Str::limit($t->content, 60)); ?></small></td>
                    <td class="text-center">
                        <div class="custom-control custom-switch d-inline-block">
                            <input type="checkbox" class="custom-control-input toggle-switch" 
                                   id="switch_<?php echo e($t->id); ?>" data-url="<?php echo e(route('admin.testimonials.toggle', $t->id)); ?>" 
                                   <?php echo e($t->is_active ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="switch_<?php echo e($t->id); ?>"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary btn-edit" 
                                data-id="<?php echo e($t->id); ?>" data-name="<?php echo e($t->name); ?>" data-role="<?php echo e($t->role); ?>" 
                                data-content="<?php echo e($t->content); ?>" data-rating="<?php echo e($t->rating); ?>" 
                                data-bs-toggle="modal" data-bs-target="#modalEdit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="<?php echo e(route('admin.testimonials.destroy', $t->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-name="<?php echo e($t->name); ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum depoimento cadastrado</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal ADD -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.testimonials.store')); ?>" method="POST" class="ajax-form">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">Novo Depoimento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome do Cliente *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Função ou Veículo</label>
                        <input type="text" name="role" class="form-control" placeholder="Ex: Dono de Golf GTI">
                    </div>
                    <div class="mb-3">
                        <label>Avaliação (Estrelas)</label>
                        <select name="rating" class="form-control">
                            <option value="5" selected>5 Estrelas</option>
                            <option value="4">4 Estrelas</option>
                            <option value="3">3 Estrelas</option>
                            <option value="2">2 Estrelas</option>
                            <option value="1">1 Estrela</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Depoimento *</label>
                        <textarea name="content" class="form-control" rows="3" required maxlength="300"></textarea>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="is_active" id="t_active" value="1" checked>
                        <label class="custom-control-label" for="t_active">Ativo no site</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT -->
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdit" method="POST" class="ajax-form">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-header"><h5 class="modal-title">Editar Depoimento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome do Cliente *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Função ou Veículo</label>
                        <input type="text" name="role" id="edit_role" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Avaliação (Estrelas)</label>
                        <select name="rating" id="edit_rating" class="form-control">
                            <option value="5">5 Estrelas</option>
                            <option value="4">4 Estrelas</option>
                            <option value="3">3 Estrelas</option>
                            <option value="2">2 Estrelas</option>
                            <option value="1">1 Estrela</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Depoimento *</label>
                        <textarea name="content" id="edit_content" class="form-control" rows="3" required maxlength="300"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(function(){
    // Setup Edit Modal
    $('.btn-edit').click(function(){
        $('#formEdit').attr('action', '<?php echo e(url('admin/testimonials')); ?>/' + $(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_role').val($(this).data('role'));
        $('#edit_rating').val($(this).data('rating'));
        $('#edit_content').val($(this).data('content'));
    });

    // Sortable
    var el = document.getElementById('sortable');
    if (el) {
        new window.Sortable(el, {
            animation: 150,
            ghostClass: 'bg-light',
            handle: '.fa-grip-vertical',
            onEnd: function () {
                var order = [];
                $('#sortable tr').each(function(){ order.push($(this).data('id')); });
                $.post('<?php echo e(route('admin.testimonials.reorder')); ?>', { order: order })
                 .done(function(res) { if(res.success) HMToast.success('Ordem atualizada!'); });
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\testimonials\index.blade.php ENDPATH**/ ?>