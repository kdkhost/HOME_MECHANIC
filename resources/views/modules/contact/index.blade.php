@extends('layouts.admin')
@section('title', 'Mensagens')
@section('page-title', 'Mensagens de Contato')
@section('breadcrumb')
    <li class="breadcrumb-item active">Mensagens</li>
@endsection

@section('content')
<div class="page-header">
    <h2 class="page-header-title"><i class="fas fa-envelope mr-2" style="color:var(--hm-primary);"></i>Mensagens Recebidas</h2>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-inbox"></i> Caixa de Entrada</span>
    </div>
    <div class="card-body p-0">
        @if(count($messages) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Assunto</th>
                            <th>Data</th>
                            <th style="width:100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                        <tr style="{{ $message['status'] === 'new' ? 'font-weight:600;' : '' }}">
                            <td>
                                @if($message['status'] === 'new')
                                    <span class="badge badge-warning">Nova</span>
                                @else
                                    <span class="badge badge-success">Respondida</span>
                                @endif
                            </td>
                            <td>{{ $message['name'] }}</td>
                            <td><a href="mailto:{{ $message['email'] }}" style="color:var(--hm-primary);">{{ $message['email'] }}</a></td>
                            <td>
                                {{ $message['subject'] }}
                                <div class="text-muted" style="font-size:0.78rem;font-weight:400;">{{ Str::limit($message['message'], 50) }}</div>
                            </td>
                            <td>{{ $message['created_at']->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.contact.show', $message['id']) }}" class="btn btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                    <form method="POST" action="{{ route('admin.contact.destroy', $message['id']) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-delete" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>Nenhuma mensagem</h5>
                <p>As mensagens de contato aparecerão aqui.</p>
            </div>
        @endif
    </div>
</div>
@endsection
