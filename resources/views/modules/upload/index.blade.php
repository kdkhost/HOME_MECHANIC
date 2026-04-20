@extends('layouts.admin')
@section('title', 'Upload de Arquivos')
@section('page-title', 'Upload de Arquivos')
@section('breadcrumb')
    <li class="breadcrumb-item active">Upload</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-cloud-upload-alt mr-2" style="color:var(--hm-primary);"></i>Upload de Arquivos</h2>
</div>

<!-- Área de upload -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-upload"></i> Enviar Arquivos</span>
    </div>
    <div class="card-body">
        <div class="file-upload-area" id="dropZone">
            <i class="fas fa-cloud-upload-alt" style="font-size:3rem;color:var(--hm-primary);display:block;margin-bottom:1rem;"></i>
            <h5>Arraste arquivos aqui ou clique para selecionar</h5>
            <p class="text-muted mb-3">Imagens, vídeos e documentos são aceitos</p>
            <input type="file" id="fileInput" multiple style="display:none;">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-folder-open"></i> Selecionar Arquivos
            </button>
        </div>

        <!-- Progresso -->
        <div id="uploadProgress" style="display:none;" class="mt-3">
            <div class="progress" style="height:8px;border-radius:4px;">
                <div class="progress-bar bg-warning" id="progressBar" style="width:0%;transition:width 0.3s;"></div>
            </div>
            <small class="text-muted mt-1 d-block" id="progressText">Enviando...</small>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-list"></i> Arquivos Enviados</span>
        <div class="card-tools">
            <div class="input-group input-group-sm" style="width:220px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar arquivos...">
                <div class="input-group-append">
                    <button class="btn btn-secondary" onclick="loadFiles()"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Filtros de tipo -->
        <div class="px-3 pt-3 pb-2 border-bottom d-flex gap-2" style="gap:0.5rem;">
            <button class="btn btn-sm btn-primary filter-btn active" data-type="">Todos</button>
            <button class="btn btn-sm btn-secondary filter-btn" data-type="images"><i class="fas fa-image"></i> Imagens</button>
            <button class="btn btn-sm btn-secondary filter-btn" data-type="videos"><i class="fas fa-video"></i> Vídeos</button>
            <button class="btn btn-sm btn-secondary filter-btn" data-type="documents"><i class="fas fa-file-alt"></i> Documentos</button>
        </div>

        <!-- Lista de arquivos -->
        <div id="filesContainer" class="p-3">
            <div class="text-center py-4">
                <div class="spinner-border text-warning" role="status"><span class="sr-only">Carregando...</span></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
let currentType = '';

$(document).ready(function() {
    loadFiles();

    // Filtros de tipo
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('btn-primary').addClass('btn-secondary');
        $(this).removeClass('btn-secondary').addClass('btn-primary');
        currentType = $(this).data('type');
        loadFiles();
    });

    // Busca
    let searchTimer;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadFiles, 400);
    });

    // Drag & drop
    const dropZone = document.getElementById('dropZone');
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        uploadFiles(e.dataTransfer.files);
    });

    // Input file
    document.getElementById('fileInput').addEventListener('change', function() {
        uploadFiles(this.files);
    });
});

function loadFiles() {
    const params = new URLSearchParams({
        type: currentType,
        search: $('#searchInput').val(),
        per_page: 20
    });

    $.ajax({
        url: '{{ route("admin.upload.index") }}?' + params,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
            if (res.success) renderFiles(res.data);
        },
        error: function() {
            $('#filesContainer').html('<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h5>Erro ao carregar arquivos</h5></div>');
        }
    });
}

function renderFiles(files) {
    if (!files.length) {
        $('#filesContainer').html(`
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h5>Nenhum arquivo encontrado</h5>
                <p>Envie arquivos usando a área acima.</p>
            </div>
        `);
        return;
    }

    const html = `
        <div class="row">
            ${files.map(f => {
                const imgSrc = f.is_image ? (f.thumbnail_url || f.url) : '';
                return `
                <div class="col-md-3 col-sm-4 col-6 mb-3">
                    <div class="card h-100" style="border:1px solid var(--hm-border)!important;box-shadow:none!important;">
                        <div style="height:120px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;border-radius:10px 10px 0 0;overflow:hidden;cursor:pointer;" onclick="openPreview('${f.url}', '${f.original_name.replace(/'/g, "\\'")}', ${f.is_image})">
                            ${imgSrc
                                ? '<img src="' + imgSrc + '" style="width:100%;height:100%;object-fit:cover;">'
                                : '<i class="' + (f.icon || 'fas fa-file') + '" style="font-size:2.5rem;color:var(--hm-primary);"></i>'
                            }
                        </div>
                        <div class="card-body p-2">
                            <div style="font-size:0.78rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${f.original_name}">${f.original_name}</div>
                            <div style="font-size:0.72rem;color:#718096;">${f.formatted_size || ''}</div>
                        </div>
                        <div class="card-footer p-1 d-flex justify-content-between" style="background:transparent;border-top:1px solid var(--hm-border);">
                            <button onclick="openPreview('${f.url}', '${f.original_name.replace(/'/g, "\\'")}', ${f.is_image})" class="btn btn-sm btn-info" title="Ver"><i class="fas fa-eye"></i></button>
                            <button onclick="deleteFile('${f.uuid}')" class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>`;
            }).join('')}
        </div>
    `;
    $('#filesContainer').html(html);
}

function openPreview(url, title, isImage) {
    if (isImage) {
        GLightbox({ elements: [{ href: url, title: title }] }).open();
    } else {
        window.open(url, '_blank');
    }
}

function uploadFiles(files) {
    if (!files.length) return;

    const formData = new FormData();
    Array.from(files).forEach(f => formData.append('files[]', f));

    $('#uploadProgress').show();
    $('#progressBar').css('width', '0%');
    $('#progressText').text('Enviando...');

    $.ajax({
        url: '{{ route("admin.upload.store-multiple") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    $('#progressBar').css('width', pct + '%');
                    $('#progressText').text(`Enviando... ${pct}%`);
                }
            });
            return xhr;
        },
        success: function(res) {
            $('#progressBar').css('width', '100%');
            $('#progressText').text(res.message || 'Concluído!');
            setTimeout(() => $('#uploadProgress').hide(), 2000);
            loadFiles();
            Swal.fire({ icon: 'success', title: 'Upload concluído!', text: res.message, timer: 2000, showConfirmButton: false });
        },
        error: function() {
            $('#uploadProgress').hide();
            Swal.fire({ icon: 'error', title: 'Erro no upload', text: 'Tente novamente.' });
        }
    });
}

function deleteFile(uuid) {
    Swal.fire({
        title: 'Excluir arquivo?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        $.ajax({
            url: `/admin/upload/${uuid}`,
            method: 'DELETE',
            success: function(res) {
                if (res.success) {
                    loadFiles();
                    Swal.fire({ icon: 'success', title: 'Excluído!', timer: 1500, showConfirmButton: false });
                }
            }
        });
    });
}
</script>
@endsection
