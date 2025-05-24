<?php

namespace Modules\Coupon\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use IlluminateAgnostic\Arr\Support\Carbon;
use Modules\Catalog\Entities\Category;
use Modules\Catalog\Entities\Product;
use Modules\Core\Traits\ScopesTrait;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderCoupon;
use Modules\User\Entities\User;
use Modules\Vendor\Entities\Vendor;

use Spatie\Translatable\HasTranslations;
class Coupon extends Model 
{
    use HasTranslations, SoftDeletes, ScopesTrait;

    protected $with = [];
    protected $guarded = ['id'];

    public $translatable = ['title'];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'coupon_vendors')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_users')->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_categories')->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products')->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(OrderCoupon::class);
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($q) {

                $q->whereDate('start_at', '<=', Carbon::now())
                    ->orWhereNull('start_at');
            })->where(function ($q) {

                $q->whereDate('expired_at', '>=', Carbon::now())
                    ->orWhereNull('expired_at');
            });
        });
    }

    /*public function vendors()
    {
        return $this->hasMany(CouponVendor::class);
    }

    public function users()
    {
        return $this->hasMany(CouponUser::class);
    }

    public function categories()
    {
        return $this->hasMany(CouponCategory::class);
    }

    public function products()
    {
        return $this->hasMany(CouponProduct::class);
    }*/

}
