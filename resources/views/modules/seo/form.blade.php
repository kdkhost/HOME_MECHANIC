@extends('layouts.admin')
@section('title', 'SEO — ' . ($pageTypes[$seoSetting->page_type] ?? 'Configuração'))
@section('page-title', 'SEO')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">SEO</a></li>
    <li class="breadcrumb-item active">{{ $pageTypes[$seoSetting->page_type] ?? 'Configuração' }}</li>
@endsection

@section('styles')
<style>
.seo-preview-google {
    background: #fff;
    border: 1px solid var(--hm-border);
    border-radius: 8px;
    padding: 1.25rem;
    font-family: Arial, sans-serif;
}
.seo-preview-url  { color: #006621; font-size: 0.82rem; margin-bottom: 0.2rem; }
.seo-preview-title{ color: #1a0dab; font-size: 1.1rem; font-weight: 400; margin-bottom: 0.2rem; cursor: pointer; }
.seo-preview-title:hover { text-decoration: underline; }
.seo-preview-desc { color: #545454; font-size: 0.85rem; line-height: 1.5; }
.seo-preview-og {
    background: #f8f9fa;
    border: 1px solid var(--hm-border);
    border-radius: 8px;
    overflow: hidden;
}
.seo-preview-og-img {
    width: 100%; height: 160px;
    background: #e9ecef;
    display: flex; align-items: center; justify-content: center;
    color: #adb5bd; font-size: 2rem;
}
.seo-preview-og-body { padding: 0.85rem; }
.seo-preview-og-site { font-size: 0.72rem; text-transform: uppercase; color: #718096; }
.seo-preview-og-title{ font-weight: 700; font-size: 0.9rem; color: #2d3748; margin: 0.2rem 0; }
.seo-preview-og-desc { font-size: 0.82rem; color: #718096; }
.char-counter { font-size: 0.75rem; margin-top: 0.25rem; }
.char-ok      { color: #28a745; }
.char-warn    { color: #ffc107; }
.char-bad     { color: #dc3545; }
.score-bar { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; }
.score-fill { height: 100%; border-radius: 4px; transition: width 0.4s ease; }
.tab-nav { display: flex; gap: 0; border-bottom: 2px solid var(--hm-border); margin-bottom: 1.5rem; }
.tab-btn {
    background: none; border: none; padding: 0.65rem 1.25rem;
    font-size: 0.82rem; font-weight: 600; color: #718096;
    cursor: pointer; border-bottom: 2px solid transparent;
    margin-bottom: -2px; transition: all 0.2s;
}
.tab-btn.active { color: var(--hm-primary); border-bottom-color: var(--hm-primary); }
.tab-pane { display: none; }
.tab-pane.active { display: block; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-search me-2" style="color:var(--hm-primary);"></i>
        SEO — {{ $pageTypes[$seoSetting->page_type] ?? 'Configuração' }}
    </h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.seo.store') }}" id="seoForm">
    @csrf
    <input type="hidden" name="page_type" value="{{ $seoSetting->page_type }}">
    <input type="hidden" name="page_identifier" value="{{ $seoSetting->page_identifier }}">

    <div class="row g-4">

        {{-- Coluna principal --}}
        <div class="col-lg-8">

            {{-- Tabs --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-tags"></i> Meta Tags</span>
                </div>
                <div class="card-body">

                    <div class="tab-nav">
                        <button type="button" class="tab-btn active" data-tab="basic">Básico</button>
                        <button type="button" class="tab-btn" data-tab="og">Open Graph</button>
                        <button type="button" class="tab-btn" data-tab="twitter">Twitter</button>
                        <button type="button" class="tab-btn" data-tab="advanced">Avançado</button>
                    </div>

                    {{-- Tab: Básico --}}
                    <div class="tab-pane active" id="tab-basic">
                        <div class="form-group">
                            <label>Meta Title
                                <span style="font-weight:400; color:#718096; font-size:0.78rem;">(recomendado: 50–60 caracteres)</span>
                            </label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                   value="{{ old('meta_title', $seoSetting->meta_title) }}"
                                   placeholder="Título da página para mecanismos de busca"
                                   maxlength="255">
                            <div class="char-counter" id="title-counter">0 caracteres</div>
                        </div>

                        <div class="form-group">
                            <label>Meta Description
                                <span style="font-weight:400; color:#718096; font-size:0.78rem;">(recomendado: 120–160 caracteres)</span>
                            </label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                      placeholder="Descrição da página para mecanismos de busca"
                                      maxlength="500">{{ old('meta_description', $seoSetting->meta_description) }}</textarea>
                            <div class="char-counter" id="desc-counter">0 caracteres</div>
                        </div>

                        <div class="form-group">
                            <label>Meta Keywords
                                <span style="font-weight:400; color:#718096; font-size:0.78rem;">(separadas por vírgula)</span>
                            </label>
                            <input type="text" name="meta_keywords" class="form-control"
                                   value="{{ old('meta_keywords', $seoSetting->meta_keywords) }}"
                                   placeholder="tuning, carros de luxo, oficina, performance">
                        </div>

                        <div class="form-group">
                            <label>URL Canônica</label>
                            <input type="url" name="canonical_url" class="form-control"
                                   value="{{ old('canonical_url', $seoSetting->canonical_url) }}"
                                   placeholder="https://homemechanic.com.br/pagina">
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="index"
                                           name="index" value="1"
                                           {{ old('index', $seoSetting->index ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="index">
                                        Indexar página (index)
                                    </label>
                                </div>
                                <small class="form-text">Permite que mecanismos de busca indexem esta página.</small>
                            </div>
                            <div class="col-sm-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="follow"
                                           name="follow" value="1"
                                           {{ old('follow', $seoSetting->follow ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="follow">
                                        Seguir links (follow)
                                    </label>
                                </div>
                                <small class="form-text">Permite que mecanismos de busca sigam os links desta página.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Open Graph --}}
                    <div class="tab-pane" id="tab-og">
                        <div class="form-group">
                            <label>OG Title</label>
                            <input type="text" name="og_title" class="form-control"
                                   value="{{ old('og_title', $seoSetting->og_title) }}"
                                   placeholder="Título para compartilhamento (Facebook, LinkedIn...)">
                        </div>
                        <div class="form-group">
                            <label>OG Description</label>
                            <textarea name="og_description" class="form-control" rows="3"
                                      placeholder="Descrição para compartilhamento">{{ old('og_description', $seoSetting->og_description) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>OG Image (URL)</label>
                            <input type="url" name="og_image" id="og_image" class="form-control"
                                   value="{{ old('og_image', $seoSetting->og_image) }}"
                                   placeholder="https://homemechanic.com.br/img/og.jpg">
                            <small class="form-text">Recomendado: 1200×630px</small>
                        </div>
                        <div class="form-group">
                            <label>OG Type</label>
                            <select name="og_type" class="form-control" style="max-width:200px;">
                                @foreach(['website'=>'Website','article'=>'Article','blog'=>'Blog','profile'=>'Profile'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('og_type', $seoSetting->og_type ?? 'website') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tab: Twitter --}}
                    <div class="tab-pane" id="tab-twitter">
                        <div class="form-group">
                            <label>Twitter Card</label>
                            <select name="twitter_card" class="form-control" style="max-width:250px;">
                                @foreach(['summary_large_image'=>'Summary Large Image','summary'=>'Summary','app'=>'App','player'=>'Player'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('twitter_card', $seoSetting->twitter_card ?? 'summary_large_image') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Twitter Title</label>
                            <input type="text" name="twitter_title" class="form-control"
                                   value="{{ old('twitter_title', $seoSetting->twitter_title) }}"
                                   placeholder="Título para Twitter">
                        </div>
                        <div class="form-group">
                            <label>Twitter Description</label>
                            <textarea name="twitter_description" class="form-control" rows="3"
                                      placeholder="Descrição para Twitter">{{ old('twitter_description', $seoSetting->twitter_description) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Twitter Image (URL)</label>
                            <input type="url" name="twitter_image" class="form-control"
                                   value="{{ old('twitter_image', $seoSetting->twitter_image) }}"
                                   placeholder="https://homemechanic.com.br/img/twitter.jpg">
                        </div>
                    </div>

                    {{-- Tab: Avançado --}}
                    <div class="tab-pane" id="tab-advanced">
                        <div class="form-group">
                            <label>Schema.org (JSON-LD)</label>
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-secondary btn-sm" id="generateSchema">
                                    <i class="fas fa-magic"></i> Gerar Automaticamente
                                </button>
                                <input type="hidden" name="generate_schema" id="generate_schema" value="0">
                            </div>
                            <textarea name="schema_markup" id="schema_markup" class="form-control"
                                      rows="8" style="font-family:monospace; font-size:0.82rem;"
                                      placeholder='{"@context":"https://schema.org","@type":"AutoRepair",...}'>{{ old('schema_markup', $seoSetting->schema_markup) }}</textarea>
                            <small class="form-text">JSON-LD para dados estruturados. Deixe em branco para não usar.</small>
                        </div>

                        <div class="form-group">
                            <label>Tags Personalizadas no &lt;head&gt;</label>
                            <textarea name="custom_head_tags" class="form-control" rows="5"
                                      style="font-family:monospace; font-size:0.82rem;"
                                      placeholder="<!-- Tags HTML personalizadas -->">{{ old('custom_head_tags', $seoSetting->custom_head_tags) }}</textarea>
                            <small class="form-text">HTML que será inserido no &lt;head&gt; desta página.</small>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </div>

        </div>

        {{-- Coluna lateral --}}
        <div class="col-lg-4">

            {{-- Preview Google --}}
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fab fa-google"></i> Preview Google</span>
                </div>
                <div class="card-body">
                    <div class="seo-preview-google">
                        <div class="seo-preview-url" id="prev-url">homemechanic.com.br</div>
                        <div class="seo-preview-title" id="prev-title">Título da página</div>
                        <div class="seo-preview-desc" id="prev-desc">Descrição da página para mecanismos de busca...</div>
                    </div>
                </div>
            </div>

            {{-- Preview OG --}}
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fab fa-facebook"></i> Preview Social</span>
                </div>
                <div class="card-body">
                    <div class="seo-preview-og">
                        <div class="seo-preview-og-img" id="prev-og-img">
                            <i class="fas fa-image"></i>
                        </div>
                        <div class="seo-preview-og-body">
                            <div class="seo-preview-og-site">homemechanic.com.br</div>
                            <div class="seo-preview-og-title" id="prev-og-title">Título OG</div>
                            <div class="seo-preview-og-desc" id="prev-og-desc">Descrição OG...</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Score SEO --}}
            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-chart-bar"></i> Score SEO</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:0.85rem; font-weight:600;">Pontuação</span>
                        <span id="seo-score-num" style="font-size:1.5rem; font-weight:700; color:var(--hm-primary);">0</span>
                    </div>
                    <div class="score-bar mb-3">
                        <div class="score-fill" id="seo-score-bar" style="width:0%; background:var(--hm-primary);"></div>
                    </div>
                    <div id="seo-issues" style="font-size:0.82rem;"></div>
                </div>
            </div>

            {{-- Hashtags --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-hashtag"></i> Hashtags Sugeridas</span>
                </div>
                <div class="card-body">
                    <div id="hashtags-list" class="d-flex flex-wrap gap-1">
                        @foreach($hashtags as $tag)
                            <span class="badge badge-secondary" style="cursor:pointer; font-size:0.75rem;"
                                  onclick="copyHashtag('{{ $tag }}')">{{ $tag }}</span>
                        @endforeach
                    </div>
                    <small class="form-text mt-2">Clique para copiar</small>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
// ── Tabs ──────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

// ── Char counters ─────────────────────────────────────────
function updateCounter(inputId, counterId, min, max) {
    const el = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    if (!el || !counter) return;

    function update() {
        const len = el.value.length;
        counter.textContent = len + ' caracteres';
        counter.className = 'char-counter ' + (len === 0 ? '' : len < min ? 'char-warn' : len > max ? 'char-bad' : 'char-ok');
    }
    el.addEventListener('input', update);
    update();
}
updateCounter('meta_title', 'title-counter', 30, 60);
updateCounter('meta_description', 'desc-counter', 120, 160);

// ── Live preview ──────────────────────────────────────────
function updatePreview() {
    const title = document.getElementById('meta_title').value;
    const desc  = document.getElementById('meta_description').value;
    const ogImg = document.getElementById('og_image')?.value;

    document.getElementById('prev-title').textContent = title || 'Título da página';
    document.getElementById('prev-desc').textContent  = desc  || 'Descrição da página...';
    document.getElementById('prev-og-title').textContent = title || 'Título OG';
    document.getElementById('prev-og-desc').textContent  = desc  || 'Descrição OG...';

    if (ogImg) {
        document.getElementById('prev-og-img').innerHTML = `<img src="${ogImg}" style="width:100%;height:160px;object-fit:cover;" onerror="this.parentElement.innerHTML='<i class=\\'fas fa-image\\'></i>'">`;
    }

    updateScore(title, desc);
}

document.getElementById('meta_title').addEventListener('input', updatePreview);
document.getElementById('meta_description').addEventListener('input', updatePreview);
document.getElementById('og_image')?.addEventListener('input', updatePreview);
updatePreview();

// ── SEO Score ─────────────────────────────────────────────
function updateScore(title, desc) {
    let score = 0;
    const issues = [];

    if (!title) {
        issues.push({ type: 'bad', text: 'Título não definido' });
    } else if (title.length < 30) {
        issues.push({ type: 'warn', text: 'Título muito curto (' + title.length + ' chars)' });
        score += 10;
    } else if (title.length > 60) {
        issues.push({ type: 'warn', text: 'Título muito longo (' + title.length + ' chars)' });
        score += 15;
    } else {
        issues.push({ type: 'ok', text: 'Título com tamanho ideal' });
        score += 35;
    }

    if (!desc) {
        issues.push({ type: 'bad', text: 'Descrição não definida' });
    } else if (desc.length < 120) {
        issues.push({ type: 'warn', text: 'Descrição muito curta (' + desc.length + ' chars)' });
        score += 10;
    } else if (desc.length > 160) {
        issues.push({ type: 'warn', text: 'Descrição muito longa (' + desc.length + ' chars)' });
        score += 15;
    } else {
        issues.push({ type: 'ok', text: 'Descrição com tamanho ideal' });
        score += 35;
    }

    const keywords = document.querySelector('[name="meta_keywords"]')?.value;
    if (keywords) {
        issues.push({ type: 'ok', text: 'Palavras-chave definidas' });
        score += 15;
    } else {
        issues.push({ type: 'warn', text: 'Palavras-chave não definidas' });
    }

    const canonical = document.querySelector('[name="canonical_url"]')?.value;
    if (canonical) {
        issues.push({ type: 'ok', text: 'URL canônica definida' });
        score += 15;
    }

    document.getElementById('seo-score-num').textContent = score;
    const bar = document.getElementById('seo-score-bar');
    bar.style.width = score + '%';
    bar.style.background = score >= 70 ? '#28a745' : score >= 40 ? '#ffc107' : '#dc3545';

    const icons = { ok: '✅', warn: '⚠️', bad: '❌' };
    document.getElementById('seo-issues').innerHTML = issues.map(i =>
        `<div style="margin-bottom:0.3rem;">${icons[i.type]} ${i.text}</div>`
    ).join('');
}

// ── Schema auto-generate ──────────────────────────────────
document.getElementById('generateSchema').addEventListener('click', function() {
    document.getElementById('generate_schema').value = '1';
    document.getElementById('seoForm').submit();
});

// ── Copy hashtag ──────────────────────────────────────────
function copyHashtag(tag) {
    navigator.clipboard.writeText(tag).then(() => {
        Swal.fire({ icon: 'success', title: 'Copiado!', text: tag, timer: 1200, showConfirmButton: false });
    });
}
</script>
@endsection
