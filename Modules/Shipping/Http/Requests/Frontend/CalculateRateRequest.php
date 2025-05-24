<?php
namespace Modules\Shipping\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Attribute\Traits\AttributeTrait;

class CalculateRateRequest extends FormRequest
{
    use AttributeTrait;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'username' => 'required|string',
            'mobile' => 'required|string',
        ];

        $this->validationWithAttributes($this->request, $rules,'addresses');
        
        return $this->rules;
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
}
