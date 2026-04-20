<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'id' => null,
    'multiple' => false,
    'required' => false,
    'acceptedFileTypes' => 'image/*',
    'maxFileSize' => '2MB',
    'value' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name',
    'id' => null,
    'multiple' => false,
    'required' => false,
    'acceptedFileTypes' => 'image/*',
    'maxFileSize' => '2MB',
    'value' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $id = $id ?? $name;
    $uid = 'fp_' . str_replace(['-', '.', '[', ']'], '_', $id) . '_' . uniqid();
    $fileTypes = array_map('trim', explode(',', $acceptedFileTypes));

    // Preparar source para FilePond server.load
    // Se for URL externa (http), usar direto. Se for path local, limpar barra inicial.
    $loadSource = null;
    if ($value) {
        if (str_starts_with($value, 'http')) {
            $loadSource = $value;
        } else {
            $loadSource = ltrim($value, '/');
        }
    }
?>

<div class="filepond-container">
    <input type="file"
           id="<?php echo e($id); ?>"
           name="<?php echo e($name); ?>"
           <?php echo e($multiple ? 'multiple' : ''); ?>

           <?php echo e($required ? 'required' : ''); ?>>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input_<?php echo e($uid); ?> = document.getElementById(<?php echo json_encode($id, 15, 512) ?>);
        if (!input_<?php echo e($uid); ?> || input_<?php echo e($uid); ?>.__filepond_init) return;
        input_<?php echo e($uid); ?>.__filepond_init = true;

        var opts_<?php echo e($uid); ?> = {
            allowMultiple: <?php echo e($multiple ? 'true' : 'false'); ?>,
            maxFileSize: <?php echo json_encode($maxFileSize, 15, 512) ?>,
            acceptedFileTypes: <?php echo json_encode($fileTypes, 15, 512) ?>,
            <?php if($loadSource): ?>
            files: [{
                source: <?php echo json_encode($loadSource, 15, 512) ?>,
                options: { type: 'local' }
            }],
            <?php endif; ?>
        };

        var pond_<?php echo e($uid); ?> = FilePond.create(input_<?php echo e($uid); ?>, opts_<?php echo e($uid); ?>);

        var fieldName_<?php echo e($uid); ?> = <?php echo json_encode($name, 15, 512) ?>;
        pond_<?php echo e($uid); ?>.on('removefile', function() {
            if (pond_<?php echo e($uid); ?>.getFiles().length === 0) {
                var form = input_<?php echo e($uid); ?>.closest('form');
                if (!form) return;
                var cf = form.querySelector('input[name="' + fieldName_<?php echo e($uid); ?> + '_clear"]');
                if (!cf) {
                    cf = document.createElement('input');
                    cf.type = 'hidden';
                    cf.name = fieldName_<?php echo e($uid); ?> + '_clear';
                    form.appendChild(cf);
                }
                cf.value = '1';
            }
        });
        pond_<?php echo e($uid); ?>.on('addfile', function() {
            var form = input_<?php echo e($uid); ?>.closest('form');
            if (!form) return;
            var cf = form.querySelector('input[name="' + fieldName_<?php echo e($uid); ?> + '_clear"]');
            if (cf) cf.value = '0';
        });
    });
</script>
<?php /**PATH G:\Tudo\HOME_MECHANIC\resources\views\components\filepond.blade.php ENDPATH**/ ?>