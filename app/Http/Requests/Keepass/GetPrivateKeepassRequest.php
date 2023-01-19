<?php

namespace App\Http\Requests\Keepass;

use App\Models\PrivateCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GetPrivateKeepassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return PrivateCategory::where([
            ['id', '=', $this->private_category_id],
            ['owner_id', '=', Auth::user()->id],
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
