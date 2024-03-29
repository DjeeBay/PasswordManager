<?php

namespace App\Http\Requests\Keepass;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoritesPrivateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'keepasses' => 'array|required',
            'keepasses.*.id' => 'integer|required',
        ];
    }
}
