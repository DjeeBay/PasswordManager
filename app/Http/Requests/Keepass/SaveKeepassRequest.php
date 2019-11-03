<?php

namespace App\Http\Requests\Keepass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveKeepassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $edit = $this->keepass && array_key_exists('id', $this->keepass) && $this->keepass['id'];

        return Auth::user()->is_admin || (Auth::user()->can($edit ? 'edit keepass' : 'create keepass') && Auth::user()->categories->where('id', $this->category_id)->first());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'keepass' => 'array|required',
            'keepass.is_folder' => 'boolean',
            'keepass.login' => 'string|nullable',
            'keepass.url' => 'string|nullable',
            'keepass.notes' => 'string|nullable',
            'keepass.icon_id' => 'numeric|nullable',
        ];
    }
}
