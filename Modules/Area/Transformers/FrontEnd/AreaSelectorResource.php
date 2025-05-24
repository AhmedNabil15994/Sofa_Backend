<?php

namespace Modules\Area\Transformers\FrontEnd;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaSelectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'title' => $this->title,
            'data_title' => $this->getTranslation('title','en'),
        ];

        if ($request->type && $request->type == 'city') {
            $response['states'] = StateResource::collection($this->states()->get());
        }

        return $response;
    }
}
