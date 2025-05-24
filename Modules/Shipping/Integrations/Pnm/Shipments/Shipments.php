<?php

namespace Modules\Shipping\Integrations\Pnm\Shipments;
use Carbon\Carbon;
use Setting;

trait Shipments
{
    private $shipmentRoute = "shipments";

    public function createShipments( 
        float $actual_weight,
        string $goods_origin_country_code, 
        int $receiver_id, 
        array $items, 
        string $reference_1, 
        string $customs_value_amount
        )
    {
        return $this->post($this->shipmentRoute, [
            "shipping_pnm_date" => Carbon::now()->addDay()->toDateTimeString(),
            "actual_weight" => $actual_weight,
            "items_type" => "parcel",
            "currency_code" => priceWithCurrenciesCode(0 , false , true),
            "goods_origin_country_code" => $goods_origin_country_code,
            "shipper_id" => (int)$this->getShipperId(),
            "receiver_id" => $receiver_id,
            "items" => $items,
            "reference_1" => (string)$reference_1,
            "service_type" => "INTERNATIONAL_ECONOMY",
            "customs_value_amount" => $customs_value_amount,
            "description_of_goods" => "Example Description"
        ]);
    }

    public function cancelShipment(string $id,string $reason)
    {
        return $this->post("{$this->shipmentRoute}/cancel", [
            "pnm_number" => $id,
            "reason" => $reason
        ]);
    }

    private function getShipperId(): int
    {
        $model = Setting::get('shiping.pnm.mode');
        return Setting::get("shiping.pnm.{$model}.shipper_id");
    }
}
