
<?php $__env->startSection('title', 'Visualizar Post'); ?>
<?php $__env->startSection('page-title', 'Blog'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.blog.index')); ?>">Blog</a></li>
    <li class="breadcrumb-item active">Visualizar</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.post-content img { max-width:100%; height:auto; border-radius:6px; margin:1rem 0; }
.post-content h2, .post-content h3 { margin-top:1.5rem; margin-bottom:0.75rem; font-weight:600; }
.post-content ul, .post-content ol { padding-left:1.5rem; margin-bottom:1rem; }
.post-content p { margin-bottom:1rem; line-height:1.7; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-newspaper mr-2" style="color:var(--hm-primary);"></i><?php echo e($post->title); ?></h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.blog.edit', $post->id)); ?>" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Editar</a>
        <a href="<?php echo e(route('admin.blog.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-file-alt"></i> Detalhes do Post</span>
    </div>
    <div class="card-body">
        <div class="mb-3 d-flex align-items-center gap-2" style="gap:0.5rem;">
            <?php if($post->status === 'published'): ?>
                <span class="badge badge-success">Publicado</span>
            <?php else: ?>
                <span class="badge badge-warning">Rascunho</span>
            <?php endif; ?>
            <small class="text-muted ml-2">por <?php echo e($post->author?->name ?? 'Admin'); ?></small>
        </div>

        <?php if($post->cover_image): ?>
        <div class="mb-3">
            <img src="<?php echo e($post->cover_image); ?>" alt="<?php echo e($post->title); ?>" class="img-fluid rounded" style="max-height:300px; object-fit:cover;">
        </div>
        <?php endif; ?>

        <div class="post-content p-3" style="background:#f8f9fa;border-radius:8px;">
            <?php echo $post->content; ?>

        </div>
    </div>
    <div class="card-footer">
        <a href="<?php echo e(route('admin.blog.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\blog\show.blade.php ENDPATH**/ ?>