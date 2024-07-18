<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->website);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:191',
            'url' => 'sometimes|required|url|max:191|'.Rule::unique('websites')->ignore($this->website),
            'description' => 'nullable|string|max:2000',
            'category_ids' =>'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id', 
        ];
    }
}
