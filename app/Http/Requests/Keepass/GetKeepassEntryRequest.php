<?php

namespace App\Http\Requests\Keepass;

use App\Models\Keepass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GetKeepassEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $keepassEntry = Keepass::findOrFail($this->id);

        return Auth::user()->categories->where('id', $keepassEntry->category_id)->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
