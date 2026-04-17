/**
 * HomeMechanic Admin — JS Global v2
 */

/* ── Toastify ─────────────────────────────────────────────── */
const HMToast = {
    _cfg: {
        success: { bg: 'linear-gradient(135deg,#16a34a,#15803d)', icon: 'fas fa-check-circle', title: 'Sucesso' },
        error:   { bg: 'linear-gradient(135deg,#dc2626,#b91c1c)', icon: 'fas fa-times-circle',  title: 'Erro' },
        warning: { bg: 'linear-gradient(135deg,#d97706,#b45309)', icon: 'fas fa-exclamation-triangle', title: 'Atenção' },
        info:    { bg: 'linear-gradient(135deg,#0891b2,#0e7490)', icon: 'fas fa-info-circle',   title: 'Informação' },
    },

    show(message, type = 'success', duration = 4000) {
        const cfg = this._cfg[type] || this._cfg.info;
        const id  = 'toast-' + Date.now();

        const html = `
            <div class="hm-toast" id="${id}">
                <div class="hm-toast-icon" style="background:rgba(255,255,255,0.15);">
                    <i class="${cfg.icon}" style="color:#fff;"></i>
                </div>
                <div class="hm-toast-body">
                    <div class="hm-toast-title" style="color:#fff;">${cfg.title}</div>
                    <div class="hm-toast-msg"   style="color:rgba(255,255,255,0.9);">${message}</div>
                </div>
                <div class="hm-toast-close" style="color:#fff;" onclick="this.closest('.toastify').remove()">
                    <i class="fas fa-times"></i>
                </div>
                <div class="hm-toast-progress" id="${id}-bar" style="background:rgba(255,255,255,0.4); width:100%;"></div>
            </div>`;

        const t = Toastify({
            node: (() => { const d = document.createElement('div'); d.innerHTML = html; return d.firstElementChild; })(),
            duration,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            style: { background: cfg.bg, padding: '0', borderRadius: '10px' },
            callback: () => {},
        });
        t.showToast();

        // Barra de progresso
        const bar = document.getElementById(id + '-bar');
        if (bar) {
            bar.style.transition = `width ${duration}ms linear`;
            requestAnimationFrame(() => { bar.style.width = '0%'; });
        }
        return t;
    },

    success(msg, dur) { return this.show(msg, 'success', dur); },
    error(msg, dur)   { return this.show(msg, 'error',   dur || 6000); },
    warning(msg, dur) { return this.show(msg, 'warning', dur); },
    info(msg, dur)    { return this.show(msg, 'info',    dur); },
};

// Alias global
window.toast = (msg, type, dur) => HMToast.show(msg, type, dur);

/* ── jQuery ready ─────────────────────────────────────────── */
$(document).ready(function () {

    // Tooltips Bootstrap 5
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

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
            customClass: { popup: 'swal-hm' },
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
            url:    form.attr('action'),
            method: form.attr('method') || 'POST',
            data:   form.serialize(),
            success(res) {
                if (res.success) {
                    HMToast.success(res.message || 'Operação realizada com sucesso!');
                    if (res.redirect) setTimeout(() => window.location.href = res.redirect, 1200);
                    else if (res.reload) setTimeout(() => location.reload(), 1200);
                } else {
                    HMToast.error(res.message || 'Ocorreu um erro.');
                }
            },
            error(xhr) {
                let msg = 'Ocorreu um erro ao processar a solicitação.';
                if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                HMToast.error(msg);
            },
            complete() { btn.prop('disabled', false).html(orig); },
        });
    });

    // ── Toggle switches ────────────────────────────────────
    $(document).on('change', '.toggle-switch', function () {
        const cb    = $(this);
        const url   = cb.data('url');
        const field = cb.data('field');
        if (!url) return;
        $.post(url, { [field]: cb.is(':checked') ? 1 : 0 })
            .done(res => {
                if (res.success) HMToast.success(res.message || 'Atualizado!');
                else { cb.prop('checked', !cb.is(':checked')); HMToast.error(res.message || 'Erro ao atualizar.'); }
            })
            .fail(() => { cb.prop('checked', !cb.is(':checked')); HMToast.error('Erro de conexão.'); });
    });

    // ── Copy to clipboard ──────────────────────────────────
    $(document).on('click', '.copy-to-clipboard', function () {
        const text = $(this).data('text') || $(this).text().trim();
        navigator.clipboard?.writeText(text)
            .then(() => HMToast.success('Copiado para a área de transferência!'))
            .catch(() => HMToast.error('Não foi possível copiar.'));
    });

    // ── Auto-dismiss alerts HTML (fallback) ────────────────
    setTimeout(() => {
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 6000);

    // ── Char counter para textareas com maxlength ──────────
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
});

/* ── Helpers globais ──────────────────────────────────────── */

/** Confirmar ação genérica */
function confirmAction(message, callback, opts = {}) {
    Swal.fire({
        title: opts.title || 'Confirmar',
        text: message,
        icon: opts.icon || 'question',
        showCancelButton: true,
        confirmButtonColor: opts.confirmColor || 'var(--hm-primary)',
        cancelButtonColor: '#64748b',
        confirmButtonText: opts.confirmText || 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(r => { if (r.isConfirmed && typeof callback === 'function') callback(); });
}

/** Loading button */
function btnLoading(btn, text = 'Processando...') {
    const $b = $(btn);
    $b.data('orig', $b.html()).prop('disabled', true)
      .html(`<i class="fas fa-spinner fa-spin me-1"></i> ${text}`);
}
function btnReset(btn) {
    const $b = $(btn);
    $b.prop('disabled', false).html($b.data('orig') || $b.html());
}
