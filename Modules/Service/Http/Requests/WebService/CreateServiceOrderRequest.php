<?php

namespace Modules\Service\Http\Requests\WebService;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceOrderRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|max:500',
            'files' => 'nullable|array',
            'files.*' => 'mimes:pdf,jpeg,png,jpg,gif,svg|max:5000',
        ];

        if (auth('api')->guest()) {
            $rules['user_token'] = 'required';
            $rules['contact_info.name'] = 'nullable|string|max:200';
            $rules['contact_info.email'] = 'nullable|email|max:200';
            $rules['contact_info.mobile'] = 'required';
        }

        return $rules;
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    /* public function withValidator($validator)
{
$validator->after(function ($validator) {

if (auth('api')->guest()) {
return $validator->errors()->add(
'user_id', __('order::api.orders.validations.user_id.required')
);
}

});
return true;
} */
}
