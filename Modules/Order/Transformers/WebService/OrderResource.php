<?php

namespace Modules\Order\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Vendor\Traits\VendorTrait;
use Modules\Vendor\Transformers\WebService\VendorResource;

class OrderResource extends JsonResource
{

    public function toArray($request)
    {
        $allOrderProducts = $this->orderProducts->mergeRecursive($this->orderVariations);
        $result = [
            'id' => $this->id,
            'total' => $this->total,
            'shipping' => $this->shipping,
            'subtotal' => $this->subtotal,
            'transaction' => $this->transactions->method,
            'order_status' => [
                'code' => $this->orderStatus->code,
                'title' => $this->orderStatus->title,
            ],
            'created_at' => date('d-m-Y H:i', strtotime($this->created_at)),
            'notes' => $this->notes,
            'products' => OrderProductResource::collection($allOrderProducts),
        ];

        $result['address'] = new OrderAddressResource($this->orderAddress);

        /*if (is_null($this->unknownOrderAddress)) {
            $result['address'] = new OrderAddressResource($this->orderAddress);
        } else {
            $result['address'] = new UnknownOrderAddressResource($this->unknownOrderAddress);
        }*/

        return $result;
    }
}
