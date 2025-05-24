<?php

namespace Modules\Wrapping\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest {
    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */

    public function rules() {
        switch ( $this->getMethod() ) {

            // handle creates
            case 'post':
            case 'POST':

            return [
                'title.*'             => 'required',
                'price'               => 'required|numeric|min:1',
                'sku'                 => 'nullable',
            ];

            //handle updates
            case 'put':
            case 'PUT':
            return [
                'title.*'             => 'required',
                'price'               => 'required|numeric|min:1',
                'sku'                 => 'nullable',
            ];
        }
    }

    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */

    public function authorize() {
        return true;
    }

    public function messages() {
        $v = [
            'price.required'                => __( 'wrapping::dashboard.cards.validation.price.required' ),
            'qty.integer'                   => __( 'wrapping::dashboard.cards.validation.qty.numeric' ),
            'price.numeric'                 => __( 'wrapping::dashboard.cards.validation.price.numeric' ),
            'sku.required'                  => __( 'wrapping::dashboard.cards.validation.sku.required' ),
        ];

        foreach ( config( 'laravellocalization.supportedLocales' ) as $key => $value ) {

            $v['title.'.$key.'.required']  = __( 'wrapping::dashboard.cards.validation.title.required' ).' - '.$value['native'].'';

        }

        return $v;

    }
}
