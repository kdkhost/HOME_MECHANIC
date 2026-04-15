<?php

namespace App\Modules\Gallery\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryPhotoRequest extends FormRequest
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
        return [
            'category_id' => 'required|integer|exists:gallery_categories,id',
            'title' => 'required|string|max:255',
            'filename' => 'required|string|max:36', // UUID
            'thumbnail' => 'nullable|string|max:36', // UUID
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'title.required' => 'O título da foto é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'filename.required' => 'A imagem é obrigatória.',
            'filename.max' => 'UUID da imagem inválido.',
            'thumbnail.max' => 'UUID do thumbnail inválido.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
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
            'title' => $this->sanitizeText($this->input('title')),
            'description' => $this->sanitizeText($this->input('description'))
        ]);

        // Converter valores booleanos
        if ($this->has('active')) {
            $this->merge(['active' => $this->boolean('active')]);
        }

        // Converter sort_order para inteiro
        if ($this->has('sort_order') && $this->input('sort_order') !== null) {
            $this->merge(['sort_order' => (int) $this->input('sort_order')]);
        }

        // Converter category_id para inteiro
        if ($this->has('category_id')) {
            $this->merge(['category_id' => (int) $this->input('category_id')]);
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
}