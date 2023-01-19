<?php

namespace App\Http\Requests\Keepass;

use App\Models\Keepass;
use App\Models\PrivateCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GetPrivateKeepassEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $keepassEntry = Keepass::findOrFail($this->id);

        return PrivateCategory::where([
            ['owner_id', '=', Auth::user()->id],
            ['id', '=', $keepassEntry->private_category_id],
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
            //
        ];
    }
}
