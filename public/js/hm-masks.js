/**
 * HomeMechanic — Módulo Global de Máscaras v1.0
 *
 * Uso:  <input data-mask="phone">
 *       <input data-mask="cpf">
 *       <input data-mask="cnpj">
 *       <input data-mask="cpf-cnpj">
 *       <input data-mask="cep">
 *       <input data-mask="whatsapp">
 *       <input data-mask="date">
 *       <input data-mask="time">
 *
 * JS:   HMMask.format('11999998888', 'phone')  → '(11) 99999-8888'
 *       HMMask.unmask('(11) 99999-8888')        → '11999998888'
 */
(function () {
    'use strict';

    /* ── Helpers ──────────────────────────────────────────── */
    function digits(v) { return (v || '').replace(/\D/g, ''); }

    /* ── Formatadores ────────────────────────────────────── */
    const formatters = {
        phone: function (v) {
            v = digits(v).slice(0, 11);
            if (v.length <= 10) {
                return v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            }
            return v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        },
        whatsapp: function (v) {
            v = digits(v).slice(0, 11);
            if (v.length <= 10) {
                return v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            }
            return v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        },
        cpf: function (v) {
            v = digits(v).slice(0, 11);
            return v
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        },
        cnpj: function (v) {
            v = digits(v).slice(0, 14);
            return v
                .replace(/(\d{2})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1/$2')
                .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
        },
        'cpf-cnpj': function (v) {
            v = digits(v);
            if (v.length <= 11) return formatters.cpf(v);
            return formatters.cnpj(v);
        },
        cep: function (v) {
            v = digits(v).slice(0, 8);
            if (v.length > 5) return v.slice(0, 5) + '-' + v.slice(5);
            return v;
        },
        date: function (v) {
            v = digits(v).slice(0, 8);
            return v
                .replace(/(\d{2})(\d)/, '$1/$2')
                .replace(/(\d{2})(\d)/, '$1/$2');
        },
        time: function (v) {
            v = digits(v).slice(0, 4);
            if (v.length > 2) return v.slice(0, 2) + ':' + v.slice(2);
            return v;
        }
    };

    /* ── Aplicar máscara a um elemento ───────────────────── */
    function applyMask(el) {
        var type = el.getAttribute('data-mask');
        if (!type || !formatters[type]) return;

        // Formatar valor existente ao carregar
        if (el.value) {
            el.value = formatters[type](el.value);
        }

        el.addEventListener('input', function () {
            var pos = this.selectionStart;
            var oldLen = this.value.length;
            this.value = formatters[type](this.value);
            var newLen = this.value.length;
            var newPos = pos + (newLen - oldLen);
            if (newPos < 0) newPos = 0;
            this.setSelectionRange(newPos, newPos);
        });

        // ViaCEP automático para CEP
        if (type === 'cep') {
            el.addEventListener('input', function () {
                var raw = digits(this.value);
                if (raw.length === 8) {
                    _fetchCep(raw, el);
                }
            });
        }
    }

    /* ── ViaCEP ──────────────────────────────────────────── */
    function _fetchCep(cep, el) {
        // Buscar elementos de feedback próximos
        var wrap = el.closest('.form-group') || el.closest('.cep-wrap') || el.parentElement;
        var spinner = wrap ? wrap.querySelector('.cep-spinner') : null;
        var ok = wrap ? (wrap.querySelector('.cep-ok') || wrap.parentElement.querySelector('.cep-ok')) : null;
        var err = wrap ? (wrap.querySelector('.cep-err') || wrap.parentElement.querySelector('.cep-err')) : null;

        if (spinner) spinner.style.display = 'block';
        if (ok) ok.style.display = 'none';
        if (err) err.style.display = 'none';

        fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (spinner) spinner.style.display = 'none';
                if (data.erro) {
                    if (err) err.style.display = 'block';
                    return;
                }

                // Preencher campos do formulário
                var form = el.closest('form') || document;
                var fields = {
                    'address_street': data.logradouro,
                    'address_district': data.bairro,
                    'address_city': data.localidade,
                    'address_state': data.uf
                };

                for (var name in fields) {
                    var field = form.querySelector('[name="' + name + '"], #' + name);
                    if (field && fields[name]) {
                        field.value = fields[name];
                    }
                }

                if (ok) ok.style.display = 'block';

                // Focar no campo número
                var numField = form.querySelector('[name="address_number"], #address_number');
                if (numField) {
                    numField.value = '';
                    numField.focus();
                }
            })
            .catch(function () {
                if (spinner) spinner.style.display = 'none';
                if (err) err.style.display = 'block';
            });
    }

    /* ── Auto-inicialização ──────────────────────────────── */
    function initAll(root) {
        var els = (root || document).querySelectorAll('[data-mask]');
        for (var i = 0; i < els.length; i++) {
            if (!els[i]._hmMask) {
                applyMask(els[i]);
                els[i]._hmMask = true;
            }
        }
    }

    // Inicializar quando DOM pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(); });
    } else {
        initAll();
    }

    // Observar novos elementos adicionados ao DOM
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var added = mutations[i].addedNodes;
                for (var j = 0; j < added.length; j++) {
                    if (added[j].nodeType === 1) {
                        if (added[j].hasAttribute && added[j].hasAttribute('data-mask')) {
                            applyMask(added[j]);
                            added[j]._hmMask = true;
                        }
                        if (added[j].querySelectorAll) {
                            initAll(added[j]);
                        }
                    }
                }
            }
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    /* ── API pública ─────────────────────────────────────── */
    window.HMMask = {
        apply: applyMask,
        init: initAll,
        format: function (value, type) {
            if (formatters[type]) return formatters[type](value);
            return value;
        },
        unmask: function (value) {
            return digits(value);
        },
        formatters: formatters
    };

})();
