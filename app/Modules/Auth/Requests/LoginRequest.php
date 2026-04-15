<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'string'
            ],
            'password' => [
                'required',
                'string',
                'min:1', // Não validar tamanho mínimo aqui para não dar dicas
                'max:255'
            ],
            'remember' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.max' => 'A senha deve ter no máximo 255 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'e-mail',
            'password' => 'senha',
            'remember' => 'lembrar-me'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitizar e normalizar entrada
        $this->merge([
            'email' => $this->sanitizeEmail($this->input('email', '')),
            'password' => $this->sanitizePassword($this->input('password', '')),
            'remember' => $this->boolean('remember', false)
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Verificar rate limiting antes da validação completa
            $key = 'login.' . $this->ip();
            
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                $validator->errors()->add('email', 
                    "Muitas tentativas de login. Tente novamente em {$seconds} segundos."
                );
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Dados inválidos. Verifique os campos e tente novamente.',
                'errors' => $validator->errors()
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }

    /**
     * Sanitizar e-mail
     */
    private function sanitizeEmail(string $email): string
    {
        // Remover tags HTML e espaços
        $email = strip_tags(trim($email));
        
        // Converter para minúsculas
        $email = strtolower($email);
        
        // Remover caracteres perigosos mas manter formato de e-mail
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        return $email ?: '';
    }

    /**
     * Sanitizar senha
     */
    private function sanitizePassword(string $password): string
    {
        // Remover apenas tags HTML, manter outros caracteres
        // pois senhas podem ter caracteres especiais válidos
        $password = strip_tags($password);
        
        // Não fazer trim na senha pois espaços podem ser intencionais
        
        return $password;
    }

    /**
     * Get sanitized and validated data
     */
    public function getSanitizedData(): array
    {
        return [
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'remember' => $this->boolean('remember', false)
        ];
    }

    /**
     * Verificar se request está sendo feito via AJAX
     */
    public function isAjax(): bool
    {
        return $this->ajax() || $this->wantsJson() || $this->expectsJson();
    }

    /**
     * Obter informações de contexto para logs
     */
    public function getContextInfo(): array
    {
        return [
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'referer' => $this->header('referer'),
            'timestamp' => now()->toISOString(),
            'method' => $this->method(),
            'url' => $this->fullUrl()
        ];
    }
}