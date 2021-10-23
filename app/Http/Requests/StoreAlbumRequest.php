<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO permissions for store Album
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
            'name' => 'sometimes|string',
            'name_real' => 'string',
            'name_trans' => 'string',
            'catalog' => 'string',
            'barcode' => 'string',
            'classification' => 'string',
            'release_date' => 'date',
            'discs' => 'array',
            'media_format' => 'string',
            'desc' => 'string',
            'roles' => 'nullable|array|min:1',
            'roles.composers' => 'nullable|array',
            'roles.performers' => 'nullable|array',
            'roles.lyricists' => 'nullable|array',
            'roles.arrangers' => 'nullable|array',
            'roles.composers.*' => 'sometimes|numeric|exists:artists,id',
            'roles.performers.*' => 'sometimes|numeric|exists:artists,id',
            'roles.lyricists.*' => 'sometimes|numeric|exists:artists,id',
            'roles.arrangers.*' => 'sometimes|numeric|exists:artists,id',
        ];
    }
}
