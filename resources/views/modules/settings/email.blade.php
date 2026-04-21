@extends('layouts.admin')
@section('title', 'E-mail (SMTP)')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">E-mail (SMTP)</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title">
        <i class="fas fa-envelope me-2" style="color:var(--hm-primary);"></i>
        Configurações de E-mail
    </h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'email'])

    <div class="col-md-9">

        {{-- Formulário SMTP --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-server"></i> Configurações SMTP</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}" id="smtpForm">
                @csrf
                <input type="hidden" name="section" value="email">
                <div class="card-body">

                    <div class="form-group">
                        <label>Driver de E-mail</label>
                        <select class="form-control" name="mail_driver" id="mail_driver" style="max-width:220px;">
                            <option value="smtp"     {{ ($settings['mail_driver'] ?? '') === 'smtp'     ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="log"      {{ ($settings['mail_driver'] ?? '') === 'log'      ? 'selected' : '' }}>Log (Desenvolvimento)</option>
                        </select>
                        <small class="form-text">Use <strong>Log</strong> para desenvolvimento — os e-mails ficam no arquivo de log.</small>
                    </div>

                    <div class="row" id="smtpFields">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Servidor SMTP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mail_host" id="mail_host"
                                       value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}"
                                       placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Porta <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="mail_port" id="mail_port"
                                       value="{{ $settings['mail_port'] ?? '587' }}"
                                       placeholder="587">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Usuário SMTP</label>
                                <input type="text" class="form-control" name="mail_username" id="mail_username"
                                       value="{{ $settings['mail_username'] ?? '' }}"
                                       placeholder="seu@email.com"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Senha SMTP</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" 
                                           name="mail_password" id="mail_password"
                                           value=""
                                           placeholder="{{ ($settings['mail_password_set'] ?? false) ? '•••••••• (manter em branco para usar a senha salva)' : 'Sua senha SMTP' }}"
                                           autocomplete="new-password">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" title="Mostrar/ocultar senha">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @if($settings['mail_password_set'] ?? false)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle"></i> Senha já configurada no sistema. 
                                        <span class="text-muted">Mantenha em branco para não alterar.</span>
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label>Criptografia</label>
                                <select class="form-control" name="mail_encryption" id="mail_encryption">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (porta 587)</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (porta 465)</option>
                                    <option value=""    {{ ($settings['mail_encryption'] ?? '') === ''    ? 'selected' : '' }}>Nenhuma (porta 25)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="mail_verify_peer" name="mail_verify_peer" value="1" 
                                    {{ ($settings['mail_verify_peer'] ?? '1') === '0' ? '' : 'checked' }}>
                                <label class="custom-control-label font-weight-bold" for="mail_verify_peer">Verificar Certificado SSL (Recomendado)</label>
                                <small class="d-block text-muted">Desmarque apenas se houver erro de "Peer certificate mismatch" ou STARTTLS.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>E-mail Remetente</label>
                                <input type="email" class="form-control" name="mail_from_address" id="mail_from_address"
                                       value="{{ $settings['mail_from_address'] ?? '' }}"
                                       placeholder="noreply@homemechanic.com.br">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome Remetente</label>
                                <input type="text" class="form-control" name="mail_from_name" id="mail_from_name"
                                       value="{{ $settings['mail_from_name'] ?? 'HomeMechanic' }}"
                                       placeholder="HomeMechanic">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>

        {{-- Testar SMTP --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-paper-plane"></i> Testar Configuração SMTP</span>
            </div>
            <div class="card-body">
                <p style="color:#718096; font-size:0.9rem; margin-bottom:1.25rem;">
                    Envie um e-mail de teste usando as configurações acima (sem precisar salvar primeiro).
                    O e-mail será enviado com os valores preenchidos no formulário.
                </p>
                <div class="row align-items-end g-3">
                    <div class="col-md-6">
                        <label>E-mail de Destino para Teste</label>
                        <input type="email" class="form-control" id="test_email"
                               placeholder="seu@email.com"
                               value="{{ auth()->user()->email ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100" id="btnTestSmtp">
                            <i class="fas fa-paper-plane"></i> Enviar E-mail de Teste
                        </button>
                    </div>
                </div>

                {{-- Resultado do teste --}}
                <div id="testResult" class="mt-3" style="display:none;">
                    <div id="testResultContent" class="p-3 rounded" style="font-size:0.88rem;"></div>
                </div>
            </div>
        </div>

        {{-- Dicas de configuração --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-lightbulb"></i> Dicas de Configuração</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="background:#f8f9fa; border-radius:8px; padding:1rem; border-left:3px solid var(--hm-primary);">
                            <div style="font-weight:700; font-size:0.88rem; margin-bottom:0.5rem;">
                                <i class="fab fa-google text-danger me-1"></i> Gmail
                            </div>
                            <div style="font-size:0.82rem; color:#718096; line-height:1.6;">
                                Host: <code>smtp.gmail.com</code><br>
                                Porta: <code>587</code> (TLS) ou <code>465</code> (SSL)<br>
                                Use uma <strong>Senha de App</strong> (não a senha normal).<br>
                                Ative a verificação em 2 etapas primeiro.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#f8f9fa; border-radius:8px; padding:1rem; border-left:3px solid #0078d4;">
                            <div style="font-weight:700; font-size:0.88rem; margin-bottom:0.5rem;">
                                <i class="fab fa-microsoft" style="color:#0078d4;" ></i> Outlook / Office 365
                            </div>
                            <div style="font-size:0.82rem; color:#718096; line-height:1.6;">
                                Host: <code>smtp.office365.com</code><br>
                                Porta: <code>587</code> (TLS)<br>
                                Use seu e-mail e senha normais.<br>
                                Certifique-se que SMTP AUTH está habilitado.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#f8f9fa; border-radius:8px; padding:1rem; border-left:3px solid #ff6900;">
                            <div style="font-weight:700; font-size:0.88rem; margin-bottom:0.5rem;">
                                <i class="fas fa-server" style="color:#ff6900;"></i> cPanel / Hospedagem
                            </div>
                            <div style="font-size:0.82rem; color:#718096; line-height:1.6;">
                                Host: <code>mail.seudominio.com.br</code><br>
                                Porta: <code>465</code> (SSL) ou <code>587</code> (TLS)<br>
                                Use o e-mail e senha criados no cPanel.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="background:#f8f9fa; border-radius:8px; padding:1rem; border-left:3px solid #28a745;">
                            <div style="font-weight:700; font-size:0.88rem; margin-bottom:0.5rem;">
                                <i class="fas fa-code" style="color:#28a745;"></i> Desenvolvimento Local
                            </div>
                            <div style="font-size:0.82rem; color:#718096; line-height:1.6;">
                                Use o driver <strong>Log</strong> para não enviar e-mails reais.<br>
                                Os e-mails ficam em <code>storage/logs/laravel.log</code>.<br>
                                Ou use <a href="https://mailtrap.io" target="_blank" style="color:var(--hm-primary);">Mailtrap.io</a> para testes.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
// ── Mostrar/ocultar senha ─────────────────────────────────
document.getElementById('togglePassword').addEventListener('click', function() {
    const input = document.getElementById('mail_password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
});

// ── Auto-preencher porta ao mudar criptografia ────────────
document.getElementById('mail_encryption').addEventListener('change', function() {
    const portMap = { tls: '587', ssl: '465', '': '25' };
    document.getElementById('mail_port').value = portMap[this.value] || '587';
});

// ── Testar SMTP ───────────────────────────────────────────
document.getElementById('btnTestSmtp').addEventListener('click', function() {
    const btn       = this;
    const testEmail = document.getElementById('test_email').value.trim();
    const resultDiv = document.getElementById('testResult');
    const resultContent = document.getElementById('testResultContent');

    if (!testEmail) {
        toast('Informe um e-mail de destino para o teste.', 'warning');
        document.getElementById('test_email').focus();
        return;
    }

    // Loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    resultDiv.style.display = 'none';

    $.ajax({
        url: '{{ route("admin.settings.email.test") }}',
        method: 'POST',
        data: {
            _token:             '{{ csrf_token() }}',
            test_email:         testEmail,
            mail_host:          document.getElementById('mail_host').value,
            mail_port:          document.getElementById('mail_port').value,
            mail_username:      document.getElementById('mail_username').value,
            mail_password:      document.getElementById('mail_password').value || '',
            mail_encryption:    document.getElementById('mail_encryption').value,
            mail_verify_peer:   document.getElementById('mail_verify_peer').checked ? '1' : '0',
            mail_from_address:  document.getElementById('mail_from_address').value,
            mail_from_name:     document.getElementById('mail_from_name').value,
        },
        success: function(res) {
            toast(res.message, 'success');
            resultContent.innerHTML =
                '<i class="fas fa-check-circle text-success me-2"></i>' +
                '<strong>Sucesso!</strong> ' + res.message;
            resultContent.style.background = '#d4edda';
            resultContent.style.color = '#155724';
            resultDiv.style.display = 'block';
        },
        error: function(xhr) {
            const data = xhr.responseJSON || {};
            const msg = data.message || 'Erro ao enviar e-mail de teste.';
            toast(msg, 'error');

            let html = '<i class="fas fa-times-circle text-danger me-2"></i>' +
                '<strong>Falha!</strong> ' + msg;

            // Diagnostico
            if (data.diagnostic) {
                const d = data.diagnostic;
                html += '<div style="margin-top:10px;padding:10px;background:rgba(0,0,0,0.05);border-radius:6px;font-size:0.82rem;">';
                html += '<strong>Diagnóstico:</strong><br>';
                html += 'Servidor: ' + (d.host || 'N/A') + ':' + (d.port || 'N/A') + '<br>';
                html += 'Usuário: ' + (d.username || 'N/A') + '<br>';
                html += 'Criptografia: ' + (d.encryption || 'N/A') + '<br>';
                html += 'Senha: ' + (d.password_status === 'vazia' ? '<span style="color:#dc2626;">VAZIA</span>' : (d.password_len || 0) + ' caracteres') + '<br>';
                if (d.raw_password_empty !== undefined) {
                    html += 'Senha do campo: ' + (d.raw_password_empty ? '<span style="color:#dc2626;">vazia</span>' : 'preenchida') + ' | ';
                    html += 'Senha no banco: ' + (d.db_password_empty ? '<span style="color:#dc2626;">vazia</span>' : 'salva') + '<br>';
                }
                html += '</div>';
            }

            // Sugestoes
            if (data.suggestions && data.suggestions.length > 0) {
                html += '<div style="margin-top:8px;padding:10px;background:#fff8f5;border:1px solid #ffe0cc;border-radius:6px;font-size:0.82rem;">';
                html += '<strong><i class="fas fa-lightbulb me-1" style="color:#FF6B00;"></i>Sugestões:</strong><ul style="margin:4px 0 0 18px;padding:0;">';
                data.suggestions.forEach(function(s) { html += '<li>' + s + '</li>'; });
                html += '</ul></div>';
            }

            resultContent.innerHTML = html;
            resultContent.style.background = '#f8d7da';
            resultContent.style.color = '#721c24';
            resultDiv.style.display = 'block';
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar E-mail de Teste';
        }
    });
});
</script>
@endsection
