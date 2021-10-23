<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtistRequest extends FormRequest
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
            'names' => 'required|string',
            'name_real' => 'nullable|string',
            'name_trans' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'birthplace' => 'nullable|string',
            'meta' => 'nullable|array|min:1',
            'desc' => 'nullable|string',
            'sex' => 'nullable|alpha',
        ];
    }
}
