<?php
namespace Modules\Shipping\Components\Dashboard\Order;
 
use Illuminate\View\Component;
use Modules\Order\Entities\Order;

class ShipmentDetails extends Component
{
    public $order;

    public $type;
    public $shipmentStatus;
 
    /**
     * Create the component instance.
     * @param  Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->shipmentStatus = $order->shipmentStatus;
        $this->type = $this->shipmentStatus ? $order->address_type : 'local';
    }
 
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    { 
        return view("shipping::dashboard.components.orders.{$this->type}");
    }
}