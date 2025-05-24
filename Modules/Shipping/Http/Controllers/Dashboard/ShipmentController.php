<?php

namespace Modules\Shipping\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Order\Repositories\Dashboard\OrderRepository as Order;
use Modules\Shipping\Http\Requests\Dashboard\ShippingCancelRequest;
use Modules\Shipping\Traits\ShippingTrait;

class ShipmentController extends Controller
{
    use ShippingTrait;

    protected $order;

    function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function cancel(ShippingCancelRequest $request,$orderId)
    {
        try {
            $order = $this->order->findById($orderId);
            $this->setShippingTypeByOrder($order);

            if($order->shipmentStatus)
                $this->shipping->cancelShipment($order, $request->reason, $order->shipmentStatus->shipment_id);
                
            return Response()->json([true, __('apps::dashboard.general.message_update_success'),'url' => route('dashboard.orders.show',$orderId)]);
        } catch (\PDOException $e) {
            return redirect()->back()->withErrors($e->errorInfo[2]);
        }
    }
}
