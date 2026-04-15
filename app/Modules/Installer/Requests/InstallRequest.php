<?php

namespace App\Modules\Installer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Instalação é pública
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Configurações do banco de dados
            'db_host' => 'required|string|max:255',
            'db_port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'db_user' => 'required|string|max:32',
            'db_password' => 'nullable|string|max:255',

            // Dados do administrador
            'admin_name' => 'required|string|max:255|min:2',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
            'admin_password_confirmation' => 'required|string',

            // Dados da empresa (opcionais - serão preenchidos automaticamente se não fornecidos)
            'company_name' => 'nullable|string|max:255|min:2',
            'company_description' => 'nullable|string|max:500',
            'system_url' => 'nullable|url|max:255',

            // Termos de uso
            'terms_accepted' => 'required|accepted'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Banco de dados
            'db_host.required' => 'O host do banco de dados é obrigatório.',
            'db_port.integer' => 'A porta deve ser um número válido.',
            'db_port.min' => 'A porta deve ser maior que 0.',
            'db_port.max' => 'A porta deve ser menor que 65536.',
            'db_name.required' => 'O nome do banco de dados é obrigatório.',
            'db_name.regex' => 'O nome do banco deve conter apenas letras, números e underscore.',
            'db_user.required' => 'O usuário do banco de dados é obrigatório.',

            // Administrador
            'admin_name.required' => 'O nome do administrador é obrigatório.',
            'admin_name.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'admin_email.required' => 'O e-mail do administrador é obrigatório.',
            'admin_email.email' => 'Digite um e-mail válido.',
            'admin_password.required' => 'A senha do administrador é obrigatória.',
            'admin_password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'admin_password.confirmed' => 'A confirmação da senha não confere.',
            'admin_password_confirmation.required' => 'A confirmação da senha é obrigatória.',

            // Empresa (agora opcionais)
            'company_name.min' => 'O nome da empresa deve ter pelo menos 2 caracteres.',
            'company_description.max' => 'A descrição deve ter no máximo 500 caracteres.',
            'system_url.url' => 'Digite uma URL válida (ex: https://suaempresa.com.br).',

            // Termos
            'terms_accepted.required' => 'Você deve aceitar os termos de uso.',
            'terms_accepted.accepted' => 'Você deve aceitar os termos de uso.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'db_host' => 'host do banco',
            'db_port' => 'porta do banco',
            'db_name' => 'nome do banco',
            'db_user' => 'usuário do banco',
            'db_password' => 'senha do banco',
            'admin_name' => 'nome do administrador',
            'admin_email' => 'e-mail do administrador',
            'admin_password' => 'senha do administrador',
            'admin_password_confirmation' => 'confirmação da senha',
            'company_name' => 'nome da empresa',
            'company_description' => 'descrição da empresa',
            'system_url' => 'URL do sistema',
            'terms_accepted' => 'termos de uso'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitizar campos de entrada
        $this->merge([
            'db_host' => strip_tags(trim($this->db_host ?? '')),
            'db_name' => strip_tags(trim($this->db_name ?? '')),
            'db_user' => strip_tags(trim($this->db_user ?? '')),
            'admin_name' => strip_tags(trim($this->admin_name ?? '')),
            'admin_email' => strip_tags(trim(strtolower($this->admin_email ?? ''))),
            'company_name' => strip_tags(trim($this->company_name ?? '')),
            'company_description' => strip_tags(trim($this->company_description ?? '')),
            'system_url' => trim($this->system_url ?? '')
        ]);

        // Garantir que a URL tenha protocolo se fornecida
        if ($this->system_url && !preg_match('/^https?:\/\//', $this->system_url)) {
            $this->merge([
                'system_url' => 'https://' . $this->system_url
            ]);
        }

        // Definir porta padrão se não informada
        if (empty($this->db_port)) {
            $this->merge(['db_port' => 3306]);
        }
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
}