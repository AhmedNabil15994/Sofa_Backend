<?php

namespace Modules\Cart\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Transformers\WebService\ProductOptionResource;
use Modules\Catalog\Transformers\WebService\ProductVariantResource;
use Modules\Variation\Entities\ProductVariant;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => (string)$this->id,
            'qty' => $this->quantity,
            'image' => url($this->attributes->product->image),
            'product_type' => $this->attributes->product->product_type,
            'notes' => $this->attributes->notes,
        ];

        if ($this->attributes->product->product_type == 'product') {
            $result['title'] = $this->attributes->product->title;
            $currentProduct = Product::find($this->attributes->product->id);
            $result['remaining_qty'] = intval($currentProduct->qty) - intval($this->quantity);
        } else {
            $result['title'] = $this->attributes->product->product->title;
            $result['product_options'] = CartProductOptionsResource::collection($this->attributes->product->productValues);
            $currentProduct = ProductVariant::find($this->attributes->product->id);
            $result['remaining_qty'] = intval($currentProduct->qty) - intval($this->quantity);
        }

        if ($this->attributes->addonsOptions) {
            $price = floatval($this->price) - floatval($this->attributes->addonsOptions['total_amount']);
            $result['price'] = number_format($price, 3);
        } else
            $result['price'] = number_format($this->price, 3);

        $result['addons'] = $this->attributes->addonsOptions;
        return $result;
    }
}
