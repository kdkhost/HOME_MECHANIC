
<?php $__env->startSection('title', 'Analytics'); ?>
<?php $__env->startSection('page-title', 'Analytics'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Analytics</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .analytics-card { border-radius:12px; color:#fff; padding:1.25rem; position:relative; overflow:hidden; }
    .analytics-card .card-value { font-size:1.8rem; font-weight:700; line-height:1.2; }
    .analytics-card .card-label { font-size:0.82rem; opacity:0.85; margin-top:2px; }
    .analytics-card .card-icon { font-size:2.5rem; opacity:0.2; position:absolute; right:15px; top:50%; transform:translateY(-50%); }
    .analytics-card .card-sub { font-size:0.72rem; opacity:0.75; margin-top:6px; }
    .period-btn.active { background:var(--hm-primary)!important; color:#fff!important; border-color:var(--hm-primary)!important; }
    .live-dot { width:8px; height:8px; background:#28a745; border-radius:50%; display:inline-block; animation:pulse-dot 1.5s infinite; }
    @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.3} }
    .visitors-table td { font-size:0.82rem; padding:0.4rem 0.6rem; vertical-align:middle; }
    .visitors-table th { font-size:0.78rem; padding:0.4rem 0.6rem; background:#f8f9fa; }
    /* Limitar altura dos graficos para evitar crescimento infinito */
    .chart-container { position:relative; height:300px; max-height:300px; overflow:hidden; }
    .chart-container-sm { position:relative; height:250px; max-height:250px; overflow:hidden; }
    #devicesChart { max-height:250px !important; }

    /* Animações de atualização em tempo real */
    .refreshing { animation: pulse-refresh 0.5s ease; }
    @keyframes pulse-refresh {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .value-changed {
        animation: highlight-change 1s ease;
        color: #28a745 !important;
    }
    @keyframes highlight-change {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); color: #28a745; }
        100% { transform: scale(1); }
    }
    .pulse-highlight {
        animation: card-pulse 1.5s ease;
    }
    @keyframes card-pulse {
        0%, 100% { box-shadow: none; }
        50% { box-shadow: 0 0 30px rgba(40, 167, 69, 0.5); }
    }
    #lastUpdate {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    #lastUpdate:hover {
        color: var(--hm-primary) !important;
    }
    #lastUpdate::after {
        content: ' ↻';
        font-size: 0.9em;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-chart-line mr-2" style="color:var(--hm-primary);"></i>Analytics <small class="text-muted" style="font-size:0.6em;"><span class="live-dot"></span> Tempo Real</small></h2>
    <div class="page-header-actions d-flex gap-2">
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary period-btn active" data-period="7">7 dias</button>
            <button class="btn btn-outline-secondary period-btn" data-period="30">30 dias</button>
            <button class="btn btn-outline-secondary period-btn" data-period="90">90 dias</button>
        </div>
        <small class="text-muted align-self-center ms-2" id="lastUpdate"></small>
    </div>
</div>

<!-- Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="analytics-card" style="background:linear-gradient(135deg,#FF6B00,#E55A00);">
            <div class="card-value" id="statTotal"><?php echo e(number_format($stats['total_visits'])); ?></div>
            <div class="card-label">Visitas no Periodo</div>
            <div class="card-sub">Hoje: <span id="statToday">0</span></div>
            <i class="fas fa-eye card-icon"></i>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="analytics-card" style="background:linear-gradient(135deg,#28a745,#20c997);">
            <div class="card-value" id="statUnique"><?php echo e(number_format($stats['unique_visits'])); ?></div>
            <div class="card-label">Visitas Unicas</div>
            <div class="card-sub">Hoje: <span id="statTodayUnique">0</span></div>
            <i class="fas fa-user card-icon"></i>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="analytics-card" style="background:linear-gradient(135deg,#17a2b8,#6f42c1);">
            <div class="card-value"><span class="live-dot me-1"></span> <span id="statOnline"><?php echo e($stats['online_now']); ?></span></div>
            <div class="card-label">Online Agora</div>
            <div class="card-sub">Ultimos 5 min</div>
            <i class="fas fa-wifi card-icon"></i>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="analytics-card" style="background:linear-gradient(135deg,#ffc107,#fd7e14);">
            <div class="card-value"><span id="statAvg"><?php echo e(number_format($stats['avg_time'])); ?></span>s</div>
            <div class="card-label">Tempo Medio</div>
            <div class="card-sub">Por visita</div>
            <i class="fas fa-clock card-icon"></i>
        </div>
    </div>
</div>

<!-- Graficos Principais -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card h-100">
            <div class="card-header"><span class="card-title"><i class="fas fa-chart-area"></i> Visitas</span></div>
            <div class="card-body chart-container"><canvas id="visitsChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-header"><span class="card-title"><i class="fas fa-mobile-alt"></i> Dispositivos</span></div>
            <div class="card-body chart-container-sm d-flex align-items-center justify-content-center"><canvas id="devicesChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Graficos Secundarios -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><span class="card-title"><i class="fas fa-globe"></i> Navegadores</span></div>
            <div class="card-body chart-container-sm"><canvas id="browsersChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><span class="card-title"><i class="fas fa-flag"></i> Paises</span></div>
            <div class="card-body chart-container-sm"><canvas id="countriesChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Tabelas -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><span class="card-title"><i class="fas fa-file-alt"></i> Paginas Mais Visitadas</span></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover visitors-table mb-0">
                    <thead><tr><th>Pagina</th><th class="text-end" style="width:80px;">Visitas</th></tr></thead>
                    <tbody id="pagesTable"><tr><td colspan="2" class="text-center text-muted py-3">Carregando...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><span class="card-title"><i class="fas fa-link"></i> Referenciadores</span></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover visitors-table mb-0">
                    <thead><tr><th>Origem</th><th class="text-end" style="width:80px;">Visitas</th></tr></thead>
                    <tbody id="referrersTable"><tr><td colspan="2" class="text-center text-muted py-3">Carregando...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Visitantes Recentes -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="card-title"><i class="fas fa-users"></i> Visitantes Recentes</span>
        <span class="text-muted" style="font-size:0.78rem;" id="visitorsInfo"></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover visitors-table mb-0">
                <thead><tr><th>IP</th><th>Pais</th><th>Dispositivo</th><th>Navegador</th><th>Quando</th></tr></thead>
                <tbody id="visitorsTable"><tr><td colspan="5" class="text-center text-muted py-3">Carregando...</td></tr></tbody>
            </table>
        </div>
    </div>
    <div class="card-footer py-2" id="visitorsPagination" style="display:none;"></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
let currentPeriod = 7;
let currentVisitorsPage = 1;
let visitsChart = null, devicesChart = null, browsersChart = null, countriesChart = null;
let dataTimer = null, visitorsTimer = null;

const DATA_URL = <?php echo json_encode(route('admin.analytics.data'), 15, 512) ?>;
const VISITORS_URL = <?php echo json_encode(route('admin.analytics.visitors'), 15, 512) ?>;

$(document).ready(function() {
    initCharts();
    loadData();
    loadVisitors();

    // Auto-refresh mais frequente para tempo real
    // Dados gerais: 30s | Visitantes online: 10s (tempo real)
    dataTimer = setInterval(loadData, 30000);
    visitorsTimer = setInterval(loadVisitors, 10000);

    // Seletor de periodo
    $('.period-btn').on('click', function() {
        $('.period-btn').removeClass('active');
        $(this).addClass('active');
        currentPeriod = $(this).data('period');
        loadData();
    });

    // Forcar atualizacao ao clicar no indicador de tempo real
    $('#lastUpdate').on('click', function() {
        loadData();
        loadVisitors();
        showRefreshIndicator();
    });
});

function showRefreshIndicator() {
    var badge = $('.live-dot').parent();
    badge.addClass('refreshing');
    setTimeout(() => badge.removeClass('refreshing'), 500);
}

// Animar mudanca de valor
function animateValue(id, newValue, duration = 600) {
    var el = $('#' + id);
    var oldValue = parseInt(el.text().replace(/\D/g, '')) || 0;
    if (oldValue === newValue) return;

    var start = performance.now();
    var animate = function(currentTime) {
        var elapsed = currentTime - start;
        var progress = Math.min(elapsed / duration, 1);
        var easeProgress = 1 - Math.pow(1 - progress, 3); // easeOutCubic
        var current = Math.floor(oldValue + (newValue - oldValue) * easeProgress);
        el.text(numFmt(current));
        if (progress < 1) requestAnimationFrame(animate);
    };
    requestAnimationFrame(animate);

    // Destacar mudanca visualmente
    if (newValue !== oldValue) {
        el.addClass('value-changed');
        setTimeout(() => el.removeClass('value-changed'), 1000);
    }
}

// Limpar timers ao sair da pagina
$(window).on('beforeunload', function() {
    if (dataTimer) clearInterval(dataTimer);
    if (visitorsTimer) clearInterval(visitorsTimer);
});

function initCharts() {
    var defaultFont = { family: "'Source Sans 3', sans-serif", size: 12 };
    Chart.defaults.font = defaultFont;

    // Destruir graficos existentes antes de criar novos
    destroyCharts();

    var ctxVisits = document.getElementById('visitsChart');
    if (ctxVisits) {
        visitsChart = new Chart(ctxVisits, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                animation: { duration: 400 }
            }
        });
    }

    var ctxDevices = document.getElementById('devicesChart');
    if (ctxDevices) {
        devicesChart = new Chart(ctxDevices, {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], backgroundColor: ['#FF6B00','#0D0D0D','#17a2b8','#ffc107','#6f42c1'] }] },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom', labels: { padding: 15 } } },
                animation: { duration: 400 }
            }
        });
    }

    var ctxBrowsers = document.getElementById('browsersChart');
    if (ctxBrowsers) {
        browsersChart = new Chart(ctxBrowsers, {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Acessos', data: [], backgroundColor: '#FF6B00', borderRadius: 6 }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                animation: { duration: 400 }
            }
        });
    }

    var ctxCountries = document.getElementById('countriesChart');
    if (ctxCountries) {
        countriesChart = new Chart(ctxCountries, {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Acessos', data: [], backgroundColor: '#17a2b8', borderRadius: 6 }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                animation: { duration: 400 }
            }
        });
    }
}

function destroyCharts() {
    if (visitsChart) { visitsChart.destroy(); visitsChart = null; }
    if (devicesChart) { devicesChart.destroy(); devicesChart = null; }
    if (browsersChart) { browsersChart.destroy(); browsersChart = null; }
    if (countriesChart) { countriesChart.destroy(); countriesChart = null; }
}

function loadData() {
    $.ajax({
        url: DATA_URL,
        data: { period: currentPeriod },
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
            if (!res.success) return;
            var d = res.data;

            // Atualizar cards com animacao
            animateValue('statTotal', d.stats.total_visits);
            animateValue('statUnique', d.stats.unique_visits);
            animateValue('statOnline', d.stats.online_now, 800); // mais lento para online
            animateValue('statAvg', d.stats.avg_time);
            animateValue('statToday', d.stats.today_visits);
            animateValue('statTodayUnique', d.stats.today_unique);

            // Destacar contador de online se mudou significativamente
            var oldOnline = parseInt($('#statOnline').data('last')) || 0;
            var newOnline = d.stats.online_now;
            if (Math.abs(newOnline - oldOnline) >= 2) {
                $('#statOnline').closest('.analytics-card').addClass('pulse-highlight');
                setTimeout(() => $('#statOnline').closest('.analytics-card').removeClass('pulse-highlight'), 1500);
            }
            $('#statOnline').data('last', newOnline);

            // Grafico de visitas
            if (visitsChart && d.visits && d.visits.labels) {
                visitsChart.data.labels = d.visits.labels;
                visitsChart.data.datasets = (d.visits.datasets || []).map(function(ds) {
                    return { label: ds.label, data: ds.data, borderColor: ds.borderColor, backgroundColor: ds.backgroundColor, tension: ds.tension || 0.4, fill: true, pointRadius: 3 };
                });
                visitsChart.update('none'); // 'none' = sem animacao para evitar flicker
            }

            // Grafico de dispositivos
            if (devicesChart && d.devices && d.devices.labels) {
                devicesChart.data.labels = d.devices.labels;
                if (d.devices.datasets && d.devices.datasets[0]) {
                    devicesChart.data.datasets[0].data = d.devices.datasets[0].data;
                    if (d.devices.datasets[0].backgroundColor) devicesChart.data.datasets[0].backgroundColor = d.devices.datasets[0].backgroundColor;
                }
                devicesChart.update('none');
            }

            // Grafico de navegadores
            if (browsersChart && d.browsers) {
                browsersChart.data.labels = d.browsers.labels || [];
                browsersChart.data.datasets[0].data = d.browsers.data || [];
                browsersChart.update('none');
            }

            // Grafico de paises
            if (countriesChart && d.countries) {
                countriesChart.data.labels = d.countries.labels || [];
                countriesChart.data.datasets[0].data = d.countries.data || [];
                countriesChart.update('none');
            }

            // Tabelas
            renderTable('#pagesTable', d.pages);
            renderTable('#referrersTable', d.referrers);

            $('#lastUpdate').text('Atualizado: ' + new Date().toLocaleTimeString('pt-BR'));
        }
    });
}

function loadVisitors(page) {
    if (page === undefined) page = currentVisitorsPage;
    currentVisitorsPage = page;
    $.ajax({
        url: VISITORS_URL,
        data: { page: page },
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
            if (!res.success || !res.data) return;
            var visitors = res.data;
            if (!visitors.length) {
                $('#visitorsTable').html('<tr><td colspan="5" class="text-center text-muted py-3">Nenhum visitante registrado</td></tr>');
                $('#visitorsPagination').hide();
                $('#visitorsInfo').text('');
                return;
            }
            var html = '';
            visitors.forEach(function(v) {
                var dt = new Date(v.created_at);
                var timeStr = dt.toLocaleString('pt-BR', { day:'2-digit', month:'2-digit', hour:'2-digit', minute:'2-digit' });
                var icon = v.device_type === 'mobile' ? 'fa-mobile-alt' : (v.device_type === 'tablet' ? 'fa-tablet-alt' : 'fa-desktop');
                html += '<tr><td><code style="font-size:0.75rem;">' + (v.ip_address || '-') + '</code></td>'
                    + '<td>' + (v.country || '-') + (v.city ? '/' + v.city : '') + '</td>'
                    + '<td><i class="fas ' + icon + ' me-1"></i>' + ucf(v.device_type || 'desktop') + '</td>'
                    + '<td>' + (v.browser || '-') + '</td>'
                    + '<td>' + timeStr + '</td></tr>';
            });
            $('#visitorsTable').html(html);

            // Paginação
            var p = res.pagination;
            if (p && p.last_page > 1) {
                $('#visitorsInfo').text(p.total + ' visitantes - Pág. ' + p.current_page + '/' + p.last_page);
                var pagHtml = '<nav><ul class="pagination pagination-sm mb-0 justify-content-center">';
                pagHtml += '<li class="page-item' + (p.current_page <= 1 ? ' disabled' : '') + '"><a class="page-link" href="#" onclick="loadVisitors(' + (p.current_page - 1) + ');return false;">&laquo;</a></li>';
                var startP = Math.max(1, p.current_page - 2);
                var endP = Math.min(p.last_page, p.current_page + 2);
                for (var i = startP; i <= endP; i++) {
                    pagHtml += '<li class="page-item' + (i === p.current_page ? ' active' : '') + '"><a class="page-link" href="#" onclick="loadVisitors(' + i + ');return false;">' + i + '</a></li>';
                }
                pagHtml += '<li class="page-item' + (p.current_page >= p.last_page ? ' disabled' : '') + '"><a class="page-link" href="#" onclick="loadVisitors(' + (p.current_page + 1) + ');return false;">&raquo;</a></li>';
                pagHtml += '</ul></nav>';
                $('#visitorsPagination').html(pagHtml).show();
            } else {
                $('#visitorsPagination').hide();
                if (p) $('#visitorsInfo').text(p.total + ' visitantes');
            }
        }
    });
}

function renderTable(sel, chartData) {
    if (!chartData || !chartData.labels || !chartData.labels.length) {
        $(sel).html('<tr><td colspan="2" class="text-center text-muted py-3">Sem dados</td></tr>');
        return;
    }
    var html = '';
    chartData.labels.forEach(function(label, i) {
        html += '<tr><td>' + label + '</td><td class="text-end fw-bold">' + numFmt(chartData.data[i] || 0) + '</td></tr>';
    });
    $(sel).html(html);
}

function numFmt(n) { return (n || 0).toLocaleString('pt-BR'); }
function ucf(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\analytics\index.blade.php ENDPATH**/ ?>