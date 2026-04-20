
<?php $__env->startSection('title', 'Contato — HomeMechanic Tuning & Performance'); ?>

<?php $__env->startSection('styles'); ?>
<style>
.page-hero {
    min-height: 45vh; display: flex; align-items: flex-end; padding-bottom: 4rem;
    background: linear-gradient(to bottom, rgba(10,10,10,0.5) 0%, rgba(10,10,10,0.95) 100%),
        url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1600&q=80') center/cover no-repeat;
    padding-top: 100px;
}
.contact-info-card {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; padding: 2rem;
    height: 100%; transition: var(--transition);
}
.contact-info-card:hover { border-color: rgba(255,107,0,0.3); }
.contact-info-icon {
    width: 52px; height: 52px;
    background: rgba(255,107,0,0.1);
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: var(--orange); font-size: 1.4rem;
    margin-bottom: 1.25rem;
}
.contact-info-title { font-weight: 600; font-size: 0.9rem; color: var(--white); margin-bottom: 0.5rem; }
.contact-info-text { color: var(--gray); font-size: 0.88rem; line-height: 1.7; margin: 0; }
.contact-form-wrap {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; padding: 2.5rem;
}
.form-label-custom {
    font-size: 0.75rem; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(255,255,255,0.6); margin-bottom: 0.5rem;
}
.form-control-custom {
    background: var(--dark3) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    color: var(--white) !important;
    border-radius: 4px !important;
    padding: 0.85rem 1rem !important;
    font-size: 0.9rem !important;
    transition: var(--transition) !important;
}
.form-control-custom:focus {
    border-color: rgba(255,107,0,0.5) !important;
    box-shadow: 0 0 0 3px rgba(255,107,0,0.1) !important;
    outline: none !important;
}
.form-control-custom::placeholder { color: rgba(255,255,255,0.25) !important; }
.map-wrap {
    background: var(--dark2);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; overflow: hidden;
    height: 300px;
    display: flex; align-items: center; justify-content: center;
}
.map-placeholder {
    text-align: center; color: var(--gray);
}
.map-placeholder i { font-size: 3rem; color: var(--orange); margin-bottom: 1rem; display: block; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-hero">
    <div class="container">
        <div class="section-label">Fale Conosco</div>
        <h1 class="section-title">Entre em <span>Contato</span></h1>
        <div class="divider-orange"></div>
        <p class="section-sub">Agende uma visita ou solicite um orçamento sem compromisso.</p>
    </div>
</div>

<section style="padding:5rem 0; background:var(--black);">
    <div class="container">

        <!-- Info cards -->
        <div class="row g-4 mb-5">
            <?php
            $infos = [
                ['icon'=>'bi-geo-alt-fill','title'=>'Endereço','text'=> !empty($siteSettings['address']) ? nl2br(e($siteSettings['address'])) : 'Av. das Supercars, 1500<br>Jardim Europa — São Paulo, SP'],
                ['icon'=>'bi-telephone-fill','title'=>'Telefone & WhatsApp','text'=> ($siteSettings['phone'] ?? '(11) 99999-9999') . (!empty($siteSettings['whatsapp']) ? '<br>'.$siteSettings['whatsapp'] : '') . '<br>Seg–Sex: 8h–18h'],
                ['icon'=>'bi-envelope-fill','title'=>'E-mail','text'=> $siteSettings['email'] ?? 'contato@homemechanic.com.br'],
                ['icon'=>'bi-clock-fill','title'=>'Horário de Funcionamento','text'=>'Segunda a Sexta: 8h às 18h<br>Sábado: 8h às 13h<br>Domingo: Fechado'],
            ];
            ?>
            <?php $__currentLoopData = $infos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo e($i * 80); ?>">
                <div class="contact-info-card">
                    <div class="contact-info-icon"><i class="bi <?php echo e($info['icon']); ?>"></i></div>
                    <div class="contact-info-title"><?php echo e($info['title']); ?></div>
                    <p class="contact-info-text"><?php echo $info['text']; ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="row g-5">
            <!-- Form -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="contact-form-wrap">
                    <h3 style="font-family:var(--font-head); font-size:1.8rem; font-weight:700; margin-bottom:0.5rem;">
                        Solicite um <span style="color:var(--orange);">Orçamento</span>
                    </h3>
                    <p style="color:var(--gray); font-size:0.88rem; margin-bottom:2rem;">
                        Preencha o formulário e nossa equipe entrará em contato em até 2 horas úteis.
                    </p>

                    <?php if(session('success')): ?>
                        <div style="background:rgba(40,167,69,0.15); border:1px solid rgba(40,167,69,0.3); color:#75b798; padding:1rem; border-radius:4px; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem;">
                            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('contact.send')); ?>" id="contactForm">
                        <?php echo csrf_field(); ?>
                        <?php $recaptchaKey = \App\Models\Setting::get('recaptcha_site_key',''); $recaptchaOn = \App\Models\Setting::get('recaptcha_enabled','0') === '1' && !empty($recaptchaKey); ?>
                        <?php if($recaptchaOn): ?>
                            <input type="hidden" name="recaptcha_token" id="recaptchaToken">
                        <?php endif; ?>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label-custom">Nome Completo *</label>
                                <input type="text" name="name" class="form-control form-control-custom"
                                       placeholder="Seu nome" value="<?php echo e(old('name')); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-custom">E-mail *</label>
                                <input type="email" name="email" class="form-control form-control-custom"
                                       placeholder="seu@email.com" value="<?php echo e(old('email')); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-custom">Telefone / WhatsApp</label>
                                <input type="text" name="phone" class="form-control form-control-custom"
                                       placeholder="(11) 99999-9999" value="<?php echo e(old('phone')); ?>" data-mask="phone">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label-custom">Assunto *</label>
                                <select name="subject" class="form-control form-control-custom" required>
                                    <option value="" disabled <?php echo e(old('subject') ? '' : 'selected'); ?>>Selecione...</option>
                                    <option value="Orçamento Tuning" <?php echo e(old('subject')=='Orçamento Tuning'?'selected':''); ?>>Orçamento — Tuning</option>
                                    <option value="Orçamento Suspensão" <?php echo e(old('subject')=='Orçamento Suspensão'?'selected':''); ?>>Orçamento — Suspensão</option>
                                    <option value="Orçamento Estética" <?php echo e(old('subject')=='Orçamento Estética'?'selected':''); ?>>Orçamento — Estética</option>
                                    <option value="Orçamento Freios" <?php echo e(old('subject')=='Orçamento Freios'?'selected':''); ?>>Orçamento — Freios</option>
                                    <option value="Manutenção" <?php echo e(old('subject')=='Manutenção'?'selected':''); ?>>Manutenção Preventiva</option>
                                    <option value="Outro" <?php echo e(old('subject')=='Outro'?'selected':''); ?>>Outro Assunto</option>
                                </select>
                                <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Mensagem *</label>
                                <textarea name="message" class="form-control form-control-custom" rows="5"
                                          placeholder="Descreva seu veículo e o serviço desejado..." required><?php echo e(old('message')); ?></textarea>
                                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div style="color:#ea868f;font-size:0.78rem;margin-top:0.25rem;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <?php $__errorArgs = ['recaptcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="col-12">
                                <div style="background:rgba(220,38,38,0.12);border:1px solid rgba(220,38,38,0.3);color:#fca5a5;padding:0.75rem 1rem;border-radius:6px;font-size:0.85rem;display:flex;align-items:center;gap:0.6rem;">
                                    <i class="bi bi-shield-exclamation"></i> <?php echo e($message); ?>

                                </div>
                            </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            <div class="col-12">
                                <button type="submit" class="btn-orange w-100" id="submitBtn" style="justify-content:center;">
                                    <i class="bi bi-send-fill"></i> Enviar Mensagem
                                </button>
                            </div>

                            <?php if($recaptchaOn): ?>
                            <div class="col-12">
                                <div style="font-size:0.72rem;color:rgba(255,255,255,0.3);text-align:center;line-height:1.5;">
                                    <i class="bi bi-shield-check" style="color:rgba(255,107,0,0.5);"></i>
                                    Protegido por reCAPTCHA v3 —
                                    <a href="https://policies.google.com/privacy" target="_blank" style="color:rgba(255,107,0,0.5);">Privacidade</a> &
                                    <a href="https://policies.google.com/terms" target="_blank" style="color:rgba(255,107,0,0.5);">Termos</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Map + WhatsApp -->
            <div class="col-lg-5" data-aos="fade-left">

                
                <?php
                    $mapAddress = trim(implode(', ', array_filter([
                        \App\Models\Setting::get('address_street',''),
                        \App\Models\Setting::get('address_number',''),
                        \App\Models\Setting::get('address_district',''),
                        \App\Models\Setting::get('address_city',''),
                        \App\Models\Setting::get('address_state',''),
                        'Brasil'
                    ])));
                    $mapLabel1 = trim(implode(', ', array_filter([
                        \App\Models\Setting::get('address_street',''),
                        \App\Models\Setting::get('address_number',''),
                    ]))) ?: ($siteSettings['address'] ?: 'Nossa Localização');
                    $mapLabel2 = trim(implode(' — ', array_filter([
                        \App\Models\Setting::get('address_district',''),
                        \App\Models\Setting::get('address_city',''),
                        \App\Models\Setting::get('address_state',''),
                    ])));
                    if (empty($mapAddress) || $mapAddress === 'Brasil') {
                        $mapAddress = ($siteSettings['address'] ?? '') . ', Brasil';
                    }
                ?>

                <div style="border-radius:8px;overflow:hidden;border:1px solid rgba(255,107,0,0.2);margin-bottom:1.5rem;position:relative;">
                    
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css">

                    <div id="frontendMap" style="width:100%;height:320px;background:#1a1a1a;"></div>

                    
                    <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(to top,rgba(10,10,10,0.92),transparent);padding:1rem 1.25rem;z-index:500;pointer-events:none;">
                        <div style="font-family:'Rajdhani',sans-serif;font-size:1rem;font-weight:700;color:#fff;">
                            <?php echo e($mapLabel1); ?>

                        </div>
                        <?php if($mapLabel2): ?>
                        <div style="font-size:0.8rem;color:rgba(255,255,255,0.6);"><?php echo e($mapLabel2); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="background:var(--dark2); border:1px solid rgba(255,255,255,0.06); border-radius:4px; padding:2rem;">
                    <h4 style="font-family:var(--font-head); font-size:1.3rem; font-weight:700; margin-bottom:0.5rem;">
                        Atendimento <span style="color:var(--orange);">Imediato</span>
                    </h4>
                    <?php
                        $whatsappContact = preg_replace('/\D/', '', $siteSettings['whatsapp'] ?? '');
                        if (!empty($whatsappContact) && !str_starts_with($whatsappContact, '55') && strlen($whatsappContact) >= 10) {
                            $whatsappContact = '55' . $whatsappContact;
                        }
                    ?>
                    <p style="color:var(--gray); font-size:0.88rem; margin-bottom:1.5rem;">
                        Para respostas rápidas, fale diretamente com nossa equipe pelo WhatsApp.
                    </p>
                    <a href="https://wa.me/<?php echo e($whatsappContact ?: '5511999999999'); ?>" target="_blank" class="btn-orange w-100" style="justify-content:center; background:linear-gradient(135deg,#128C7E,#25D366) !important; border-color:transparent !important; box-shadow:0 4px 16px rgba(37,211,102,0.3) !important;">
                        <i class="bi bi-whatsapp"></i> Chamar no WhatsApp
                    </a>
                    <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,0.06);">
                        <div style="font-size:0.75rem; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:var(--orange); margin-bottom:0.75rem;">
                            Redes Sociais
                        </div>
                        <div class="social-links">
                            <?php if(!empty($siteSettings['social_instagram'])): ?>
                                <a href="<?php echo e($siteSettings['social_instagram']); ?>" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a>
                            <?php endif; ?>
                            <?php if(!empty($siteSettings['social_facebook'])): ?>
                                <a href="<?php echo e($siteSettings['social_facebook']); ?>" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a>
                            <?php endif; ?>
                            <?php if(!empty($siteSettings['social_youtube'])): ?>
                                <a href="<?php echo e($siteSettings['social_youtube']); ?>" target="_blank" rel="noopener"><i class="bi bi-youtube"></i></a>
                            <?php endif; ?>
                            <?php if(!empty($siteSettings['social_twitter'])): ?>
                                <a href="<?php echo e($siteSettings['social_twitter']); ?>" target="_blank" rel="noopener"><i class="bi bi-twitter-x"></i></a>
                            <?php endif; ?>
                            <?php if(!empty($siteSettings['social_tiktok'])): ?>
                                <a href="<?php echo e($siteSettings['social_tiktok']); ?>" target="_blank" rel="noopener"><i class="bi bi-tiktok"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php
    $recaptchaKey = \App\Models\Setting::get('recaptcha_site_key','');
    $recaptchaOn  = \App\Models\Setting::get('recaptcha_enabled','0') === '1' && !empty($recaptchaKey);
?>
<?php if($recaptchaOn): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($recaptchaKey); ?>"></script>
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn  = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-shield-check"></i> Verificando...';
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo e($recaptchaKey); ?>', { action: 'contact' }).then(function(token) {
            document.getElementById('recaptchaToken').value = token;
            form.submit();
        }).catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill"></i> Enviar Mensagem';
        });
    });
});
</script>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
<script>
(function() {
    var address = <?php echo e(Js::from($mapAddress)); ?>;
    var label1  = <?php echo e(Js::from($mapLabel1)); ?>;
    var label2  = <?php echo e(Js::from($mapLabel2)); ?>;

    // Ícone personalizado laranja
    var orangeIcon = L.divIcon({
        html: '<div style="width:32px;height:32px;background:#FF6B00;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 3px 12px rgba(255,107,0,0.6);"></div>',
        iconSize:   [32, 32],
        iconAnchor: [16, 32],
        popupAnchor:[0, -36],
        className:  '',
    });

    function initMap(lat, lng) {
        var map = L.map('frontendMap', {
            center:          [lat, lng],
            zoom:            16,
            zoomControl:     true,
            scrollWheelZoom: false,
            attributionControl: false,
        });

        // Tile escuro (compatível com o design dark)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Atribuição discreta
        L.control.attribution({ prefix: false })
            .addAttribution('© <a href="https://carto.com" style="color:#FF6B00;">CARTO</a> | © <a href="https://www.openstreetmap.org/copyright" style="color:#FF6B00;">OSM</a>')
            .addTo(map);

        var popup = '<div style="font-family:\'Rajdhani\',sans-serif;min-width:160px;text-align:center;">' +
                    '<strong style="font-size:1rem;color:#FF6B00;">' + label1 + '</strong>' +
                    (label2 ? '<br><span style="font-size:0.82rem;color:#555;">' + label2 + '</span>' : '') +
                    '</div>';

        L.marker([lat, lng], { icon: orangeIcon })
            .addTo(map)
            .bindPopup(popup, { maxWidth: 220 })
            .openPopup();
    }

    // Geocodificar via Nominatim
    fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address), {
        headers: { 'Accept-Language': 'pt-BR', 'User-Agent': 'HomeMechanic/1.0' }
    })
    .then(function(r) { return r.json(); })
    .then(function(results) {
        if (results && results.length > 0) {
            initMap(parseFloat(results[0].lat), parseFloat(results[0].lon));
        } else {
            // Fallback: São Paulo centro
            initMap(-23.5505, -46.6333);
        }
    })
    .catch(function() {
        initMap(-23.5505, -46.6333);
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\modules\frontend\contact.blade.php ENDPATH**/ ?>