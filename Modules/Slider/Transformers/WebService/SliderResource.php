<?php

namespace Modules\Slider\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'image' => asset($this->image),
            'link' => $this->link,
            'title' => optional($this)->title,
            'short_description' => optional($this)->short_description,
        ];

        if ($this->morph_model == 'Category') {
            $result['target'] = 'categories';
            $result['target_id'] = $this->sliderable_id;
        } elseif ($this->morph_model == 'Product') {
            $result['target'] = 'products';
            $result['target_id'] = $this->sliderable_id;
        } else {
            $result['target'] = 'external';
        }
        return $result;
    }
}
