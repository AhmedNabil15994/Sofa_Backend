<?php

namespace Modules\Order\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'unread'               => $this->unread,
            'total'                => $this->total,
            'shipping'             => $this->shipping,
            'subtotal'             => $this->subtotal,
            'transaction'          => $this->transactions->method,
            'coupon'               => $this->orderCoupons ? $this->orderCoupons->code : __('order::dashboard.orders.datatable.no_coupon_used'),
            'state'                => optional(optional(optional($this->orderAddress)->state))->title,
            'order_status_id'      => $this->orderStatus->title,
            'mobile'               => optional($this->orderAddress)->mobile ?? optional($this->unknownOrderAddress)->receiver_mobile,
            'name'               => optional($this->orderAddress)->username ?? optional($this->unknownOrderAddress)->receiver_name,
            'deleted_at'           => $this->deleted_at,
            'created_at'           => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
