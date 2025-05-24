<?php

namespace Modules\Wrapping\Entities;

use Modules\Core\Traits\ScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

use Modules\Core\Traits\HasSlugTranslation;
use Illuminate\Database\Eloquent\SoftDeletes;

class WrappingAddons extends Model
{
    protected $table = 'wrapping_addons';
    use HasSlugTranslation ;
    use HasTranslations, SoftDeletes, ScopesTrait;

    protected $with = [];

    protected $fillable 					= ["image","price","sku","qty","status","title"];
      

    public $translatable = [
        'title',"slug"
    ];
}
