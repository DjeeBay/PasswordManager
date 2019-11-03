<?php

namespace App\Http\Requests\Icon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveIconRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->is_admin || Auth::user()->can('edit keepass') || Auth::user()->can('create keepass');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'icon' => $this->icon ? 'image|mimes:png|nullable' : 'required|image|mimes:png',
            'title' => 'string|nullable'
        ];
    }
}
