<?php

namespace App\Modules\Gallery\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GalleryCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('gallery_categories', 'slug')->ignore($categoryId)
            ],
            'sort_order' => 'nullable|integer|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.max' => 'O nome da categoria não pode ter mais de 255 caracteres.',
            'slug.unique' => 'Este slug já está sendo usado por outra categoria.',
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífens.',
            'sort_order.integer' => 'A ordem deve ser um número inteiro.',
            'sort_order.min' => 'A ordem deve ser maior ou igual a zero.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitizar campos de texto
        $this->merge([
            'name' => $this->sanitizeText($this->input('name'))
        ]);

        // Converter sort_order para inteiro
        if ($this->has('sort_order') && $this->input('sort_order') !== null) {
            $this->merge(['sort_order' => (int) $this->input('sort_order')]);
        }
    }

    /**
     * Sanitizar texto removendo tags HTML
     */
    private function sanitizeText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        return trim(strip_tags($text));
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Se não foi fornecido slug, será gerado automaticamente no model
        if (empty($validated['slug'])) {
            unset($validated['slug']);
        }

        return $validated;
    }
}