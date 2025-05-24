<?php

namespace Modules\Shipping\Integrations\Pnm\Addresses;

trait Addresses
{
    private $addressRoute = "address_books";

    public function createAddress(
        string $personName, 
        string $phone, 
        string $email, 
        string $countryCode, 
        string $cityName, 
        int $postCode, 
        string $lineOne
        )
    {
        return $this->post($this->addressRoute, [
            'person_name' => $personName,
            'company_name' => config('setting.app_name.' . 'en'),
            'phone' => $phone,
            'email' => $email,
            'line_1' =>  substr($lineOne,0,15),
            'city' => $cityName,
            'post_code' => $postCode,
            'country_code' => $countryCode,
        ]);
    }
}
