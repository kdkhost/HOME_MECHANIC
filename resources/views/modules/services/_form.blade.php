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
        <div class="form-group">
            <label>Imagem de Capa</label>
            <div class="upload-area border rounded p-3 text-center" style="min-height: 120px; cursor: pointer;" onclick="openUploadModal()">
                <div id="imagePreview"></div>
                <div id="uploadPlaceholder">
                    <i class="bi bi-cloud-upload display-4 text-muted"></i>
                    <p class="text-muted mb-0">Clique para selecionar imagem</p>
                    <small class="text-muted">JPG, PNG, WebP (máx. 10MB)</small>
                </div>
            </div>
            <input type="hidden" id="cover_image" name="cover_image">
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

<!-- Modal de Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="bi bi-cloud-upload mr-2"></i>
                    Selecionar Imagem
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="dropzone" class="dropzone border-dashed border-2 rounded p-4 text-center">
                    <div class="dz-message">
                        <i class="bi bi-cloud-upload display-4 text-muted"></i>
                        <h4>Arraste arquivos aqui ou clique para selecionar</h4>
                        <p class="text-muted">Apenas imagens JPG, PNG, WebP (máx. 10MB)</p>
                    </div>
                </div>
                
                <!-- Lista de uploads recentes -->
                <div class="mt-4">
                    <h6>Imagens Recentes</h6>
                    <div id="recentImages" class="row">
                        <div class="col-12 text-center py-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Carregando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Preview do ícone
$('#icon').on('input', function() {
    const iconClass = $(this).val() || 'bi-tools';
    $('#iconPreview').attr('class', `bi ${iconClass}`);
});

// Função para abrir modal de upload
function openUploadModal() {
    $('#uploadModal').modal('show');
    loadRecentImages();
}

// Carregar imagens recentes
async function loadRecentImages() {
    try {
        const response = await fetch('{{ route("admin.upload.index") }}?type=images&per_page=12', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const html = data.data.map(upload => `
                <div class="col-md-3 mb-3">
                    <div class="card upload-card" style="cursor: pointer;" onclick="selectImage('${upload.uuid}', '${upload.thumbnail_url}')">
                        <img src="${upload.thumbnail_url}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${upload.original_name}</small>
                        </div>
                    </div>
                </div>
            `).join('');
            
            $('#recentImages').html(html);
        } else {
            $('#recentImages').html('<div class="col-12 text-center text-muted">Nenhuma imagem encontrada</div>');
        }
    } catch (error) {
        console.error('Erro ao carregar imagens:', error);
        $('#recentImages').html('<div class="col-12 text-center text-danger">Erro ao carregar imagens</div>');
    }
}

// Selecionar imagem
function selectImage(uuid, thumbnailUrl) {
    $('#cover_image').val(uuid);
    $('#imagePreview').html(`<img src="${thumbnailUrl}" class="img-thumbnail" style="max-height: 100px;">`);
    $('#uploadPlaceholder').hide();
    $('#uploadModal').modal('hide');
}

// Configurar Dropzone quando o modal for mostrado
$('#uploadModal').on('shown.bs.modal', function() {
    if (!window.serviceDropzone) {
        window.serviceDropzone = new Dropzone("#dropzone", {
            url: "{{ route('admin.upload.store') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            acceptedFiles: 'image/*',
            maxFilesize: 10,
            maxFiles: 1,
            addRemoveLinks: true,
            dictDefaultMessage: '',
            success: function(file, response) {
                if (response.success) {
                    selectImage(response.data.uuid, response.data.thumbnail_url);
                    this.removeAllFiles();
                }
            },
            error: function(file, errorMessage) {
                console.error('Erro no upload:', errorMessage);
                Toastify({
                    text: typeof errorMessage === 'string' ? errorMessage : 'Erro no upload',
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            }
        });
    }
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
.dropzone {
    border: 2px dashed #dee2e6 !important;
    background: #f8f9fa;
}
.dropzone.dz-drag-hover {
    border-color: #007bff !important;
    background: #e3f2fd;
}
</style>