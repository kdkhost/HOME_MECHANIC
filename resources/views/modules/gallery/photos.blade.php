@extends('layouts.admin')

@section('title', 'Fotos da Galeria - Home Mechanic')
@section('page-title', 'Fotos da Galeria')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
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
                    <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#massUploadModal">
                        <i class="bi bi-clouds"></i> Upload em Massa
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
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold">Imagem Principal <span class="text-danger">*</span></label>
                                <x-filepond name="filename" id="photo_filename" required="true" />
                                <small class="text-muted">Arraste a foto principal aqui (Máx: 10MB)</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label font-weight-bold">Thumbnail Personalizado</label>
                                <x-filepond name="thumbnail" id="photo_thumbnail" />
                                <small class="form-text text-muted">Opcional. Se vazio, será gerado automaticamente.</small>
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

<!-- Modal de Upload Massa (Novo) -->
<div class="modal fade" id="massUploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="bi bi-clouds mr-2"></i> Upload em Massa</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Categoria de Destino</label>
                    <select class="form-control" id="mass_category_id">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Fotos</label>
                    <x-filepond name="mass_photos[]" id="mass_photos" multiple="true" />
                    <small class="text-muted">Arraste múltiplas fotos de uma vez.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block" onclick="photosManager.processMassUpload()">
                    <i class="bi bi-check-circle"></i> Iniciar Importação
                </button>
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

    async handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        try {
            const url = this.editingId 
                ? `{{ route('admin.gallery.photos.update', '') }}/${this.editingId}`
                : `{{ route('admin.gallery.photos.store') }}`;
            
            const method = this.editingId ? 'PUT' : 'POST';
            
            if (this.editingId) formData.append('_method', 'PUT');

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess(data.message);
                $('#photoModal').modal('hide');
                this.loadPhotos(this.currentPage);
            } else {
                this.showError(data.message || 'Erro ao salvar foto');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro ao processar solicitação');
        }
    }

    async processMassUpload() {
        const categoryId = $('#mass_category_id').val();
        const photos     = [];
        
        document.querySelectorAll('input[name="mass_photos[]"]').forEach(input => {
            if (input.value) photos.push(input.value);
        });

        if (photos.length === 0) {
            this.showError('Selecione ao menos uma foto para o upload em massa.');
            return;
        }

        try {
            Swal.fire({
                title: 'Processando...',
                text: 'Por favor, aguarde enquanto importamos suas fotos.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const response = await fetch('{{ route("admin.gallery.photos.mass-store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    category_id: categoryId,
                    mass_photos: photos
                })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire('Sucesso!', data.message, 'success');
                $('#massUploadModal').modal('hide');
                this.loadPhotos(1);
                
                const pond = FilePond.find(document.getElementById('mass_photos'));
                if (pond) pond.removeFiles();
            } else {
                Swal.fire('Erro', data.message, 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
        }
    }

    async editPhoto(id) {
        try {
            this.editingId = id;
            $('#modalTitle').text('Editar Foto');
            
            // Note: Usando a rota de photos com parâmetro search ou algo que retorne JSON da foto específica se não houver rota direta
            // Para simplificar, assumimos que photos Manager já tem os dados ou que photos(Request, photo_id) funciona
            const response = await fetch(`{{ route('admin.gallery.photos') }}?photo_id=${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                const photo = data.data[0];
                $('#title').val(photo.title);
                $('#category_id').val(photo.category_id);
                $('#description').val(photo.description);
                $('#sort_order').val(photo.sort_order);
                $('#active').prop('checked', !!photo.active);

                if (photo.image_url) {
                    const pond = FilePond.find(document.getElementById('photo_filename'));
                    if (pond) pond.addFile(photo.image_url);
                }
                if (photo.thumbnail_url) {
                    const pond = FilePond.find(document.getElementById('photo_thumbnail'));
                    if (pond) pond.addFile(photo.thumbnail_url);
                }

                $('#photoModal').modal('show');
            }
        } catch (error) {
            console.error('Erro ao buscar foto:', error);
            this.showError('Não foi possível carregar os dados da foto.');
        }
    }

    async deletePhoto(id) {
        const result = await Swal.fire({
            title: 'Excluir foto?',
            text: 'Esta ação não poderá ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`{{ route('admin.gallery.photos.destroy', '') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.showSuccess(data.message);
                    this.loadPhotos(this.currentPage);
                }
            } catch (error) {
                this.showError('Erro ao excluir foto');
            }
        }
    }

    async toggleActive(id) {
        try {
            const response = await fetch(`{{ route('admin.gallery.photos.toggle-active', '') }}/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                this.showSuccess(data.message);
                this.loadPhotos(this.currentPage);
            }
        } catch (error) {
            this.showError('Erro ao alterar status');
        }
    }

    showSuccess(message) {
        HMToast.success(message);
    }

    showError(message) {
        HMToast.error(message);
    }

    resetForm() {
        $('#photoForm')[0].reset();
        this.editingId = null;
        $('#modalTitle').text('Nova Foto');
        FilePond.find(document.getElementById('photo_filename'))?.removeFiles();
        FilePond.find(document.getElementById('photo_thumbnail'))?.removeFiles();
    }
}

$(document).ready(() => {
    window.photosManager = new PhotosManager();
});
</script>
@endsection