<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'menu_id' => 'required|exists:menus,menu_id',
            'url_type' => 'required|in:internal,external,module',
            'target' => 'required|in:_self,_blank,_parent,_top',
            'parent_id' => 'nullable|exists:menu_items,item_id',
            'css_class' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];

        // Multi-language title validation
        $languages = $this->input('languages', []);
        foreach ($languages as $lang) {
            $rules["title.{$lang}"] = 'required_with:languages|string|max:255';
        }

        // URL data validation based on type
        $urlType = $this->input('url_type');
        
        if ($urlType === 'internal' || $urlType === 'external') {
            $rules['url_data.url'] = 'required|string';
            
            // External URL must be valid URL
            if ($urlType === 'external') {
                $rules['url_data.url'] = 'required|url';
            }
        } elseif ($urlType === 'module') {
            $rules['url_data.module'] = 'required|string';
            $rules['url_data.type'] = 'required|string';
            $rules['url_data.id'] = 'nullable|integer';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.*.required_with' => __('menumanagement::admin.title_required'),
            'url_data.url.required' => __('menumanagement::admin.url_required'),
            'url_data.url.url' => __('menumanagement::admin.invalid_url_format'),
            'url_data.module.required' => __('menumanagement::admin.module_required'),
            'url_data.type.required' => __('menumanagement::admin.url_type_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'url_type' => __('menumanagement::admin.url_type'),
            'url_data.url' => __('menumanagement::admin.url'),
            'url_data.module' => __('menumanagement::admin.module'),
            'parent_id' => __('menumanagement::admin.parent_item'),
        ];

        // Add dynamic language attributes
        $languages = $this->input('languages', []);
        foreach ($languages as $lang) {
            $attributes["title.{$lang}"] = __('menumanagement::admin.title') . " ({$lang})";
        }

        return $attributes;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up empty title fields
        if ($this->has('title')) {
            $title = array_filter($this->input('title', []));
            $this->merge(['title' => $title]);
        }

        // Set defaults
        $this->merge([
            'is_active' => $this->input('is_active', true),
            'target' => $this->input('target', '_self'),
        ]);
    }
}