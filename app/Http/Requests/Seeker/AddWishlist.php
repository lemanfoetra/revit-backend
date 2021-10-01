<?php

namespace App\Http\Requests\Seeker;

use Illuminate\Foundation\Http\FormRequest;

class AddWishlist extends FormRequest
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
            'user_id'       =>  'required',
            'bengkel_id'    =>  'required'
        ];
    }
}
