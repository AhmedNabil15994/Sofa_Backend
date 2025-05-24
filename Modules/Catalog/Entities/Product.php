<?php

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Modules\Advertising\Entities\Advertising;
use Modules\Area\Entities\Country;
use Modules\Attribute\Entities\Attribute;
use Modules\Core\Traits\ScopesTrait;
use Modules\Notification\Entities\GeneralNotification;
use Modules\Order\Entities\OrderProduct;
use Modules\Slider\Entities\Slider;
use Modules\Tags\Entities\Tag;
use Modules\Variation\Entities\Option;

use Spatie\Translatable\HasTranslations;
use Modules\Core\Traits\HasSlugTranslation;

class Product extends Model
{
    use HasSlugTranslation;
    use HasTranslations, SoftDeletes, ScopesTrait;

    const SINGLE_PRODUCT_COLS_NEEDS = ['','','',''];

    protected $with = [];

    protected $fillable = [
        "status",
        "is_new",
        "featured",
        "image",
        "price",
        "sku",       
        "international_code",
        "qty",
        "shipment",
        "sort",
        "title",
        "usage",
        "description",
        "slug",
        "seo_description",
        "seo_keywords",
        "ingredients",
        "all_countries",
        "gender",
        "imported_excel",
        'short_description'];


    protected $casts = [
        "shipment" => "array"
    ];

    public $translatable = [
        'title', 'usage','ingredients','short_description', 'description', 'slug', 'seo_description', 'seo_keywords'
    ];

    // END - Override active scope to add `pending_for_approval`

    public function countries()
    {
        return $this->belongsToMany(Country::class,'product_country');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function subCategories()
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->whereNotNull('categories.category_id');
    }

    public function parentCategories()
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->whereNull('categories.category_id');
    }


    public function ages()
    {
        return $this->belongsToMany(Age::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class);
    }
    
    public function offer()
    {
        return $this->hasOne(ProductOffer::class, 'product_id');
    }

    public function addOns()
    {
        return $this->hasMany(AddOn::class, 'product_id');
    }

    // variations
    public function options()
    {
        return $this->hasMany(\Modules\Variation\Entities\ProductOption::class);
    }

    public function productOptions()
    {
        return $this->belongsToMany(Option::class, 'product_options');
    }

    public function variants()
    {
        return $this->hasMany(\Modules\Variation\Entities\ProductVariant::class);
    }

    public function variantChosed()
    {
        return $this->hasOne(\Modules\Variation\Entities\ProductVariant::class);
    }

    public function variantValues()
    {
        return $this->hasMany(\Modules\Variation\Entities\ProductVariantValue::class);
    }

    public function checkIfHaveOption($optionId)
    {
        return $this->variantValues->contains('option_value_id', $optionId);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class, 'product_id');
    }

    public function adverts()
    {
        return $this->morphMany(Advertising::class, 'advertable');
    }

    public function generalNotifications()
    {
        return $this->morphMany(GeneralNotification::class, 'notifiable');
    }

    public function sliders()
    {
        return $this->morphMany(Slider::class, 'sliderable');
    }
    
    public function attributes()
    {
        return $this->morphToMany(Attribute::class, 'catalogable','catalog_attributes');
    }

    public function inputAttributes()
    {
        $categoriesIds = '';
        foreach($this->categories()->get() as $category){
            
            $categoriesIds .= ($categoriesIds != '' ? ',' : '') . $this->categoryParentsTreeIds($category);
        }
        
        $categoriesIds = explode(',',$categoriesIds);
        
        return Attribute::whereHas('products',function($query){
            $query->where('catalogable_id' , $this->id);
        })->orWhereHas('categories', function($query) use($categoriesIds){
            $query->whereIn('catalogable_id', $categoriesIds);
        })->get();
    }

    private function categoryParentsTreeIds($category ,$ids = ''){

        if($category){
            $ids .= ($ids != '' ? ',' : '') . $category->id;
            return $this->categoryParentsTreeIds($category->parent, $ids);
        }else{

            return $ids;
        }
    }

    /**
     * Get all of the search keywords for the product.
     */
    public function searchKeywords()
    {
        return $this->morphToMany(SearchKeyword::class, 'searchable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)
        ->where(function ($query){
            $query->doesnthave('variants')->orWhereHas('variants', function ($query){
                $query->active();
            });
        });
    }
}
