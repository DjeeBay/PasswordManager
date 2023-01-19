<?php

namespace App\Http\Requests\Keepass;

use App\Models\PrivateCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeletePrivateKeepassRequest extends FormRequest
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
            //
        ];
    }
}
