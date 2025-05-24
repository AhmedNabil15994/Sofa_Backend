<?php

namespace Modules\Catalog\Entities;

use Modules\Core\Traits\ScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Age extends Model
{
    use ScopesTrait;

    protected $fillable = ["status","title"];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
