
<?php $__env->startSection('title', 'Detalhes do Usuário'); ?>
<?php $__env->startSection('page-title', 'Usuários'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Usuários</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $uid   = $user->id   ?? $user['id'];
    $uname = $user->name  ?? $user['name'];
    $uemail= $user->email ?? $user['email'];
    $urole = $user->role  ?? $user['role'];
    $udate = $user->created_at ?? $user['created_at'];
    $udate = is_string($udate) ? \Carbon\Carbon::parse($udate) : $udate;
?>

<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-user mr-2" style="color:var(--hm-primary);"></i><?php echo e($uname); ?></h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.users.edit', $uid)); ?>" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Editar</a>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-id-card"></i> Informações do Usuário</span>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0" style="font-size:0.87rem;">
                    <tr><td class="font-weight-bold" width="35%">Nome</td><td><?php echo e($uname); ?></td></tr>
                    <tr><td class="font-weight-bold">E-mail</td><td><?php echo e($uemail); ?></td></tr>
                    <tr>
                        <td class="font-weight-bold">Função</td>
                        <td>
                            <?php if($urole === 'admin'): ?>
                                <span class="badge badge-danger">Administrador</span>
                            <?php else: ?>
                                <span class="badge badge-info">Usuário</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><td class="font-weight-bold">Cadastrado em</td><td><?php echo e($udate->format('d/m/Y H:i')); ?></td></tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\users\show.blade.php ENDPATH**/ ?>