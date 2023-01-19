<?php

namespace App\Http\Requests\Keepass;

use App\Models\PrivateCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SavePrivateKeepassRequest extends FormRequest
{
    public function authorize()
    {
        return PrivateCategory::where([
            ['owner_id', '=', Auth::user()->id],
            ['id', '=', $this->category_id],
        ])->exists();
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
            'keepass.parent_id' => 'numeric|nullable',
        ];
    }
}
