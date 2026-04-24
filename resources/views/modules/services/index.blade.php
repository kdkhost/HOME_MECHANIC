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

/* ===== Icon Picker Floating Panel ===== */
#iconPickerBackdrop {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.55);
    backdrop-filter: blur(3px);
    z-index: 9998;
    animation: ipFadeIn .2s ease;
}
#iconPickerPanel {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%) scale(0.92) translateY(12px);
    width: min(780px, 94vw);
    max-height: 82vh;
    background: #fff;
    border-radius: 18px;
    z-index: 9999;
    box-shadow:
        0 2px 4px rgba(0,0,0,.06),
        0 8px 24px rgba(0,0,0,.14),
        0 24px 56px rgba(0,0,0,.22),
        0 0 0 1px rgba(255,255,255,.6) inset;
    overflow: hidden;
    flex-direction: column;
    opacity: 0;
    transition: opacity .22s ease, transform .25s cubic-bezier(.34,1.56,.64,1);
}
#iconPickerPanel.ip-open {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1) translateY(0);
}
.ip-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px 12px;
    background: linear-gradient(135deg, var(--hm-primary) 0%, #e05c00 100%);
    border-radius: 18px 18px 0 0;
    flex-shrink: 0;
}
.ip-header-title { font-weight: 700; font-size: 1rem; color: #fff; display: flex; align-items: center; gap: 8px; }
.ip-close-btn {
    background: rgba(255,255,255,.2); border: none; border-radius: 50%;
    width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
    color: #fff; cursor: pointer; transition: background .15s; font-size: 1rem;
}
.ip-close-btn:hover { background: rgba(255,255,255,.35); }
.ip-search-wrap {
    padding: 12px 16px 8px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.ip-search-wrap input {
    border-radius: 10px !important;
    border: 1.5px solid #cbd5e1 !important;
    padding: 8px 12px !important;
    font-size: 0.88rem !important;
    transition: border-color .15s;
}
.ip-search-wrap input:focus { border-color: var(--hm-primary) !important; outline: none; box-shadow: 0 0 0 3px rgba(234,88,12,.12) !important; }
.ip-search-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 4px; }
.ip-body {
    overflow-y: auto; padding: 12px 14px 14px;
    flex: 1;
    scroll-behavior: smooth;
}
.icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(74px, 1fr)); gap: 7px; }
.icon-item {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 9px 4px 7px; border-radius: 10px; cursor: pointer; border: 2px solid transparent;
    transition: all .16s cubic-bezier(.4,0,.2,1); font-size: 0.62rem; color: #475569; background: #f8fafc;
    gap: 5px; min-height: 66px; user-select: none;
}
.icon-item i { font-size: 1.5rem; color: #334155; transition: transform .15s; }
.icon-item:hover { border-color: var(--hm-primary); background: var(--hm-primary-light); color: var(--hm-primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(234,88,12,.18); }
.icon-item:hover i { color: var(--hm-primary); transform: scale(1.15); }
.icon-item:active { transform: scale(.95); }
.icon-item.selected { border-color: var(--hm-primary); background: var(--hm-primary); color: #fff; box-shadow: 0 4px 14px rgba(234,88,12,.35); }
.icon-item.selected i { color: #fff; transform: scale(1.1); }
.icon-name-label { word-break: break-all; text-align: center; line-height: 1.2; font-weight: 500; }
#iconPickerEmpty { color: #94a3b8; }
@keyframes ipFadeIn { from { opacity:0; } to { opacity:1; } }
.ip-tab { border: 1.5px solid #e2e8f0 !important; color: #475569 !important; background: #f8fafc !important; }
.ip-tab.active { background: var(--hm-primary) !important; color: #fff !important; border-color: var(--hm-primary) !important; }
.ip-tab:hover:not(.active) { border-color: var(--hm-primary) !important; color: var(--hm-primary) !important; }
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
                                <label>Icone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="iconPreview" class="bi bi-tools"></i></span>
                                    <input type="text" class="form-control" name="icon" id="svcIcon" placeholder="ex: fas fa-car" maxlength="100" oninput="previewIconField(this.value)">
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

{{-- Icon Picker Floating Panel --}}
<div id="iconPickerBackdrop" onclick="closeIconPicker()"></div>
<div id="iconPickerPanel" role="dialog" aria-label="Seletor de Icones">
    <div class="ip-header">
        <span class="ip-header-title">
            <i class="bi bi-grid-3x3-gap-fill"></i> Escolher Icone
        </span>
        <button class="ip-close-btn" onclick="closeIconPicker()" title="Fechar"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="ip-search-wrap">
        <input type="text" id="iconSearchInput" class="form-control" placeholder="Pesquisar icone... (car, wrench, tools, star...)" autocomplete="off">
        <div class="d-flex gap-1 mt-2 flex-wrap" id="ipTabs">
            <button class="btn btn-xs ip-tab active" data-provider="" onclick="setIpTab(this,'')" style="font-size:0.72rem;padding:2px 8px;border-radius:20px;">Todos</button>
            <button class="btn btn-xs ip-tab" data-provider="bi" onclick="setIpTab(this,'bi')" style="font-size:0.72rem;padding:2px 8px;border-radius:20px;">Bootstrap Icons</button>
            <button class="btn btn-xs ip-tab" data-provider="fas" onclick="setIpTab(this,'fas')" style="font-size:0.72rem;padding:2px 8px;border-radius:20px;">FA Solid</button>
            <button class="btn btn-xs ip-tab" data-provider="far" onclick="setIpTab(this,'far')" style="font-size:0.72rem;padding:2px 8px;border-radius:20px;">FA Regular</button>
            <button class="btn btn-xs ip-tab" data-provider="fab" onclick="setIpTab(this,'fab')" style="font-size:0.72rem;padding:2px 8px;border-radius:20px;">FA Brands</button>
        </div>
        <div class="ip-search-hint mt-1"><i class="bi bi-hand-index me-1"></i>Clique no icone para aplicar automaticamente</div>
    </div>
    <div class="ip-body">
        <div class="icon-grid" id="iconGrid"></div>
        <div id="iconPickerEmpty" class="text-center py-4 d-none">
            <i class="bi bi-search" style="font-size:2.2rem;"></i>
            <p class="mt-2 mb-0 fw-semibold">Nenhum icone encontrado</p>
            <small>Tente outro termo de busca</small>
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

/* helpers */
function parseIconCls(v) {
    if (!v) return 'bi bi-tools';
    if (v.indexOf(' ') !== -1) return v;
    if (v.indexOf('bi-') === 0) return 'bi ' + v;
    return v;
}
function previewIconField(v) {
    document.getElementById('iconPreview').className = parseIconCls(v) || 'bi bi-tools';
}

/* ===== ICON PICKER ===== */
var ICON_DATA = (function(){
    var bi = ['alarm','alarm-fill','archive','archive-fill','award','award-fill',
      'bag','bag-fill','bar-chart','bar-chart-fill','battery','battery-charging','battery-full',
      'bell','bell-fill','bicycle','bookmark','bookmark-fill','box','box-seam','briefcase',
      'brush','building','building-fill','calendar','calendar-fill','calendar-check',
      'camera','camera-fill','car-front','car-front-fill','cart','cart-fill','cash','cash-coin',
      'check-circle','check-circle-fill','clock','clock-fill','cloud','cloud-upload','code',
      'cpu','cpu-fill','credit-card','credit-card-fill','database','database-fill',
      'droplet','droplet-fill','envelope','envelope-fill','eye','eye-fill','fan',
      'file','file-earmark','file-pdf','file-text','filter','fire','flag','flag-fill',
      'folder','folder-fill','fuel-pump','fuel-pump-fill','funnel','gear','gear-fill',
      'gear-wide','gift','gift-fill','globe','globe2','graph-up','graph-down','hammer',
      'hand-thumbs-up','headphones','heart','heart-fill','house','house-fill','house-door',
      'image','info-circle','info-circle-fill','key','key-fill','lightning','lightning-fill',
      'list','list-check','lock','lock-fill','map','map-fill','megaphone','mic','mic-fill',
      'moon','nut','nut-fill','palette','pencil','pencil-fill','pencil-square',
      'people','people-fill','person','person-fill','person-circle','phone','phone-fill',
      'pie-chart','pin','pin-fill','plug','plug-fill','printer','puzzle','qr-code',
      'rocket','rocket-fill','save','save-fill','scissors','search','send','send-fill',
      'server','shield','shield-fill','shield-check','shop','shop-window','sliders',
      'sliders2','speedometer','speedometer2','star','star-fill','star-half','stopwatch',
      'sun','sun-fill','table','tag','tag-fill','telephone','telephone-fill',
      'thermometer','three-dots','three-dots-vertical','tools','trash','trash-fill',
      'truck','truck-fill','truck-front','truck-front-fill','tv','upload','wallet',
      'wallet-fill','watch','water','wifi','wind','wrench','wrench-adjustable',
      'x-circle','x-circle-fill','zoom-in','zoom-out','screwdriver','lightbulb','lightbulb-fill',
      'snow2','snow','recycle','robot','recycle','translate','toggles','trophy','trophy-fill'];
    var fas = ['car','car-side','truck','truck-monster','motorcycle','bus','taxi','ship','plane',
      'wrench','tools','screwdriver','hammer','cog','cogs','bolt','fire','tint','oil-can',
      'gas-pump','battery-full','battery-half','battery-empty','battery-quarter',
      'tachometer-alt','gauge','road','map','map-marked','map-marker','map-pin',
      'star','star-half','heart','thumbs-up','thumbs-down','check','check-circle',
      'times','times-circle','info-circle','exclamation-triangle','exclamation-circle',
      'question-circle','plus','plus-circle','minus','minus-circle','arrow-up','arrow-down',
      'arrow-left','arrow-right','home','user','users','user-circle','user-shield',
      'phone','phone-alt','envelope','envelope-open','comment','comments','sms',
      'calendar','calendar-alt','calendar-check','clock','stopwatch','hourglass',
      'bell','bell-slash','tag','tags','bookmark','bookmarks','flag','flag-checkered',
      'trash','trash-alt','edit','pencil-alt','pen','save','file','file-alt','file-pdf',
      'folder','folder-open','image','images','camera','video','play','pause','stop',
      'search','filter','sort','sort-alpha-down','sort-amount-down','bars','list','th',
      'th-large','th-list','table','chart-bar','chart-line','chart-pie','chart-area',
      'dollar-sign','coins','wallet','credit-card','receipt','cash-register','shopping-cart',
      'shopping-bag','box','boxes','cube','cubes','layer-group','database','server',
      'cloud','cloud-upload-alt','cloud-download-alt','wifi','bluetooth','satellite',
      'shield-alt','shield-check','lock','unlock','key','fingerprint','eye','eye-slash',
      'award','medal','trophy','crown','gem','diamond','magic','palette','paint-brush',
      'print','qrcode','barcode','toggle-on','toggle-off','sliders-h','sliders-v',
      'industry','warehouse','store','building','city','tree','leaf','seedling','sun',
      'moon','cloud-sun','snow','umbrella','wind','thermometer','tachometer','gauge-high',
      'spray-can','brush','broom','soap','hand-sparkles','recycle','trash-restore',
      'running','walking','biking','swimmer','dumbbell','football','futbol','basketball',
      'globe','globe-americas','compass','directions','route','road','traffic-light',
      'parking','charging-station','wheelchair','baby','child','user-graduate',
      'stethoscope','heartbeat','medkit','prescription','pills','syringe',
      'rocket','space-shuttle','satellite-dish','microchip','robot','android','apple',
      'laptop','desktop','mobile-alt','tablet-alt','keyboard','mouse','headset',
      'code','code-branch','terminal','bug','cog','cogs','wrench','tools','toolbox',
      'hard-hat','helmet-safety','vest','traffic-cone','sign','exclamation'];
    var far = ['clock','calendar','calendar-alt','bookmark','heart','star','star-half',
      'comment','comments','envelope','image','images','file','file-alt','folder',
      'folder-open','bell','flag','eye','eye-slash','check-circle','times-circle',
      'question-circle','plus-square','minus-square','edit','trash-alt','save',
      'user','user-circle','smile','frown','meh','thumbs-up','thumbs-down',
      'hand-point-up','hand-point-down','hand-point-left','hand-point-right',
      'lightbulb','paper-plane','compass','map','life-ring','credit-card','gem',
      'chart-bar','dot-circle','circle','square','keyboard','clipboard','sticky-note',
      'id-card','address-book','address-card','building','hospital','money-bill-alt',
      'lemon','snowflake','sun','moon','hourglass'];
    var fab = ['whatsapp','facebook','instagram','twitter','youtube','google','apple',
      'android','windows','linux','github','gitlab','npm','wordpress','shopify',
      'paypal','stripe','amazon','google-pay','apple-pay','cc-visa','cc-mastercard',
      'cc-amex','cc-paypal','chrome','firefox','safari','edge','opera','telegram',
      'linkedin','pinterest','tiktok','discord','slack','skype','spotify','airbnb',
      'uber','lyft','bluetooth','usb','wifi','cc-stripe'];
    var out = [];
    bi.forEach(function(n){ out.push({cls:'bi bi-'+n, name:n, provider:'bi'}); });
    fas.forEach(function(n){ out.push({cls:'fas fa-'+n, name:n, provider:'fas'}); });
    far.forEach(function(n){ out.push({cls:'far fa-'+n, name:n, provider:'far'}); });
    fab.forEach(function(n){ out.push({cls:'fab fa-'+n, name:n, provider:'fab'}); });
    return out;
})();

var _iconsRendered = false, _ipTab = '', _ipSearch = '';

function openIconPicker() {
    if (!_iconsRendered) { renderIconGrid(); _iconsRendered = true; }
    var current = document.getElementById('svcIcon').value;
    document.getElementById('iconGrid').querySelectorAll('.icon-item').forEach(function(el){
        el.classList.toggle('selected', el.dataset.icon === current);
    });
    document.getElementById('iconSearchInput').value = '';
    _ipSearch = ''; _ipTab = '';
    document.querySelectorAll('.ip-tab').forEach(function(t){ t.classList.toggle('active', t.dataset.provider === ''); });
    applyIpFilter();
    var bd = document.getElementById('iconPickerBackdrop');
    var pn = document.getElementById('iconPickerPanel');
    bd.style.display = 'block';
    pn.style.display = 'flex';
    /* Desativa focus-trap do BS5 modal principal */
    try { var bm = bootstrap.Modal.getInstance(document.getElementById('svcModal')); if(bm && bm._focustrap) bm._focustrap.deactivate(); } catch(e){}
    requestAnimationFrame(function(){ requestAnimationFrame(function(){
        pn.classList.add('ip-open');
        var inp = document.getElementById('iconSearchInput');
        setTimeout(function(){ inp.focus(); }, 150);
        /* loop de foco para caso o BS5 tente resgatar */
        var attempts = 0;
        var keepFocus = setInterval(function(){
            if (document.activeElement !== inp) inp.focus();
            if (++attempts > 10) clearInterval(keepFocus);
        }, 100);
    }); });
}
function closeIconPicker() {
    var pn = document.getElementById('iconPickerPanel');
    pn.classList.remove('ip-open');
    setTimeout(function(){
        pn.style.display = 'none';
        document.getElementById('iconPickerBackdrop').style.display = 'none';
    }, 260);
    /* Reativa focus-trap do BS5 */
    try { var bm = bootstrap.Modal.getInstance(document.getElementById('svcModal')); if(bm && bm._focustrap) bm._focustrap.activate(); } catch(e){}
}
function setIpTab(btn, provider) {
    _ipTab = provider;
    document.querySelectorAll('.ip-tab').forEach(function(t){ t.classList.toggle('active', t.dataset.provider === provider); });
    applyIpFilter();
}
function filterIcons(q) { _ipSearch = q; applyIpFilter(); }
function applyIpFilter() {
    var q = _ipSearch.toLowerCase().trim();
    var items = document.getElementById('iconGrid').querySelectorAll('.icon-item');
    var found = 0;
    items.forEach(function(el){
        var matchQ = !q || el.dataset.name.indexOf(q) !== -1 || el.dataset.icon.indexOf(q) !== -1;
        var matchT = !_ipTab || el.dataset.provider === _ipTab;
        var show = matchQ && matchT;
        el.style.display = show ? '' : 'none';
        if (show) found++;
    });
    document.getElementById('iconPickerEmpty').classList.toggle('d-none', found > 0);
}
function renderIconGrid() {
    var grid = document.getElementById('iconGrid');
    var html = '';
    var provColors = {bi:'#0d6efd', fas:'#198754', far:'#6f42c1', fab:'#dc3545'};
    ICON_DATA.forEach(function(ic){
        var badge = '<span style="font-size:0.52rem;padding:1px 4px;border-radius:10px;background:'+(provColors[ic.provider]||'#64748b')+';color:#fff;line-height:1.4;">'+ic.provider+'</span>';
        html += '<div class="icon-item" data-icon="'+ic.cls+'" data-name="'+ic.name+'" data-provider="'+ic.provider+'" onclick="selectIcon(\''+ic.cls+'\')" title="'+ic.cls+'">';
        html += '<i class="'+ic.cls+'"></i>';
        html += '<span class="icon-name-label">'+ic.name+'</span>';
        html += badge+'</div>';
    });
    grid.innerHTML = html;
}
function selectIcon(iconClass) {
    document.getElementById('svcIcon').value = iconClass;
    document.getElementById('iconPreview').className = iconClass;
    document.getElementById('iconGrid').querySelectorAll('.icon-item').forEach(function(el){
        el.classList.toggle('selected', el.dataset.icon === iconClass);
    });
    closeIconPicker();
    HMToast.success('Icone selecionado: ' + iconClass);
}
/* listener direto no documento para garantir busca mesmo com focus-trap */
document.addEventListener('DOMContentLoaded', function(){
    var inp = document.getElementById('iconSearchInput');
    if (inp) {
        inp.addEventListener('input', function(){ filterIcons(this.value); });
        inp.addEventListener('keyup', function(){ filterIcons(this.value); });
    }
});
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
    document.getElementById('svcIcon').value='';
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
            document.getElementById('svcIcon').value=s.icon||'bi-tools';
            document.getElementById('svcOrder').value=s.sort_order||'';
            document.getElementById('svcFeatured').checked=!!s.featured;
            document.getElementById('svcActive').checked=!!s.active;
            document.getElementById('iconPreview').className=parseIconCls(s.icon||'bi-tools');
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