<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SoftwareMenuActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $softwareMenuActionId = $this->route('internal_link')?->id;

        return [
            'software_menu_id' => 'required|exists:software_menus,id',
            'action' => [
                'required',
                'in:view,create,edit,delete',
                Rule::unique('software_menu_actions')->where(function ($query) {
                    return $query->where('software_menu_id', $this->software_menu_id);
                })->ignore($softwareMenuActionId),
            ],
            'route' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'software_menu_id.required' => 'Menu is required.',
            'software_menu_id.exists' => 'Selected menu does not exist.',
            'action.required' => 'Action is required.',
            'action.in' => 'Action must be one of: view, create, edit, delete.',
            'action.unique' => 'This action already exists for the selected menu.',
            'route.required' => 'Route is required.',
            'route.string' => 'Route must be a valid string.',
            'route.max' => 'Route cannot exceed 255 characters.',
        ];
    }
}
