<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Entities\AttributeValue;

class Address extends Model
{
    protected $guarded = ['id'];
//    protected $fillable = ['email','username','mobile','block','street','building','address','land_mark','state_id','user_id'];


    protected $casts = [
        'json_data' => 'array',
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    
    public function state()
    {
        return $this->belongsTo(\Modules\Area\Entities\State::class);
    }

    public function country()
    {
        return $this->belongsTo(\Modules\Area\Entities\Country::class, 'json_data.country_id');
    }

    public function attributes()
    {
        return $this->morphMany(AttributeValue::class,'attributeValuable','order_product_attributes_type','order_product_attributes_id');
    }

    public function getLineOneAttribute()
    {
        $lineOne = '';
        $first = '';
        $attrs = $this->attributes()->get();
        
        if (count($attrs)) {
            foreach ($attrs as $attr) {

                $lineOne .= "{$first}{$attr->name}:{$attr->value}";
                $first = ',';
            }
        }

        return $lineOne;
    }
}
