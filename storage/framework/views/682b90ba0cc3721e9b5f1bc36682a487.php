
<?php $__env->startSection('title', 'Mensagem'); ?>
<?php $__env->startSection('page-title', 'Mensagens'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.contact.index')); ?>">Mensagens</a></li>
    <li class="breadcrumb-item active">Visualizar</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isObj   = is_object($message);
    $id      = $isObj ? $message->id         : $message['id'];
    $name    = $isObj ? $message->name       : $message['name'];
    $email   = $isObj ? $message->email      : $message['email'];
    $phone   = $isObj ? ($message->phone ?? null) : ($message['phone'] ?? null);
    $subject = $isObj ? $message->subject    : $message['subject'];
    $body    = $isObj ? $message->message    : $message['message'];
    $read    = $isObj ? $message->read       : ($message['status'] === 'replied');
    $date    = $isObj ? $message->created_at : $message['created_at'];
    $date    = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
?>

<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope-open me-2" style="color:var(--hm-primary);"></i><?php echo e($subject); ?></h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.contact.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-envelope"></i> Mensagem Recebida</span>
                <div class="card-tools">
                    <?php if(!$read): ?>
                        <span class="badge badge-warning">Nova</span>
                    <?php else: ?>
                        <span class="badge badge-success">Lida</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-4" style="font-size:0.87rem;">
                    <tr><td class="font-weight-bold" width="25%">Nome</td><td><?php echo e($name); ?></td></tr>
                    <tr><td class="font-weight-bold">E-mail</td><td><a href="mailto:<?php echo e($email); ?>" style="color:var(--hm-primary);"><?php echo e($email); ?></a></td></tr>
                    <?php if($phone): ?>
                    <tr><td class="font-weight-bold">Telefone</td><td><?php echo e($phone); ?></td></tr>
                    <?php endif; ?>
                    <tr><td class="font-weight-bold">Data</td><td><?php echo e($date->format('d/m/Y H:i')); ?></td></tr>
                </table>
                <label class="mb-2">Mensagem:</label>
                <div class="p-3" style="background:#f8f9fa;border-radius:8px;line-height:1.7;font-size:0.9rem;">
                    <?php echo e($body); ?>

                </div>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-reply"></i> Responder</span>
            </div>
            <form method="POST" action="<?php echo e(route('admin.contact.reply', $id)); ?>">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="form-group">
                        <label>Sua Resposta <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reply" rows="6" required
                                  placeholder="Digite sua resposta..."><?php echo e(old('reply')); ?></textarea>
                    </div>
                    <small class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        A resposta será enviada para <strong><?php echo e($email); ?></strong> via e-mail.
                    </small>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Resposta</button>
                    <a href="<?php echo e(route('admin.contact.index')); ?>" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-user"></i> Remetente</span></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:48px;height:48px;border-radius:50%;background:var(--hm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;flex-shrink:0;">
                        <?php echo e(strtoupper(substr($name, 0, 1))); ?>

                    </div>
                    <div>
                        <div style="font-weight:700;"><?php echo e($name); ?></div>
                        <div style="font-size:0.82rem;color:var(--hm-text-muted);"><?php echo e($email); ?></div>
                    </div>
                </div>
                <a href="mailto:<?php echo e($email); ?>" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-envelope"></i> Enviar E-mail Direto
                </a>
                <?php if($phone): ?>
                <a href="tel:<?php echo e($phone); ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    <i class="fas fa-phone"></i> <?php echo e($phone); ?>

                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\contact\show.blade.php ENDPATH**/ ?>