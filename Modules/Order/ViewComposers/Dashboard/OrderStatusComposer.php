<?php

namespace Modules\Order\ViewComposers\Dashboard;

use Modules\Order\Repositories\Dashboard\OrderStatusRepository as OrderStatus;
use Modules\Order\Repositories\Dashboard\OrderRepository;
use Illuminate\View\View;

class OrderStatusComposer
{
    public $orderStatuses = [];
    public $order;

    public function __construct(OrderStatus $orderStatus ,OrderRepository $order)
    {
        $this->orderStatuses =  $orderStatus->getAll();
        $this->order = $order;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $orders_count = $this->order->getOrdersQuery()->count();
        $orders_total = $this->order->getOrdersQuery()->whereHas('paymentStatus' , function ($q){

            $q->whereIn('flag' , ['success']);
        })->sum('total');
        $view->with(['orderStatuses' => $this->orderStatuses , 'orders_count' => $orders_count , 'orders_total' => $orders_total]);
    }
}
