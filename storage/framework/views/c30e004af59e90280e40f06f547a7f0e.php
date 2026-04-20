
<?php $__env->startSection('title', 'Editar Post'); ?>
<?php $__env->startSection('page-title', 'Blog'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.blog.index')); ?>">Blog</a></li>
    <li class="breadcrumb-item active">Editar</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
.note-editor { background:#fff; }
.note-editor .note-toolbar { background:#f8f9fa; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-pencil-alt mr-2" style="color:var(--hm-primary);"></i>Editar Post</h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.blog.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-edit"></i> <?php echo e($post['title']); ?></span>
    </div>
    <form method="POST" action="<?php echo e(route('admin.blog.update', $post['id'])); ?>" id="postForm">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>
                    <ul class="mb-0 mt-1"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" value="<?php echo e(old('title', $post['title'])); ?>" required>
            </div>
            <div class="form-group mb-4">
                <label class="form-label font-weight-bold">Imagem de Capa</label>
                <?php if (isset($component)) { $__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filepond','data' => ['name' => 'cover_image','value' => $post['cover_image'] ? '/' . ltrim($post['cover_image'], '/') : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filepond'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cover_image','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post['cover_image'] ? '/' . ltrim($post['cover_image'], '/') : null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5)): ?>
<?php $attributes = $__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5; ?>
<?php unset($__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5)): ?>
<?php $component = $__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5; ?>
<?php unset($__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5); ?>
<?php endif; ?>
                <small class="text-muted">Recomendado: 1200x600px. Arraste e solte para enviar.</small>
            </div>
            <div class="form-group">
                <label>Conteúdo <span class="text-danger">*</span></label>
                <textarea id="summernote" name="content"><?php echo e(old('content', $post['content'])); ?></textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status" style="max-width:200px;">
                    <option value="draft" <?php echo e($post['status'] === 'draft' ? 'selected' : ''); ?>>Rascunho</option>
                    <option value="published" <?php echo e($post['status'] === 'published' ? 'selected' : ''); ?>>Publicado</option>
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
            <a href="<?php echo e(route('admin.blog.index')); ?>" class="btn btn-secondary ml-2">Cancelar</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#summernote').summernote({
        height: 300,
        lang: 'pt-BR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        styleTags: ['p', 'h2', 'h3', 'h4'],
        callbacks: {
            onImageUpload: function(files) {
                alert('Use o botão Inserir > Imagem e cole a URL da imagem. Upload direto não está disponível.');
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\blog\edit.blade.php ENDPATH**/ ?>