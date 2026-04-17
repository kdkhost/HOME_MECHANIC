@extends('layouts.admin')

@section('title', 'Mensagens de Contato')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Mensagens de Contato</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mensagens</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Lista de mensagens -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-envelope"></i>
                                Mensagens Recebidas
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(count($messages) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Nome</th>
                                                <th>Email</th>
                                                <th>Assunto</th>
                                                <th>Data</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($messages as $message)
                                            <tr class="{{ $message['status'] === 'new' ? 'table-warning' : '' }}">
                                                <td>
                                                    @if($message['status'] === 'new')
                                                        <span class="badge badge-warning">Nova</span>
                                                    @else
                                                        <span class="badge badge-success">Respondida</span>
                                                    @endif
                                                </td>
                                                <td>{{ $message['name'] }}</td>
                                                <td>{{ $message['email'] }}</td>
                                                <td>
                                                    <strong>{{ $message['subject'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($message['message'], 50) }}</small>
                                                </td>
                                                <td>{{ $message['created_at']->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.contact.show', $message['id']) }}" 
                                                           class="btn btn-info" title="Visualizar">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.contact.destroy', $message['id']) }}" 
                                                              style="display: inline;" 
                                                              onsubmit="return confirm('Tem certeza que deseja excluir esta mensagem?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" title="Excluir">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-envelope" style="font-size: 4rem; color: #ccc;"></i>
                                    <h4 class="mt-3 text-muted">Nenhuma mensagem encontrada</h4>
                                    <p class="text-muted">As mensagens de contato aparecerão aqui.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable se houver mensagens
    @if(count($messages) > 0)
    $('.table').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[4, 'desc']]
    });
    @endif
});
</script>
@endsection