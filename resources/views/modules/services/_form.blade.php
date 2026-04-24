<div class="row">
    <div class="col-md-8">
        <!-- Título -->
        <div class="form-group">
            <label for="title">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $service->title ?? '') }}" required maxlength="255">
            <small class="form-text text-muted">Nome do serviço que será exibido no site</small>
        </div>

        <!-- Slug -->
        <div class="form-group">
            <label for="slug">Slug (URL)</label>
            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $service->slug ?? '') }}" maxlength="255">
            <small class="form-text text-muted">Deixe em branco para gerar automaticamente. Use apenas letras minúsculas, números e hífens.</small>
        </div>

        <!-- Descrição -->
        <div class="form-group">
            <label for="description">Descrição <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" rows="3" required maxlength="500">{{ old('description', $service->description ?? '') }}</textarea>
            <small class="form-text text-muted">Descrição curta que aparece nos cards e listagens (máx. 500 caracteres)</small>
        </div>

        <!-- Conteúdo -->
        <div class="form-group">
            <label for="content">Conteúdo Completo</label>
            <textarea class="form-control" id="content" name="content" rows="6">{{ old('content', $service->content ?? '') }}</textarea>
            <small class="form-text text-muted">Descrição detalhada do serviço (aceita HTML básico)</small>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Imagem de Capa -->
        <div class="form-group mb-4">
            <label class="form-label font-weight-bold">Imagem de Capa</label>
            <x-filepond name="cover_image" :value="isset($service) && $service->cover_image ? $service->cover_image_url : null" />
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
                <input type="text" class="form-control" id="icon" name="icon" value="{{ old('icon', $service->icon ?? '') }}" placeholder="bi-tools" maxlength="100">
            </div>
            <small class="form-text text-muted">
                <a href="https://icons.getbootstrap.com/" target="_blank">Ver ícones disponíveis</a>
            </small>
        </div>

        <!-- Ordem -->
        <div class="form-group">
            <label for="sort_order">Ordem de Exibição</label>
            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? '') }}" min="0">
            <small class="form-text text-muted">Deixe em branco para adicionar ao final</small>
        </div>

        <!-- Opções -->
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1" {{ old('featured', $service->featured ?? false) ? 'checked' : '' }}>
                <label class="custom-control-label" for="featured">Serviço em Destaque</label>
            </div>
            <small class="form-text text-muted">Serviços em destaque aparecem na página inicial</small>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', $service->active ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="active">Ativo</label>
            </div>
            <small class="form-text text-muted">Apenas serviços ativos são exibidos no site</small>
        </div>
    </div>
</div>

<script>
// Preview do ícone - inicializa com valor atual
$(function() {
    const initialIcon = $('#icon').val() || 'bi-tools';
    $('#iconPreview').attr('class', `bi ${initialIcon}`);
});

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
</style>