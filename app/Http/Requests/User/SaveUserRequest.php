<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->is_admin || Auth::user()->can('create user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|unique:users,email,'.$this->user ?? null,
            'name' => 'required|string|unique:users,name,'.$this->user ?? null,
            'firstname' => 'string|nullable',
            'lastname' => 'string|nullable',
            'is_admin' => 'boolean|nullable',
            'password' => 'string|min:8|nullable'.(!$this->user ? '|required' : null),
            'password_confirmation' => 'string|nullable',
        ];
    }
}
