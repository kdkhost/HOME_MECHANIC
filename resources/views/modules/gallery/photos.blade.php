@extends('layouts.admin')

@section('title', 'Fotos da Galeria - Home Mechanic')
@section('page-title', 'Fotos da Galeria')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.gallery.index') }}">Galeria</a></li>
<li class="breadcrumb-item active">
    @if($category)
        {{ $category->name }}
    @else
        Todas as Fotos
    @endif
</li>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<style>
.photo-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}
.photo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.photo-image {
    height: 200px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.photo-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.photo-card:hover .photo-image img {
    transform: scale(1.05);
}
.photo-actions {
    opacity: 0;
    transition: opacity 0.2s;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}
.photo-card:hover .photo-actions {
    opacity: 1;
}
.photo-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    padding: 20px 15px 15px;
    transform: translateY(100%);
    transition: transform 0.3s;
}
.photo-card:hover .photo-overlay {
    transform: translateY(0);
}
.drag-handle {
    cursor: move;
    color: #6c757d;
}
.sortable-ghost {
    opacity: 0.4;
}
.status-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
}
.lightbox-trigger {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 5;
}
.photo-card:hover .lightbox-trigger {
    opacity: 1;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-image mr-2"></i>
                    @if($category)
                        Fotos: {{ $category->name }}
                    @else
                        Todas as Fotos
                    @endif
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#photoModal">
                        <i class="bi bi-plus"></i> Nova Foto
                    </button>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary ml-2">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar fotos...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select id="categoryFilter" class="form-control">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $category && $category->id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="statusFilter" class="form-control">
                            <option value="">Todos os Status</option>
                            <option value="1">Ativas</option>
                            <option value="0">Inativas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="sortBy" class="form-control">
                            <option value="sort_order">Ordem</option>
                            <option value="title">Título</option>
                            <option value="created_at">Data de Criação</option>
                            <option value="updated_at">Última Atualização</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-right">
                        <button type="button" class="btn btn-info" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise"></i> Atualizar
                        </button>
                        <button type="button" class="btn btn-warning" id="lightboxBtn">
                            <i class="bi bi-eye"></i> Visualizar Galeria
                        </button>
                    </div>
                </div>

                <!-- Lista de Fotos -->
                <div id="photosContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                    </div>
                </div>

                <!-- Paginação -->
                <div id="paginationContainer" class="d-flex justify-content-center mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal da Foto -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="bi bi-image mr-2"></i>
                    <span id="modalTitle">Nova Foto</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="photoForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">Título da Foto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required maxlength="255">
                            </div>

                            <div class="form-group">
                                <label for="category_id">Categoria <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"></textarea>
                                <small class="form-text text-muted">Descrição opcional da foto (máx. 1000 caracteres)</small>
                            </div>

                            <div class="form-group">
                                <label for="sort_order">Ordem de Exibição</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" min="0">
                                <small class="form-text text-muted">Deixe em branco para adicionar ao final</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" checked>
                                    <label class="custom-control-label" for="active">Foto Ativa</label>
                                </div>
                                <small class="form-text text-muted">Apenas fotos ativas são exibidas na galeria pública</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Imagem Principal <span class="text-danger">*</span></label>
                                <div class="upload-area border rounded p-3 text-center" style="min-height: 200px; cursor: pointer;" onclick="openUploadModal()">
                                    <div id="imagePreview"></div>
                                    <div id="uploadPlaceholder">
                                        <i class="bi bi-cloud-upload display-4 text-muted"></i>
                                        <p class="text-muted mb-0">Clique para selecionar imagem</p>
                                        <small class="text-muted">JPG, PNG, WebP (máx. 10MB)</small>
                                    </div>
                                </div>
                                <input type="hidden" id="filename" name="filename" required>
                            </div>

                            <div class="form-group">
                                <label>Thumbnail Personalizado</label>
                                <div class="upload-area border rounded p-2 text-center" style="min-height: 100px; cursor: pointer;" onclick="openThumbnailModal()">
                                    <div id="thumbnailPreview"></div>
                                    <div id="thumbnailPlaceholder">
                                        <i class="bi bi-image text-muted"></i>
                                        <small class="text-muted">Opcional</small>
                                    </div>
                                </div>
                                <input type="hidden" id="thumbnail" name="thumbnail">
                                <small class="form-text text-muted">Se não fornecido, será usado o thumbnail da imagem principal</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Salvar
                    </button>
                </div>
            </form>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://unpkg.com/dropzone@6/dist/dropzone-min.js"></script>
<script>
class PhotosManager {
    constructor() {
        this.currentPage = 1;
        this.perPage = 20;
        this.editingId = null;
        this.sortable = null;
        this.lightbox = null;
        this.uploadType = 'main'; // 'main' ou 'thumbnail'
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadPhotos();
        this.initLightbox();
    }

    bindEvents() {
        // Busca e filtros
        $('#searchInput').on('keyup', this.debounce(() => this.loadPhotos(), 500));
        $('#searchBtn, #categoryFilter, #statusFilter, #sortBy').on('change click', () => this.loadPhotos());
        
        // Refresh
        $('#refreshBtn').on('click', () => this.loadPhotos());
        
        // Lightbox da galeria
        $('#lightboxBtn').on('click', () => this.openGalleryLightbox());
        
        // Form
        $('#photoForm').on('submit', (e) => this.handleSubmit(e));
        
        // Modal reset
        $('#photoModal').on('hidden.bs.modal', () => this.resetForm());
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async loadPhotos(page = 1) {
        try {
            this.currentPage = page;
            
            const params = new URLSearchParams({
                page: page,
                per_page: this.perPage,
                search: $('#searchInput').val(),
                active: $('#statusFilter').val(),
                category_id: $('#categoryFilter').val(),
                sort_by: $('#sortBy').val()
            });

            const categoryId = $('#categoryFilter').val();
            const url = categoryId 
                ? `{{ route('admin.gallery.photos', '') }}/${categoryId}?${params}`
                : `{{ route('admin.gallery.photos') }}?${params}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.renderPhotos(data.data);
                this.renderPagination(data.pagination);
                this.initSortable();
                this.updateLightbox();
            } else {
                this.showError('Erro ao carregar fotos');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    renderPhotos(photos) {
        const container = $('#photosContainer');
        
        if (photos.length === 0) {
            container.html(`
                <div class="text-center py-4">
                    <i class="bi bi-image-alt display-4 text-muted"></i>
                    <p class="text-muted mt-2">Nenhuma foto encontrada</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#photoModal">
                        <i class="bi bi-plus"></i> Adicionar Primeira Foto
                    </button>
                </div>
            `);
            return;
        }

        const html = `
            <div id="photosList" class="row">
                ${photos.map(photo => this.renderPhotoCard(photo)).join('')}
            </div>
        `;
        
        container.html(html);
    }

    renderPhotoCard(photo) {
        const statusBadge = photo.active 
            ? '<span class="badge badge-success status-badge">Ativa</span>'
            : '<span class="badge badge-secondary status-badge">Inativa</span>';

        const imageUrl = photo.thumbnail_url || photo.image_url || '/img/placeholder.jpg';

        return `
            <div class="col-md-3 col-lg-2 mb-4" data-photo-id="${photo.id}">
                <div class="card photo-card h-100">
                    <div class="photo-image">
                        <img src="${imageUrl}" alt="${photo.title}" loading="lazy">
                        ${statusBadge}
                        <div class="photo-actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-light" onclick="photosManager.editPhoto(${photo.id})" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-light drag-handle" title="Arrastar">
                                    <i class="bi bi-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="lightbox-trigger">
                            <button class="btn btn-primary btn-lg" onclick="photosManager.openPhotoLightbox('${imageUrl}', '${photo.title}')" title="Visualizar">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="photo-overlay">
                            <h6 class="mb-1">${photo.title}</h6>
                            <small>${photo.category_name || 'Sem categoria'}</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-light" onclick="photosManager.toggleActive(${photo.id})">
                                    <i class="bi bi-${photo.active ? 'pause' : 'play'}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="photosManager.deletePhoto(${photo.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    renderPagination(pagination) {
        const container = $('#paginationContainer');
        
        if (pagination.last_page <= 1) {
            container.empty();
            return;
        }

        let html = '<nav><ul class="pagination">';
        
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                       <a class="page-link" href="#" onclick="photosManager.loadPhotos(${pagination.current_page - 1})">Anterior</a>
                     </li>`;
        }
        
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item">
                           <a class="page-link" href="#" onclick="photosManager.loadPhotos(${i})">${i}</a>
                         </li>`;
            }
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                       <a class="page-link" href="#" onclick="photosManager.loadPhotos(${pagination.current_page + 1})">Próximo</a>
                     </li>`;
        }
        
        html += '</ul></nav>';
        container.html(html);
    }

    initSortable() {
        if (this.sortable) {
            this.sortable.destroy();
        }

        const list = document.getElementById('photosList');
        if (!list) return;

        this.sortable = Sortable.create(list, {
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: (evt) => this.handleReorder(evt)
        });
    }

    initLightbox() {
        this.lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: false
        });
    }

    updateLightbox() {
        if (this.lightbox) {
            this.lightbox.destroy();
        }
        this.initLightbox();
    }

    async handleReorder(evt) {
        const photos = [];
        const cards = document.querySelectorAll('[data-photo-id]');
        
        cards.forEach((card, index) => {
            photos.push({
                id: parseInt(card.dataset.photoId),
                sort_order: index + 1
            });
        });

        try {
            const response = await fetch('{{ route("admin.gallery.photos.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ photos })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
            } else {
                this.showError('Erro ao reordenar fotos');
                this.loadPhotos(this.currentPage);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
            this.loadPhotos(this.currentPage);
        }
    }

    openPhotoLightbox(imageUrl, title) {
        GLightbox({
            elements: [{
                href: imageUrl,
                title: title
            }]
        }).open();
    }

    openGalleryLightbox() {
        const photos = [];
        document.querySelectorAll('.photo-image img').forEach(img => {
            photos.push({
                href: img.src.replace('_thumb', ''), // Usar imagem original
                title: img.alt
            });
        });

        if (photos.length > 0) {
            GLightbox({
                elements: photos,
                loop: true
            }).open();
        }
    }

    // Continuar com os métodos restantes...
    async editPhoto(id) {
        // Implementar edição
    }

    async toggleActive(id) {
        // Implementar toggle
    }

    async deletePhoto(id) {
        // Implementar exclusão
    }

    showSuccess(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#28a745"
        }).showToast();
    }

    showError(message) {
        Toastify({
            text: message,
            duration: 5000,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545"
        }).showToast();
    }

    resetForm() {
        $('#photoForm')[0].reset();
        $('#imagePreview, #thumbnailPreview').empty();
        $('#uploadPlaceholder, #thumbnailPlaceholder').show();
        this.editingId = null;
        $('#modalTitle').text('Nova Foto');
    }
}

// Funções globais para upload
function openUploadModal() {
    window.photosManager.uploadType = 'main';
    $('#uploadModal').modal('show');
    loadRecentImages();
}

function openThumbnailModal() {
    window.photosManager.uploadType = 'thumbnail';
    $('#uploadModal').modal('show');
    loadRecentImages();
}

function selectImage(uuid, thumbnailUrl) {
    if (window.photosManager.uploadType === 'main') {
        $('#filename').val(uuid);
        $('#imagePreview').html(`<img src="${thumbnailUrl}" class="img-thumbnail" style="max-height: 180px;">`);
        $('#uploadPlaceholder').hide();
    } else {
        $('#thumbnail').val(uuid);
        $('#thumbnailPreview').html(`<img src="${thumbnailUrl}" class="img-thumbnail" style="max-height: 80px;">`);
        $('#thumbnailPlaceholder').hide();
    }
    $('#uploadModal').modal('hide');
}

async function loadRecentImages() {
    try {
        const response = await fetch('{{ route("admin.upload.index") }}?type=images&per_page=12', {
            headers: { 'Accept': 'application/json' }
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

// Inicializar quando o documento estiver pronto
$(document).ready(() => {
    window.photosManager = new PhotosManager();
    
    // Configurar Dropzone
    Dropzone.autoDiscover = false;
    
    $('#uploadModal').on('shown.bs.modal', function() {
        if (!window.photoDropzone) {
            window.photoDropzone = new Dropzone("#dropzone", {
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
                    window.photosManager.showError(typeof errorMessage === 'string' ? errorMessage : 'Erro no upload');
                }
            });
        }
    });
});
</script>
@endsection