<?php

namespace Modules\Wrapping\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class AddonsResource extends JsonResource
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
            'title' => $this->title,
            'image' => url($this->image),
            'price' => is_null($this->price) ? 0 : $this->price,
            'status' => $this->status,
            'total_quantity' => $this->qty,
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
