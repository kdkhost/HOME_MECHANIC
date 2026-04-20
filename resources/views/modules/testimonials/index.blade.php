@extends('layouts.admin')
@section('title', 'Depoimentos')
@section('page-title', 'Depoimentos')
@section('breadcrumb')
    <li class="breadcrumb-item active">Depoimentos</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listagem de Depoimentos</h3>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="fas fa-plus"></i> Novo Depoimento
        </button>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-valign-middle">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Avaliação</th>
                    <th>Depoimento</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="sortable">
                @forelse($testimonials as $t)
                <tr data-id="{{ $t->id }}">
                    <td>
                        <i class="fas fa-grip-vertical text-muted me-2" style="cursor: grab;"></i>
                        @if($t->photo_url)
                            <img src="{{ $t->photo_url }}" alt="{{ $t->name }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;display:inline-block;vertical-align:middle;margin-right:0.5rem;">
                        @else
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--hm-primary-light);color:var(--hm-primary);display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;vertical-align:middle;margin-right:0.5rem;">{{ strtoupper(substr($t->name, 0, 2)) }}</div>
                        @endif
                        <strong>{{ $t->name }}</strong>
                        @if($t->role)<div class="small text-muted" style="margin-left:46px;">{{ $t->role }}</div>@endif
                    </td>
                    <td>
                        <div class="text-warning">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $t->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                    </td>
                    <td><small>{{ Str::limit($t->content, 60) }}</small></td>
                    <td class="text-center">
                        <div class="custom-control custom-switch d-inline-block">
                            <input type="checkbox" class="custom-control-input toggle-switch" 
                                   id="switch_{{ $t->id }}" data-url="{{ route('admin.testimonials.toggle', $t->id) }}" 
                                   {{ $t->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="switch_{{ $t->id }}"></label>
                        </div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary btn-edit" 
                                data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-role="{{ $t->role }}" 
                                data-content="{{ $t->content }}" data-rating="{{ $t->rating }}" 
                                data-email="{{ $t->email ?? '' }}"
                                data-bs-toggle="modal" data-bs-target="#modalEdit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.testimonials.destroy', $t->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $t->name }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum depoimento cadastrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal ADD -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.testimonials.store') }}" method="POST" class="ajax-form">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Novo Depoimento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome do Cliente *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email <small class="text-muted">(para vincular ao cliente cadastrado)</small></label>
                        <input type="email" name="email" class="form-control" placeholder="email@exemplo.com">
                    </div>
                    <div class="mb-3">
                        <label>Função ou Veículo</label>
                        <input type="text" name="role" class="form-control" placeholder="Ex: Dono de Golf GTI">
                    </div>
                    <div class="mb-3">
                        <label>Avaliação (Estrelas)</label>
                        <select name="rating" class="form-control">
                            <option value="5" selected>5 Estrelas</option>
                            <option value="4">4 Estrelas</option>
                            <option value="3">3 Estrelas</option>
                            <option value="2">2 Estrelas</option>
                            <option value="1">1 Estrela</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Depoimento *</label>
                        <textarea name="content" class="form-control" rows="3" required maxlength="300"></textarea>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="is_active" id="t_active" value="1" checked>
                        <label class="custom-control-label" for="t_active">Ativo no site</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT -->
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdit" method="POST" class="ajax-form">
                @csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title">Editar Depoimento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nome do Cliente *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email <small class="text-muted">(para vincular ao cliente cadastrado)</small></label>
                        <input type="email" name="email" id="edit_email" class="form-control" placeholder="email@exemplo.com">
                    </div>
                    <div class="mb-3">
                        <label>Função ou Veículo</label>
                        <input type="text" name="role" id="edit_role" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Avaliação (Estrelas)</label>
                        <select name="rating" id="edit_rating" class="form-control">
                            <option value="5">5 Estrelas</option>
                            <option value="4">4 Estrelas</option>
                            <option value="3">3 Estrelas</option>
                            <option value="2">2 Estrelas</option>
                            <option value="1">1 Estrela</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Depoimento *</label>
                        <textarea name="content" id="edit_content" class="form-control" rows="3" required maxlength="300"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(function(){
    // Setup Edit Modal
    $('.btn-edit').click(function(){
        $('#formEdit').attr('action', '{{ url('admin/testimonials') }}/' + $(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_role').val($(this).data('role'));
        $('#edit_rating').val($(this).data('rating'));
        $('#edit_content').val($(this).data('content'));
    });

    // Sortable
    var el = document.getElementById('sortable');
    if (el) {
        new window.Sortable(el, {
            animation: 150,
            ghostClass: 'bg-light',
            handle: '.fa-grip-vertical',
            onEnd: function () {
                var order = [];
                $('#sortable tr').each(function(){ order.push($(this).data('id')); });
                $.post('{{ route('admin.testimonials.reorder') }}', { order: order })
                 .done(function(res) { if(res.success) HMToast.success('Ordem atualizada!'); });
            }
        });
    }
});
</script>
@endsection
