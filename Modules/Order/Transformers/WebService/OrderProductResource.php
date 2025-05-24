<?php

namespace Modules\Order\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Catalog\Transformers\WebService\ProductResource;

class OrderProductResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'selling_price' => $this->price,
            'qty' => $this->qty,
            'total' => $this->total,
            'notes' => $this->notes,
        ];

        if (isset($this->product_variant_id) && !empty($this->product_variant_id)) {
            $prdTitle = '';
            foreach ($this->orderVariantValues as $k => $orderVal) {
                $prdTitle .= optional(optional(optional($orderVal->productVariantValue)->optionValue))->title . ' ,';
            }
            $result['title'] = $this->variant->product->title . ' - ' . rtrim($prdTitle, ' ,');
            $result['image'] = url($this->variant->image);
            $result['sku'] = $this->variant->sku;
        } else {
            $result['title'] = $this->product->title;
            $result['image'] = url($this->product->image);
            $result['sku'] = $this->product->sku;
        }

        return $result;
    }
}
