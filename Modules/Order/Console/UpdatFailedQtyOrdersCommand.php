<?php

namespace Modules\Order\Console;

use Modules\Catalog\Notifications\Dashboard\AlertOutStockListNotification;
use Modules\Order\Repositories\FrontEnd\OrderRepository as Order;
use Illuminate\Console\Command;

class UpdatFailedQtyOrdersCommand extends Command
{
    protected $name = 'order:update';

    protected $description = 'Update Qty of products for failed orders';

    public function __construct(Order $order)
    {
        $this->order = $order;
        parent::__construct();
    }

    public function handle()
    {

        $orders = $this->order->getAllFailedOrdersIncrement();

        foreach ($orders as $order) {

            foreach ($order->orderProducts as $value) {

                $value->product()->increment('qty',$value['qty']);

                $value->product()->decrement('selling',1);

                $variant = $value->orderVariant;

                if (!is_null($variant))
                  $variant->variant()->increment('qty',$value['qty']);

                $order->update([
                  'increment_qty'   => true,
                ]);

            }

        }

    }

}
