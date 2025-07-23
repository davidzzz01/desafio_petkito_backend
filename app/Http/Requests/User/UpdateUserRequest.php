<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $userId = $this->route('id');
        return [
            'name' => 'sometimes|string|max:191',
            'email' => "sometimes|email|unique:users,email,$userId",
            'password' => 'nullable|min:6|confirmed',
            'is_admin' => 'sometimes|boolean',
        ];
    }
}
