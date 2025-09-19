<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeviceRequest extends Request
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
            'name' => ['required', 'string', 'min:4', 'max:20'],
            'mac_address' => ['required', 'string', 'min:17', 'max:17'],
            'sector_id' => ['required', 'exists:sectors,id'],
        ];

        /// Store
        if ($action === "store") {
            return [
                'name' => $rules['name'],
                'mac_address' => $rules['mac_address'],
                'sector_id' => $rules['sector_id'],
            ];
        }

        /// Update hanya nama perangkat dan sektor


        return [

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
            'name.required' => 'Nama perangkat tidak boleh kosong',
            'name.min' => 'Nama perangkat kurang dari :8 karakter',
            'name.max' => 'Nama perangkat tidak boleh lebih dari :max karakter',
            'sector_id.required' => 'Sektor wajib dipilih',
            'sector_id.exists' => 'Sektor tidak terdaftar',
        ];
    }
}
