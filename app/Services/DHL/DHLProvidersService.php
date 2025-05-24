<?php

namespace App\Services\DHL;

class DHLProvidersService
{
    public static function shipment($order,$data)
    {
        $service = new DHLService(new DHLProvider);
        return $service->shipment($order,$data);
    }

    public static function rate($data)
    {
        $service = new DHLService(new DHLProvider);
        return $service->rate($data);
    }

    public static function details($id)
    {
        $service = new DHLService(new DHLProvider);
        return $service->details($id);
    }

    public static function validate($cityName,$countryCode)
    {
        $service = new DHLService(new DHLProvider);
        return $service->validate($cityName,$countryCode);
    }
}
