<?php

namespace Modules\Catalog\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'image' => url($this->image),
            'title' => $this->title,
            'description' => $this->description,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
