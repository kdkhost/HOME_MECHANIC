@extends('layouts.admin')
@section('title', 'Patrocinadores')
@section('page-title', 'Patrocinadores')
@section('breadcrumb')
    <li class="breadcrumb-item active">Patrocinadores</li>
@endsection

@section('styles')
<style>
.sponsor-card { transition: var(--hm-transition); cursor: default; }
.sponsor-card:hover { transform: translateY(-2px); box-shadow: var(--hm-shadow-md) !important; }
.sponsor-logo { width:100%; height:120px; object-fit:contain; padding:1rem; background:#f8f9fa; border-radius:var(--hm-radius) var(--hm-radius) 0 0; }
.sponsor-logo-placeholder { width:100%; height:120px; background:var(--hm-primary-light); display:flex; align-items:center; justify-content:center; border-radius:var(--hm-radius) var(--hm-radius) 0 0; color:var(--hm-primary); font-size:2.5rem; }
.animation-badge { font-size:0.7rem; padding:0.2rem 0.5rem; border-radius:4px; background:#e9ecef; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-handshake me-2" style="color:var(--hm-primary);"></i>Gerenciar Patrocinadores</h2>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i> Novo Patrocinador
        </button>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Buscar patrocinadores..." oninput="debounceLoad()">
            </div>
            <div class="col-md-2">
                <select id="statusFilter" class="form-control form-control-sm" onchange="loadSponsors()">
                    <option value="">Todos</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Lista --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Patrocinadores</span>
        <div class="card-tools" id="paginationInfo"></div>
    </div>
    <div class="card-body p-0">
        <div id="sponsorsContainer">
            <div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--hm-primary);"></i></div>
        </div>
        <div id="paginationContainer" class="px-3 py-2"></div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="sponsorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Novo Patrocinador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sponsorForm">
                @csrf
                <input type="hidden" id="sponsorId" name="id">
                <input type="hidden" id="sponsorMethod" name="_method" value="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label>Nome *</label>
                                <input type="text" class="form-control" name="name" id="sponsorName" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Website</label>
                                <input type="url" class="form-control" name="website" id="sponsorWebsite" placeholder="https://">
                            </div>
                            <div class="form-group mb-3">
                                <label>Descricao</label>
                                <textarea class="form-control" name="description" id="sponsorDescription" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Animação</label>
                                        <select class="form-control" name="animation" id="sponsorAnimation">
                                            <option value="fade">Fade</option>
                                            <option value="slide">Slide Up</option>
                                            <option value="zoom">Zoom</option>
                                            <option value="flip">Flip</option>
                                            <option value="bounce">Bounce</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Velocidade</label>
                                        <select class="form-control" name="speed" id="sponsorSpeed">
                                            <option value="slow">Lenta</option>
                                            <option value="normal" selected>Normal</option>
                                            <option value="fast">Rapida</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-block">Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="sponsorActive" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="sponsorActive">Ativo no site</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Logo</label>
                                <x-filepond name="logo" id="sponsorLogo" />
                                <small class="text-muted">Formato: PNG, SVG ou JPG com fundo transparente recomendado.</small>
                            </div>
                            <div id="logoPreview" class="text-center mb-3 d-none">
                                <img src="" class="img-fluid rounded" style="max-height:100px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSave"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let currentPage = 1;
let debTimer = null;
let sortable = null;

function loadSponsors(page = 1) {
    currentPage = page;
    var params = new URLSearchParams({
        page: page,
        per_page: 12,
        search: document.getElementById('searchInput').value,
        active: document.getElementById('statusFilter').value,
    });

    document.getElementById('sponsorsContainer').innerHTML =
        '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--hm-primary);"></i></div>';

    $.ajax({
        url: '{{ route("admin.sponsors.index") }}?' + params.toString(),
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            if (data.success) {
                renderSponsors(data.data);
                renderPagination(data.pagination);
                document.getElementById('paginationInfo').textContent = data.pagination.total + ' patrocinador(es)';
            } else {
                HMToast.error('Erro ao carregar.');
            }
        },
        error: function() { HMToast.error('Erro de conexao.'); }
    });
}

function debounceLoad() {
    clearTimeout(debTimer);
    debTimer = setTimeout(() => loadSponsors(1), 450);
}

function renderSponsors(sponsors) {
    var c = document.getElementById('sponsorsContainer');
    if (!sponsors.length) {
        c.innerHTML = '<div class="empty-state"><i class="fas fa-handshake"></i><h5>Nenhum patrocinador</h5><p>Adicione o primeiro patrocinador.</p></div>';
        return;
    }

    var html = '<div class="row g-3" id="sponsorList">';
    sponsors.forEach(function(s) {
        var img = s.logo
            ? '<img src="/' + s.logo.replace(/^\//, '') + '" class="sponsor-logo" alt="' + s.name + '">'
            : '<div class="sponsor-logo-placeholder"><i class="fas fa-image"></i></div>';

        var animBadge = '<span class="animation-badge">' + s.animation + '</span>';
        var speedBadge = '<span class="animation-badge ms-1">' + s.speed + '</span>';

        html += '<div class="col-md-4 col-lg-3" data-id="' + s.id + '">' +
            '<div class="card sponsor-card">' + img +
            '<div class="card-body pb-2">' +
            '<div class="d-flex align-items-start justify-content-between gap-1">' +
            '<div style="font-weight:700;font-size:0.9rem;color:var(--hm-text);">' + s.name + '</div>' +
            '<i class="fas fa-grip-vertical drag-handle" style="color:#94a3b8;cursor:move;flex-shrink:0;margin-top:2px;"></i>' +
            '</div>' +
            '<div class="d-flex gap-1 mt-1">' + animBadge + speedBadge + '</div>' +
            '<div class="d-flex gap-1 flex-wrap mt-2">' +
            (s.is_active ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-secondary">Inativo</span>') +
            '</div></div>' +
            '<div class="card-footer py-2">' +
            '<div class="btn-group btn-group-sm w-100">' +
            '<button class="btn btn-warning" onclick="editSponsor(' + s.id + ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>' +
            '<button class="btn btn-' + (s.is_active ? 'secondary' : 'success') + '" onclick="toggleActive(' + s.id + ')" title="' + (s.is_active ? 'Desativar' : 'Ativar') + '"><i class="fas fa-' + (s.is_active ? 'pause' : 'play') + '"></i></button>' +
            '<button class="btn btn-danger btn-delete" onclick="deleteSponsor(' + s.id + ', \'' + s.name.replace(/'/g,'') + '\')" title="Excluir"><i class="fas fa-trash"></i></button>' +
            '</div></div></div></div>';
    });
    html += '</div>';
    c.innerHTML = html;

    if (sortable) sortable.destroy();
    var list = document.getElementById('sponsorList');
    if (list) {
        sortable = Sortable.create(list, {
            handle: '.drag-handle',
            ghostClass: 'opacity-50',
            onEnd: function() {
                var items = [];
                document.querySelectorAll('#sponsorList [data-id]').forEach(function(el, i) {
                    items.push({ id: parseInt(el.dataset.id), sort_order: i + 1 });
                });
                $.ajax({
                    url: '{{ route("admin.sponsors.reorder") }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: JSON.stringify({ sponsors: items }),
                    success: function(r) { if (r.success) HMToast.success('Ordem salva!'); }
                });
            }
        });
    }
}

function renderPagination(p) {
    var c = document.getElementById('paginationContainer');
    if (p.last_page <= 1) { c.innerHTML = ''; return; }
    var html = '<nav><ul class="pagination pagination-sm justify-content-center">';
    if (p.current_page > 1) html += '<li class="page-item"><a class="page-link" href="#" onclick="loadSponsors(' + (p.current_page-1) + ');return false;">‹</a></li>';
    for (var i = 1; i <= p.last_page; i++) {
        html += '<li class="page-item' + (i===p.current_page?' active':'') + '"><a class="page-link" href="#" onclick="loadSponsors(' + i + ');return false;">' + i + '</a></li>';
    }
    if (p.current_page < p.last_page) html += '<li class="page-item"><a class="page-link" href="#" onclick="loadSponsors(' + (p.current_page+1) + ');return false;">›</a></li>';
    html += '</ul></nav>';
    c.innerHTML = html;
}

window.openModal = function() {
    try {
        console.log('Abrindo modal de patrocinador...');
        document.getElementById('sponsorForm').reset();
        document.getElementById('sponsorId').value = '';
        document.getElementById('sponsorMethod').value = 'POST';
        document.getElementById('modalTitle').textContent = 'Novo Patrocinador';
        document.getElementById('sponsorActive').checked = true;
        document.getElementById('logoPreview').classList.add('d-none');
        if (window.filePondInstances && window.filePondInstances.logo) {
            window.filePondInstances.logo.removeFiles();
        }
        var modalEl = document.getElementById('sponsorModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            console.error('Bootstrap Modal não está disponível');
            HMToast.error('Erro ao abrir modal. Recarregue a página.');
        }
    } catch (e) {
        console.error('Erro ao abrir modal:', e);
        HMToast.error('Erro ao abrir modal: ' + e.message);
    }
};

function editSponsor(id) {
    $.ajax({
        url: '{{ url("admin/sponsors") }}/' + id,
        headers: { 'Accept': 'application/json' },
        success: function(r) {
            if (!r.success) { HMToast.error('Erro ao carregar.'); return; }
            var s = r.data;
            document.getElementById('sponsorId').value = s.id;
            document.getElementById('sponsorMethod').value = 'PUT';
            document.getElementById('modalTitle').textContent = 'Editar Patrocinador';
            document.getElementById('sponsorName').value = s.name;
            document.getElementById('sponsorWebsite').value = s.website || '';
            document.getElementById('sponsorDescription').value = s.description || '';
            document.getElementById('sponsorAnimation').value = s.animation;
            document.getElementById('sponsorSpeed').value = s.speed;
            document.getElementById('sponsorActive').checked = s.is_active;

            if (s.logo) {
                document.getElementById('logoPreview').querySelector('img').src = '/' + s.logo.replace(/^\//, '');
                document.getElementById('logoPreview').classList.remove('d-none');
            } else {
                document.getElementById('logoPreview').classList.add('d-none');
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('sponsorModal')).show();
        },
        error: function() { HMToast.error('Erro de conexao.'); }
    });
}

document.getElementById('sponsorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

    var id = document.getElementById('sponsorId').value;
    var method = document.getElementById('sponsorMethod').value;
    var url = id ? '{{ url("admin/sponsors") }}/' + id : '{{ route("admin.sponsors.store") }}';

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
                bootstrap.Modal.getInstance(document.getElementById('sponsorModal')).hide();
                loadSponsors(currentPage);
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

function toggleActive(id) {
    $.ajax({
        url: '{{ url("admin/sponsors") }}/' + id + '/toggle-active',
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
        success: function(d) { if (d.success) { HMToast.success(d.message); loadSponsors(currentPage); } else HMToast.error(d.message); },
        error: function() { HMToast.error('Erro de conexao.'); }
    });
}

function deleteSponsor(id, name) {
    Swal.fire({
        title: 'Excluir patrocinador?',
        html: 'Deseja excluir <strong>' + name + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (!r.isConfirmed) return;
        $.ajax({
            url: '{{ url("admin/sponsors") }}/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
            success: function(d) { if (d.success) { HMToast.success(d.message); loadSponsors(currentPage); } else HMToast.error(d.message); },
            error: function() { HMToast.error('Erro de conexao.'); }
        });
    });
}

$(function() { loadSponsors(); });
</script>
@endsection
