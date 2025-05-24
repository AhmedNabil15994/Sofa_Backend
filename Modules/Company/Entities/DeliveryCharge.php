<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Area\Entities\State;
use Modules\Core\Traits\ScopesTrait;

class DeliveryCharge extends Model
{
    use ScopesTrait;

    protected $fillable = ['delivery','min_order_amount','status', 'delivery_time', 'company_id', 'state_id'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function state()
    {
        return $this->belongsTo(state::class, 'state_id');
    }

    public function scopeFilterWithCountry($query , $country)
    {
        return $query->whereHas('state', function ($query) use($country){

            $query->where('country_id' , $country);
        });
    }
}
