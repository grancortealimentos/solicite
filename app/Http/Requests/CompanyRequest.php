<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        return [
            'is_active' => ['boolean'],
            'code' => [
                'required', 'string', 'max:255',
                Rule::unique('companies', 'code')->ignore($companyId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:50'],
            'cnpj' => [
                'nullable', 'string', 'max:14',
                Rule::unique('companies', 'cnpj')->ignore($companyId),
            ],
            'ie' => ['nullable', 'string', 'max:20'],
            'zip' => ['nullable', 'string', 'max:8'],
            'street' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'complement' => ['nullable', 'string', 'max:255'],
            'geolocation' => ['nullable', 'string', 'max:255'],
        ];
    }
}
