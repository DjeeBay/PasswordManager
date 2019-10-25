<?php

namespace App\Http\Requests\Keepass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ImportKeepassController extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->is_admin || Auth::user()->can('import keepass');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'xml' => 'required|file|mimes:xml',
            'category_name' => 'string|required|unique:categories,name,'.$this->category_name
        ];
    }
}
