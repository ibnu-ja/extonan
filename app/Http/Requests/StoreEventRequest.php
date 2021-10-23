<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|required',
            'name_real' => 'string|nullable',
            'abbr' => 'string|nullable',
            'desc' => 'string|nullable',
            'held_from' => 'date|nullable',
            'held_to' => 'date|nullable',
        ];
    }
}
