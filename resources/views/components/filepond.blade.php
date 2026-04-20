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
    $uid = 'fp_' . str_replace(['-', '.', '[', ']'], '_', $id) . '_' . uniqid();
@endphp

<div class="filepond-container">
    <input type="file"
           id="{{ $id }}"
           name="{{ $name }}"
           {{ $multiple ? 'multiple' : '' }}
           {{ $required ? 'required' : '' }}>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input_{{ $uid }} = document.getElementById(@json($id));
        if (!input_{{ $uid }} || input_{{ $uid }}.__filepond_init) return;
        input_{{ $uid }}.__filepond_init = true;

        var opts_{{ $uid }} = {
            allowMultiple: {{ $multiple ? 'true' : 'false' }},
            maxFileSize: @json($maxFileSize),
            acceptedFileTypes: @json(array_map('trim', explode(',', $acceptedFileTypes))),
        };

        @if($value)
        opts_{{ $uid }}.files = [{
            source: @json($value),
            options: { type: 'local' }
        }];
        @endif

        var pond_{{ $uid }} = FilePond.create(input_{{ $uid }}, opts_{{ $uid }});

        var fieldName_{{ $uid }} = @json($name);
        pond_{{ $uid }}.on('removefile', function() {
            if (pond_{{ $uid }}.getFiles().length === 0) {
                var form = input_{{ $uid }}.closest('form');
                if (!form) return;
                var cf = form.querySelector('input[name="' + fieldName_{{ $uid }} + '_clear"]');
                if (!cf) {
                    cf = document.createElement('input');
                    cf.type = 'hidden';
                    cf.name = fieldName_{{ $uid }} + '_clear';
                    form.appendChild(cf);
                }
                cf.value = '1';
            }
        });
        pond_{{ $uid }}.on('addfile', function() {
            var form = input_{{ $uid }}.closest('form');
            if (!form) return;
            var cf = form.querySelector('input[name="' + fieldName_{{ $uid }} + '_clear"]');
            if (cf) cf.value = '0';
        });
    });
</script>
