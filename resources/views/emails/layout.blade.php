<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Home Mechanic' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #ff6b00, #ff8c00);
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header .logo {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px;
            background-color: #ffffff;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer a {
            color: #ff6b00;
            text-decoration: none;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #ff6b00, #ff8c00);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
        }
        .info-box {
            background-color: #fff3e0;
            border-left: 4px solid #ff6b00;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .reply-box {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .reply-box h3 {
            margin-top: 0;
            color: #ff6b00;
            font-size: 16px;
        }
        .divider {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="logo">🔧</div>
            <h1>{{ $siteName ?? 'Home Mechanic' }}</h1>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>Este é um e-mail automático. Por favor, não responda diretamente a este e-mail.</p>
            <p>Para entrar em contato, visite nosso site: <a href="{{ url('/') }}">{{ url('/') }}</a></p>
            <p>&copy; {{ date('Y') }} {{ $siteName ?? 'Home Mechanic' }}. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
