<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Advertising\Transformers\WebService\AdvertisingResource;
use Modules\Tags\Transformers\WebService\TagsResource;
use Modules\Vendor\Transformers\WebService\OpeningStatusResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'type' => $this->variants()->count() ? 'variant' : 'single',
            'sku' => $this->sku,
            'price' => $this->price,
            'origin_price' => $this->origin_price,
            'qty' => $this->qty,
            'image' => asset($this->image),
            'title' => optional($this)->title,
            'description' => htmlView(optional($this)->description),
            'short_description' => optional($this)->short_description,
            'dimensions' => $this->shipment,
            'offer' => new ProductOfferResource($this->offer),
            'images' => ProductImagesResource::collection($this->images),
            'tags' => TagsResource::collection($this->tags),
            'products_options' => ProductOptionResource::collection($this->options),
            'variations_values' => ProductVariantResource::collection($this->variants),

            //'categories' => $this->parentCategories->pluck('id'),
            //'sub_categories' => CategoryDetailsResource::collection($this->subCategories),
        ];

        return $result;
    }
}
