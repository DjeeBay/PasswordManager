<?php

namespace App\Http\Requests\Keepass;

use App\Models\PrivateCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateMultiplePrivateKeepassesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'keepasses' => 'array|required',
            'keepasses.*.is_folder' => 'boolean',
            'keepasses.*.login' => 'string|nullable',
            'keepasses.*.url' => 'string|nullable',
            'keepasses.*.notes' => 'string|nullable',
            'keepasses.*.icon_id' => 'numeric|nullable',
            'keepasses.*.parent_id' => 'numeric|nullable',
        ];
    }
}
