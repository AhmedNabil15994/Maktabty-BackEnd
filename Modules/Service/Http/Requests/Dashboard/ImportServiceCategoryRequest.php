<?php

namespace Modules\Service\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ImportServiceCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title_ar' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return ['title_ar' => __('service::dashboard.service_categories.validation.title.required')];
    }
}
