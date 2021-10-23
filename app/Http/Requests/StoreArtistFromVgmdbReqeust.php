<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtistFromVgmdbReqeust extends FormRequest
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
            'link' => 'string',
            'names' => 'required|array|min:1',
            // 'names.en' => 'string|required',
            // 'names.ja' => 'string|nullable',
            'names.ja' => 'required_without_all:names.en',
            'names.en' => 'required_without_all:names.ja',
        ];
    }
}
