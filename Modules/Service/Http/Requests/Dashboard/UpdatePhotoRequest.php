<?php

namespace Modules\Service\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'image' => 'required|image|max:2048',
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
        $v = [
    
            'image.required' => __('service::dashboard.service_categories.validation.image.required'),
            'image.image' => __('service::dashboard.service_categories.validation.image.image'),
            'image.mimes' => __('service::dashboard.service_categories.validation.image.mimes'),
            'image.max' => __('service::dashboard.service_categories.validation.image.max'),
        ];

        return $v;
    }
}
