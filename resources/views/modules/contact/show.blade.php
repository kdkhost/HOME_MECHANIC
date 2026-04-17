@extends('layouts.admin')
@section('title', 'Mensagem')
@section('page-title', 'Mensagens')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.contact.index') }}">Mensagens</a></li>
    <li class="breadcrumb-item active">Visualizar</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope-open mr-2" style="color:var(--hm-primary);"></i>{{ $message['subject'] }}</h2>
    <div class="page-header-actions">
        <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Mensagem -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-envelope"></i> Mensagem Recebida</span>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-4" style="font-size:0.87rem;">
                    <tr><td class="font-weight-bold" width="25%">Nome</td><td>{{ $message['name'] }}</td></tr>
                    <tr><td class="font-weight-bold">E-mail</td><td><a href="mailto:{{ $message['email'] }}" style="color:var(--hm-primary);">{{ $message['email'] }}</a></td></tr>
                    @if(!empty($message['phone']))
                    <tr><td class="font-weight-bold">Telefone</td><td>{{ $message['phone'] }}</td></tr>
                    @endif
                    <tr><td class="font-weight-bold">Data</td><td>{{ $message['created_at']->format('d/m/Y H:i') }}</td></tr>
                    <tr>
                        <td class="font-weight-bold">Status</td>
                        <td>
                            @if($message['status'] === 'new')
                                <span class="badge badge-warning">Nova</span>
                            @else
                                <span class="badge badge-success">Respondida</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <label class="mb-2">Mensagem:</label>
                <div class="p-3" style="background:#f8f9fa;border-radius:8px;line-height:1.7;font-size:0.9rem;">
                    {{ $message['message'] }}
                </div>
            </div>
        </div>

        <!-- Resposta -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-reply"></i> Responder</span>
            </div>
            <form method="POST" action="{{ route('admin.contact.reply', $message['id']) }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Sua Resposta <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reply" rows="6" required placeholder="Digite sua resposta..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar Resposta</button>
                    <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
