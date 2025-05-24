<?php

namespace Modules\Shipping\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Order\Repositories\Dashboard\OrderRepository as Order;
use Modules\Shipping\Http\Requests\Dashboard\ShippingCancelRequest;
use Modules\Shipping\Http\Requests\Frontend\CalculateRateRequest;
use Modules\Shipping\Traits\ShippingTrait;

class ShipmentController extends Controller
{
    use ShippingTrait;

    protected $order;

    function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function calculateRate(CalculateRateRequest $request)
    {
        try {
            if (auth()->check())
                $userToken = auth()->user()->id ?? null;
            else
                $userToken = get_cookie_value(config('core.config.constants.CART_KEY')) ?? null;

            $this->setShippingTypeByRequest($request);

            return $this->shipping->getDeliveryPrice($request,null,$userToken);
        } catch (\PDOException $e) {
            return redirect()->back()->withErrors($e->errorInfo[2]);
        }
    }
}
