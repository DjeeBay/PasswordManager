<?php

namespace App\Http\Requests\Keepass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateMultipleKeepassesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->is_admin || (Auth::user()->can('create keepass') && Auth::user()->categories->where('id', $this->category_id)->first());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'keepasses' => 'array:required',
            'keepasses.*.is_folder' => 'boolean',
            'keepasses.*.login' => 'string|nullable',
            'keepasses.*.url' => 'string|nullable',
            'keepasses.*.notes' => 'string|nullable',
            'keepasses.*.icon_id' => 'numeric|nullable',
            'keepasses.*.parent_id' => 'numeric|nullable',
        ];
    }
}
