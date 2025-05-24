<?php
namespace Modules\Shipping\Integrations\Pnm\Rates;

Trait CalculateRates
{
    private $rateRoute = "calculate_rate";
    
    public function calculateRate($items,$actualWeight,$receiverId)
    {
        return $this->post($this->rateRoute , [
            'items' => $items,
            'actual_weight' => $actualWeight,
            'receiver_id' => $receiverId,
        ]);
    }
}
