<?php

namespace App\Http\Requests;

use App\Models\Website;
use Illuminate\Foundation\Http\FormRequest;

class StoreWebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Website::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'url' => 'required|url|max:191|unique:websites,url',
            'description' => 'nullable|string|max:2000',
            'category_ids' =>'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id', 
        ];
    }
}
