<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumGalleryRequest extends FormRequest
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
            'images' => 'array|nullable',
            'images.*' => 'mimes:jpeg,jpg,png,gif|required|max:5000', //max 5MB
            'imageLabel' => 'array|nullable',
            'imageLabel.*' => 'string|nullable',
        ];
    }
}
