@extends('layouts.admin')
@section('title', 'Template: ' . $meta['name'])
@section('page-title', 'Templates de E-mail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email') }}">E-mail</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.email.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">{{ $meta['name'] }}</li>
@endsection

@section('styles')
{{-- Bootstrap 4 CSS necessário para o Summernote --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css">
<style>
/* Reset conflito Bootstrap 4 vs 5 */
.note-editor { z-index: 1; }
.note-toolbar .btn { display: inline-flex !important; align-items: center !important; }

/* Variáveis */
.var-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: rgba(255,107,0,0.1); color: #FF6B00;
    border: 1px solid rgba(255,107,0,0.3);
    padding: 0.25rem 0.7rem; border-radius: 20px;
    font-size: 0.73rem; font-weight: 700; font-family: monospace;
    cursor: pointer; transition: all 0.2s; user-select: none;
    white-space: nowrap;
}
.var-chip:hover { background: #FF6B00; color: #fff; transform: translateY(-1px); }

/* Preview */
#preview-frame {
    width: 100%; height: 480px;
    border: 1px solid #e2e8f0; border-radius: 8px;
    background: #f8fafc;
}
.preview-subject-bar {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 0.65rem 1rem;
    font-size: 0.85rem; margin-bottom: 0.75rem;
    display: flex; align-items: center; gap: 0.75rem;
}
.preview-subject-bar .label {
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #94a3b8; flex-shrink: 0;
}
.preview-subject-bar .value { color: #1e293b; font-weight: 600; }

/* Tabs modo */
.mode-tabs { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 1rem; }
.mode-tab {
    background: none; border: none; padding: 0.5rem 1rem;
    font-size: 0.82rem; font-weight: 600; color: #64748b;
    cursor: pointer; border-bottom: 2px solid transparent;
    margin-bottom: -2px; transition: all 0.2s;
}
.mode-tab.active { color: #FF6B00; border-bottom-color: #FF6B00; }
.mode-tab i { margin-right: 0.35rem; }

/* Sticky preview */
.preview-sticky { position: sticky; top: 72px; }

/* Summernote height */
.note-editable { min-height: 260px !important; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-envelope-open-text me-2" style="color:#FF6B00;"></i>
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
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-edit"></i> Editor de Template</span>
                <div class="card-tools">
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.8);">{{ $meta['name'] }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.settings.email.templates.update', $slug) }}" id="tplForm">
                @csrf
                <div class="card-body">

                    {{-- Variáveis --}}
                    <div class="mb-3">
                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:0.5rem;">
                            Variáveis disponíveis
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;">(clique para inserir)</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($meta['vars'] as $var)
                                <span class="var-chip" onclick="insertVariable({{ json_encode($var) }})">
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
                               placeholder="Ex: Bem-vindo à @{{site_name}}!">
                        <small class="form-text">Você pode usar variáveis como <code style="color:#FF6B00;">@{{site_name}}</code> no assunto.</small>
                    </div>

                    {{-- Corpo --}}
                    <div class="form-group mb-0">
                        <label>Corpo do E-mail <span class="text-danger">*</span></label>

                        <div class="mode-tabs">
                            <button type="button" class="mode-tab active" id="tabRich" onclick="switchMode('rich')">
                                <i class="fas fa-paint-brush"></i> Editor Visual
                            </button>
                            <button type="button" class="mode-tab" id="tabHtml" onclick="switchMode('html')">
                                <i class="fas fa-code"></i> HTML Direto
                            </button>
                        </div>

                        {{-- Summernote --}}
                        <div id="richEditorWrap">
                            <textarea id="summernote" name="body">{{ old('body', $body) }}</textarea>
                        </div>

                        {{-- HTML textarea --}}
                        <div id="htmlEditorWrap" style="display:none;">
                            <textarea id="htmlBody" class="form-control"
                                      style="font-family:monospace;font-size:0.82rem;min-height:260px;resize:vertical;"
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
    <div class="col-lg-6">
        <div class="preview-sticky">

            {{-- Preview do e-mail --}}
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
                        <span class="label">Assunto:</span>
                        <span class="value" id="preview-subject">{{ $subject }}</span>
                    </div>
                    <iframe id="preview-frame"
                            srcdoc="<div style='padding:2rem;color:#94a3b8;text-align:center;font-family:sans-serif;'><i style='font-size:2.5rem;display:block;margin-bottom:1rem;'>📧</i>O preview aparecerá aqui automaticamente.</div>">
                    </iframe>
                </div>
            </div>

            {{-- Tabela de variáveis de exemplo --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-table"></i> Dados de Exemplo no Preview</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0" style="font-size:0.8rem;">
                        <thead>
                            <tr>
                                <th style="width:45%;">Variável</th>
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
                                    <code style="color:#FF6B00;font-size:0.75rem;background:rgba(255,107,0,0.08);padding:0.1rem 0.4rem;border-radius:4px;">{{ $var }}</code>
                                </td>
                                <td style="color:#64748b;">{{ Str::limit($val, 45) }}</td>
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
{{-- Bootstrap 4 JS + Summernote (precisam do jQuery já carregado no layout) --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-pt-BR.min.js"></script>

<script>
var PREVIEW_URL  = '{{ route("admin.settings.email.templates.preview") }}';
var TEST_URL     = '{{ route("admin.settings.email.test") }}';
var CSRF         = document.querySelector('meta[name="csrf-token"]').content;
var currentMode  = 'rich';
var previewTimer = null;

// ── Inicializar Summernote ────────────────────────────────
$(function() {
    $('#summernote').summernote({
        lang: 'pt-BR',
        height: 260,
        toolbar: [
            ['style',  ['style']],
            ['font',   ['bold', 'italic', 'underline', 'clear']],
            ['color',  ['color']],
            ['para',   ['ul', 'ol', 'paragraph']],
            ['table',  ['table']],
            ['insert', ['link', 'hr']],
            ['view',   ['codeview', 'fullscreen']],
        ],
        callbacks: {
            onChange: function() { schedulePreview(); }
        },
        placeholder: 'Digite o conteúdo do e-mail aqui...',
    });

    // Assunto → preview ao digitar
    $('#subject').on('input', schedulePreview);

    // Preview inicial após 500ms
    setTimeout(refreshPreview, 500);
});

// ── Alternar modo ─────────────────────────────────────────
function switchMode(mode) {
    currentMode = mode;
    $('#tabRich').toggleClass('active', mode === 'rich');
    $('#tabHtml').toggleClass('active', mode === 'html');

    if (mode === 'html') {
        var html = $('#summernote').summernote('code');
        $('#htmlBody').val(html);
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
    if (currentMode === 'html') return $('#htmlBody').val();
    return $('#summernote').summernote('code');
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

// ── Preview com debounce ──────────────────────────────────
function schedulePreview() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 700);
}

function refreshPreview() {
    var subject = $('#subject').val() || '';
    var body    = getBody() || '';

    if (!subject && !body) return;

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
        html: '<input type="email" id="swalTestEmail" class="swal2-input" placeholder="seu@email.com" value="' + userEmail + '">',
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
