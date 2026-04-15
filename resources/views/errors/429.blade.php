<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muitas Tentativas - HomeMechanic</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0D0D0D 0%, #2C2C2C 100%);
            color: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #FF6B00;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 0 30px rgba(255, 107, 0, 0.3);
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #FFFFFF;
        }
        
        .error-message {
            font-size: 1.2rem;
            color: #CCCCCC;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .countdown {
            background: rgba(255, 107, 0, 0.1);
            border: 2px solid #FF6B00;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .countdown-time {
            font-size: 2rem;
            font-weight: 700;
            color: #FF6B00;
            margin: 0.5rem 0;
        }
        
        .back-button {
            display: inline-block;
            background: #FF6B00;
            color: #FFFFFF;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .back-button:hover {
            background: #E55A00;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.3);
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">429</div>
        <h1 class="error-title">Muitas Tentativas</h1>
        <p class="error-message">
            Você fez muitas tentativas de login em pouco tempo. 
            Por segurança, o acesso foi temporariamente bloqueado.
        </p>
        
        <div class="countdown">
            <div>Tente novamente em:</div>
            <div class="countdown-time" id="countdown">
                @if(isset($retry_after))
                    {{ $retry_after > 60 ? ceil($retry_after / 60) . ' minutos' : $retry_after . ' segundos' }}
                @else
                    Alguns minutos
                @endif
            </div>
        </div>
        
        <a href="{{ route('admin.login') }}" class="back-button">
            Voltar ao Login
        </a>
    </div>
    
    @if(isset($retry_after) && $retry_after > 0)
    <script>
        let timeLeft = {{ $retry_after }};
        const countdownElement = document.getElementById('countdown');
        
        function updateCountdown() {
            if (timeLeft <= 0) {
                countdownElement.textContent = 'Agora você pode tentar novamente';
                return;
            }
            
            if (timeLeft > 60) {
                const minutes = Math.ceil(timeLeft / 60);
                countdownElement.textContent = minutes + (minutes === 1 ? ' minuto' : ' minutos');
            } else {
                countdownElement.textContent = timeLeft + (timeLeft === 1 ? ' segundo' : ' segundos');
            }
            
            timeLeft--;
        }
        
        // Atualizar a cada segundo
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Executar imediatamente
    </script>
    @endif
</body>
</html>