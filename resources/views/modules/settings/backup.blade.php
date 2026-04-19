@extends('layouts.admin')
@section('title', 'Backup e Manutenção')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Configurações</a></li>
    <li class="breadcrumb-item active">Backup</li>
@endsection

@section('styles')
<style>
/* Estilos IPs */
.ip-tag {
    display: inline-flex;
    align-items: center;
    background-color: var(--hm-primary);
    color: white;
    padding: 0.25rem 0.6rem;
    border-radius: 50px;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.ip-tag .remove-ip {
    margin-left: 6px;
    cursor: pointer;
    font-weight: bold;
    color: rgba(255,255,255,0.7);
    transition: color 0.2s;
}
.ip-tag .remove-ip:hover { color: white; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-tools mr-2" style="color:var(--hm-primary);"></i>Backup e Manutenção</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'backup'])

    <div class="col-md-9">

        <!-- Modo de Manutenção -->
        <div class="card" style="border: 1px solid rgba(255,107,0,0.1);">
            <div class="card-header" style="background: rgba(255,107,0,0.02);">
                <span class="card-title font-weight-bold" style="color: #ff6b00;"><i class="fas fa-hard-hat"></i> Parâmetros de Manutenção</span>
            </div>
            <div class="card-body py-4">
                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="section" value="maintenance">
                    <div class="row">
                        <div class="col-md-12 mb-4 border-bottom pb-3">
                            <div class="custom-control custom-switch custom-switch-lg">
                                <input type="checkbox" class="custom-control-input" id="maintenance_mode"
                                       name="maintenance_mode" value="1"
                                       {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" style="font-size: 1.1rem; padding-top: 2px;" for="maintenance_mode">Ativar Modo de Manutenção</label>
                            </div>
                            <small class="form-text mt-2" style="font-size: 0.95rem; color: #6c757d;">
                                Quando ativado, o site exibe uma página de manutenção isolada. Administradores e IPs autorizados continuam com acesso normal.
                            </small>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Título da Página de Manutenção</label>
                                <input type="text" class="form-control" name="maintenance_title"
                                       value="{{ old('maintenance_title', $settings['maintenance_title'] ?? 'Site em Manutenção') }}"
                                       placeholder="Ex: Site em Manutenção">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Mensagem para Visitantes</label>
                                <input type="text" class="form-control" name="maintenance_message"
                                       value="{{ old('maintenance_message', $settings['maintenance_message'] ?? 'Voltaremos em breve. Estamos realizando atualizações.') }}"
                                       placeholder="Ex: Voltaremos em breve.">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label>Temporizador de Retorno (Opcional)</label>
                                <input type="datetime-local" class="form-control" name="maintenance_timer"
                                       value="{{ old('maintenance_timer', $settings['maintenance_timer'] ?? '') }}">
                                <small class="form-text text-muted mt-1">Defina quando a página voltará para exibir um <strong>Cronômetro</strong>.</small>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold">Imagem de Fundo (Manutenção)</label>
                                <x-filepond name="maintenance_bg_image" :value="!empty($settings['maintenance_bg_image']) ? asset('storage/' . $settings['maintenance_bg_image']) : null" />
                                <small class="text-muted">Recomendado: 1920x1080px. Arraste e solte para enviar.</small>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group mb-4">
                                <label>IPs com Acesso Liberado</label>
                                
                                <div class="ip-manager-container p-3" style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:8px;">
                                    <input type="hidden" name="maintenance_ips" id="maintenance_ips" value="{{ old('maintenance_ips', $settings['maintenance_ips'] ?? '') }}">
                                    
                                    <div id="ip-tags-list" class="mb-3 d-flex flex-wrap">
                                        <!-- IP Badges serão renderizadas via JS -->
                                    </div>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-network-wired text-orange"></i></span>
                                        </div>
                                        <input type="text" id="ip_input" class="form-control bg-white" placeholder="Digite IPv4 (ex: 192.168.0.1) ou faixa CIDR (ex: 10.0.0.0/24)">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btnAddIp"><i class="fas fa-plus"></i> Inserir</button>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 d-flex align-items-center justify-content-between">
                                        <button type="button" class="btn btn-sm" id="btnMyIp" style="background:rgba(255,107,0,0.1); color:#ff6b00; border:1px solid rgba(255,107,0,0.3);">
                                            <i class="fas fa-wifi me-1"></i> Autorizar meu IP Atual
                                        </button>
                                        <small class="text-muted">Seu IP detectado: <strong id="my_current_ip">{{ request()->ip() }}</strong></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Salvar Manutenção</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 🛡️ Sistema de Backup Nativo -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <span class="card-title font-weight-bold" style="color:var(--hm-primary);"><i class="fas fa-shield-alt me-2"></i>Backup do Sistema</span>
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <h5 class="font-weight-bold mb-2" style="font-size:1rem;">Gerar Novo Backup</h5>
                            <p class="text-muted small mb-3">Recomendamos realizar backups periódicos do banco de dados e dos arquivos de upload.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <button onclick="runBackup('all')" class="btn btn-primary btn-sm px-3">
                                    <i class="fas fa-archive me-1"></i> Backup Completo (BD + Arquivos)
                                </button>
                                <button onclick="runBackup('db')" class="btn btn-outline-primary btn-sm px-3">
                                    <i class="fas fa-database me-1"></i> Apenas Banco de Dados
                                </button>
                                <button onclick="runBackup('files')" class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="fas fa-images me-1"></i> Apenas Arquivos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border" id="backupsTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Arquivo</th>
                                <th>Tamanho</th>
                                <th>Data de Criação</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="backupsList">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Carregando backups...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Limpeza de Cache -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-broom"></i> Limpeza de Cache</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Limpe os caches do sistema para forçar a atualização de configurações e views.</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button onclick="clearCacheType('all')"    class="btn btn-primary"><i class="fas fa-broom"></i> Limpar Tudo</button>
                    <button onclick="clearCacheType('config')" class="btn btn-secondary"><i class="fas fa-cog"></i> Config</button>
                    <button onclick="clearCacheType('view')"   class="btn btn-secondary"><i class="fas fa-eye"></i> Views</button>
                    <button onclick="clearCacheType('route')"  class="btn btn-secondary"><i class="fas fa-route"></i> Rotas</button>
                    <button onclick="clearCacheType('app')"    class="btn btn-secondary"><i class="fas fa-database"></i> App Cache</button>
                </div>
                <div id="cacheResult" style="display:none;"></div>
            </div>
        </div>

        <!-- Banco de Dados -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-database"></i> Banco de Dados</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Execute as migrations pendentes para atualizar a estrutura do banco de dados.</p>
                <button type="button" class="btn btn-primary" id="btnMigrate" onclick="runMigrations()">
                    <i class="fas fa-play-circle"></i> Rodar Migrations Pendentes
                </button>
                <div id="migrateResult" class="mt-3" style="display:none;"></div>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle"></i> Informações do Sistema</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" style="font-size:0.87rem;">
                    <tr><td class="font-weight-bold" width="35%">Versão PHP</td><td>{{ PHP_VERSION }}</td></tr>
                    <tr><td class="font-weight-bold">Versão Laravel</td><td>{{ app()->version() }}</td></tr>
                    <tr>
                        <td class="font-weight-bold">Ambiente</td>
                        <td>
                            @if(app()->environment('production'))
                                <span class="badge badge-success">Produção</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst(app()->environment()) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Debug</td>
                        <td>
                            @if(config('app.debug'))
                                <span class="badge badge-warning">Ativado</span>
                            @else
                                <span class="badge badge-success">Desativado</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="font-weight-bold">Fuso Horário</td><td>{{ config('app.timezone') }}</td></tr>
                    <tr><td class="font-weight-bold">Data/Hora do Servidor</td><td>{{ now()->format('d/m/Y H:i:s') }}</td></tr>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    // Inicializar Tags de IPs e Backups
    renderIpTags();
    loadBackups();

    // ── Event Listeners para IPs ────────────────────────
    $('#btnAddIp').on('click', function() {
        addIpFromInput();
    });

    $('#ip_input').on('keypress', function(e) {
        if (e.which === 13) { e.preventDefault(); addIpFromInput(); }
    });

    $('#btnMyIp').on('click', function() {
        var myIp = $('#my_current_ip').text().trim();
        if (myIp && myIp !== '127.0.0.1') {
            addIp(myIp);
            HMToast.success('Seu IP (' + myIp + ') foi adicionado!');
        } else {
            HMToast.warning('Não foi possível detectar seu IP público.');
        }
    });
});

// ── IP Manager ──────────────────────────────────────────
function getIpList() {
    var val = $('#maintenance_ips').val();
    if (!val || !val.trim()) return [];
    return val.split(',').map(function(ip) { return ip.trim(); }).filter(function(ip) { return ip.length > 0; });
}

function setIpList(ips) {
    $('#maintenance_ips').val(ips.join(','));
    renderIpTags();
}

function addIp(ip) {
    ip = ip.trim();
    if (!ip) return;

    // Validação simples de IPv4 ou CIDR
    var ipRegex = /^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/;
    if (!ipRegex.test(ip)) {
        HMToast.error('IP inválido. Use formato IPv4 (ex: 192.168.0.1) ou CIDR (ex: 10.0.0.0/24).');
        return;
    }

    var list = getIpList();
    if (list.indexOf(ip) !== -1) {
        HMToast.warning('Este IP já está na lista.');
        return;
    }

    list.push(ip);
    setIpList(list);
}

function removeIp(ip) {
    var list = getIpList().filter(function(i) { return i !== ip; });
    setIpList(list);
}

function addIpFromInput() {
    var input = $('#ip_input');
    var ip = input.val().trim();
    if (ip) {
        addIp(ip);
        input.val('');
        input.focus();
    }
}

function renderIpTags() {
    var container = $('#ip-tags-list');
    container.empty();

    var ips = getIpList();
    if (ips.length === 0) {
        container.html('<span class="text-muted" style="font-size:0.85rem;">Nenhum IP autorizado.</span>');
        return;
    }

    ips.forEach(function(ip) {
        var tag = $('<span class="ip-tag"></span>');
        tag.text(ip);
        var removeBtn = $('<span class="remove-ip" title="Remover">&times;</span>');
        removeBtn.on('click', function() { removeIp(ip); });
        tag.append(removeBtn);
        container.append(tag);
    });
}

// ── Migrations ──────────────────────────────────────────
function runMigrations() {
    Swal.fire({
        title: 'Rodar Migrations?',
        html: '<div style="font-size:0.88rem;color:#64748b;">Isso executará todas as migrations pendentes no banco de dados.<br><strong>Recomendado após atualizar o sistema.</strong></div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-play-circle me-1"></i> Executar',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (!r.isConfirmed) return;

        var btn       = document.getElementById('btnMigrate');
        var resultDiv = document.getElementById('migrateResult');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Executando...';
        resultDiv.style.display = 'none';

        $.ajax({
            url: '{{ route("admin.system.migrate") }}',
            method: 'POST',
            success: function(data) {
                var output = data.output ? '<pre style="font-size:0.78rem;margin-top:0.5rem;background:#f8fafc;padding:0.75rem;border-radius:6px;max-height:200px;overflow-y:auto;">' + data.output + '</pre>' : '';
                resultDiv.innerHTML = data.success
                    ? '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><strong>' + data.message + '</strong>' + output + '</div>'
                    : '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' + data.message + '</div>';
                resultDiv.style.display = 'block';
                if (data.success) HMToast.success(data.message);
                else HMToast.error(data.message);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao executar migrations.';
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' + msg + '</div>';
                resultDiv.style.display = 'block';
                HMToast.error(msg);
            },
            complete: function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-play-circle me-1"></i> Rodar Migrations Pendentes';
            }
        });
    });
}

// ── Cache ────────────────────────────────────────────────
window.clearCacheType = function(type) {
    var labels = { all:'Limpar TODOS os caches', config:'Configuração', view:'Views', route:'Rotas', app:'App Cache' };
    Swal.fire({
        title: labels[type] || 'Limpar cache?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-broom me-1"></i> Limpar',
        cancelButtonText: 'Cancelar',
    }).then(function(r) {
        if (!r.isConfirmed) return;

        var resultDiv = document.getElementById('cacheResult');
        resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Limpando cache...</div>';
        resultDiv.style.display = 'block';

        $.ajax({
            url: '{{ route("admin.system.clear-cache") }}',
            method: 'POST',
            data: JSON.stringify({ type: type }),
            contentType: 'application/json',
            success: function(data) {
                var details = (data.details || []).join('<br>');
                resultDiv.innerHTML = data.success
                    ? '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><strong>Concluído!</strong><br>' + details + '</div>'
                    : '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>' + data.message + '<br>' + details + '</div>';
                if (data.success) HMToast.success(data.message);
                else HMToast.warning(data.message);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao limpar cache.';
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' + msg + '</div>';
                HMToast.error(msg);
            }
        });
    });
};

// ── Backup Manager ───────────────────────────────────────
function loadBackups() {
    var list = document.getElementById('backupsList');
    $.ajax({
        url: '{{ route("admin.settings.backup.list") }}',
        method: 'GET',
        success: function(res) {
            if (!res.success || !res.data.length) {
                list.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Nenhum backup encontrado.</td></tr>';
                return;
            }
            list.innerHTML = res.data.map(function(b) {
                return '<tr>' +
                    '<td><i class="fas fa-file-archive me-2 text-primary"></i> <strong>' + b.name + '</strong></td>' +
                    '<td><span class="badge bg-light text-dark border">' + b.size + '</span></td>' +
                    '<td>' + b.date + '</td>' +
                    '<td class="text-end"><div class="btn-group">' +
                        '<a href="' + b.url + '" class="btn btn-sm btn-outline-success" title="Download"><i class="fas fa-download"></i></a>' +
                        '<button onclick="deleteBackup(\'' + b.name + '\')" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="fas fa-trash"></i></button>' +
                    '</div></td></tr>';
            }).join('');
        },
        error: function() {
            list.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">Erro ao carregar lista de backups.</td></tr>';
        }
    });
}

function runBackup(type) {
    var labels = { all: 'Completo', db: 'Banco de Dados', files: 'Arquivos' };
    Swal.fire({
        title: 'Gerar Backup ' + (labels[type] || '') + '?',
        html: '<div style="font-size:0.88rem;color:#64748b;">Dependendo do tamanho do seu site, isso pode levar alguns segundos.</div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FF6B00',
        confirmButtonText: '<i class="fas fa-play me-1"></i> Iniciar',
        cancelButtonText: 'Cancelar'
    }).then(function(r) {
        if (!r.isConfirmed) return;

        Swal.fire({
            title: 'Gerando Backup...',
            html: 'Aguarde um momento, estamos compactando os dados.',
            allowOutsideClick: false,
            didOpen: function() { Swal.showLoading(); }
        });

        $.ajax({
            url: '{{ route("admin.settings.backup.run") }}',
            method: 'POST',
            data: JSON.stringify({ type: type }),
            contentType: 'application/json',
            success: function(res) {
                Swal.close();
                if (res.success) {
                    Swal.fire({ title: '✅ Pronto!', text: res.message, icon: 'success', confirmButtonColor: '#FF6B00' });
                    loadBackups();
                } else {
                    Swal.fire({ title: '❌ Erro', text: res.message, icon: 'error', confirmButtonColor: '#FF6B00' });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({ title: '❌ Erro Fatal', text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro inesperado.', icon: 'error', confirmButtonColor: '#FF6B00' });
            }
        });
    });
}

function deleteBackup(file) {
    Swal.fire({
        title: 'Excluir Backup?',
        text: 'Arquivo: ' + file,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then(function(r) {
        if (!r.isConfirmed) return;

        $.ajax({
            url: '{{ route("admin.settings.backup.delete") }}',
            method: 'DELETE',
            data: JSON.stringify({ file: file }),
            contentType: 'application/json',
            success: function(res) {
                if (res.success) {
                    HMToast.success(res.message);
                    loadBackups();
                }
            }
        });
    });
}
</script>
@endsection

