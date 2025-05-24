<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => asset($this->image),
            'description' => htmlView(optional($this)->description),
            'short_description' => optional($this)->short_description,
        ];
    }
}
