<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShinraiPostRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'description' => 'required',
            'metadata.post_type' => [Rule::enum(ShinraiPostType::class), 'required'],
            'metadata.vgmdb_data' => 'array|nullable',
            'resources' => 'array|nullable',
            'thumbnail_item' => 'nullable',
            'is_published' => 'required|boolean'
        ];
    }
}
