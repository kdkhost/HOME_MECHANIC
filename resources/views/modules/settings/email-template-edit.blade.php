@extends('layouts.admin')
@section('title', 'Editar Template — ' . $meta['name'])
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email') }}">E-mail</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">{{ $meta['name'] }}</li>
@endsection

@section('styles')
<style>
.var-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: var(--hm-primary-light); color: var(--hm-primary);
    border: 1px solid rgba(255,107,0,0.2);
    padding: 0.2rem 0.6rem; border-radius: 5px;
    font-size: 0.75rem; font-weight: 600; font-family: monospace;
    cursor: pointer; transition: var(--hm-transition);
}
.var-chip:hover { background: var(--hm-primary); color: #fff; }
.preview-frame {
    width: 100%; height: 520px;
    border: 1px solid var(--hm-border);
    border-radius: 8px; background: #f8fafc;
}
.preview-subject {
    background: #f8fafc; border: 1px solid var(--hm-border);
    border-radius: 8px; padding: 0.75rem 1rem;
    font-size: 0.88rem; color: var(--hm-text);
    margin-bottom: 0.75rem;
}
.preview-subject strong { color: var(--hm-text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 0.2rem; }
.editor-wrap { position: relative; }
.editor-toolbar {
    display: flex; gap: 0.4rem; flex-wrap: wrap;
    padding: 0.5rem; background: #f8fafc;
    border: 1px solid var(--hm-border); border-bottom: none;
    border-radius: 8px 8px 0 0;
}
.editor-toolbar button {
    background: none; border: 1px solid var(--hm-border);
    border-radius: 5px; padding: 0.25rem 0.6rem;
    font-size: 0.78rem; color: var(--hm-text-muted);
    cursor: pointer; transition: var(--hm-transition);
}
.editor-toolbar button:hover { background: var(--hm-primary-light); color: var(--hm-primary); border-color: var(--hm-primary); }
#body-editor {
    border-radius: 0 0 8px 8px !important;
    font-family: 'Courier New', monospace !important;
    font-size: 0.875rem !important;
    min-height: 280px;
    resize: vertical;
}
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-envelope-open-text me-2" style="color:var(--hm-primary);"></i>
        {{ $meta['name'] }}
    </h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.settings.email.templates') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- Editor --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-code"></i> Editor de Template</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.email.templates.update', $slug) }}" id="tplForm">
                @csrf
                <div class="card-body">

                    {{-- Variáveis disponíveis --}}
                    <div class="mb-3">
                        <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--hm-text-muted);">
                            Variáveis disponíveis <small style="font-weight:400;">(clique para inserir)</small>
                        </label>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($meta['vars'] as $var)
                                <span class="var-chip" onclick="insertVar('{{ $var }}')">{{ $var }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Assunto --}}
                    <div class="form-group">
                        <label>Assunto do E-mail <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="{{ old('subject', $subject) }}" required
                               placeholder="Assunto do e-mail">
                    </div>

                    {{-- Corpo --}}
                    <div class="form-group">
                        <label>Corpo do E-mail <span class="text-danger">*</span></label>
                        <div class="editor-wrap">
                            <div class="editor-toolbar">
                                <button type="button" onclick="wrapText('\n\n', '')">¶ Parágrafo</button>
                                <button type="button" onclick="wrapText('**', '**')"><b>Negrito</b></button>
                                <button type="button" onclick="wrapText('\n• ', '')">• Lista</button>
                                <button type="button" onclick="wrapText('\n---\n', '')">— Divisor</button>
                            </div>
                            <textarea name="body" id="body-editor" class="form-control"
                                      rows="12" required>{{ old('body', $body) }}</textarea>
                        </div>
                        <small class="form-text">Use texto simples. Quebras de linha serão convertidas em parágrafos no e-mail.</small>
                    </div>

                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Template
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnPreview">
                        <i class="fas fa-eye"></i> Atualizar Preview
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview --}}
    <div class="col-lg-6">
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-eye"></i> Preview em Tempo Real</span>
                <div class="card-tools">
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.7);">Dados de exemplo</span>
                </div>
            </div>
            <div class="card-body">
                <div class="preview-subject">
                    <strong>Assunto</strong>
                    <span id="preview-subject">{{ $subject }}</span>
                </div>
                <iframe id="preview-frame" class="preview-frame" srcdoc="<p style='padding:2rem;color:#94a3b8;text-align:center;'>Clique em &quot;Atualizar Preview&quot; ou edite o template para ver o resultado.</p>"></iframe>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const previewUrl = '{{ route("admin.settings.email.templates.preview") }}';
const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

// ── Preview ───────────────────────────────────────────────
function updatePreview() {
    const subject = document.getElementById('subject').value;
    const body    = document.getElementById('body-editor').value;

    fetch(previewUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ subject, body }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('preview-subject').textContent = data.subject;
        document.getElementById('preview-frame').srcdoc = data.html;
    })
    .catch(() => HMToast.error('Erro ao gerar preview.'));
}

// Preview automático com debounce
let previewTimer;
['subject', 'body-editor'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(updatePreview, 600);
    });
});

document.getElementById('btnPreview').addEventListener('click', updatePreview);

// Preview inicial
updatePreview();

// ── Inserir variável no cursor ────────────────────────────
function insertVar(varName) {
    const ta  = document.getElementById('body-editor');
    const pos = ta.selectionStart;
    const val = ta.value;
    ta.value = val.slice(0, pos) + varName + val.slice(ta.selectionEnd);
    ta.selectionStart = ta.selectionEnd = pos + varName.length;
    ta.focus();
    ta.dispatchEvent(new Event('input'));
}

// ── Wrap text ─────────────────────────────────────────────
function wrapText(before, after) {
    const ta    = document.getElementById('body-editor');
    const start = ta.selectionStart;
    const end   = ta.selectionEnd;
    const sel   = ta.value.slice(start, end);
    ta.value    = ta.value.slice(0, start) + before + sel + after + ta.value.slice(end);
    ta.selectionStart = start + before.length;
    ta.selectionEnd   = start + before.length + sel.length;
    ta.focus();
    ta.dispatchEvent(new Event('input'));
}
</script>
@endsection
