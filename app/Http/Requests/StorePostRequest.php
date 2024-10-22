<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
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
            'title' => 'required|max:255',
            'description' => 'required',
            'metadata.post_type' => [Rule::enum(PostType::class), 'required'],
            'metadata.ep_no' => 'string',
            'links' => 'array|nullable',
            'thumbnail_item' => 'nullable',
            'is_published' => 'required|boolean'
        ];
    }
}
