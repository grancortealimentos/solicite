<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PapelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissoes' => ['array'],
            'permissoes.*' => [Rule::exists(Permission::class, 'name')],
        ];
    }
}
