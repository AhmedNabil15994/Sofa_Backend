<?php
namespace Modules\Shipping\Integrations\Pnm\Requests;

Trait fetchCities
{
    private $citiesRoute = "cities";
    
    public function cities($country_code,$q = "")
    {
        return $this->get($this->citiesRoute , [
            'country_code' => $country_code,
            'q' => $q,
        ]);
    }
}
