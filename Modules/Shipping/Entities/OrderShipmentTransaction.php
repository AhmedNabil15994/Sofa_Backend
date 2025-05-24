<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use IlluminateAgnostic\Str\Support\Carbon;
use Modules\Core\Traits\ScopesTrait;

class OrderShipmentTransaction extends Model 
{
    use ScopesTrait;

    protected $fillable = ["shipment_id","status","json_data","order_id","type"];

    protected $casts = [
        'json_data' => 'array',
    ];
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getDateAttribute()
    {
        $type = $this->type;
        
        return isset($this->json_data['shipping_pnm_date']) ?
        Carbon::parse($this->json_data['shipping_pnm_date'])->toDateTimeString() : null;
    }
}
