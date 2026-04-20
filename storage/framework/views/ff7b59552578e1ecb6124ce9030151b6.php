
<?php $__env->startSection('title', 'Mensagens'); ?>
<?php $__env->startSection('page-title', 'Mensagens de Contato'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Mensagens</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope me-2" style="color:var(--hm-primary);"></i>Mensagens Recebidas</h2>
</div>


<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Buscar por nome, e-mail ou assunto..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Todas</option>
                    <option value="new"     <?php echo e(request('status') === 'new'     ? 'selected' : ''); ?>>Não lidas</option>
                    <option value="replied" <?php echo e(request('status') === 'replied' ? 'selected' : ''); ?>>Lidas</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search"></i> Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('admin.contact.index')); ?>" class="btn btn-secondary btn-sm w-100"><i class="fas fa-times"></i> Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-inbox"></i> Caixa de Entrada</span>
        <div class="card-tools">
            <span style="font-size:0.78rem;color:rgba(255,255,255,0.7);">
                <?php echo e(method_exists($messages, 'total') ? $messages->total() : count($messages)); ?> mensagem(ns)
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if(count($messages) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Assunto</th>
                        <th>Data</th>
                        <th style="width:100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isObj = is_object($msg);
                        $id      = $isObj ? $msg->id         : $msg['id'];
                        $name    = $isObj ? $msg->name       : $msg['name'];
                        $email   = $isObj ? $msg->email      : $msg['email'];
                        $subject = $isObj ? $msg->subject    : $msg['subject'];
                        $read    = $isObj ? $msg->read       : ($msg['status'] === 'replied');
                        $date    = $isObj ? $msg->created_at : $msg['created_at'];
                        $date    = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
                    ?>
                    <tr style="<?php echo e(!$read ? 'font-weight:600;' : ''); ?>">
                        <td>
                            <?php if(!$read): ?>
                                <span class="badge badge-warning">Nova</span>
                            <?php else: ?>
                                <span class="badge badge-success">Lida</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($name); ?></td>
                        <td><a href="mailto:<?php echo e($email); ?>" style="color:var(--hm-primary);"><?php echo e($email); ?></a></td>
                        <td><?php echo e(Str::limit($subject, 40)); ?></td>
                        <td><?php echo e($date->format('d/m/Y H:i')); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.contact.show', $id)); ?>" class="btn btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.contact.destroy', $id)); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-delete" title="Excluir"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if(method_exists($messages, 'links')): ?>
        <div class="px-3 py-2"><?php echo e($messages->withQueryString()->links()); ?></div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5>Nenhuma mensagem</h5>
            <p>As mensagens de contato aparecerão aqui.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\contact\index.blade.php ENDPATH**/ ?>