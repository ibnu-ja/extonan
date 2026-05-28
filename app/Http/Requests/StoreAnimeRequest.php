<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.en' => 'nullable|string|max:255',
            'title.id' => 'nullable|string|max:255',
            'title.romaji' => 'required|string|max:255',
            'title.native' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'nullable|string|max:10000',
            'description.id' => 'nullable|string|max:10000',
            'anilist_id' => 'nullable|integer|min:1',
            'metadata' => 'required|array',
            'metadata.coverImage' => 'required|array',
            'metadata.genres' => 'nullable|array',
            'metadata.tags' => 'nullable|array',
            'metadata.bannerImage' => 'nullable|string|max:2048',
            'is_published' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => filter_var($this->is_published, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }
}
