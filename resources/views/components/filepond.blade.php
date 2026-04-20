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
    $fileTypes = array_map('trim', explode(',', $acceptedFileTypes));
@endphp

<div class="filepond-wrapper" id="fpw_{{ $uid }}">
    {{-- Preview da imagem existente --}}
    @if($value)
    <div class="fp-existing-preview mb-2" id="fpPreview_{{ $uid }}" style="position:relative; display:inline-block;">
        <img src="{{ $value }}" alt="Imagem atual" style="max-height:120px; max-width:100%; border-radius:6px; border:1px solid #ddd; padding:4px; background:#fff;">
        <button type="button" onclick="fpRemoveExisting_{{ $uid }}()" title="Remover imagem"
            style="position:absolute; top:-6px; right:-6px; width:22px; height:22px; border-radius:50%; background:#dc3545; color:#fff; border:none; cursor:pointer; font-size:12px; line-height:22px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,.3);">
            &times;
        </button>
    </div>
    <input type="hidden" name="{{ $name }}_existing" value="{{ $value }}">
    @endif

    {{-- Area do FilePond --}}
    <div class="filepond-container">
        <input type="file"
               id="{{ $id }}"
               name="{{ $name }}"
               {{ $multiple ? 'multiple' : '' }}
               {{ $required ? 'required' : '' }}>
    </div>
</div>

<script>
    function fpRemoveExisting_{{ $uid }}() {
        var preview = document.getElementById('fpPreview_{{ $uid }}');
        if (preview) preview.style.display = 'none';
        var existing = document.querySelector('#fpw_{{ $uid }} input[name="{{ $name }}_existing"]');
        if (existing) existing.value = '';
        var form = document.getElementById('fpw_{{ $uid }}').closest('form');
        if (form) {
            var cf = form.querySelector('input[name="{{ $name }}_clear"]');
            if (!cf) {
                cf = document.createElement('input');
                cf.type = 'hidden';
                cf.name = '{{ $name }}_clear';
                form.appendChild(cf);
            }
            cf.value = '1';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var input_{{ $uid }} = document.getElementById(@json($id));
        if (!input_{{ $uid }} || input_{{ $uid }}.__filepond_init) return;
        input_{{ $uid }}.__filepond_init = true;

        var opts_{{ $uid }} = {
            allowMultiple: {{ $multiple ? 'true' : 'false' }},
            maxFileSize: @json($maxFileSize),
            acceptedFileTypes: @json($fileTypes),
        };

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
            var preview = document.getElementById('fpPreview_{{ $uid }}');
            if (preview) preview.style.display = 'none';
            var existing = document.querySelector('#fpw_{{ $uid }} input[name="{{ $name }}_existing"]');
            if (existing) existing.value = '';
        });
    });
</script>
