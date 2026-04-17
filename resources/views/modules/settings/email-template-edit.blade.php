@extends('layouts.admin')
@section('title', 'Template: ' . $meta['name'])
@section('page-title', 'Templates de E-mail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email') }}">E-mail</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">{{ $meta['name'] }}</li>
@endsection

@section('styles')
<!-- Summernote -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css">
<style>
/* ── Variáveis ─────────────────────────────────────────── */
.var-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: var(--hm-primary-light); color: var(--hm-primary);
    border: 1px solid rgba(255,107,0,0.25);
    padding: 0.2rem 0.65rem; border-radius: 20px;
    font-size: 0.73rem; font-weight: 700; font-family: monospace;
    cursor: pointer; transition: all 0.2s; user-select: none;
}
.var-chip:hover { background: var(--hm-primary); color: #fff; transform: translateY(-1px); }

/* ── Preview ───────────────────────────────────────────── */
.preview-wrap {
    position: sticky; top: 72px;
}
.preview-subject-bar {
    background: #f8fafc; border: 1px solid var(--hm-border);
    border-radius: 8px; padding: 0.65rem 1rem;
    font-size: 0.85rem; margin-bottom: 0.75rem;
    display: flex; align-items: center; gap: 0.5rem;
}
.preview-subject-bar strong { color: var(--hm-text-muted); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px; flex-shrink: 0; }
.preview-subject-bar span   { color: var(--hm-text); font-weight: 600; }
#preview-frame {
    width: 100%; height: 500px;
    border: 1px solid var(--hm-border); border-radius: 8px;
    background: #f8fafc;
}
.preview-loading {
    display: flex; align-items: center; justify-content: center;
    height: 500px; color: var(--hm-text-muted); font-size: 0.88rem;
    border: 1px solid var(--hm-border); border-radius: 8px;
    background: #f8fafc; gap: 0.5rem;
}

/* ── Summernote overrides ──────────────────────────────── */
.note-editor.note-frame { border-radius: 0 0 8px 8px !important; border-color: var(--hm-border) !important; }
.note-toolbar { border-radius: 8px 8px 0 0 !important; background: #f8fafc !important; border-color: var(--hm-border) !important; }
.note-editable { min-height: 280px !important; font-family: 'Segoe UI', sans-serif !important; font-size: 0.9rem !important; }
.note-statusbar { border-radius: 0 0 8px 8px !important; }

/* ── Tabs ──────────────────────────────────────────────── */
.mode-tabs { display: flex; gap: 0; border-bottom: 2px solid var(--hm-border); margin-bottom: 1rem; }
.mode-tab  {
    background: none; border: none; padding: 0.55rem 1.1rem;
    font-size: 0.82rem; font-weight: 600; color: var(--hm-text-muted);
    cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px;
    transition: all 0.2s;
}
.mode-tab.active { color: var(--hm-primary); border-bottom-color: var(--hm-primary); }
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

    {{-- ── EDITOR ─────────────────────────────────────── --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-edit"></i> Editor de Template</span>
                <div class="card-tools">
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.75);">{{ $meta['name'] }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.settings.email.templates.update', $slug) }}" id="tplForm">
                @csrf
                <div class="card-body">

                    {{-- Variáveis --}}
                    <div class="mb-3">
                        <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--hm-text-muted);">
                            Variáveis disponíveis
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;">(clique para inserir no editor)</span>
                        </label>
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            @foreach($meta['vars'] as $var)
                                <span class="var-chip" onclick="insertVariable('{{ $var }}')">
                                    <i class="fas fa-code" style="font-size:0.6rem;"></i>{{ $var }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Assunto --}}
                    <div class="form-group">
                        <label>Assunto do E-mail <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="{{ old('subject', $subject) }}" required
                               placeholder="Ex: Bem-vindo à {{site_name}}!">
                        <small class="form-text">Use variáveis como <code>{{site_name}}</code> no assunto.</small>
                    </div>

                    {{-- Modo editor --}}
                    <div class="form-group">
                        <label>Corpo do E-mail <span class="text-danger">*</span></label>

                        <div class="mode-tabs">
                            <button type="button" class="mode-tab active" id="tabRich" onclick="switchMode('rich')">
                                <i class="fas fa-paint-brush me-1"></i> Editor Visual
                            </button>
                            <button type="button" class="mode-tab" id="tabHtml" onclick="switchMode('html')">
                                <i class="fas fa-code me-1"></i> HTML Direto
                            </button>
                        </div>

                        {{-- Summernote (editor visual) --}}
                        <div id="richEditor">
                            <textarea id="summernote" name="body">{{ old('body', $body) }}</textarea>
                        </div>

                        {{-- Textarea HTML --}}
                        <div id="htmlEditor" style="display:none;">
                            <textarea id="htmlBody" class="form-control"
                                      style="font-family:monospace;font-size:0.82rem;min-height:280px;"
                                      placeholder="<p>Seu HTML aqui...</p>"></textarea>
                        </div>

                        <small class="form-text mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            Use o editor visual para formatação fácil ou HTML direto para controle total.
                        </small>
                    </div>

                </div>
                <div class="card-footer d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Template
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="refreshPreview()">
                        <i class="fas fa-sync-alt"></i> Atualizar Preview
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="sendTestEmail()">
                        <i class="fas fa-paper-plane"></i> Enviar Teste
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── PREVIEW ─────────────────────────────────────── --}}
    <div class="col-xl-6">
        <div class="preview-wrap">
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-eye"></i> Preview em Tempo Real</span>
                    <div class="card-tools">
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.7);">
                            <i class="fas fa-flask me-1"></i>Dados de exemplo
                        </span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="preview-subject-bar">
                        <strong>Assunto:</strong>
                        <span id="preview-subject">{{ $subject }}</span>
                    </div>
                    <div id="preview-loading" class="preview-loading" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i> Gerando preview...
                    </div>
                    <iframe id="preview-frame" srcdoc="<div style='padding:2rem;color:#94a3b8;text-align:center;font-family:sans-serif;'><i style='font-size:2rem;'>📧</i><br><br>O preview aparecerá aqui automaticamente.</div>"></iframe>
                </div>
            </div>

            {{-- Variáveis de exemplo --}}
            <div class="card mt-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-flask"></i> Dados de Exemplo Usados no Preview</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" style="font-size:0.8rem;">
                        <thead><tr><th>Variável</th><th>Valor de Exemplo</th></tr></thead>
                        <tbody>
                            @foreach([
                                '{{nome}}'              => 'João Silva',
                                '{{email}}'             => 'joao@email.com',
                                '{{assunto}}'           => 'Orçamento para revisão',
                                '{{resposta}}'          => 'Nosso orçamento é de R$ 350,00.',
                                '{{site_name}}'         => $siteName,
                                '{{login_url}}'         => url('/admin/login'),
                                '{{reset_url}}'         => url('/admin/login').'?reset=ex',
                                '{{expiry}}'            => '2 horas',
                                '{{titulo}}'            => 'Nova mensagem recebida',
                                '{{mensagem}}'          => 'Você recebeu uma nova mensagem.',
                                '{{acao_url}}'          => url('/admin/contact'),
                                '{{acao_texto}}'        => 'Ver Mensagem',
                            ] as $var => $val)
                            @if(in_array($var, $meta['vars']))
                            <tr>
                                <td><code style="color:var(--hm-primary);font-size:0.75rem;">{{ $var }}</code></td>
                                <td style="color:var(--hm-text-muted);">{{ Str::limit($val, 40) }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<!-- jQuery UI (necessário para Summernote) -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-pt-BR.min.js"></script>

<script>
const PREVIEW_URL = '{{ route("admin.settings.email.templates.preview") }}';
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
let   currentMode = 'rich';
let   previewTimer;

// ── Inicializar Summernote ────────────────────────────────
$(document).ready(function() {
    $('#summernote').summernote({
        lang: 'pt-BR',
        height: 320,
        toolbar: [
            ['style',   ['style']],
            ['font',    ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['color',   ['color']],
            ['para',    ['ul', 'ol', 'paragraph']],
            ['table',   ['table']],
            ['insert',  ['link', 'hr']],
            ['view',    ['fullscreen', 'codeview']],
        ],
        callbacks: {
            onChange: function() {
                schedulePreview();
            }
        },
        placeholder: 'Digite o conteúdo do e-mail aqui...',
    });

    // Preview inicial
    refreshPreview();

    // Assunto → preview ao digitar
    document.getElementById('subject').addEventListener('input', schedulePreview);
});

// ── Alternar modo editor ──────────────────────────────────
function switchMode(mode) {
    currentMode = mode;
    document.getElementById('tabRich').classList.toggle('active', mode === 'rich');
    document.getElementById('tabHtml').classList.toggle('active', mode === 'html');

    if (mode === 'html') {
        // Pegar HTML do Summernote e colocar no textarea
        const html = $('#summernote').summernote('code');
        document.getElementById('htmlBody').value = html;
        document.getElementById('richEditor').style.display = 'none';
        document.getElementById('htmlEditor').style.display = 'block';
        document.getElementById('htmlBody').addEventListener('input', schedulePreview);
    } else {
        // Pegar HTML do textarea e colocar no Summernote
        const html = document.getElementById('htmlBody').value;
        if (html) $('#summernote').summernote('code', html);
        document.getElementById('richEditor').style.display = 'block';
        document.getElementById('htmlEditor').style.display = 'none';
    }
}

// ── Obter corpo atual ─────────────────────────────────────
function getBody() {
    if (currentMode === 'html') {
        return document.getElementById('htmlBody').value;
    }
    return $('#summernote').summernote('code');
}

// ── Inserir variável no editor ────────────────────────────
function insertVariable(varName) {
    if (currentMode === 'html') {
        const ta  = document.getElementById('htmlBody');
        const pos = ta.selectionStart;
        ta.value  = ta.value.slice(0, pos) + varName + ta.value.slice(ta.selectionEnd);
        ta.selectionStart = ta.selectionEnd = pos + varName.length;
        ta.focus();
    } else {
        $('#summernote').summernote('insertText', varName);
    }
    schedulePreview();
}

// ── Preview com debounce ──────────────────────────────────
function schedulePreview() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 700);
}

function refreshPreview() {
    const subject = document.getElementById('subject').value;
    const body    = getBody();

    document.getElementById('preview-loading').style.display = 'flex';
    document.getElementById('preview-frame').style.display   = 'none';

    $.ajax({
        url:         PREVIEW_URL,
        method:      'POST',
        contentType: 'application/json',
        headers:     { 'X-CSRF-TOKEN': CSRF },
        data:        JSON.stringify({ subject, body }),
        success: function(data) {
            document.getElementById('preview-subject').textContent = data.subject;
            document.getElementById('preview-frame').srcdoc        = data.html;
            document.getElementById('preview-loading').style.display = 'none';
            document.getElementById('preview-frame').style.display   = 'block';
        },
        error: function() {
            document.getElementById('preview-loading').style.display = 'none';
            document.getElementById('preview-frame').style.display   = 'block';
            HMToast.error('Erro ao gerar preview.');
        }
    });
}

// ── Sincronizar body antes de salvar ─────────────────────
document.getElementById('tplForm').addEventListener('submit', function() {
    if (currentMode === 'html') {
        // Colocar HTML do textarea no Summernote para o form pegar
        $('#summernote').summernote('code', document.getElementById('htmlBody').value);
    }
    // O Summernote já atualiza o textarea original automaticamente
});

// ── Enviar e-mail de teste ────────────────────────────────
function sendTestEmail() {
    Swal.fire({
        title: 'Enviar e-mail de teste',
        html: '<input type="email" id="testEmailAddr" class="swal2-input" placeholder="seu@email.com" value="{{ auth()->user()->email ?? '' }}">',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> Enviar',
        cancelButtonText: 'Cancelar',
        preConfirm: function() {
            const email = document.getElementById('testEmailAddr').value;
            if (!email || !email.includes('@')) {
                Swal.showValidationMessage('Informe um e-mail válido.');
                return false;
            }
            return email;
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;

        const subject = document.getElementById('subject').value;
        const body    = getBody();

        $.ajax({
            url:         '{{ route("admin.settings.email.test") }}',
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            data:        JSON.stringify({
                test_email:         result.value,
                mail_subject:       subject,
                mail_body:          body,
            }),
            success: function(data) {
                if (data.success) HMToast.success(data.message);
                else HMToast.error(data.message);
            },
            error: function(xhr) {
                HMToast.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao enviar.');
            }
        });
    });
}
</script>
@endsection
