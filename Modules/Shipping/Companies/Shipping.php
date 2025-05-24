<?php

namespace Modules\Shipping\Companies;

use Modules\Area\Entities\Country;
use Setting;

class Shipping
{
    public function getCompanyByType($request = null, $address = null)
    {
        $country = null;
        $company = null;
        
        if($request && $request->country_id){

            $country = Country::active()->findOrFail($request->country_id);

            foreach(config("shipping.supported_companies") as $shippingCompany){

                if(in_array($country->id, (array)Setting::get("shiping.{$shippingCompany}.countries"))){
                    $shippingCompany = 'Modules\Shipping\Companies\\'.ucfirst($shippingCompany);
                    $company = new $shippingCompany;
                    break;
                }
            }

        }elseif($address){

            $country = Country::active()->findOrFail(isset($address->json_data['country_id']) ? $address->json_data['country_id'] : optional($address->state)->country_id);
            $shippingCompany = 'Modules\Shipping\Companies\\'.ucfirst($address->address_type);
            $company = new $shippingCompany;
        }
        
        if(!$company)
            $company = new Local;

        $company->country = $country;
        return $company;
    }
}
