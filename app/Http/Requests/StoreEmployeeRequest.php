<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // or your logic
    }

    public function rules()
    {
        // determine if this is an update and get the user id from route
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // if update, exclude current user id
                $userId ? Rule::unique('users', 'email')->ignore($userId) : 'unique:users,email'
            ],
            'password' => $userId ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            // roles as array of role names (since in edit form we send role names)
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'salary' => 'nullable|numeric',
            'joining_date' => 'nullable|date',
            'next_increment_date' => 'nullable|date',
            'relieving_date' => 'nullable|date',
            'photo' => 'nullable|file|image|max:5120', // 5MB
            'kyc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ];
    }
}
