@props([
    'name',
    'id' => null,
    'multiple' => false,
    'required' => false,
    'acceptedFileTypes' => 'image/*',
    'maxFileSize' => '2MB',
    'value' => null,
])

@php
    $id = $id ?? $name;
@endphp

<div class="filepond-container" wire:ignore>
    <input type="file" 
           id="{{ $id }}" 
           name="{{ $name }}" 
           {{ $multiple ? 'multiple' : '' }} 
           {{ $required ? 'required' : '' }}
           data-allow-reorder="true"
           data-max-file-size="{{ $maxFileSize }}"
           data-accepted-file-types="{{ $acceptedFileTypes }}">
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('{{ $id }}');
        if (!input) return;

        const pond = FilePond.create(input, {
            @if($value)
            files: [
                {
                    source: '{{ $value }}',
                    options: {
                        type: 'local'
                    }
                }
            ],
            @endif
        });

        // Garantir que o valor seja repassado se o formulário for submetido
        pond.on('processfile', (error, file) => {
            if (!error) {
                // O FilePond já injeta um hidden input com o UUID retornado pelo 'onload' do server
                console.log('Arquivo processado:', file.serverId);
            }
        });
    });
</script>
