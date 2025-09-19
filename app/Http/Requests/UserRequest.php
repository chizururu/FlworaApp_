<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $action = $this->route()?->getActionMethod() ?? $this->method();

        $rules = [
            'name' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'string', 'min:8', 'same:password']
        ];

        /// Register
        if ($action === "store") {
            return [
                'name' => $rules['name'],
                'email' => array_merge($rules['email'], ['unique:users,email']),
                'password' => $rules['password'],
                'confirm_password' => $rules['confirm_password'],
            ];
        }
        /// Login
        if ($action === "login") {
            return [
                'email' => $rules['email'],
                'password' => $rules['password']
            ];
        }
        ///

        return [];
    }

    /**
     * Get the message errors in each validation rule.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi',
            'email' => 'Format email tidak valid',
            'unique' => ':Attribute sudah terdaftar',
            'min' => ':Attribute kurang dari :min karakter',
            'max' => ':Attribute tidak boleh lebih dari :max karakter',
            'same' => ':Attribute harus sama',
        ];
    }
}
