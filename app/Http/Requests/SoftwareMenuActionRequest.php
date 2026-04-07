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
        
        // Check if action is an array (multi-select from resource mode)
        $actionRules = [
            'required',
            function ($attribute, $value, $fail) use ($softwareMenuActionId) {
                // Handle both array and string values
                $actions = is_array($value) ? $value : [$value];
                
                // Validate each action is valid using Model constants
                $validActions = \App\Models\SoftwareMenuAction::ACTIONS;
                foreach ($actions as $action) {
                    if (!in_array($action, $validActions)) {
                        $fail('Action must be one of: ' . implode(', ', $validActions) . '.');
                        return;
                    }
                    
                    // Check uniqueness for each action (only in create mode)
                    if (!$softwareMenuActionId) {
                        $exists = \App\Models\SoftwareMenuAction::where('software_menu_id', $this->software_menu_id)
                            ->where('action', $action)
                            ->exists();
                        
                        if ($exists) {
                            $fail("The action '{$action}' already exists for the selected menu.");
                            return;
                        }
                    }
                }
            }
        ];

        return [
            'software_menu_id' => 'required|exists:software_menus,id',
            'action' => $actionRules,
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
            'route.required' => 'Route is required.',
            'route.string' => 'Route must be a valid string.',
            'route.max' => 'Route cannot exceed 255 characters.',
        ];
    }
    
    /**
     * Get the validated action(s) as an array
     * @return array
     */
    public function getActions(): array
    {
        $action = $this->validated('action');
        return is_array($action) ? $action : [$action];
    }
}
