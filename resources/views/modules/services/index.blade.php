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

/* Botões dos cards — btn-group padronizado */
.card-footer .btn-group .btn { border-radius: 0 !important; }
.card-footer .btn-group .btn:first-child { border-radius: 6px 0 0 6px !important; }
.card-footer .btn-group .btn:last-child  { border-radius: 0 6px 6px 0 !important; }
.card-footer .btn-group .btn:only-child  { border-radius: 6px !important; }
.card-footer .btn-group .btn { height: 34px; display: inline-flex; align-items: center; justify-content: center; }

/* Upload area */
.img-upload-area { border:2px dashed var(--hm-border); border-radius:8px; padding:1.25rem; text-align:center; cursor:pointer; transition:var(--hm-transition); background:#fafafa; min-height:100px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.4rem; }
.img-upload-area:hover { border-color:var(--hm-primary); background:var(--hm-primary-light); }
.img-upload-area img { max-height:100px; border-radius:6px; object-fit:cover; }

/* Summernote */
.note-editor.note-frame { border:1.5px solid var(--hm-border) !important; border-radius:0 0 6px 6px !important; }
.note-toolbar { background:#f8fafc !important; border:1.5px solid var(--hm-border) !important; border-bottom:none !important; border-radius:6px 6px 0 0 !important; }
.note-toolbar .note-btn:hover, .note-toolbar .note-btn.active { background:var(--hm-primary) !important; color:#fff !important; border-color:var(--hm-primary) !important; }
.note-editable { min-height:100px !important; font-size:0.88rem !important; padding:10px !important; }

/* Icon Picker */
#iconPickerModal .modal-body { max-height: 420px; overflow-y: auto; }
.icon-picker-search { position: sticky; top: 0; z-index: 10; background: #fff; padding: 10px 0 8px; }
.icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 6px; }
.icon-item {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 8px 4px; border-radius: 8px; cursor: pointer; border: 2px solid transparent;
    transition: all .18s; font-size: 0.65rem; color: #475569; background: #f8fafc;
    gap: 4px; min-height: 64px;
}
.icon-item i { font-size: 1.4rem; color: #334155; }
.icon-item:hover { border-color: var(--hm-primary); background: var(--hm-primary-light); color: var(--hm-primary); }
.icon-item:hover i { color: var(--hm-primary); }
.icon-item.selected { border-color: var(--hm-primary); background: var(--hm-primary); color: #fff; }
.icon-item.selected i { color: #fff; }
.icon-name-label { word-break: break-all; text-align: center; line-height: 1.2; }
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
                                    <button type="button" class="btn btn-outline-secondary" id="btnOpenIconPicker" onclick="openIconPicker()" title="Escolher icone visualmente">
                                        <i class="bi bi-grid-3x3-gap-fill"></i>
                                    </button>
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

{{-- Modal Icon Picker --}}
<div class="modal fade" id="iconPickerModal" tabindex="-1" style="z-index:1060">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--hm-primary);">
                <span class="modal-title" style="font-weight:700;font-size:1rem;color:#fff;">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Escolher Icone Bootstrap
                </span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="icon-picker-search">
                    <input type="text" id="iconSearchInput" class="form-control form-control-sm" placeholder="Pesquisar icone... (ex: car, wrench, tools)" oninput="filterIcons(this.value)">
                    <div class="mt-1" style="font-size:0.78rem;color:#64748b;">Clique no icone para selecionar</div>
                </div>
                <div class="icon-grid mt-2" id="iconGrid"></div>
                <div id="iconPickerEmpty" class="text-center py-4 d-none" style="color:#94a3b8;">
                    <i class="bi bi-search" style="font-size:2rem;"></i>
                    <p class="mt-2 mb-0">Nenhum icone encontrado</p>
                </div>
            </div>
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

/* ===== BOOTSTRAP ICONS PICKER ===== */
var BI_ICONS = [
  'alarm','alarm-fill','archive','archive-fill','arrow-left','arrow-right','arrow-up','arrow-down',
  'asterisk','award','award-fill','bag','bag-fill','bag-check','bag-check-fill','bag-dash','bag-plus',
  'bar-chart','bar-chart-fill','bar-chart-line','bar-chart-steps','battery','battery-charging','battery-full','battery-half',
  'bell','bell-fill','bell-slash','bicycle','bookmarks','bookmarks-fill','bookmark','bookmark-fill',
  'box','box-fill','box-seam','boxes','briefcase','briefcase-fill','brush','brush-fill',
  'building','building-fill','calendar','calendar-fill','calendar-check','calendar-event','calendar-range',
  'camera','camera-fill','camera-video','camera-video-fill','car-front','car-front-fill',
  'cart','cart-fill','cart-check','cart-plus','cash','cash-coin','cash-stack','check-circle',
  'check-circle-fill','check-square','check-square-fill','chevron-down','chevron-left','chevron-right','chevron-up',
  'circle','circle-fill','clipboard','clipboard-fill','clipboard-check','clock','clock-fill','cloud',
  'cloud-fill','cloud-upload','cloud-download','code','code-slash','collection','compass','compass-fill',
  'cpu','cpu-fill','credit-card','credit-card-fill','cup','cup-fill','cup-hot','cursor',
  'dash-circle','dash-circle-fill','dash-square','database','database-fill','diagram-3','display',
  'droplet','droplet-fill','ear','egg','egg-fill','emoji-smile','emoji-smile-fill','envelope',
  'envelope-fill','envelope-open','exclamation-circle','exclamation-circle-fill','exclamation-triangle','eye',
  'eye-fill','eye-slash','eyeglasses','fan','file','file-fill','file-earmark','file-pdf',
  'file-text','film','filter','fire','flag','flag-fill','folder','folder-fill',
  'folder-open','fuel-pump','fuel-pump-fill','funnel','funnel-fill','gear','gear-fill',
  'gear-wide','gift','gift-fill','globe','globe2','graph-up','graph-down','hammer',
  'hand-thumbs-up','hand-thumbs-down','handbag','handbag-fill','headphones','heart','heart-fill',
  'house','house-fill','house-door','house-door-fill','image','image-fill','inbox','info-circle',
  'info-circle-fill','joystick','key','key-fill','keyboard','lamp','laptop','layout-text-sidebar',
  'lightning','lightning-fill','lightning-charge','list','list-check','list-ul','list-ol','lock',
  'lock-fill','magic','map','map-fill','megaphone','megaphone-fill','mic','mic-fill',
  'minecart','minecart-loaded','moon','moon-fill','motherboard','mouse','music-note','newspaper',
  'nut','nut-fill','palette','palette-fill','patch-check','patch-check-fill','pause-circle',
  'pc','pc-display','pencil','pencil-fill','pencil-square','people','people-fill','person',
  'person-fill','person-badge','person-circle','phone','phone-fill','pie-chart','pie-chart-fill',
  'pin','pin-fill','pin-map','play-circle','play-circle-fill','plug','plug-fill','plus-circle',
  'plus-circle-fill','plus-square','printer','puzzle','qr-code','question-circle','question-circle-fill',
  'receipt','recycle','robot','rocket','rocket-fill','router','rss','safe','save',
  'save-fill','scissors','search','send','send-fill','server','share','shield',
  'shield-fill','shield-check','shield-exclamation','shop','shop-window','signpost','signpost-fill',
  'sim','skip-forward','sliders','sliders2','smartwatch','snow','speaker','speedometer',
  'speedometer2','star','star-fill','star-half','stars','stopwatch','stopwatch-fill','suitcase',
  'suitcase-fill','sun','sun-fill','sunset','table','tag','tag-fill','tags','telephone',
  'telephone-fill','thermometer','thermometer-half','three-dots','three-dots-vertical','toggles',
  'tools','tools-fill','tornado','translate','trash','trash-fill','tree','trophy','trophy-fill',
  'truck','truck-fill','truck-front','truck-front-fill','tv','tv-fill','type','umbrella',
  'unlock','unlock-fill','upc-scan','upload','usb','virus','wallet','wallet-fill',
  'watch','water','wifi','wind','wrench','wrench-adjustable','wrench-adjustable-circle','x-circle',
  'x-circle-fill','zoom-in','zoom-out',
  'car-front','fuel-pump','wrench-adjustable','tools','gear-wide-connected','speedometer2',
  'wind','droplet-half','screwdriver','hammer','nut-fill','lightbulb','lightbulb-fill',
  'lightbulb-off','battery-charging','battery-full','plug-fill','snow2','fire','thermometer-sun'
];

var _iconsRendered = false;
function openIconPicker() {
    if (!_iconsRendered) { renderIconGrid(BI_ICONS); _iconsRendered = true; }
    var current = document.getElementById('svcIcon').value;
    document.getElementById('iconGrid').querySelectorAll('.icon-item').forEach(function(el){
        el.classList.toggle('selected', el.dataset.icon === current);
    });
    document.getElementById('iconSearchInput').value = '';
    filterIcons('');
    var m = new bootstrap.Modal(document.getElementById('iconPickerModal')); m.show();
    setTimeout(function(){ document.getElementById('iconSearchInput').focus(); }, 400);
}
function renderIconGrid(icons) {
    var grid = document.getElementById('iconGrid');
    var html = '';
    var unique = [...new Set(icons)];
    unique.forEach(function(ic){
        var cls = 'bi-' + ic;
        html += '<div class="icon-item" data-icon="bi-'+ic+'" onclick="selectIcon(\'bi-'+ic+'\')" title="bi-'+ic+'">';
        html += '<i class="bi '+cls+'"></i>';
        html += '<span class="icon-name-label">'+ic+'</span>';
        html += '</div>';
    });
    grid.innerHTML = html;
}
function filterIcons(q) {
    q = q.toLowerCase().trim();
    var items = document.getElementById('iconGrid').querySelectorAll('.icon-item');
    var found = 0;
    items.forEach(function(el){
        var match = !q || el.dataset.icon.indexOf(q) !== -1;
        el.style.display = match ? '' : 'none';
        if (match) found++;
    });
    document.getElementById('iconPickerEmpty').classList.toggle('d-none', found > 0);
}
function selectIcon(iconClass) {
    document.getElementById('svcIcon').value = iconClass;
    document.getElementById('iconPreview').className = 'bi ' + iconClass;
    document.getElementById('iconGrid').querySelectorAll('.icon-item').forEach(function(el){
        el.classList.toggle('selected', el.dataset.icon === iconClass);
    });
    bootstrap.Modal.getInstance(document.getElementById('iconPickerModal')).hide();
    HMToast.success('Icone selecionado: ' + iconClass);
}
/* ===== FIM ICON PICKER ===== */

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
        var img = s.cover_image_url
            ? '<img src="'+s.cover_image_url+'" class="svc-img" alt="'+s.title+'">'
            : '<div class="svc-img-placeholder"><i class="bi '+(s.icon||'bi-tools')+'"></i></div>';

        // Botões — btn-group horizontal padronizado (mesmo modelo anterior)
        var btnEdit     = '<button class="btn btn-warning" onclick="editService('+s.id+')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
        var btnToggle   = s.active
            ? '<button class="btn btn-secondary" onclick="toggleActive('+s.id+')" title="Desativar"><i class="fas fa-pause"></i></button>'
            : '<button class="btn btn-success"   onclick="toggleActive('+s.id+')" title="Ativar"><i class="fas fa-play"></i></button>';
        var btnFeatured = s.featured
            ? '<button class="btn btn-warning"         onclick="toggleFeatured('+s.id+')" title="Remover destaque"><i class="fas fa-star"></i></button>'
            : '<button class="btn btn-outline-secondary" onclick="toggleFeatured('+s.id+')" title="Destacar"><i class="far fa-star"></i></button>';
        var btnDelete   = '<button class="btn btn-danger" onclick="deleteService('+s.id+',\''+((s.title||'').replace(/\'/g,''))+'\')" title="Excluir"><i class="fas fa-trash"></i></button>';

        html += '<div class="col-md-4 col-lg-3" data-id="'+s.id+'">'
            + '<div class="card svc-card h-100 d-flex flex-column">'
            + img
            + '<div class="card-body pb-2 flex-grow-1">'
            + '<div class="d-flex align-items-start justify-content-between gap-1 mb-1">'
            + '<div style="font-weight:700;font-size:0.88rem;color:var(--hm-text);line-height:1.3;">'+s.title+'</div>'
            + '<i class="fas fa-grip-vertical drag-handle" style="color:#94a3b8;cursor:move;flex-shrink:0;margin-top:2px;font-size:0.85rem;"></i>'
            + '</div>'
            + '<p style="font-size:0.78rem;color:var(--hm-text-muted);margin:0 0 0.5rem;line-height:1.4;">'+(s.description||'').replace(/<[^>]*>/g,'').substring(0,80)+((s.description||'').length>80?'…':'')+'</p>'
            + '<div class="d-flex gap-1 flex-wrap">'
            + (s.active ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-secondary">Inativo</span>')
            + (s.featured ? '<span class="badge badge-warning">Destaque</span>' : '')
            + '</div>'
            + '</div>'
            + '<div class="card-footer py-2">'
            + '<div class="btn-group btn-group-sm w-100">'
            + btnEdit + btnToggle + btnFeatured + btnDelete
            + '</div>'
            + '</div>'
            + '</div></div>';
    });
    html += '</div>';
    c.innerHTML = html;

    // Tooltips Bootstrap
    document.querySelectorAll('.svc-btn[title]').forEach(function(el) {
        new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' });
    });

    // Sortable
    if(sortable) sortable.destroy();
    var list = document.getElementById('svcList');
    if(list){
        sortable = Sortable.create(list, {
            handle: '.drag-handle', ghostClass: 'opacity-50',
            onEnd: function(){
                var items = [];
                document.querySelectorAll('#svcList [data-id]').forEach(function(el, i){
                    items.push({ id: parseInt(el.dataset.id), sort_order: i+1 });
                });
                $.ajax({
                    url: '{{ route("admin.services.reorder") }}', method: 'POST',
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: JSON.stringify({ services: items }),
                    success: function(r){ if(r.success) HMToast.success('Ordem salva!'); }
                });
            }
        });
    }
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