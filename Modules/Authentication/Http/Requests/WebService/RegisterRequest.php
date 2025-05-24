<?php

namespace Modules\Authentication\Http\Requests\WebService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required',
            'calling_code' => 'nullable|numeric',
//           'mobile' => 'required|unique:users,mobile|numeric|digits_between:8,8',
//           'mobile' => 'nullable|unique:users,mobile|numeric',
            'mobile' => ['nullable',
                Rule::unique("users")->where(function ($query) {
                    $query->where("mobile", $this->mobile)
                        ->where("calling_code", $this->calling_code ?? '965');
                }),
                'numeric', 'digits_between:3,20'],
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ];

        if (empty($this->email) && empty($this->mobile)) {
            $rules['email_or_mobile'] = 'required';
        }

        if (!empty($this->mobile)) {
            $rules["firebase_uuid"] = "sometimes|nullable|unique:users,firebase_uuid";
        }

        return $rules;
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
            'name.required' => __('authentication::api.register.validation.name.required'),
            'calling_code.required' => __('authentication::api.register.validation.calling_code.required'),
            'calling_code.numeric' => __('authentication::api.register.validation.calling_code.numeric'),
            'calling_code.max' => __('authentication::api.register.validation.calling_code.max'),
            'mobile.required' => __('authentication::api.register.validation.mobile.required'),
            'mobile.unique' => __('authentication::api.register.validation.mobile.unique'),
            'mobile.numeric' => __('authentication::api.register.validation.mobile.numeric'),
            'mobile.digits_between' => __('authentication::api.register.validation.mobile.digits_between'),
            'email.required' => __('authentication::api.register.validation.email.required'),
            'email.unique' => __('authentication::api.register.validation.email.unique'),
            'email.email' => __('authentication::api.register.validation.email.email'),
            'password.required' => __('authentication::api.register.validation.password.required'),
            'password.min' => __('authentication::api.register.validation.password.min'),
            'password.confirmed' => __('authentication::api.register.validation.password.confirmed'),

            'email_or_mobile.required' => __('authentication::api.register.validation.email_or_mobile.required'),
            'firebase_uuid.unique' => __('authentication::api.register.validation.firebase_id.unique'),
            /*'firebase_id.required' => __('authentication::api.register.validation.firebase_id.required'),
            'firebase_id.unique' => __('authentication::api.register.validation.firebase_id.unique'),*/
        ];

        return $v;
    }
}
