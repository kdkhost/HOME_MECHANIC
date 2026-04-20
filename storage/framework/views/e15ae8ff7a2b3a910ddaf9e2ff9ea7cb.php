<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e($title ?? 'Sistema em Manutenção'); ?></title>
    
    <?php if(!empty($contact['favicon'])): ?>
        <link rel="icon" href="<?php echo e(asset('storage/' . $contact['favicon'])); ?>" type="image/x-icon">
    <?php endif; ?>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --hm-primary: #FF6B00;
            --hm-primary-dark: #E55A00;
            --hm-dark: #0D0D0D;
            --hm-darker: #050505;
            --hm-light: #F8F9FA;
            --hm-gray: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--hm-darker);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Bg Background */
        .maintenance-bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 1;
            /* Se houver bg_image configurado, usa ele. Senão usa gradient */
            <?php if(!empty($bg)): ?>
                background: url('<?php echo e(asset('storage/' . $bg)); ?>') no-repeat center center;
                background-size: cover;
            <?php else: ?>
                background: linear-gradient(135deg, #0D0D0D 0%, #2C2C2C 100%);
            <?php endif; ?>
        }

        /* Overlay para escurecer o fundo sempre */
        .bg-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Engrenagens Animadas (Fallback se não tiver img) */
        .gears-container {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 2;
        }
        .gear {
            position: absolute;
            color: rgba(255, 107, 0, 0.15);
            animation: rotate 20s linear infinite;
        }
        .gear-1 { top: 10%; left: 5%; font-size: 8rem; animation-duration: 25s; }
        .gear-2 { bottom: 15%; right: 10%; font-size: 14rem; animation-duration: 35s; animation-direction: reverse; }
        .gear-3 { top: 40%; right: 25%; font-size: 5rem; animation-duration: 15s; }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* Content Plate */
        .maintenance-glass {
            position: relative;
            z-index: 10;
            background: rgba(13, 13, 13, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 3rem 4rem;
            max-width: 800px;
            width: 90%;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            animation: slideUpFade 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-img {
            max-width: 280px;
            max-height: 80px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
        }

        h1 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--hm-primary);
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        p.msg {
            font-size: 1.25rem;
            line-height: 1.6;
            color: #d1d5db;
            margin-bottom: 2.5rem;
            font-weight: 300;
        }

        /* Countdown */
        .countdown-wrapper {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        .time-box {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 107, 0, 0.3);
            border-radius: 16px;
            padding: 1.5rem 1rem 1rem;
            width: 110px;
            box-shadow: inset 0 0 20px rgba(255, 107, 0, 0.05);
            backdrop-filter: blur(4px);
        }
        .time-box span {
            display: block;
            font-family: 'Rajdhani', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .time-box label {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 2px;
            color: var(--hm-primary);
            font-weight: 600;
        }

        /* Contatos */
        .contacts {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
        }
        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .contact-btn i {
            color: var(--hm-primary);
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        .contact-btn:hover {
            background: rgba(255, 107, 0, 0.1);
            border-color: rgba(255, 107, 0, 0.5);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .maintenance-glass { padding: 2rem 1.5rem; }
            h1 { font-size: 2.2rem; }
            p.msg { font-size: 1rem; }
            .countdown-wrapper { gap: 0.75rem; }
            .time-box { width: 75px; padding: 1rem 0.5rem 0.75rem; }
            .time-box span { font-size: 2rem; }
            .time-box label { font-size: 0.6rem; letter-spacing: 1px; }
            .contact-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <div class="maintenance-bg">
        <div class="bg-overlay"></div>
    </div>

    <?php if(empty($bg)): ?>
    <div class="gears-container">
        <i class="bi bi-gear-fill gear gear-1"></i>
        <i class="bi bi-gear-wide-connected gear gear-2"></i>
        <i class="bi bi-gear-fill gear gear-3"></i>
    </div>
    <?php endif; ?>

    <div class="maintenance-glass">
        
        <?php if(!empty($contact['logo'])): ?>
            <img src="<?php echo e(asset('storage/' . $contact['logo'])); ?>" alt="Logo" class="logo-img">
        <?php else: ?>
            <i class="bi bi-tools" style="font-size: 4rem; color: var(--hm-primary); margin-bottom:1rem; display:inline-block;"></i>
        <?php endif; ?>

        <h1><?php echo e($title ?? 'Sistema em Manutenção'); ?></h1>
        <p class="msg"><?php echo e($message ?? 'Estamos realizando melhorias.'); ?></p>

        
        <?php if(!empty($timer)): ?>
            <div class="countdown-wrapper" id="countdown">
                <div class="time-box">
                    <span id="c-days">00</span>
                    <label>Dias</label>
                </div>
                <div class="time-box">
                    <span id="c-hours">00</span>
                    <label>Horas</label>
                </div>
                <div class="time-box">
                    <span id="c-minutes">00</span>
                    <label>Min</label>
                </div>
                <div class="time-box">
                    <span id="c-seconds">00</span>
                    <label>Seg</label>
                </div>
            </div>
            
            <script>
                const targetDate = new Date("<?php echo e($timer); ?>").getTime();
                
                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    if (distance < 0) {
                        document.getElementById("countdown").style.display = "none";
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("c-days").innerText = String(days).padStart(2, '0');
                    document.getElementById("c-hours").innerText = String(hours).padStart(2, '0');
                    document.getElementById("c-minutes").innerText = String(minutes).padStart(2, '0');
                    document.getElementById("c-seconds").innerText = String(seconds).padStart(2, '0');
                }

                setInterval(updateTimer, 1000);
                updateTimer();
            </script>
        <?php endif; ?>

        
        <div class="contacts">
            <?php if(!empty($contact['whatsapp'])): ?>
                <a href="https://wa.me/55<?php echo e(preg_replace('/\D/', '', $contact['whatsapp'])); ?>" class="contact-btn" target="_blank">
                    <i class="bi bi-whatsapp"></i>
                    <span>Tire Dúvidas</span>
                </a>
            <?php endif; ?>
            <?php if(!empty($contact['phone'])): ?>
                <a href="tel:+55<?php echo e(preg_replace('/\D/', '', $contact['phone'])); ?>" class="contact-btn">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Ligue para nós</span>
                </a>
            <?php endif; ?>
            <?php if(!empty($contact['email'])): ?>
                <a href="mailto:<?php echo e($contact['email']); ?>" class="contact-btn">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Enviar E-mail</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html><?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\errors\503.blade.php ENDPATH**/ ?>