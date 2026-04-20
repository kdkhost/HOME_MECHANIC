
<?php $__env->startSection('title', 'Template: ' . $meta['name']); ?>
<?php $__env->startSection('page-title', 'Templates de E-mail'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.settings.email')); ?>">E-mail</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.settings.email.templates')); ?>">Templates</a></li>
    <li class="breadcrumb-item active"><?php echo e($meta['name']); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<style>
/* ── Summernote overrides ──────────────────────────────── */
.note-editor.note-frame { border: 1.5px solid #e2e8f0 !important; border-radius: 0 0 8px 8px !important; }
.note-toolbar {
    background: #f8fafc !important; border: 1.5px solid #e2e8f0 !important;
    border-bottom: none !important; border-radius: 8px 8px 0 0 !important; padding: 6px 8px !important;
}
.note-toolbar .note-btn {
    border-radius: 5px !important; font-size: 0.8rem !important; padding: 3px 7px !important;
    border: 1px solid #e2e8f0 !important; background: #fff !important; color: #475569 !important; transition: all 0.15s !important;
}
.note-toolbar .note-btn:hover, .note-toolbar .note-btn.active {
    background: #FF6B00 !important; color: #fff !important; border-color: #FF6B00 !important;
}
.note-editable {
    min-height: 280px !important; font-family: 'Segoe UI', Arial, sans-serif !important;
    font-size: 14px !important; line-height: 1.7 !important; color: #374151 !important; padding: 16px !important;
}
.note-statusbar { border-radius: 0 0 8px 8px !important; }

/* ── Variáveis ─────────────────────────────────────────── */
.var-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: rgba(255,107,0,0.08); color: #FF6B00;
    border: 1px solid rgba(255,107,0,0.25);
    padding: 0.22rem 0.65rem; border-radius: 20px;
    font-size: 0.72rem; font-weight: 700; font-family: monospace;
    cursor: pointer; transition: all 0.18s; user-select: none; white-space: nowrap;
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
    width: 100%; height: 500px;
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

/* ── Preview loading ───────────────────────────────────── */
.preview-loading {
    display: flex; align-items: center; justify-content: center;
    height: 500px; color: #94a3b8; font-family: sans-serif;
    flex-direction: column; gap: 0.75rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title">
        <i class="<?php echo e($meta['icon'] ?? 'fas fa-envelope'); ?> me-2" style="color:#FF6B00;"></i>
        <?php echo e($meta['name']); ?>

    </h2>
    <div class="page-header-actions">
        <a href="<?php echo e(route('admin.settings.email.templates')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row g-4">

    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-edit"></i> Editor de Template</span>
                <div class="card-tools">
                    <span style="font-size:0.75rem;color:rgba(255,255,255,0.8);"><?php echo e($meta['desc']); ?></span>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('admin.settings.email.templates.update', $slug)); ?>" id="tplForm">
                <?php echo csrf_field(); ?>
                <div class="card-body">

                    
                    <div class="mb-3">
                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin-bottom:0.5rem;">
                            Variáveis disponíveis
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;"> — clique para inserir no editor</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <?php $__currentLoopData = $meta['vars']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="var-chip" onclick="insertVariable(<?php echo e(json_encode($var)); ?>)">
                                    <i class="fas fa-code" style="font-size:0.55rem;"></i><?php echo e($var); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label>Assunto do E-mail <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="<?php echo e(old('subject', $subject)); ?>" required
                               placeholder="Ex: Bem-vindo à &#123;&#123;site_name&#125;&#125;!">
                        <small class="form-text">Use variáveis como <code style="color:#FF6B00;background:rgba(255,107,0,0.08);padding:0 4px;border-radius:3px;">&#123;&#123;site_name&#125;&#125;</code> no assunto.</small>
                    </div>

                    
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
                            <textarea id="summernote" name="body"><?php echo e(old('body', $body)); ?></textarea>
                        </div>

                        <div id="htmlEditorWrap" style="display:none;">
                            <textarea id="htmlBody" class="form-control"
                                      style="font-family:'Courier New',monospace;font-size:0.82rem;min-height:280px;resize:vertical;border-radius:0 0 8px 8px;border-top:none;"
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
                        <span class="val" id="preview-subject"><?php echo e($subject); ?></span>
                    </div>
                    
                    <div id="preview-container" style="position:relative;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;min-height:500px;background:#f8fafc;">
                        <div id="preview-loading" class="preview-loading">
                            <div style="font-size:2.5rem;">📧</div>
                            <div>Carregando preview...</div>
                        </div>
                        <iframe id="preview-frame"
                                style="width:100%;height:500px;border:none;display:none;"
                                sandbox="allow-same-origin"></iframe>
                    </div>
                </div>
            </div>

            
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
                            <?php
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
                            ?>
                            <?php $__currentLoopData = $exampleVars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(in_array($var, $meta['vars'])): ?>
                            <tr>
                                <td>
                                    <code style="color:#FF6B00;font-size:0.73rem;background:rgba(255,107,0,0.08);padding:0.1rem 0.4rem;border-radius:4px;"><?php echo e($var); ?></code>
                                </td>
                                <td style="color:#64748b;"><?php echo e(Str::limit($val, 42)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-pt-BR.min.js"></script>

<script>
var PREVIEW_URL  = '<?php echo e(route("admin.settings.email.templates.preview")); ?>';
var TEST_URL     = '<?php echo e(route("admin.settings.email.test")); ?>';
var CSRF         = document.querySelector('meta[name="csrf-token"]').content;
var currentMode  = 'rich';
var previewTimer = null;
var summernoteReady = false;

$(function() {
    // ── Inicializar Summernote Lite ───────────────────────
    $('#summernote').summernote({
        lang: 'pt-BR',
        height: 280,
        toolbar: [
            ['style',    ['style']],
            ['font',     ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontsize', ['fontsize']],
            ['color',    ['color']],
            ['para',     ['ul', 'ol', 'paragraph', 'height']],
            ['table',    ['table']],
            ['insert',   ['link', 'picture', 'hr']],
            ['view',     ['codeview', 'fullscreen']],
        ],
        fontSizes: ['12', '13', '14', '15', '16', '18', '20', '24'],
        callbacks: {
            onInit: function() {
                summernoteReady = true;
                // Pequeno delay para garantir que o DOM do Summernote está pronto
                setTimeout(function() { refreshPreview(); }, 300);
            },
            onChange: function() {
                schedulePreview();
            },
            onImageUpload: function(files) {
                HMToast.warning('Upload de imagens não suportado. Use uma URL externa.');
            }
        },
        placeholder: 'Digite o conteúdo do e-mail aqui...',
        tabsize: 2,
    });

    // Assunto → preview ao digitar
    $('#subject').on('input', schedulePreview);

    // Fallback: se onInit não disparar em 2s, forçar preview
    setTimeout(function() {
        if (!summernoteReady) {
            summernoteReady = true;
            refreshPreview();
        }
    }, 2000);
});

// ── Alternar modo ─────────────────────────────────────────
function switchMode(mode) {
    currentMode = mode;
    $('#tabRich').toggleClass('active', mode === 'rich');
    $('#tabHtml').toggleClass('active', mode === 'html');

    if (mode === 'html') {
        var code = '';
        try { code = $('#summernote').summernote('code'); } catch(e) {}
        $('#htmlBody').val(code);
        $('#richEditorWrap').hide();
        $('#htmlEditorWrap').show();
        $('#htmlBody').off('input').on('input', schedulePreview);
    } else {
        var html = $('#htmlBody').val();
        if (html) {
            try { $('#summernote').summernote('code', html); } catch(e) {}
        }
        $('#richEditorWrap').show();
        $('#htmlEditorWrap').hide();
    }
    schedulePreview();
}

// ── Obter corpo atual ─────────────────────────────────────
function getBody() {
    if (currentMode === 'html') {
        return $('#htmlBody').val() || '';
    }
    try {
        return $('#summernote').summernote('code') || '';
    } catch(e) {
        return $('#summernote').val() || '';
    }
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
        try {
            $('#summernote').summernote('focus');
            $('#summernote').summernote('insertText', varName);
        } catch(e) {
            HMToast.warning('Clique no editor antes de inserir a variável.');
        }
    }
    schedulePreview();
}

// ── Preview ───────────────────────────────────────────────
function schedulePreview() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 600);
}

function showPreviewLoading() {
    document.getElementById('preview-loading').style.display = 'flex';
    document.getElementById('preview-frame').style.display   = 'none';
}

function showPreviewFrame(html) {
    var frame = document.getElementById('preview-frame');
    frame.style.display = 'block';
    document.getElementById('preview-loading').style.display = 'none';

    // Escrever diretamente no documento do iframe
    try {
        var doc = frame.contentDocument || frame.contentWindow.document;
        doc.open();
        doc.write(html);
        doc.close();
    } catch(e) {
        // Fallback: srcdoc
        frame.srcdoc = html;
    }
}

function showPreviewError(msg) {
    document.getElementById('preview-loading').innerHTML =
        '<div style="font-size:1.5rem;margin-bottom:0.5rem;">❌</div>' +
        '<div style="color:#dc2626;font-size:0.85rem;text-align:center;padding:0 1rem;">' + msg + '</div>';
    document.getElementById('preview-loading').style.display = 'flex';
    document.getElementById('preview-frame').style.display   = 'none';
}

function refreshPreview() {
    var subject = $('#subject').val() || '';
    var body    = getBody();

    if (!body && !subject) {
        showPreviewLoading();
        return;
    }

    showPreviewLoading();

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
                showPreviewFrame(data.html);
            } else {
                showPreviewError('Resposta inválida do servidor.');
            }
        },
        error: function(xhr) {
            var msg = 'Erro ao gerar preview.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            else if (xhr.status === 419) msg = 'Sessão expirada. Recarregue a página.';
            else if (xhr.status === 500) msg = 'Erro interno do servidor (500).';
            showPreviewError(msg);
        }
    });
}

// ── Sincronizar antes de salvar ───────────────────────────
$('#tplForm').on('submit', function() {
    if (currentMode === 'html') {
        try { $('#summernote').summernote('code', $('#htmlBody').val()); } catch(e) {}
    }
    // Garantir que o textarea name="body" tem o valor correto
    var body = getBody();
    if (!$('textarea[name="body"]').length) {
        $('<input>').attr({ type: 'hidden', name: 'body', value: body }).appendTo(this);
    }
});

// ── Enviar e-mail de teste ────────────────────────────────
function sendTestEmail() {
    var userEmail = '<?php echo e(addslashes(auth()->user()->email ?? "")); ?>';

    Swal.fire({
        title: 'Enviar e-mail de teste',
        html: '<p style="font-size:0.88rem;color:#64748b;margin-bottom:0.75rem;">O e-mail será enviado com o conteúdo atual do editor.</p>' +
              '<input type="email" id="swalTestEmail" class="swal2-input" placeholder="seu@email.com" value="' + userEmail + '">',
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\settings\email-template-edit.blade.php ENDPATH**/ ?>