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
            'name_real' => 'string|nullable',
            'name_trans' => 'string|nullable',
            'catalog' => 'string|nullable',
            'barcode' => 'string|nullable',
            'classification' => 'string|nullable',
            'release_date' => 'date|nullable',
            'discs' => 'array|nullable',
            'media_format' => 'string|nullable',
            'desc' => 'string|nullable',
            'event_id' => 'nullable|numeric|exists:events,id',
            'roles' => 'nullable|array|min:1',
            'orgs' => 'nullable|array|min:1',
            'roles.composers' => 'nullable|array',
            'roles.performers' => 'nullable|array',
            'roles.lyricists' => 'nullable|array',
            'roles.arrangers' => 'nullable|array',
            'roles.composers.*' => 'sometimes|numeric|exists:artists,id',
            'roles.performers.*' => 'sometimes|numeric|exists:artists,id',
            'roles.lyricists.*' => 'sometimes|numeric|exists:artists,id',
            'roles.arrangers.*' => 'sometimes|numeric|exists:artists,id',
            'orgs.label' => 'nullable|array',
            'orgs.publisher' => 'nullable|array',
            'orgs.distributor' => 'nullable|array',
            'orgs.manufacturer' => 'nullable|array',
            'orgs.composers.*' => 'sometimes|numeric|exists:organizations,id',
            'orgs.performers.*' => 'sometimes|numeric|exists:organizations,id',
            'orgs.lyricists.*' => 'sometimes|numeric|exists:organizations,id',
            'orgs.arrangers.*' => 'sometimes|numeric|exists:organizations,id',
        ];
    }
}
