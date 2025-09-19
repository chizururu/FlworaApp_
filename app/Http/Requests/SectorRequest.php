<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectorRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:8',
                'max:20',
                Rule::unique('sectors', 'name')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                })
            ]
        ];
    }

    /**
     * Get the message errors in each validation rule.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Nama sektor tidak boleh kosong',
            'unique' => 'Nama sektor sudah terdaftar',
            'min' => 'Nama sektor tidak boleh kurang dari :min karakter',
            'max' => 'Nama sektor lebih dari :max karakter'
        ];
    }
}
