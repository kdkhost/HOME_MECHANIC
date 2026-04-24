@extends('layouts.admin')
@section('title', 'Serviços')
@section('page-title', 'Serviços')
@section('breadcrumb')
    <li class="breadcrumb-item active">Serviços</li>
@endsection

@section('styles')
<style>
.svc-card { transition: var(--hm-transition); cursor: default; }
.svc-card:hover { transform: translateY(-2px); box-shadow: var(--hm-shadow-md) !important; }
.svc-img { width:100%; height:160px; object-fit:cover; border-radius:var(--hm-radius) var(--hm-radius) 0 0; }
.svc-img-placeholder {
    width:100%; height:160px; background:var(--hm-primary-light);
    display:flex; align-items:center; justify-content:center;
    border-radius:var(--hm-radius) var(--hm-radius) 0 0;
    color:var(--hm-primary); font-size:2.5rem;
}
.img-upload-area {
    border:2px dashed var(--hm-border); border-radius:8px;
    padding:1.5rem; text-align:center; cursor:pointer;
    transition:var(--hm-transition); background:#fafafa;
}
.img-upload-area:hover { border-color:var(--hm-primary); background:var(--hm-primary-light); }
.img-upload-area img { max-height:120px; border-radius:6px; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-tools me-2" style="color:var(--hm-primary);"></i>Gerenciar Serviços</h2>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i> Novo Serviço
        </button>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                       placeholder="Buscar serviços..." oninput="debounceLoad()">
            </div>
            <div class="col-md-2">
                <select id="statusFilter" class="form-control form-control-sm" onchange="loadServices()">
                    <option value="">Todos os Status</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="featuredFilter" class="form-control form-control-sm" onchange="loadServices()">
                    <option value="">Todos</option>
                    <option value="1">Em Destaque</option>
                    <option value="0">Sem Destaque</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="sortBy" class="form-control form-control-sm" onchange="loadServices()">
                    <option value="sort_order">Ordem</option>
                    <option value="title">Título</option>
                    <option value="created_at">Data</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100" onclick="loadServices()">
                    <i class="fas fa-sync-alt"></i> Atualizar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Lista --}}
<div id="servicesContainer">
    <div class="text-center py-5">
        <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--hm-primary);"></i>
    </div>
</div>
<div id="paginationContainer" class="d-flex justify-content-center mt-3"></div>

{{-- Modal Criar/Editar --}}
<div class="modal fade" id="svcModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle" style="font-weight:700;font-size:1rem;color:#fff;">
                    <i class="fas fa-tools me-2"></i>Novo Serviço
                </span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="svcForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="svcId" name="_id">
                <input type="hidden" name="_method" id="svcMethod" value="POST">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" id="svcTitle" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Descrição Curta <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="svcDesc" rows="3" required maxlength="500"
                                          placeholder="Aparece nos cards e listagens (máx. 500 caracteres)"></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label>Conteúdo Completo</label>
                                <textarea class="form-control" name="content" id="svcContent" rows="5"
                                          placeholder="Descrição detalhada do serviço (aceita HTML)"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Imagem --}}
                            <div class="form-group">
                                <label>Imagem de Capa</label>
                                <x-filepond 
                                    name="cover_image" 
                                    id="svcCoverImage"
                                    accept="image/jpeg,image/png,image/webp"
                                    max-file-size="5MB"
                                />
                            </div>
                            {{-- Ícone --}}
                            <div class="form-group">
                                <label>Ícone <small class="text-muted">(Bootstrap Icons)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="iconPreview" class="bi bi-tools"></i></span>
                                    <input type="text" class="form-control" name="icon" id="svcIcon"
                                           placeholder="bi-tools" maxlength="100"
                                           oninput="document.getElementById('iconPreview').className='bi '+this.value">
                                </div>
                                <small class="form-text"><a href="https://icons.getbootstrap.com/" target="_blank">Ver ícones</a></small>
                            </div>
                            {{-- Ordem --}}
                            <div class="form-group">
                                <label>Ordem de Exibição</label>
                                <input type="number" class="form-control" name="sort_order" id="svcOrder" min="0">
                            </div>
                            {{-- Switches --}}
                            <div class="form-group">
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="svcFeatured" name="featured" value="1">
                                    <label class="custom-control-label" for="svcFeatured">Em Destaque</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="svcActive" name="active" value="1" checked>
                                    <label class="custom-control-label" for="svcActive">Ativo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnSave">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
var currentPage = 1;
var editingId   = null;
var debTimer    = null;
var sortable    = null;

// ── Carregar serviços ─────────────────────────────────────
function loadServices(page) {
    page = page || 1;
    currentPage = page;

    var params = new URLSearchParams({
        page:     page,
        per_page: 12,
        search:   document.getElementById('searchInput').value,
        active:   document.getElementById('statusFilter').value,
        featured: document.getElementById('featuredFilter').value,
        sort_by:  document.getElementById('sortBy').value,
    });

    document.getElementById('servicesContainer').innerHTML =
        '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--hm-primary);"></i></div>';

    $.ajax({
        url: '{{ route("admin.services.index") }}?' + params.toString(),
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            if (data.success) {
                renderServices(data.data);
                renderPagination(data.pagination);
            } else {
                HMToast.error('Erro ao carregar serviços.');
            }
        },
        error: function() { HMToast.error('Erro de conexão.'); }
    });
}

function debounceLoad() {
    clearTimeout(debTimer);
    debTimer = setTimeout(loadServices, 450);
}

// ── Renderizar cards ──────────────────────────────────────
function renderServices(services) {
    var c = document.getElementById('servicesContainer');
    if (!services.length) {
        c.innerHTML = '<div class="empty-state"><i class="fas fa-tools"></i><h5>Nenhum serviço encontrado</h5><p>Crie o primeiro serviço clicando em "Novo Serviço".</p></div>';
        return;
    }

    var html = '<div class="row g-3" id="svcList">';
    services.forEach(function(s) {
        var img = s.cover_image_url
            ? '<img src="' + s.cover_image_url + '" class="svc-img" alt="' + s.title + '">'
            : '<div class="svc-img-placeholder"><i class="bi ' + (s.icon || 'bi-tools') + '"></i></div>';

        html += '<div class="col-md-4 col-lg-3" data-id="' + s.id + '">' +
            '<div class="card svc-card">' + img +
            '<div class="card-body pb-2">' +
            '<div class="d-flex align-items-start justify-content-between gap-1">' +
            '<div style="font-weight:700;font-size:0.9rem;color:var(--hm-text);">' + s.title + '</div>' +
            '<i class="fas fa-grip-vertical drag-handle" style="color:#94a3b8;cursor:move;flex-shrink:0;margin-top:2px;"></i>' +
            '</div>' +
            '<p style="font-size:0.78rem;color:var(--hm-text-muted);margin:0.35rem 0 0.5rem;line-height:1.4;">' +
            s.description.substring(0, 80) + (s.description.length > 80 ? '…' : '') + '</p>' +
            '<div class="d-flex gap-1 flex-wrap">' +
            (s.active ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-secondary">Inativo</span>') +
            (s.featured ? '<span class="badge badge-warning">Destaque</span>' : '') +
            '</div></div>' +
            '<div class="card-footer py-2">' +
            '<div class="btn-group btn-group-sm w-100">' +
            '<button class="btn btn-warning" onclick="editService(' + s.id + ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>' +
            '<button class="btn btn-' + (s.active ? 'secondary' : 'success') + '" onclick="toggleActive(' + s.id + ')" title="' + (s.active ? 'Desativar' : 'Ativar') + '"><i class="fas fa-' + (s.active ? 'pause' : 'play') + '"></i></button>' +
            '<button class="btn btn-' + (s.featured ? 'secondary' : 'warning') + '" onclick="toggleFeatured(' + s.id + ')" title="' + (s.featured ? 'Remover destaque' : 'Destacar') + '"><i class="fas fa-star' + (s.featured ? '' : '-o') + '"></i></button>' +
            '<button class="btn btn-danger btn-delete" onclick="deleteService(' + s.id + ', \'' + s.title.replace(/'/g,'') + '\')" title="Excluir"><i class="fas fa-trash"></i></button>' +
            '</div></div></div></div>';
    });
    html += '</div>';
    c.innerHTML = html;

    // Sortable
    if (sortable) sortable.destroy();
    var list = document.getElementById('svcList');
    if (list) {
        sortable = Sortable.create(list, {
            handle: '.drag-handle',
            ghostClass: 'opacity-50',
            onEnd: function() {
                var items = [];
                document.querySelectorAll('#svcList [data-id]').forEach(function(el, i) {
                    items.push({ id: parseInt(el.dataset.id), sort_order: i + 1 });
                });
                $.ajax({
                    url: '{{ route("admin.services.reorder") }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: JSON.stringify({ services: items }),
                    success: function(r) { if (r.success) HMToast.success('Ordem salva!'); }
                });
            }
        });
    }
}

// ── Paginação ─────────────────────────────────────────────
function renderPagination(p) {
    var c = document.getElementById('paginationContainer');
    if (p.last_page <= 1) { c.innerHTML = ''; return; }
    var html = '<nav><ul class="pagination pagination-sm">';
    if (p.current_page > 1) html += '<li class="page-item"><a class="page-link" href="#" onclick="loadServices(' + (p.current_page-1) + ');return false;">‹</a></li>';
    for (var i = 1; i <= p.last_page; i++) {
        html += '<li class="page-item' + (i===p.current_page?' active':'') + '"><a class="page-link" href="#" onclick="loadServices(' + i + ');return false;">' + i + '</a></li>';
    }
    if (p.current_page < p.last_page) html += '<li class="page-item"><a class="page-link" href="#" onclick="loadServices(' + (p.current_page+1) + ');return false;">›</a></li>';
    html += '</ul></nav>';
    c.innerHTML = html;
}

// ── Modal ─────────────────────────────────────────────────
function openModal(id) {
    resetForm();
    var form = document.getElementById('svcForm');
    if (id) {
        editingId = id;
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pencil-alt me-2"></i>Editar Serviço';
    } else {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Novo Serviço';
        form.action = '{{ route("admin.services.store") }}';
    }
    var modal = new bootstrap.Modal(document.getElementById('svcModal'));
    modal.show();
}

function resetForm() {
    editingId = null;
    document.getElementById('svcForm').reset();
    document.getElementById('svcId').value = '';
    document.getElementById('svcMethod').value = 'POST';
    // Limpar FilePond se existir
    var pondElement = document.getElementById('svcCoverImage');
    if (pondElement && pondElement.filepond) {
        pondElement.filepond.removeFiles();
    }
    document.getElementById('svcActive').checked = true;
    document.getElementById('iconPreview').className = 'bi bi-tools';
}

function editService(id) {
    $.ajax({
        url: '{{ url("admin/services") }}/' + id,
        headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            if (!data.success) { HMToast.error('Erro ao carregar serviço.'); return; }
            var s = data.data;
            document.getElementById('svcId').value       = s.id;
            document.getElementById('svcMethod').value   = 'PUT';
            document.getElementById('svcTitle').value    = s.title || '';
            document.getElementById('svcDesc').value     = s.description || '';
            document.getElementById('svcContent').value  = s.content || '';
            document.getElementById('svcIcon').value     = s.icon || '';
            document.getElementById('svcOrder').value    = s.sort_order || '';
            document.getElementById('svcFeatured').checked = !!s.featured;
            document.getElementById('svcActive').checked   = !!s.active;
            document.getElementById('iconPreview').className = 'bi ' + (s.icon || 'bi-tools');

            // Carregar imagem no FilePond se existir
            if (s.cover_image) {
                var pondElement = document.getElementById('svcCoverImage');
                if (pondElement && pondElement.filepond) {
                    pondElement.filepond.removeFiles();
                    pondElement.filepond.addFile(s.cover_image);
                }
            }
            // Define o action do form para a rota de update
            document.getElementById('svcForm').action = '{{ route("admin.services.index") }}/' + s.id;
            openModal(id);
        },
        error: function() { HMToast.error('Erro de conexão.'); }
    });
}

// ── Limpar FilePond ao fechar modal ─────────────────────
$('#svcModal').on('hidden.bs.modal', function() {
    var pondElement = document.getElementById('svcCoverImage');
    if (pondElement && pondElement.filepond) {
        pondElement.filepond.removeFiles();
    }
});

// ── Submit ────────────────────────────────────────────────
document.getElementById('svcForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

    var id     = document.getElementById('svcId').value;
    var method = document.getElementById('svcMethod').value;
    var url    = id
        ? '{{ url("admin/services") }}/' + id
        : '{{ route("admin.services.store") }}';

    var fd = new FormData(this);
    if (method === 'PUT') fd.append('_method', 'PUT');

    $.ajax({
        url: url,
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(data) {
            if (data.success) {
                HMToast.success(data.message);
                bootstrap.Modal.getInstance(document.getElementById('svcModal')).hide();
                loadServices(currentPage);
            } else {
                HMToast.error(data.message || 'Erro ao salvar.');
            }
        },
        error: function(xhr) {
            var msg = 'Erro ao salvar.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            HMToast.error(msg);
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Salvar';
        }
    });
});

// ── Toggle / Delete ───────────────────────────────────────
function toggleActive(id) {
    $.ajax({
        url: '{{ route("admin.services.index") }}/' + id + '/toggle-active',
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
        success: function(d) { if (d.success) { HMToast.success(d.message); loadServices(currentPage); } else HMToast.error(d.message); },
        error: function() { HMToast.error('Erro de conexão.'); }
    });
}

function toggleFeatured(id) {
    $.ajax({
        url: '{{ route("admin.services.index") }}/' + id + '/toggle-featured',
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
        success: function(d) { if (d.success) { HMToast.success(d.message); loadServices(currentPage); } else HMToast.error(d.message); },
        error: function() { HMToast.error('Erro de conexão.'); }
    });
}

function deleteService(id, name) {
    Swal.fire({
        title: 'Excluir serviço?',
        html: 'Deseja excluir <strong>' + name + '</strong>?<br><small style="color:#64748b;">Esta ação não pode ser desfeita.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (!r.isConfirmed) return;
        $.ajax({
            url: '{{ url("admin/services") }}/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
            success: function(d) { if (d.success) { HMToast.success(d.message); loadServices(currentPage); } else HMToast.error(d.message); },
            error: function() { HMToast.error('Erro de conexão.'); }
        });
    });
}

// ── Init ──────────────────────────────────────────────────
$(function() { loadServices(); });
</script>
@endsection
