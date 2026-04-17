@extends('layouts.admin')
@section('title', 'Template: ' . $meta['name'])
@section('page-title', 'Templates de E-mail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email') }}">E-mail</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">{{ $meta['name'] }}</li>
@endsection

@section('styles')
{{-- Summernote — usa versão lite sem dependência do Bootstrap --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<style>
/* ── Summernote lite overrides ─────────────────────────── */
.note-editor.note-frame {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 0 0 8px 8px !important;
}
.note-toolbar {
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-bottom: none !important;
    border-radius: 8px 8px 0 0 !important;
    padding: 6px 8px !important;
}
.note-toolbar .note-btn {
    border-radius: 5px !important;
    font-size: 0.8rem !important;
    padding: 3px 7px !important;
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #475569 !important;
    transition: all 0.15s !important;
}
.note-toolbar .note-btn:hover,
.note-toolbar .note-btn.active {
    background: #FF6B00 !important;
    color: #fff !important;
    border-color: #FF6B00 !important;
}
.note-editable {
    min-height: 260px !important;
    font-family: 'Segoe UI', Arial, sans-serif !important;
    font-size: 14px !important;
    line-height: 1.7 !important;
    color: #374151 !important;
    padding: 16px !important;
}
.note-statusbar { border-radius: 0 0 8px 8px !important; }
.note-popover .popover-content { padding: 4px 8px !important; }

/* ── Variáveis ─────────────────────────────────────────── */
.var-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: rgba(255,107,0,0.08); color: #FF6B00;
    border: 1px solid rgba(255,107,0,0.25);
    padding: 0.22rem 0.65rem; border-radius: 20px;
    font-size: 0.72rem; font-weight: 700; font-family: monospace;
    cursor: pointer; transition: all 0.18s; user-select: none;
    white-space: nowrap;
}
.var-chip:hover { background: #FF6B00; color: #fff; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(255,107,0,0.3); }

/* ── Tabs ──────────────────────────────────────────────── */
.mode-tabs { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 0; }
.mode-tab {
    background: none; border: none; padding: 0.5rem 1rem;
    font-size: 0.82rem; font-weight: 600; color: #64748b;
    cursor: pointer; border-bottom: 2px solid transparent;
    margin-bottom: -2px; transition: all 0.18s;
    display: flex; align-items: center; gap: 0.35rem;
}
.mode-tab.active { color: #FF6B00; border-bottom-color: #FF6B00; }

/* ── Preview ───────────────────────────────────────────── */
.preview-sticky { position: sticky; top: 72px; }
#preview-frame {
    width: 100%; height: 480px;
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    background: #f8fafc; display: block;
}
.preview-subject-bar {
    background: #f8fafc; border: 1.5px solid #e2e8f0;
    border-radius: 8px; padding: 0.6rem 1rem;
    font-size: 0.85rem; margin-bottom: 0.75rem;
    display: flex; align-items: center; gap: 0.75rem;
}
.preview-subject-bar .lbl {
    font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #94a3b8; flex-shrink: 0;
}
.preview-subject-bar .val { color: #1e293b; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="{{ $meta['icon'] ?? 'fas fa-envelope' }} me-2" style="color:#FF6B00;"></i>
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
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.8);">{{ $meta['desc'] }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.settings.email.templates.update', $slug) }}" id="tplForm">
                @csrf
                <div class="card-body">

                    {{-- Variáveis --}}
                    <div class="mb-3">
                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:0.5rem;">
                            Variáveis disponíveis
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;"> — clique para inserir no editor</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($meta['vars'] as $var)
                                <span class="var-chip" onclick="insertVariable({{ json_encode($var) }})">
                                    <i class="fas fa-code" style="font-size:0.55rem;"></i>{{ $var }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Assunto --}}
                    <div class="form-group">
                        <label>Assunto do E-mail <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="{{ old('subject', $subject) }}" required
                               placeholder="Ex: Bem-vindo à @{{site_name}}!">
                        <small class="form-text">Use variáveis como <code style="color:#FF6B00;background:rgba(255,107,0,0.08);padding:0 4px;border-radius:3px;">@{{site_name}}</code> no assunto.</small>
                    </div>

                    {{-- Corpo --}}
                    <div class="form-group mb-0">
                        <label>Corpo do E-mail <span class="text-danger">*</span></label>

                        <div class="mode-tabs mb-0">
                            <button type="button" class="mode-tab active" id="tabRich" onclick="switchMode('rich')">
                                <i class="fas fa-paint-brush"></i> Editor Visual
                            </button>
                            <button type="button" class="mode-tab" id="tabHtml" onclick="switchMode('html')">
                                <i class="fas fa-code"></i> HTML Direto
                            </button>
                        </div>

                        <div id="richEditorWrap">
                            <textarea id="summernote" name="body">{{ old('body', $body) }}</textarea>
                        </div>

                        <div id="htmlEditorWrap" style="display:none;">
                            <textarea id="htmlBody" class="form-control"
                                      style="font-family:'Courier New',monospace;font-size:0.82rem;min-height:260px;resize:vertical;border-radius:0 0 8px 8px;border-top:none;"
                                      placeholder="<p>Seu HTML aqui...</p>"></textarea>
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Template
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="refreshPreview()">
                        <i class="fas fa-sync-alt"></i> Atualizar Preview
                    </button>
                    <button type="button" class="btn btn-info" onclick="sendTestEmail()">
                        <i class="fas fa-paper-plane"></i> Enviar Teste
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── PREVIEW ─────────────────────────────────────── --}}
    <div class="col-xl-6">
        <div class="preview-sticky">

            <div class="card mb-3">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-eye"></i> Preview em Tempo Real</span>
                    <div class="card-tools">
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.75);">
                            <i class="fas fa-flask me-1"></i>Dados de exemplo
                        </span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="preview-subject-bar">
                        <span class="lbl">Assunto:</span>
                        <span class="val" id="preview-subject">{{ $subject }}</span>
                    </div>
                    <iframe id="preview-frame"
                            srcdoc="<div style='padding:2rem;color:#94a3b8;text-align:center;font-family:sans-serif;'><div style='font-size:2.5rem;margin-bottom:1rem;'>📧</div>O preview aparecerá aqui automaticamente.</div>">
                    </iframe>
                </div>
            </div>

            {{-- Variáveis de exemplo --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-table"></i> Dados de Exemplo no Preview</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0" style="font-size:0.8rem;">
                        <thead>
                            <tr>
                                <th style="width:42%;">Variável</th>
                                <th>Valor de Exemplo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $exampleVars = [
                                '{{nome}}'              => 'João Silva',
                                '{{email}}'             => 'joao@email.com',
                                '{{assunto}}'           => 'Orçamento para revisão',
                                '{{resposta}}'          => 'Nosso orçamento é de R$ 350,00.',
                                '{{mensagem_original}}' => 'Gostaria de um orçamento...',
                                '{{site_name}}'         => $siteName,
                                '{{login_url}}'         => url('/admin/login'),
                                '{{reset_url}}'         => url('/admin/login').'?reset=ex',
                                '{{expiry}}'            => '2 horas',
                                '{{titulo}}'            => 'Nova mensagem recebida',
                                '{{mensagem}}'          => 'Você recebeu uma nova mensagem.',
                                '{{acao_url}}'          => url('/admin/contact'),
                                '{{acao_texto}}'        => 'Ver Mensagem',
                            ];
                            @endphp
                            @foreach($exampleVars as $var => $val)
                            @if(in_array($var, $meta['vars']))
                            <tr>
                                <td>
                                    <code style="color:#FF6B00;font-size:0.73rem;background:rgba(255,107,0,0.08);padding:0.1rem 0.4rem;border-radius:4px;">{{ $var }}</code>
                                </td>
                                <td style="color:#64748b;">{{ Str::limit($val, 42) }}</td>
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
{{-- Summernote Lite — sem dependência do Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-pt-BR.min.js"></script>

<script>
var PREVIEW_URL  = '{{ route("admin.settings.email.templates.preview") }}';
var TEST_URL     = '{{ route("admin.settings.email.test") }}';
var CSRF         = document.querySelector('meta[name="csrf-token"]').content;
var currentMode  = 'rich';
var previewTimer = null;

$(function() {
    // ── Inicializar Summernote Lite ───────────────────────
    $('#summernote').summernote({
        lang: 'pt-BR',
        height: 260,
        toolbar: [
            ['style',   ['style']],
            ['font',    ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontsize',['fontsize']],
            ['color',   ['color']],
            ['para',    ['ul', 'ol', 'paragraph', 'height']],
            ['table',   ['table']],
            ['insert',  ['link', 'picture', 'hr']],
            ['view',    ['codeview', 'fullscreen']],
        ],
        fontSizes: ['12', '13', '14', '15', '16', '18', '20', '24'],
        callbacks: {
            onChange: function() { schedulePreview(); }
        },
        placeholder: 'Digite o conteúdo do e-mail aqui...',
        disableDragAndDrop: false,
        tabsize: 2,
    });

    // Assunto → preview ao digitar
    $('#subject').on('input', schedulePreview);

    // Preview inicial
    setTimeout(refreshPreview, 600);
});

// ── Alternar modo ─────────────────────────────────────────
function switchMode(mode) {
    currentMode = mode;
    $('#tabRich').toggleClass('active', mode === 'rich');
    $('#tabHtml').toggleClass('active', mode === 'html');

    if (mode === 'html') {
        $('#htmlBody').val($('#summernote').summernote('code'));
        $('#richEditorWrap').hide();
        $('#htmlEditorWrap').show();
        $('#htmlBody').off('input').on('input', schedulePreview);
    } else {
        var html = $('#htmlBody').val();
        if (html) $('#summernote').summernote('code', html);
        $('#richEditorWrap').show();
        $('#htmlEditorWrap').hide();
    }
}

// ── Obter corpo atual ─────────────────────────────────────
function getBody() {
    return currentMode === 'html'
        ? $('#htmlBody').val()
        : $('#summernote').summernote('code');
}

// ── Inserir variável ──────────────────────────────────────
function insertVariable(varName) {
    if (currentMode === 'html') {
        var ta  = document.getElementById('htmlBody');
        var pos = ta.selectionStart;
        ta.value = ta.value.slice(0, pos) + varName + ta.value.slice(ta.selectionEnd);
        ta.selectionStart = ta.selectionEnd = pos + varName.length;
        ta.focus();
    } else {
        $('#summernote').summernote('focus');
        $('#summernote').summernote('insertText', varName);
    }
    schedulePreview();
}

// ── Preview ───────────────────────────────────────────────
function schedulePreview() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 700);
}

function refreshPreview() {
    var subject = $('#subject').val() || '';
    var body    = getBody() || '';

    $.ajax({
        url:         PREVIEW_URL,
        method:      'POST',
        contentType: 'application/json',
        headers:     { 'X-CSRF-TOKEN': CSRF },
        data:        JSON.stringify({ subject: subject, body: body }),
        success: function(data) {
            if (data.subject !== undefined) {
                $('#preview-subject').text(data.subject || '(sem assunto)');
            }
            if (data.html) {
                document.getElementById('preview-frame').srcdoc = data.html;
            }
        },
        error: function() {
            HMToast.error('Erro ao gerar preview.');
        }
    });
}

// ── Sincronizar antes de salvar ───────────────────────────
$('#tplForm').on('submit', function() {
    if (currentMode === 'html') {
        $('#summernote').summernote('code', $('#htmlBody').val());
    }
});

// ── Enviar e-mail de teste ────────────────────────────────
function sendTestEmail() {
    var userEmail = '{{ addslashes(auth()->user()->email ?? "") }}';

    Swal.fire({
        title: 'Enviar e-mail de teste',
        html: '<p style="font-size:0.88rem;color:#64748b;margin-bottom:0.75rem;">O e-mail será enviado com o conteúdo atual do editor.</p><input type="email" id="swalTestEmail" class="swal2-input" placeholder="seu@email.com" value="' + userEmail + '">',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-paper-plane"></i> Enviar',
        cancelButtonText: 'Cancelar',
        preConfirm: function() {
            var email = document.getElementById('swalTestEmail').value;
            if (!email || !email.includes('@')) {
                Swal.showValidationMessage('Informe um e-mail válido.');
                return false;
            }
            return email;
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;

        $.ajax({
            url:         TEST_URL,
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            data:        JSON.stringify({
                test_email:   result.value,
                mail_subject: $('#subject').val(),
                mail_body:    getBody(),
            }),
            success: function(data) {
                if (data.success) HMToast.success(data.message);
                else HMToast.error(data.message);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao enviar.';
                HMToast.error(msg);
            }
        });
    });
}
</script>
@endsection
