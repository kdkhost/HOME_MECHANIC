@extends('emails.layout')

@section('content')
<h2>Olá, {{ $name }}!</h2>

<p>Recebemos a sua mensagem e já preparamos uma resposta para você.</p>

<div class="info-box">
    <strong>Assunto original:</strong> "{{ $originalSubject }}"
</div>

<div class="reply-box">
    <h3>Nossa Resposta:</h3>
    {!! nl2br(e($reply)) !!}
</div>

<hr class="divider">

<p>Se você tiver mais alguma dúvida, não hesite em entrar em contato conosco novamente. Estamos à disposição para ajudar!</p>

<p style="text-align: center; margin-top: 30px;">
    <a href="{{ url('/contato') }}" class="btn">Enviar Nova Mensagem</a>
</p>

<p style="margin-top: 30px;">
    Atenciosamente,<br>
    <strong>Equipe {{ $siteName ?? 'Home Mechanic' }}</strong>
</p>
@endsection
