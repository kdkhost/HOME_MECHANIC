<div class="row">
    <div class="col-md-8">
        <!-- Título -->
        <div class="form-group">
            <label for="title">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" required maxlength="255">
            <small class="form-text text-muted">Nome do serviço que será exibido no site</small>
        </div>

        <!-- Slug -->
        <div class="form-group">
            <label for="slug">Slug (URL)</label>
            <input type="text" class="form-control" id="slug" name="slug" maxlength="255">
            <small class="form-text text-muted">Deixe em branco para gerar automaticamente. Use apenas letras minúsculas, números e hífens.</small>
        </div>

        <!-- Descrição -->
        <div class="form-group">
            <label for="description">Descrição <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" rows="3" required maxlength="500"></textarea>
            <small class="form-text text-muted">Descrição curta que aparece nos cards e listagens (máx. 500 caracteres)</small>
        </div>

        <!-- Conteúdo -->
        <div class="form-group">
            <label for="content">Conteúdo Completo</label>
            <textarea class="form-control" id="content" name="content" rows="6"></textarea>
            <small class="form-text text-muted">Descrição detalhada do serviço (aceita HTML básico)</small>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Imagem de Capa -->
        <div class="form-group mb-4">
            <label class="form-label font-weight-bold">Imagem de Capa</label>
            <?php if (isset($component)) { $__componentOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ddfa68e14b19345c5eae6d0123c1be5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filepond','data' => ['name' => 'cover_image','value' => isset($service) && $service->cover_image ? $service->cover_image_url : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filepond'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cover_image','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($service) && $service->cover_image ? $service->cover_image_url : null)]); ?>
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
            <small class="form-text text-muted">Recomendado: 1200x600px. Máx: 5MB.</small>
        </div>

        <!-- Ícone -->
        <div class="form-group">
            <label for="icon">Ícone (Bootstrap Icons)</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i id="iconPreview" class="bi bi-tools"></i>
                    </span>
                </div>
                <input type="text" class="form-control" id="icon" name="icon" placeholder="bi-tools" maxlength="100">
            </div>
            <small class="form-text text-muted">
                <a href="https://icons.getbootstrap.com/" target="_blank">Ver ícones disponíveis</a>
            </small>
        </div>

        <!-- Ordem -->
        <div class="form-group">
            <label for="sort_order">Ordem de Exibição</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" min="0">
            <small class="form-text text-muted">Deixe em branco para adicionar ao final</small>
        </div>

        <!-- Opções -->
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1">
                <label class="custom-control-label" for="featured">Serviço em Destaque</label>
            </div>
            <small class="form-text text-muted">Serviços em destaque aparecem na página inicial</small>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" checked>
                <label class="custom-control-label" for="active">Ativo</label>
            </div>
            <small class="form-text text-muted">Apenas serviços ativos são exibidos no site</small>
        </div>
    </div>
</div>

<script>
// Preview do ícone
$('#icon').on('input', function() {
    const iconClass = $(this).val() || 'bi-tools';
    $('#iconPreview').attr('class', `bi ${iconClass}`);
});
</script>

<style>
.upload-area {
    transition: all 0.2s;
}
.upload-area:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
}
.upload-card {
    transition: transform 0.2s;
}
.upload-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\services\_form.blade.php ENDPATH**/ ?>