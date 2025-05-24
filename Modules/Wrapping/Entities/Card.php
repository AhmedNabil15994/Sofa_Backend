<?php

namespace Modules\Wrapping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\ScopesTrait;

use Spatie\Translatable\HasTranslations;
use Modules\Core\Traits\HasSlugTranslation;

class Card extends Model
{
    use HasSlugTranslation ;
    use HasTranslations, SoftDeletes , ScopesTrait;

    protected $with = [];

    protected $fillable 					= ["image","price","sku","status","title","slug"];
      
    public $translatable = [
        'title', 'slug'
    ];
}
