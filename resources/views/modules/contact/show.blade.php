@extends('layouts.admin')

@section('title', 'Mensagem de Contato')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Mensagem de Contato</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contact.index') }}">Mensagens</a></li>
                        <li class="breadcrumb-item active">Visualizar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i> {{ $message['subject'] }}</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-4">
                                <tr><td class="font-weight-bold" width="25%">Nome</td><td>{{ $message['name'] }}</td></tr>
                                <tr><td class="font-weight-bold">E-mail</td><td>{{ $message['email'] }}</td></tr>
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
                            <h6>Mensagem:</h6>
                            <div class="p-3 bg-light rounded">{{ $message['message'] }}</div>
                        </div>
                    </div>

                    <!-- Formulário de resposta -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-reply mr-2"></i> Responder</h3>
                        </div>
                        <form method="POST" action="{{ route('admin.contact.reply', $message['id']) }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Sua Resposta</label>
                                    <textarea class="form-control" name="reply" rows="5" required
                                              placeholder="Digite sua resposta aqui..."></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-1"></i> Enviar Resposta
                                </button>
                                <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary ml-2">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
