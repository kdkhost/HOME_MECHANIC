/**
 * HomeMechanic Admin — JS Global v2
 */

/* ── HMToast ──────────────────────────────────────────────── */
const HMToast = {
    _cfg: {
        success: { bg: 'linear-gradient(135deg,#16a34a,#15803d)', icon: 'fas fa-check-circle',       title: 'Sucesso'    },
        error:   { bg: 'linear-gradient(135deg,#dc2626,#b91c1c)', icon: 'fas fa-times-circle',        title: 'Erro'       },
        warning: { bg: 'linear-gradient(135deg,#d97706,#b45309)', icon: 'fas fa-exclamation-triangle',title: 'Atenção'    },
        info:    { bg: 'linear-gradient(135deg,#0891b2,#0e7490)', icon: 'fas fa-info-circle',         title: 'Informação' },
    },
    show(message, type = 'success', duration = 4000) {
        const cfg = this._cfg[type] || this._cfg.info;
        const id  = 'toast-' + Date.now();
        const html = `<div class="hm-toast" id="${id}">
            <div class="hm-toast-icon" style="background:rgba(255,255,255,0.15);">
                <i class="${cfg.icon}" style="color:#fff;"></i>
            </div>
            <div class="hm-toast-body">
                <div class="hm-toast-title" style="color:#fff;">${cfg.title}</div>
                <div class="hm-toast-msg" style="color:rgba(255,255,255,0.9);">${message}</div>
            </div>
            <div class="hm-toast-close" style="color:#fff;" onclick="this.closest('.toastify').remove()">
                <i class="fas fa-times"></i>
            </div>
            <div class="hm-toast-progress" id="${id}-bar" style="background:rgba(255,255,255,0.4);width:100%;"></div>
        </div>`;
        const t = Toastify({
            node: (() => { const d = document.createElement('div'); d.innerHTML = html; return d.firstElementChild; })(),
            duration, gravity: 'top', position: 'right', stopOnFocus: true,
            style: { background: cfg.bg, padding: '0', borderRadius: '10px' },
        });
        t.showToast();
        const bar = document.getElementById(id + '-bar');
        if (bar) { bar.style.transition = `width ${duration}ms linear`; requestAnimationFrame(() => { bar.style.width = '0%'; }); }
        return t;
    },
    success(msg, dur) { return this.show(msg, 'success', dur); },
    error(msg, dur)   { return this.show(msg, 'error',   dur || 6000); },
    warning(msg, dur) { return this.show(msg, 'warning', dur); },
    info(msg, dur)    { return this.show(msg, 'info',    dur); },
};
window.toast = (msg, type, dur) => HMToast.show(msg, type, dur);

/* ── jQuery ready ─────────────────────────────────────────── */
$(document).ready(function () {

    // ── Hambúrguer — usa o toggle nativo do AdminLTE 4 ────
    // O data-lte-toggle="sidebar" já é tratado pelo adminlte4.min.js
    // Apenas salvamos o estado para restaurar no reload
    document.addEventListener('lte:sidebar-collapse', () => localStorage.setItem('hm_sidebar', 'closed'));
    document.addEventListener('lte:sidebar-open',     () => localStorage.setItem('hm_sidebar', 'open'));

    // Restaurar estado
    (function () {
        if (localStorage.getItem('hm_sidebar') === 'closed') {
            document.body.classList.add('sidebar-collapse');
        }
    })();

    // ── Dark Mode ──────────────────────────────────────────
    const darkBtn  = document.getElementById('darkModeToggle');
    const darkIcon = document.getElementById('darkIcon');

    function applyDark(on) {
        document.body.classList.toggle('dark-mode', on);
        if (darkIcon) darkIcon.className = on ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        if (darkBtn)  darkBtn.title = on ? 'Modo claro' : 'Modo escuro';
    }
    applyDark(localStorage.getItem('hm_dark') === '1');

    if (darkBtn) {
        darkBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const isDark = document.body.classList.contains('dark-mode');
            applyDark(!isDark);
            localStorage.setItem('hm_dark', isDark ? '0' : '1');
            HMToast.info(isDark ? 'Modo claro ativado' : 'Modo escuro ativado', 2000);
        });
    }

    // ── Busca rápida ───────────────────────────────────────
    const searchPages = [
        { label: 'Dashboard',          url: '/admin/dashboard',              icon: 'fas fa-tachometer-alt' },
        { label: 'Serviços',            url: '/admin/services',               icon: 'fas fa-tools' },
        { label: 'Galeria',             url: '/admin/gallery',                icon: 'fas fa-images' },
        { label: 'Blog',                url: '/admin/blog',                   icon: 'fas fa-newspaper' },
        { label: 'Mensagens',           url: '/admin/contact',                icon: 'fas fa-envelope' },
        { label: 'Upload de Arquivos',  url: '/admin/upload',                 icon: 'fas fa-cloud-upload-alt' },
        { label: 'SEO',                 url: '/admin/seo',                    icon: 'fas fa-search' },
        { label: 'Analytics',           url: '/admin/analytics',              icon: 'fas fa-chart-line' },
        { label: 'Configurações',       url: '/admin/settings',               icon: 'fas fa-cog' },
        { label: 'E-mail (SMTP)',       url: '/admin/settings/email',         icon: 'fas fa-envelope' },
        { label: 'Templates de E-mail', url: '/admin/settings/email/templates', icon: 'fas fa-envelope-open-text' },
        { label: 'Usuários',            url: '/admin/users',                  icon: 'fas fa-users' },
        { label: 'Documentação',        url: '/admin/documentacao',           icon: 'fas fa-book' },
    ];

    const searchInput   = document.getElementById('navSearch');
    const searchResults = document.getElementById('searchResults');

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            if (!q) { searchResults.classList.remove('show'); return; }
            const matches = searchPages.filter(p => p.label.toLowerCase().includes(q));
            searchResults.innerHTML = matches.length
                ? matches.map(p => `<a href="${p.url}" class="search-result-item"><i class="${p.icon}"></i> ${p.label}</a>`).join('')
                : '<div class="search-no-result"><i class="fas fa-search me-1"></i> Nenhum resultado</div>';
            searchResults.classList.add('show');
        });
        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Escape') { searchResults.classList.remove('show'); searchInput.value = ''; }
            if (e.key === 'Enter' && searchResults.querySelector('.search-result-item')) {
                searchResults.querySelector('.search-result-item').click();
            }
        });
        document.addEventListener('click', e => {
            if (!e.target.closest('#searchWrap')) searchResults.classList.remove('show');
        });
    }

    // ── Notificações ───────────────────────────────────────
    const notifList  = document.getElementById('notifList');
    const notifBadge = document.getElementById('notifBadge');
    let   notifs     = JSON.parse(localStorage.getItem('hm_notifs') || '[]');

    function renderNotifs() {
        if (!notifList) return;
        if (!notifs.length) {
            notifList.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i><span>Nenhuma notificação</span></div>';
            if (notifBadge) notifBadge.style.display = 'none';
            return;
        }
        notifList.innerHTML = notifs.map((n, i) => `
            <div class="notif-item" onclick="removeNotif(${i})">
                <div class="notif-item-icon" style="background:${n.bg||'var(--hm-primary-light)'};">
                    <i class="${n.icon||'fas fa-bell'}" style="color:${n.color||'var(--hm-primary)'}"></i>
                </div>
                <div class="notif-item-body">
                    <div class="notif-item-title">${n.title}</div>
                    <div class="notif-item-text">${n.text}</div>
                    <div class="notif-item-time">${n.time}</div>
                </div>
            </div>`).join('');
        if (notifBadge) { notifBadge.textContent = notifs.length; notifBadge.style.display = 'flex'; }
    }

    window.removeNotif = function (i) {
        notifs.splice(i, 1);
        localStorage.setItem('hm_notifs', JSON.stringify(notifs));
        renderNotifs();
    };

    window.addNotif = function (title, text, icon = 'fas fa-info-circle', color = 'var(--hm-primary)', bg = 'var(--hm-primary-light)') {
        notifs.unshift({ title, text, icon, color, bg, time: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }) });
        if (notifs.length > 20) notifs.pop();
        localStorage.setItem('hm_notifs', JSON.stringify(notifs));
        renderNotifs();
        HMToast.info(title, 3000);
    };

    document.getElementById('clearNotifs')?.addEventListener('click', function () {
        notifs = []; localStorage.setItem('hm_notifs', JSON.stringify(notifs)); renderNotifs();
    });

    renderNotifs();

    // ── Tooltips ───────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

    // ── Confirm delete ─────────────────────────────────────
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const name = $(this).data('name') || 'este item';
        Swal.fire({
            title: 'Confirmar exclusão',
            html: `Deseja excluir <strong>${name}</strong>?<br><small style="color:#64748b;">Esta ação não pode ser desfeita.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
            cancelButtonText: 'Cancelar',
        }).then(r => { if (r.isConfirmed) form.submit(); });
    });

    // ── AJAX forms (.ajax-form) ────────────────────────────
    $(document).on('submit', '.ajax-form', function (e) {
        e.preventDefault();
        const form = $(this);
        const btn  = form.find('[type="submit"]');
        const orig = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processando...');
        $.ajax({
            url: form.attr('action'), method: form.attr('method') || 'POST', data: form.serialize(),
            success(res) {
                if (res.success) {
                    HMToast.success(res.message || 'Operação realizada com sucesso!');
                    if (res.redirect) setTimeout(() => window.location.href = res.redirect, 1200);
                    else if (res.reload) setTimeout(() => location.reload(), 1200);
                } else { HMToast.error(res.message || 'Ocorreu um erro.'); }
            },
            error(xhr) {
                let msg = 'Ocorreu um erro ao processar a solicitação.';
                if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                else if (xhr.status === 422 && xhr.responseJSON?.errors)
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                HMToast.error(msg);
            },
            complete() { btn.prop('disabled', false).html(orig); },
        });
    });

    // ── Toggle switches ────────────────────────────────────
    $(document).on('change', '.toggle-switch', function () {
        const cb = $(this), url = cb.data('url'), field = cb.data('field');
        if (!url) return;
        $.post(url, { [field]: cb.is(':checked') ? 1 : 0 })
            .done(res => {
                if (res.success) HMToast.success(res.message || 'Atualizado!');
                else { cb.prop('checked', !cb.is(':checked')); HMToast.error(res.message || 'Erro.'); }
            })
            .fail(() => { cb.prop('checked', !cb.is(':checked')); HMToast.error('Erro de conexão.'); });
    });

    // ── Copy to clipboard ──────────────────────────────────
    $(document).on('click', '.copy-to-clipboard', function () {
        const text = $(this).data('text') || $(this).text().trim();
        navigator.clipboard?.writeText(text)
            .then(() => HMToast.success('Copiado!'))
            .catch(() => HMToast.error('Não foi possível copiar.'));
    });

    // ── Auto-dismiss alerts HTML ───────────────────────────
    setTimeout(() => {
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 6000);

    // ── Char counter ───────────────────────────────────────
    document.querySelectorAll('textarea[maxlength]').forEach(ta => {
        const max = parseInt(ta.getAttribute('maxlength'));
        const counter = document.createElement('small');
        counter.className = 'form-text';
        ta.after(counter);
        const update = () => {
            const rem = max - ta.value.length;
            counter.textContent = `${ta.value.length}/${max} caracteres`;
            counter.style.color = rem < 20 ? '#dc2626' : rem < 50 ? '#d97706' : '#64748b';
        };
        ta.addEventListener('input', update);
        update();
    });

    // ── Image preview ──────────────────────────────────────
    document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
        input.addEventListener('change', function () {
            const preview = document.querySelector(this.dataset.preview);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

}); // end ready

/* ── Helpers globais ──────────────────────────────────────── */
function confirmAction(message, callback, opts = {}) {
    Swal.fire({
        title: opts.title || 'Confirmar',
        text: message, icon: opts.icon || 'question',
        showCancelButton: true,
        confirmButtonColor: opts.confirmColor || 'var(--hm-primary)',
        cancelButtonColor: '#64748b',
        confirmButtonText: opts.confirmText || 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(r => { if (r.isConfirmed && typeof callback === 'function') callback(); });
}
function btnLoading(btn, text = 'Processando...') {
    const $b = $(btn);
    $b.data('orig', $b.html()).prop('disabled', true).html(`<i class="fas fa-spinner fa-spin me-1"></i> ${text}`);
}
function btnReset(btn) {
    const $b = $(btn);
    $b.prop('disabled', false).html($b.data('orig') || $b.html());
}
