@extends('layouts.admin')

@section('title', 'Serviços - HomeMechanic')
@section('page-title', 'Serviços')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Serviços</li>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.service-card {
    transition: transform 0.2s;
}
.service-card:hover {
    transform: translateY(-2px);
}
.service-status {
    font-size: 0.8rem;
}
.service-actions {
    opacity: 0;
    transition: opacity 0.2s;
}
.service-card:hover .service-actions {
    opacity: 1;
}
.drag-handle {
    cursor: move;
}
.sortable-ghost {
    opacity: 0.4;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-tools mr-2"></i>
                    Gerenciar Serviços
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#serviceModal">
                        <i class="bi bi-plus"></i> Novo Serviço
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar serviços...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select id="statusFilter" class="form-control">
                            <option value="">Todos os Status</option>
                            <option value="1">Ativos</option>
                            <option value="0">Inativos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="featuredFilter" class="form-control">
                            <option value="">Todos</option>
                            <option value="1">Em Destaque</option>
                            <option value="0">Sem Destaque</option>
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
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise"></i> Atualizar
                        </button>
                    </div>
                </div>

                <!-- Lista de Serviços -->
                <div id="servicesContainer">
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

<!-- Modal do Serviço -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="bi bi-tools mr-2"></i>
                    <span id="modalTitle">Novo Serviço</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="serviceForm">
                <div class="modal-body">
                    @include('modules.services._form')
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
class ServicesManager {
    constructor() {
        this.currentPage = 1;
        this.perPage = 15;
        this.editingId = null;
        this.sortable = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadServices();
    }

    bindEvents() {
        // Busca
        $('#searchInput').on('keyup', this.debounce(() => this.loadServices(), 500));
        $('#searchBtn').on('click', () => this.loadServices());
        
        // Filtros
        $('#statusFilter, #featuredFilter, #sortBy').on('change', () => this.loadServices());
        
        // Refresh
        $('#refreshBtn').on('click', () => this.loadServices());
        
        // Form
        $('#serviceForm').on('submit', (e) => this.handleSubmit(e));
        
        // Modal reset
        $('#serviceModal').on('hidden.bs.modal', () => this.resetForm());
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

    async loadServices(page = 1) {
        try {
            this.currentPage = page;
            
            const params = new URLSearchParams({
                page: page,
                per_page: this.perPage,
                search: $('#searchInput').val(),
                active: $('#statusFilter').val(),
                featured: $('#featuredFilter').val(),
                sort_by: $('#sortBy').val()
            });

            const response = await fetch(`{{ route('admin.services.index') }}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.renderServices(data.data);
                this.renderPagination(data.pagination);
                this.initSortable();
            } else {
                this.showError('Erro ao carregar serviços');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    renderServices(services) {
        const container = $('#servicesContainer');
        
        if (services.length === 0) {
            container.html(`
                <div class="text-center py-4">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-2">Nenhum serviço encontrado</p>
                </div>
            `);
            return;
        }

        const html = `
            <div id="servicesList" class="row">
                ${services.map(service => this.renderServiceCard(service)).join('')}
            </div>
        `;
        
        container.html(html);
    }

    renderServiceCard(service) {
        const statusBadge = service.active 
            ? '<span class="badge badge-success">Ativo</span>'
            : '<span class="badge badge-secondary">Inativo</span>';
            
        const featuredBadge = service.featured 
            ? '<span class="badge badge-warning ml-1">Destaque</span>'
            : '';

        const coverImage = service.cover_thumbnail_url 
            ? `<img src="${service.cover_thumbnail_url}" class="card-img-top" style="height: 150px; object-fit: cover;">`
            : `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                 <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
               </div>`;

        return `
            <div class="col-md-4 mb-3" data-service-id="${service.id}">
                <div class="card service-card h-100">
                    ${coverImage}
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">${service.title}</h5>
                            <div class="drag-handle text-muted">
                                <i class="bi bi-grip-vertical"></i>
                            </div>
                        </div>
                        <p class="card-text text-muted small">${service.description}</p>
                        <div class="service-status mb-2">
                            ${statusBadge}${featuredBadge}
                            <small class="text-muted ml-2">Ordem: ${service.sort_order}</small>
                        </div>
                    </div>
                    <div class="card-footer service-actions">
                        <div class="btn-group btn-group-sm w-100">
                            <button class="btn btn-outline-primary" onclick="servicesManager.editService(${service.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-${service.active ? 'warning' : 'success'}" 
                                    onclick="servicesManager.toggleActive(${service.id})">
                                <i class="bi bi-${service.active ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="btn btn-outline-${service.featured ? 'secondary' : 'warning'}" 
                                    onclick="servicesManager.toggleFeatured(${service.id})">
                                <i class="bi bi-star${service.featured ? '-fill' : ''}"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="servicesManager.deleteService(${service.id})">
                                <i class="bi bi-trash"></i>
                            </button>
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
                       <a class="page-link" href="#" onclick="servicesManager.loadServices(${pagination.current_page - 1})">Anterior</a>
                     </li>`;
        }
        
        // Páginas
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item">
                           <a class="page-link" href="#" onclick="servicesManager.loadServices(${i})">${i}</a>
                         </li>`;
            }
        }
        
        // Próximo
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                       <a class="page-link" href="#" onclick="servicesManager.loadServices(${pagination.current_page + 1})">Próximo</a>
                     </li>`;
        }
        
        html += '</ul></nav>';
        container.html(html);
    }

    initSortable() {
        if (this.sortable) {
            this.sortable.destroy();
        }

        const list = document.getElementById('servicesList');
        if (!list) return;

        this.sortable = Sortable.create(list, {
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: (evt) => this.handleReorder(evt)
        });
    }

    async handleReorder(evt) {
        const services = [];
        const cards = document.querySelectorAll('[data-service-id]');
        
        cards.forEach((card, index) => {
            services.push({
                id: parseInt(card.dataset.serviceId),
                sort_order: index + 1
            });
        });

        try {
            const response = await fetch('{{ route("admin.services.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ services })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
            } else {
                this.showError('Erro ao reordenar serviços');
                this.loadServices(this.currentPage);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
            this.loadServices(this.currentPage);
        }
    }

    async editService(id) {
        try {
            const response = await fetch(`{{ route('admin.services.index') }}/${id}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.fillForm(data.data);
                this.editingId = id;
                $('#modalTitle').text('Editar Serviço');
                $('#serviceModal').modal('show');
            } else {
                this.showError('Erro ao carregar serviço');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    fillForm(service) {
        $('#title').val(service.title);
        $('#slug').val(service.slug);
        $('#description').val(service.description);
        $('#content').val(service.content);
        $('#icon').val(service.icon);
        $('#cover_image').val(service.cover_image);
        $('#featured').prop('checked', service.featured);
        $('#sort_order').val(service.sort_order);
        $('#active').prop('checked', service.active);
        
        // Mostrar preview da imagem se existir
        if (service.cover_thumbnail_url) {
            $('#imagePreview').html(`<img src="${service.cover_thumbnail_url}" class="img-thumbnail" style="max-height: 100px;">`);
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const url = this.editingId 
            ? `{{ route('admin.services.index') }}/${this.editingId}`
            : '{{ route("admin.services.store") }}';
        const method = this.editingId ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                $('#serviceModal').modal('hide');
                this.loadServices(this.currentPage);
            } else {
                this.showError(data.message || 'Erro ao salvar serviço');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    async toggleActive(id) {
        try {
            const response = await fetch(`{{ route('admin.services.index') }}/${id}/toggle-active`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadServices(this.currentPage);
            } else {
                this.showError('Erro ao alterar status');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    async toggleFeatured(id) {
        try {
            const response = await fetch(`{{ route('admin.services.index') }}/${id}/toggle-featured`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadServices(this.currentPage);
            } else {
                this.showError('Erro ao alterar destaque');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    async deleteService(id) {
        const result = await Swal.fire({
            title: 'Confirmar Exclusão',
            text: 'Tem certeza que deseja excluir este serviço?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`{{ route('admin.services.index') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadServices(this.currentPage);
            } else {
                this.showError('Erro ao excluir serviço');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro de conexão');
        }
    }

    resetForm() {
        $('#serviceForm')[0].reset();
        $('#imagePreview').empty();
        this.editingId = null;
        $('#modalTitle').text('Novo Serviço');
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
}

// Inicializar quando o documento estiver pronto
$(document).ready(() => {
    window.servicesManager = new ServicesManager();
});
</script>
@endsection