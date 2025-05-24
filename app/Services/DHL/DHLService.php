<?php

namespace App\Services\DHL;

class DHLService
{
    private DHLInterface $dhl;

    public function __construct(DHLInterface $dhl)
    {
        $this->dhl = $dhl;
    }

    public function shipment($order, $data)
    {
        return $this->dhl->shipment($order, $data);
    }

    public function rate($data)
    {
        return $this->dhl->rate($data);
    }

    public function details($id)
    {
        return $this->dhl->details($id);
    }

    public function validate($cityName, $countryCode)
    {
        return $this->dhl->validate($cityName, $countryCode);
    }

}
