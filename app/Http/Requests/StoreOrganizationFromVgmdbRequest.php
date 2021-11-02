<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationFromVgmdbRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO permissions for store OrganizationVgmdb
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
            'names.ja' => 'required_without_all:names.en,ja-latn',
            'names.en' => 'required_without_all:names.ja,ja-latn',
            'names.ja-latn' => 'required_without_all:names.ja,en',
        ];
    }
}
