<?php

namespace Modules\Service\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ServiceCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getMethod()) {
            // handle creates
            case 'post':
            case 'POST':

                return [
                    'service_category_id' => 'nullable',
                ];

            //handle updates
            case 'put':
            case 'PUT':
                return [
                    'service_category_id' => 'nullable',
                ];
        }
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
            'service_category_id.required' => __('service::dashboard.service_categories.validation.service_category_id.required'),
            'image.required' => __('service::dashboard.service_categories.validation.image.required'),
            'color.required_if' => __('service::dashboard.service_categories.validation.color.required_if'),
        ];
        foreach (config('laravellocalization.supportedLocales') as $key => $value) {
            $v["title." . $key . ".required"] = __('service::dashboard.service_categories.validation.title.required') . ' - ' . $value['native'] . '';
            $v["title." . $key . ".unique_translation"] = __('service::dashboard.service_categories.validation.title.unique') . ' - ' . $value['native'] . '';
        }
        return $v;
    }
}
