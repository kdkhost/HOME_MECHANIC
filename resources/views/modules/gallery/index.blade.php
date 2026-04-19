@extends('layouts.admin')

@section('title', 'Galeria - HomeMechanic')
@section('page-title', 'Galeria')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Galeria</li>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.category-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.category-cover {
    height: 200px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.category-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.category-cover .placeholder {
    color: #6c757d;
    font-size: 3rem;
}
.category-actions {
    opacity: 0;
    transition: opacity 0.2s;
    position: absolute;
    top: 10px;
    right: 10px;
}
.category-card:hover .category-actions {
    opacity: 1;
}
.drag-handle {
    cursor: move;
    color: #6c757d;
}
.sortable-ghost {
    opacity: 0.4;
}
.photo-count {
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    position: absolute;
    bottom: 10px;
    left: 10px;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-images mr-2"></i>
                    Categorias da Galeria
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="bi bi-plus"></i> Nova Categoria
                    </button>
                    <a href="{{ route('admin.gallery.photos') }}" class="btn btn-info ml-2">
                        <i class="bi bi-image"></i> Gerenciar Fotos
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar categorias...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-info" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise"></i> Atualizar
                        </button>
                    </div>
                </div>

                <!-- Lista de Categorias -->
                <div id="categoriesContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                    </div>
                </div>

                <!-- PaginaÃ§Ã£o -->
                <div id="paginationContainer" class="d-flex justify-content-center mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal da Categoria -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="bi bi-folder-plus mr-2"></i>
                    <span id="modalTitle">Nova Categoria</span>
                </h4>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nome da Categoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="255">
                        <small class="form-text text-muted">Nome que serÃ¡ exibido na galeria</small>
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug (URL)</label>
                        <input type="text" class="form-control" id="slug" name="slug" maxlength="255">
                        <small class="form-text text-muted">Deixe em branco para gerar automaticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Ordem de ExibiÃ§Ã£o</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" min="0">
                        <small class="form-text text-muted">Deixe em branco para adicionar ao final</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
class GalleryManager {
    constructor() {
        this.currentPage = 1;
        this.perPage = 12;
        this.editingId = null;
        this.sortable = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCategories();
    }

    bindEvents() {
        // Busca
        $('#searchInput').on('keyup', this.debounce(() => this.loadCategories(), 500));
        $('#searchBtn').on('click', () => this.loadCategories());
        
        // Refresh
        $('#refreshBtn').on('click', () => this.loadCategories());
        
        // Form
        $('#categoryForm').on('submit', (e) => this.handleSubmit(e));
        
        // Modal reset
        $('#categoryModal').on('hidden.bs.modal', () => this.resetForm());
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

    async loadCategories(page = 1) {
        try {
            this.currentPage = page;
            
            const params = new URLSearchParams({
                page: page,
                per_page: this.perPage,
                search: $('#searchInput').val()
            });

            const response = await fetch(`{{ route('admin.gallery.index') }}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.renderCategories(data.data);
                this.renderPagination(data.pagination);
                this.initSortable();
            } else {
                this.showError('Erro ao carregar categorias');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexÃ£o');
        }
    }

    renderCategories(categories) {
        const container = $('#categoriesContainer');
        
        if (categories.length === 0) {
            container.html(`
                <div class="text-center py-4">
                    <i class="bi bi-folder-x display-4 text-muted"></i>
                    <p class="text-muted mt-2">Nenhuma categoria encontrada</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="bi bi-plus"></i> Criar Primeira Categoria
                    </button>
                </div>
            `);
            return;
        }

        const html = `
            <div id="categoriesList" class="row">
                ${categories.map(category => this.renderCategoryCard(category)).join('')}
            </div>
        `;
        
        container.html(html);
    }

    renderCategoryCard(category) {
        const coverImage = category.cover_photo?.thumbnail_url 
            ? `<img src="${category.cover_photo.thumbnail_url}" alt="${category.name}">`
            : `<div class="placeholder"><i class="bi bi-images"></i></div>`;

        return `
            <div class="col-md-4 col-lg-3 mb-4" data-category-id="${category.id}">
                <div class="card category-card h-100">
                    <div class="category-cover">
                        ${coverImage}
                        <div class="category-actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-light" onclick="galleryManager.editCategory(${category.id})" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-light drag-handle" title="Arrastar">
                                    <i class="bi bi-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="photo-count">
                            <i class="bi bi-images"></i> ${category.active_photos_count}
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-2">${category.name}</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Ordem: ${category.sort_order}</small>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.gallery.photos') }}/${category.id}" class="btn btn-outline-primary" title="Ver Fotos">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-outline-danger" onclick="galleryManager.deleteCategory(${category.id})" title="Excluir">
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
        
        // Anterior
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                       <a class="page-link" href="#" onclick="galleryManager.loadCategories(${pagination.current_page - 1})">Anterior</a>
                     </li>`;
        }
        
        // PÃ¡ginas
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item">
                           <a class="page-link" href="#" onclick="galleryManager.loadCategories(${i})">${i}</a>
                         </li>`;
            }
        }
        
        // PrÃ³ximo
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                       <a class="page-link" href="#" onclick="galleryManager.loadCategories(${pagination.current_page + 1})">PrÃ³ximo</a>
                     </li>`;
        }
        
        html += '</ul></nav>';
        container.html(html);
    }

    initSortable() {
        if (this.sortable) {
            this.sortable.destroy();
        }

        const list = document.getElementById('categoriesList');
        if (!list) return;

        this.sortable = Sortable.create(list, {
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: (evt) => this.handleReorder(evt)
        });
    }

    async handleReorder(evt) {
        const categories = [];
        const cards = document.querySelectorAll('[data-category-id]');
        
        cards.forEach((card, index) => {
            categories.push({
                id: parseInt(card.dataset.categoryId),
                sort_order: index + 1
            });
        });

        try {
            const response = await fetch('{{ route("admin.gallery.categories.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ categories })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
            } else {
                this.showError('Erro ao reordenar categorias');
                this.loadCategories(this.currentPage);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexÃ£o');
            this.loadCategories(this.currentPage);
        }
    }

    async editCategory(id) {
        try {
            const response = await fetch(`{{ route('admin.gallery.index') }}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            const category = data.data.find(c => c.id === id);
            
            if (category) {
                this.fillForm(category);
                this.editingId = id;
                $('#modalTitle').text('Editar Categoria');
                $('#categoryModal').modal('show');
            } else {
                this.showError('Categoria nÃ£o encontrada');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexÃ£o');
        }
    }

    fillForm(category) {
        $('#name').val(category.name);
        $('#slug').val(category.slug);
        $('#sort_order').val(category.sort_order);
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const url = this.editingId 
            ? `{{ route('admin.gallery.index') }}/categories/${this.editingId}`
            : '{{ route("admin.gallery.categories.store") }}';
        const method = this.editingId ? 'PUT' : 'POST';

        if (this.editingId) {
            formData.append('_method', 'PUT');
        }

        try {
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
                $('#categoryModal').modal('hide');
                this.loadCategories(this.currentPage);
            } else {
                this.showError(data.message || 'Erro ao salvar categoria');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexÃ£o');
        }
    }

    async deleteCategory(id) {
        const result = await Swal.fire({
            title: 'Confirmar ExclusÃ£o',
            text: 'Tem certeza que deseja excluir esta categoria? Todas as fotos da categoria tambÃ©m serÃ£o removidas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`{{ route('admin.gallery.index') }}/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadCategories(this.currentPage);
            } else {
                this.showError(data.message || 'Erro ao excluir categoria');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexÃ£o');
        }
    }

    resetForm() {
        $('#categoryForm')[0].reset();
        this.editingId = null;
        $('#modalTitle').text('Nova Categoria');
    }

    showSuccess(message) {
        HMToast.success(message);
    }

    showError(message) {
        HMToast.error(message);
    }
}

// Inicializar quando o documento estiver pronto
$(document).ready(() => {
    window.galleryManager = new GalleryManager();
});
</script>
@endsection