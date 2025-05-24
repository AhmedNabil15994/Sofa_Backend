<?php

namespace App\Services\DHL;

interface DHLInterface
{
    public function shipment($order,$data);

    public function rate($data);

    public function details($id);

    public function validate($cityName,$countryCode);
}
