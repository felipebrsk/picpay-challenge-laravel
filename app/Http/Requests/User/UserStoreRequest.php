<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Rules\DocumentRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'document_number' => ['required', new DocumentRule(), 'string'],
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:users,email'],
            'type' => [
                'string',
                'required',
                Rule::in(User::SHOPKEEEPER_TYPE, User::CUSTOMER_TYPE),
            ],
            'password' => [
                'string',
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->symbols()->numbers()->uncompromised(),
            ],
            'password_confirmation' => [
                'required_with:password',
            ],
        ];
    }

    /**
     * The response messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_type.in' => 'The accepted documents for now are cpf and cnpj!',
        ];
    }
}
