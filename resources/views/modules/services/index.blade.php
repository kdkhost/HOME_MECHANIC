@extends('layouts.admin')
@section('title', 'Servicos')
@section('page-title', 'Servicos')
@section('breadcrumb')
    <li class="breadcrumb-item active">Servicos</li>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<style>
.svc-card { transition: var(--hm-transition); }
.svc-card:hover { transform: translateY(-2px); box-shadow: var(--hm-shadow-md) !important; }
.svc-img { width:100%; height:160px; object-fit:cover; border-radius:var(--hm-radius) var(--hm-radius) 0 0; }
.svc-img-placeholder { width:100%; height:160px; background:var(--hm-primary-light); display:flex; align-items:center; justify-content:center; border-radius:var(--hm-radius) var(--hm-radius) 0 0; color:var(--hm-primary); font-size:2.5rem; }
.img-upload-area { border:2px dashed var(--hm-border); border-radius:8px; padding:1.25rem; text-align:center; cursor:pointer; transition:var(--hm-transition); background:#fafafa; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.4rem; }
.img-upload-area:hover { border-color:var(--hm-primary); background:var(--hm-primary-light); }
.img-upload-area img { max-height:100px; border-radius:6px; object-fit:cover; }
.note-editor.note-frame { border:1.5px solid var(--hm-border) !important; border-radius:0 0 6px 6px !important; }
.note-toolbar { background:#f8fafc !important; border:1.5px solid var(--hm-border) !important; border-bottom:none !important; border-radius:6px 6px 0 0 !important; }
.note-toolbar .note-btn:hover, .note-toolbar .note-btn.active { background:var(--hm-primary) !important; color:#fff !important; border-color:var(--hm-primary) !important; }
.note-editable { min-height:100px !important; font-size:0.88rem !important; padding:10px !important; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-tools me-2" style="color:var(--hm-primary);"></i>Gerenciar Servicos</h2>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Novo Servico</button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Buscar servicos..." oninput="debounceLoad()"></div>
            <div class="col-md-2"><select id="statusFilter" class="form-control form-control-sm" onchange="loadServices()"><option value="">Todos os Status</option><option value="1">Ativos</option><option value="0">Inativos</option></select></div>
            <div class="col-md-2"><select id="featuredFilter" class="form-control form-control-sm" onchange="loadServices()"><option value="">Todos</option><option value="1">Em Destaque</option><option value="0">Sem Destaque</option></select></div>
            <div class="col-md-2"><select id="sortBy" class="form-control form-control-sm" onchange="loadServices()"><option value="sort_order">Ordem</option><option value="title">Titulo</option><option value="created_at">Data</option></select></div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm w-100" onclick="loadServices()"><i class="fas fa-sync-alt"></i> Atualizar</button></div>
        </div>
    </div>
</div>

<div id="servicesContainer"><div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--hm-primary);"></i></div></div>
<div id="paginationContainer" class="d-flex justify-content-center mt-3"></div>

<div class="modal fade" id="svcModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle" style="font-weight:700;font-size:1rem;color:#fff;"><i class="fas fa-tools me-2"></i>Novo Servico</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="svcForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="svcId">
                <input type="hidden" name="_method" id="svcMethod" value="POST">
                <input type="hidden" name="remove_image" id="removeImage" value="0">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Titulo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" id="svcTitle" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Descricao Curta <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="svcDesc" rows="3"></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label>Conteudo Completo</label>
                                <textarea class="form-control" name="content" id="svcContent" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Imagem de Capa</label>
                                <div class="img-upload-area" id="imgUploadArea" onclick="document.getElementById('coverImageInput').click()" ondragover="event.preventDefault();this.style.borderColor='var(--hm-primary)'" ondragleave="this.style.borderColor=''" ondrop="handleImgDrop(event)">
                                    <div id="imgPreviewWrap">
                                        <i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:var(--hm-primary);opacity:0.7;"></i>
                                        <div style="font-size:0.8rem;color:var(--hm-text-muted);">Clique ou arraste aqui</div>
                                        <div style="font-size:0.72rem;color:#94a3b8;">JPG, PNG, WebP - max 5MB</div>
                                    </div>
                                </div>
                                <input type="file" id="coverImageInput" name="cover_image" accept="image/jpeg,image/png,image/webp" class="d-none" data-no-filepond onchange="previewCoverImage(this)">
                                <button type="button" class="btn btn-danger btn-sm mt-1 w-100 d-none" id="btnRemoveImg" onclick="removeCoverImage()"><i class="fas fa-trash me-1"></i> Remover imagem</button>
                            </div>
                            <div class="form-group">
                                <label>Icone (Bootstrap Icons)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="iconPreview" class="bi bi-tools"></i></span>
                                    <input type="text" class="form-control" name="icon" id="svcIcon" placeholder="bi-tools" maxlength="100" oninput="document.getElementById('iconPreview').className='bi '+this.value">
                                </div>
                                <small class="form-text"><a href="https://icons.getbootstrap.com/" target="_blank">Ver icones</a></small>
                            </div>
                            <div class="form-group">
                                <label>Ordem de Exibicao</label>
                                <input type="number" class="form-control" name="sort_order" id="svcOrder" min="0">
                            </div>
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
                    <button type="submit" class="btn btn-primary" id="btnSave"><i class="fas fa-save"></i> Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-pt-BR.min.js"></script>
<script>
var currentPage = 1, editingId = null, debTimer = null, sortable = null;

var snDesc = { lang:'pt-BR', height:110, toolbar:[['style',['bold','italic','underline','clear']],['para',['ul','ol']],['view',['codeview']]], placeholder:'Descricao curta...', callbacks:{ onImageUpload:function(){ HMToast.warning('Use o campo de imagem de capa.'); } } };
var snContent = { lang:'pt-BR', height:200, toolbar:[['style',['style']],['font',['bold','italic','underline','clear']],['color',['color']],['para',['ul','ol','paragraph']],['insert',['link','hr']],['view',['codeview','fullscreen']]], placeholder:'Conteudo detalhado...', callbacks:{ onImageUpload:function(){ HMToast.warning('Use o campo de imagem de capa.'); } } };

function loadServices(page) {
    page = page||1; currentPage = page;
    var p = new URLSearchParams({ page:page, per_page:12, search:document.getElementById('searchInput').value, active:document.getElementById('statusFilter').value, featured:document.getElementById('featuredFilter').value, sort_by:document.getElementById('sortBy').value });
    document.getElementById('servicesContainer').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--hm-primary);"></i></div>';
    $.ajax({ url:'{{ route("admin.services.index") }}?'+p.toString(), headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
        success:function(d){ if(d.success){ renderServices(d.data); renderPagination(d.pagination); } else HMToast.error('Erro ao carregar.'); },
        error:function(){ HMToast.error('Erro de conexao.'); }
    });
}
function debounceLoad(){ clearTimeout(debTimer); debTimer=setTimeout(loadServices,450); }

function renderServices(services) {
    var c = document.getElementById('servicesContainer');
    if(!services.length){ c.innerHTML='<div class="empty-state"><i class="fas fa-tools"></i><h5>Nenhum servico encontrado</h5><p>Crie o primeiro servico.</p></div>'; return; }
    var html='<div class="row g-3" id="svcList">';
    services.forEach(function(s){
        var img = s.cover_image_url ? '<img src="'+s.cover_image_url+'" class="svc-img" alt="'+s.title+'">' : '<div class="svc-img-placeholder"><i class="bi '+(s.icon||'bi-tools')+'"></i></div>';
        html+='<div class="col-md-4 col-lg-3" data-id="'+s.id+'"><div class="card svc-card">'+img+'<div class="card-body pb-2"><div class="d-flex align-items-start justify-content-between gap-1"><div style="font-weight:700;font-size:0.9rem;color:var(--hm-text);">'+s.title+'</div><i class="fas fa-grip-vertical drag-handle" style="color:#94a3b8;cursor:move;flex-shrink:0;margin-top:2px;"></i></div><p style="font-size:0.78rem;color:var(--hm-text-muted);margin:0.35rem 0 0.5rem;line-height:1.4;">'+(s.description||'').substring(0,80)+((s.description||'').length>80?'...':'')+'</p><div class="d-flex gap-1 flex-wrap">'+(s.active?'<span class="badge badge-success">Ativo</span>':'<span class="badge badge-secondary">Inativo</span>')+(s.featured?'<span class="badge badge-warning">Destaque</span>':'')+'</div></div><div class="card-footer py-2"><div class="btn-group btn-group-sm w-100"><button class="btn btn-warning" onclick="editService('+s.id+')" title="Editar"><i class="fas fa-pencil-alt"></i></button><button class="btn btn-'+(s.active?'secondary':'success')+'" onclick="toggleActive('+s.id+')" title="'+(s.active?'Desativar':'Ativar')+'"><i class="fas fa-'+(s.active?'pause':'play')+'"></i></button><button class="btn btn-'+(s.featured?'secondary':'warning')+'" onclick="toggleFeatured('+s.id+')" title="'+(s.featured?'Remover destaque':'Destacar')+'"><i class="fas fa-star'+(s.featured?'':'-o')+'"></i></button><button class="btn btn-danger" onclick="deleteService('+s.id+',\''+((s.title||'').replace(/\'/g,''))+'\')" title="Excluir"><i class="fas fa-trash"></i></button></div></div></div></div>';
    });
    html+='</div>'; c.innerHTML=html;
    if(sortable) sortable.destroy();
    var list=document.getElementById('svcList');
    if(list){ sortable=Sortable.create(list,{ handle:'.drag-handle', ghostClass:'opacity-50', onEnd:function(){ var items=[]; document.querySelectorAll('#svcList [data-id]').forEach(function(el,i){ items.push({id:parseInt(el.dataset.id),sort_order:i+1}); }); $.ajax({ url:'{{ route("admin.services.reorder") }}', method:'POST', contentType:'application/json', headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}, data:JSON.stringify({services:items}), success:function(r){ if(r.success) HMToast.success('Ordem salva!'); } }); } }); }
}

function renderPagination(p) {
    var c=document.getElementById('paginationContainer');
    if(p.last_page<=1){ c.innerHTML=''; return; }
    var html='<nav><ul class="pagination pagination-sm">';
    if(p.current_page>1) html+='<li class="page-item"><a class="page-link" href="#" onclick="loadServices('+(p.current_page-1)+');return false;">&#8249;</a></li>';
    for(var i=1;i<=p.last_page;i++) html+='<li class="page-item'+(i===p.current_page?' active':'')+'"><a class="page-link" href="#" onclick="loadServices('+i+');return false;">'+i+'</a></li>';
    if(p.current_page<p.last_page) html+='<li class="page-item"><a class="page-link" href="#" onclick="loadServices('+(p.current_page+1)+');return false;">&#8250;</a></li>';
    html+='</ul></nav>'; c.innerHTML=html;
}

function previewCoverImage(input) {
    if(!input.files||!input.files[0]) return;
    if(input.files[0].size>5*1024*1024){ HMToast.error('Imagem muito grande. Max 5MB.'); input.value=''; return; }
    var reader=new FileReader();
    reader.onload=function(e){ document.getElementById('imgPreviewWrap').innerHTML='<img src="'+e.target.result+'" style="max-height:100px;border-radius:6px;object-fit:cover;">'; document.getElementById('btnRemoveImg').classList.remove('d-none'); document.getElementById('removeImage').value='0'; };
    reader.readAsDataURL(input.files[0]);
}
function handleImgDrop(e) {
    e.preventDefault(); document.getElementById('imgUploadArea').style.borderColor='';
    var files=e.dataTransfer.files;
    if(files.length){ try{ var dt=new DataTransfer(); dt.items.add(files[0]); var inp=document.getElementById('coverImageInput'); inp.files=dt.files; previewCoverImage(inp); } catch(err){ HMToast.warning('Use o clique para selecionar.'); } }
}
function removeCoverImage() {
    document.getElementById('coverImageInput').value=''; document.getElementById('removeImage').value='1';
    document.getElementById('imgPreviewWrap').innerHTML='<i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:var(--hm-primary);opacity:0.7;"></i><div style="font-size:0.8rem;color:var(--hm-text-muted);">Clique ou arraste aqui</div><div style="font-size:0.72rem;color:#94a3b8;">JPG, PNG, WebP - max 5MB</div>';
    document.getElementById('btnRemoveImg').classList.add('d-none');
}

function openModal() {
    resetForm();
    document.getElementById('modalTitle').innerHTML='<i class="fas fa-plus me-2"></i>Novo Servico';
    var modal=new bootstrap.Modal(document.getElementById('svcModal')); modal.show();
}
function resetForm() {
    editingId=null;
    document.getElementById('svcForm').reset();
    document.getElementById('svcId').value='';
    document.getElementById('svcMethod').value='POST';
    document.getElementById('removeImage').value='0';
    document.getElementById('svcActive').checked=true;
    document.getElementById('iconPreview').className='bi bi-tools';
    document.getElementById('imgPreviewWrap').innerHTML='<i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:var(--hm-primary);opacity:0.7;"></i><div style="font-size:0.8rem;color:var(--hm-text-muted);">Clique ou arraste aqui</div><div style="font-size:0.72rem;color:#94a3b8;">JPG, PNG, WebP - max 5MB</div>';
    document.getElementById('btnRemoveImg').classList.add('d-none');
    try{ $('#svcDesc').summernote('destroy'); }catch(e){}
    try{ $('#svcContent').summernote('destroy'); }catch(e){}
}
function editService(id) {
    $.ajax({ url:'{{ url("admin/services") }}/'+id, headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
        success:function(data){
            if(!data.success){ HMToast.error('Erro ao carregar.'); return; }
            var s=data.data; resetForm(); editingId=id;
            document.getElementById('svcId').value=s.id;
            document.getElementById('svcMethod').value='PUT';
            document.getElementById('svcTitle').value=s.title||'';
            document.getElementById('svcDesc').value=s.description||'';
            document.getElementById('svcContent').value=s.content||'';
            document.getElementById('svcIcon').value=s.icon||'';
            document.getElementById('svcOrder').value=s.sort_order||'';
            document.getElementById('svcFeatured').checked=!!s.featured;
            document.getElementById('svcActive').checked=!!s.active;
            document.getElementById('iconPreview').className='bi '+(s.icon||'bi-tools');
            if(s.cover_image_url){ document.getElementById('imgPreviewWrap').innerHTML='<img src="'+s.cover_image_url+'" style="max-height:100px;border-radius:6px;object-fit:cover;">'; document.getElementById('btnRemoveImg').classList.remove('d-none'); }
            document.getElementById('modalTitle').innerHTML='<i class="fas fa-pencil-alt me-2"></i>Editar Servico';
            var modal=new bootstrap.Modal(document.getElementById('svcModal')); modal.show();
        },
        error:function(){ HMToast.error('Erro de conexao.'); }
    });
}

$('#svcModal').on('shown.bs.modal', function() {
    try{ $('#svcDesc').summernote('destroy'); }catch(e){}
    try{ $('#svcContent').summernote('destroy'); }catch(e){}
    var descVal=document.getElementById('svcDesc').value;
    var contVal=document.getElementById('svcContent').value;
    $('#svcDesc').summernote(snDesc);
    $('#svcContent').summernote(snContent);
    if(descVal) $('#svcDesc').summernote('code', descVal);
    if(contVal) $('#svcContent').summernote('code', contVal);
});
$('#svcModal').on('hidden.bs.modal', function() {
    try{ $('#svcDesc').summernote('destroy'); }catch(e){}
    try{ $('#svcContent').summernote('destroy'); }catch(e){}
});

document.getElementById('svcForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn=document.getElementById('btnSave');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Salvando...';
    var descCode='', contCode='';
    try{ descCode=$('#svcDesc').summernote('code'); }catch(ex){ descCode=document.getElementById('svcDesc').value; }
    try{ contCode=$('#svcContent').summernote('code'); }catch(ex){ contCode=document.getElementById('svcContent').value; }
    var id=document.getElementById('svcId').value;
    var method=document.getElementById('svcMethod').value;
    var url=id ? '{{ url("admin/services") }}/'+id : '{{ route("admin.services.store") }}';
    var fd=new FormData(this);
    fd.set('description', descCode);
    fd.set('content', contCode);
    if(method==='PUT') fd.append('_method','PUT');
    $.ajax({ url:url, method:'POST', data:fd, processData:false, contentType:false,
        headers:{'Accept':'application/json','X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
        success:function(data){ if(data.success){ HMToast.success(data.message); bootstrap.Modal.getInstance(document.getElementById('svcModal')).hide(); loadServices(currentPage); } else HMToast.error(data.message||'Erro ao salvar.'); },
        error:function(xhr){ var msg='Erro ao salvar.'; if(xhr.responseJSON){ if(xhr.responseJSON.message) msg=xhr.responseJSON.message; else if(xhr.responseJSON.errors) msg=Object.values(xhr.responseJSON.errors).flat().join('<br>'); } HMToast.error(msg); },
        complete:function(){ btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Salvar'; }
    });
});

function toggleActive(id) {
    $.ajax({ url:'{{ url("admin/services") }}/'+id+'/toggle-active', method:'PATCH', headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'),'Accept':'application/json'},
        success:function(d){ if(d.success){ HMToast.success(d.message); loadServices(currentPage); } else HMToast.error(d.message); },
        error:function(){ HMToast.error('Erro de conexao.'); }
    });
}
function toggleFeatured(id) {
    $.ajax({ url:'{{ url("admin/services") }}/'+id+'/toggle-featured', method:'PATCH', headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'),'Accept':'application/json'},
        success:function(d){ if(d.success){ HMToast.success(d.message); loadServices(currentPage); } else HMToast.error(d.message); },
        error:function(){ HMToast.error('Erro de conexao.'); }
    });
}
function deleteService(id, name) {
    Swal.fire({ title:'Excluir servico?', html:'Deseja excluir <strong>'+name+'</strong>?<br><small style="color:#64748b;">Esta acao nao pode ser desfeita.</small>', icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#64748b', confirmButtonText:'<i class="fas fa-trash me-1"></i> Excluir', cancelButtonText:'Cancelar' })
    .then(function(r){ if(!r.isConfirmed) return;
        $.ajax({ url:'{{ url("admin/services") }}/'+id, method:'DELETE', headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'),'Accept':'application/json'},
            success:function(d){ if(d.success){ HMToast.success(d.message); loadServices(currentPage); } else HMToast.error(d.message); },
            error:function(){ HMToast.error('Erro de conexao.'); }
        });
    });
}

$(function(){ loadServices(); });
</script>
@endsection