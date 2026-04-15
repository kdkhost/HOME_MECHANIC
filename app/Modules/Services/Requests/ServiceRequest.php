<?php

namespace App\Modules\Services\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
        $serviceId = $this->route('service')?->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('services', 'slug')->ignore($serviceId)
            ],
            'description' => 'required|string|max:500',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'cover_image' => 'nullable|string|max:36', // UUID
            'featured' => 'boolean',
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
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'slug.unique' => 'Este slug já está sendo usado por outro serviço.',
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífens.',
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição não pode ter mais de 500 caracteres.',
            'icon.max' => 'O ícone não pode ter mais de 100 caracteres.',
            'cover_image.max' => 'UUID da imagem de capa inválido.',
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
            'description' => $this->sanitizeText($this->input('description')),
            'content' => $this->sanitizeHtml($this->input('content')),
            'icon' => $this->sanitizeText($this->input('icon')),
        ]);

        // Converter valores booleanos
        if ($this->has('featured')) {
            $this->merge(['featured' => $this->boolean('featured')]);
        }

        if ($this->has('active')) {
            $this->merge(['active' => $this->boolean('active')]);
        }

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
     * Sanitizar HTML permitindo apenas tags seguras
     */
    private function sanitizeHtml(?string $html): ?string
    {
        if (!$html) {
            return null;
        }

        // Tags permitidas para conteúdo
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><blockquote>';
        
        return trim(strip_tags($html, $allowedTags));
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