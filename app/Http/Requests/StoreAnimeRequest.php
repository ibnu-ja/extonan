<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'title.romaji' => 'required|string',
            'title.native' => 'required|string',
            'anilist_id' => 'numeric|nullable',
            'description' => 'required',
            'metadata' => 'required',
            'is_published' => 'required|boolean'
        ];
    }
}
