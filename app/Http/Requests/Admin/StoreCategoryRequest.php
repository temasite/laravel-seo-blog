<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name'));
        $description = trim((string) $this->input('description'));
        $slug = $this->input('slug');

        $this->merge([
            'name' => $name,
            'description' => $description !== '' ? $description : null,
            'slug' => Category::normalizeSlug(is_string($slug) ? $slug : null, $name),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('categories.create') === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug'),
            ],
            'banner' => [
                'nullable',
                'bail',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:ratio=16/9',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'banner.dimensions' => 'The banner must have a 16:9 aspect ratio.',
        ];
    }
}
