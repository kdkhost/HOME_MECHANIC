@extends('layouts.admin')
@section('title', 'Configurações Gerais')
@section('page-title', 'Configurações')
@section('breadcrumb')
    <li class="breadcrumb-item active">Configurações</li>
@endsection

@section('styles')
<style>
/* ── CEP ───────────────────────────────────────────────── */
.cep-wrap { position: relative; }
.cep-spinner {
    position: absolute; right: 10px; top: 50%;
    transform: translateY(-50%);
    color: #FF6B00; display: none;
}
.cep-ok   { color: #16a34a; font-size: 0.78rem; margin-top: 0.25rem; display: none; }
.cep-err  { color: #dc2626; font-size: 0.78rem; margin-top: 0.25rem; display: none; }

/* ── Mapa ──────────────────────────────────────────────── */
#map {
    width: 100%; height: 320px;
    border-radius: 10px; border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    display: none;
}
.map-placeholder {
    width: 100%; height: 320px;
    border-radius: 10px; border: 1.5px dashed #e2e8f0;
    background: #f8fafc;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #94a3b8; gap: 0.5rem;
}
.map-placeholder i { font-size: 2.5rem; opacity: 0.4; }
.map-placeholder span { font-size: 0.85rem; }

/* ── Seção ─────────────────────────────────────────────── */
.section-divider {
    font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px; color: #94a3b8;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.5rem; margin: 1.5rem 0 1rem;
    display: flex; align-items: center; gap: 0.5rem;
}
.section-divider i { color: #FF6B00; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-cog me-2" style="color:#FF6B00;"></i>Configurações do Sistema</h2>
</div>

<div class="row">
    @include('modules.settings._sidebar', ['active' => 'general'])

    <div class="col-md-9">
        <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
            @csrf
            <input type="hidden" name="section" value="general">

            {{-- ── Informações do Site ─────────────────── --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-globe"></i> Informações do Site</span>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Nome do Site <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="site_name"
                               value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Descrição do Site</label>
                        <textarea class="form-control" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                    </div>

                    <div class="section-divider"><i class="fas fa-phone"></i> Contato</div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>E-mail de Contato</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="contact_email"
                                           value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                           placeholder="contato@site.com.br">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" name="contact_phone"
                                           id="contact_phone" data-mask="phone"
                                           value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                           placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#25D366;border-color:#25D366;color:#fff;"><i class="fab fa-whatsapp"></i></span>
                                    <input type="text" class="form-control" name="whatsapp"
                                           id="whatsapp" data-mask="whatsapp"
                                           value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}"
                                           placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Redes Sociais ────────────────── --}}
                    <div class="section-divider"><i class="fas fa-share-alt"></i> Redes Sociais</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#E1306C;border-color:#E1306C;color:#fff;"><i class="fab fa-instagram"></i></span>
                                    <input type="url" class="form-control" name="social_instagram"
                                           value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                                           placeholder="https://instagram.com/suapagina">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Facebook</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#1877F2;border-color:#1877F2;color:#fff;"><i class="fab fa-facebook-f"></i></span>
                                    <input type="url" class="form-control" name="social_facebook"
                                           value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                                           placeholder="https://facebook.com/suapagina">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>YouTube</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#FF0000;border-color:#FF0000;color:#fff;"><i class="fab fa-youtube"></i></span>
                                    <input type="url" class="form-control" name="social_youtube"
                                           value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}"
                                           placeholder="https://youtube.com/@seucanal">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Twitter / X</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#000;border-color:#000;color:#fff;"><i class="fab fa-x-twitter"></i></span>
                                    <input type="url" class="form-control" name="social_twitter"
                                           value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                                           placeholder="https://x.com/suaconta">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TikTok</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#010101;border-color:#010101;color:#fff;"><i class="fab fa-tiktok"></i></span>
                                    <input type="url" class="form-control" name="social_tiktok"
                                           value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}"
                                           placeholder="https://tiktok.com/@suaconta">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>LinkedIn</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#0A66C2;border-color:#0A66C2;color:#fff;"><i class="fab fa-linkedin-in"></i></span>
                                    <input type="url" class="form-control" name="social_linkedin"
                                           value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                                           placeholder="https://linkedin.com/company/suaempresa">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Endereço ─────────────────────── --}}
                    <div class="section-divider"><i class="fas fa-map-marker-alt"></i> Endereço</div>

                    {{-- CEP --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>CEP <span class="text-danger">*</span></label>
                                <div class="cep-wrap">
                                    <input type="text" class="form-control" name="address_cep" id="address_cep"
                                           data-mask="cep"
                                           value="{{ old('address_cep', $settings['address_cep'] ?? '') }}"
                                           placeholder="00000-000" maxlength="9">
                                    <i class="fas fa-spinner fa-spin cep-spinner" id="cepSpinner"></i>
                                </div>
                                <div class="cep-ok" id="cepOk"><i class="fas fa-check-circle me-1"></i>CEP encontrado!</div>
                                <div class="cep-err" id="cepErr"><i class="fas fa-times-circle me-1"></i>CEP não encontrado.</div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Logradouro</label>
                                <input type="text" class="form-control" name="address_street" id="address_street"
                                       value="{{ old('address_street', $settings['address_street'] ?? '') }}"
                                       placeholder="Rua, Avenida, etc.">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Número</label>
                                <input type="text" class="form-control" name="address_number" id="address_number"
                                       value="{{ old('address_number', $settings['address_number'] ?? '') }}"
                                       placeholder="Nº">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Complemento</label>
                                <input type="text" class="form-control" name="address_complement" id="address_complement"
                                       value="{{ old('address_complement', $settings['address_complement'] ?? '') }}"
                                       placeholder="Sala, Andar, etc.">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Bairro</label>
                                <input type="text" class="form-control" name="address_district" id="address_district"
                                       value="{{ old('address_district', $settings['address_district'] ?? '') }}"
                                       placeholder="Bairro">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cidade</label>
                                <input type="text" class="form-control" name="address_city" id="address_city"
                                       value="{{ old('address_city', $settings['address_city'] ?? '') }}"
                                       placeholder="Cidade">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>UF</label>
                                <input type="text" class="form-control" name="address_state" id="address_state"
                                       value="{{ old('address_state', $settings['address_state'] ?? '') }}"
                                       placeholder="SP" maxlength="2"
                                       style="text-transform:uppercase;">
                            </div>
                        </div>
                    </div>

                    {{-- Mapa --}}
                    <div class="mt-2">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="mb-0" style="font-size:0.82rem;font-weight:600;color:#475569;">
                                <i class="fas fa-map me-1" style="color:#FF6B00;"></i>
                                Localização no Mapa
                            </label>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnShowMap" onclick="showMap()">
                                <i class="fas fa-map-marker-alt"></i> Ver no Mapa
                            </button>
                        </div>
                        <div class="map-placeholder" id="mapPlaceholder">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Preencha o endereço e clique em "Ver no Mapa"</span>
                        </div>
                        <div id="map"></div>
                    </div>

                    {{-- ── Sistema ──────────────────────── --}}
                    <div class="section-divider mt-4"><i class="fas fa-sliders-h"></i> Sistema</div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fuso Horário</label>
                                <select class="form-control" name="timezone">
                                    @foreach([
                                        'America/Sao_Paulo'  => 'Brasília (UTC-3)',
                                        'America/Manaus'     => 'Manaus (UTC-4)',
                                        'America/Belem'      => 'Belém (UTC-3)',
                                        'America/Fortaleza'  => 'Fortaleza (UTC-3)',
                                        'America/Recife'     => 'Recife (UTC-3)',
                                        'America/Cuiaba'     => 'Cuiabá (UTC-4)',
                                        'America/Porto_Velho'=> 'Porto Velho (UTC-4)',
                                        'America/Rio_Branco' => 'Rio Branco (UTC-5)',
                                    ] as $tz => $label)
                                        <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'America/Sao_Paulo') === $tz ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    {{-- ── Analytics ──────────────────────── --}}
                    <div class="section-divider mt-4"><i class="fas fa-chart-line"></i> Integrações</div>
                    <div class="row align-items-center mb-4">
                        <div class="col-md-4">
                            <div class="custom-control custom-switch custom-switch-lg">
                                <input type="checkbox" class="custom-control-input" id="analytics_enabled"
                                       name="analytics_enabled" value="1"
                                       {{ ($settings['analytics_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" style="font-size: 1.1rem; padding-top: 2px;" for="analytics_enabled">Ativar Analytics</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Máscaras de telefone, WhatsApp e CEP agora são globais via hm-masks.js (data-mask)
// ViaCEP também é tratado automaticamente pelo hm-masks.js

// ── Mapa via OpenStreetMap + Leaflet ──────────────────────
var mapInstance = null;
var mapMarker   = null;

// Carregar Leaflet dinamicamente
function loadLeaflet(callback) {
    if (window.L) { callback(); return; }

    var css = document.createElement('link');
    css.rel = 'stylesheet';
    css.href = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css';
    document.head.appendChild(css);

    var js = document.createElement('script');
    js.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js';
    js.onload = callback;
    document.head.appendChild(js);
}

function showMap() {
    var street   = document.getElementById('address_street').value;
    var number   = document.getElementById('address_number').value;
    var district = document.getElementById('address_district').value;
    var city     = document.getElementById('address_city').value;
    var state    = document.getElementById('address_state').value;
    var cep      = document.getElementById('address_cep').value;

    var query = [street, number, district, city, state, 'Brasil'].filter(Boolean).join(', ');
    if (!city && !street) {
        HMToast.warning('Preencha pelo menos a cidade para exibir o mapa.');
        return;
    }

    loadLeaflet(function() {
        document.getElementById('mapPlaceholder').style.display = 'none';
        document.getElementById('map').style.display = 'block';

        // Geocodificar via Nominatim (OpenStreetMap)
        fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query), {
            headers: { 'Accept-Language': 'pt-BR' }
        })
        .then(function(r) { return r.json(); })
        .then(function(results) {
            if (!results.length) {
                HMToast.warning('Endereço não encontrado no mapa. Tente ser mais específico.');
                return;
            }

            var lat = parseFloat(results[0].lat);
            var lng = parseFloat(results[0].lon);

            if (!mapInstance) {
                mapInstance = L.map('map').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                }).addTo(mapInstance);
            } else {
                mapInstance.setView([lat, lng], 16);
            }

            // Ícone personalizado laranja
            var icon = L.divIcon({
                html: '<div style="background:#FF6B00;width:32px;height:32px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                className: '',
            });

            if (mapMarker) mapMarker.remove();
            mapMarker = L.marker([lat, lng], { icon: icon })
                .addTo(mapInstance)
                .bindPopup('<strong>' + (street || city) + '</strong><br>' + city + '/' + state)
                .openPopup();

            // Forçar redraw
            setTimeout(function() { mapInstance.invalidateSize(); }, 200);
        })
        .catch(function() {
            HMToast.error('Erro ao carregar o mapa.');
        });
    });
}

// ── Inicializar mapa se já tiver endereço salvo ───────────
$(function() {
    var city = document.getElementById('address_city').value;
    if (city) {
        setTimeout(showMap, 800);
    }
});
</script>
@endsection
