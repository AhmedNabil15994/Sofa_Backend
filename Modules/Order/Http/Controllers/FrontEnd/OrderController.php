<?php

namespace Modules\Order\Http\Controllers\FrontEnd;

use Cart;
use Modules\Transaction\Services\TabbyPaymentService;
use Modules\Transaction\Services\TapPaymentService;
use Modules\Transaction\Traits\PaymentTrait;
use Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;
use Modules\Order\Events\ActivityLog;
use Modules\Catalog\Traits\ShoppingCartTrait;
use Modules\Transaction\Services\UPaymentService;
use Modules\Order\Http\Requests\FrontEnd\CreateOrderRequest;
use Modules\Order\Repositories\FrontEnd\OrderRepository as Order;
use Modules\Order\Notifications\FrontEnd\AdminNewOrderNotification;
use Modules\Order\Notifications\FrontEnd\UserNewOrderNotification;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository as Product;
use Modules\Shipping\Traits\ShippingTrait;
use Modules\Transaction\Services\MyFatoorahPaymentService;
use Modules\User\Entities\Address;

class OrderController extends Controller
{
    use ShoppingCartTrait, ShippingTrait;

    protected $payment;
    protected $order;
    protected $product;

    function __construct(Order $order, UPaymentService $payment, Product $product)
    {
        $this->payment = $payment;
        $this->order = $order;
        $this->product = $product;
    }

    public function index()
    {
        $ordersIDs = isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')]) ? (array) \GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]) : [];

        if (auth()->user()) {
            $orders = $this->order->getAllByUser($ordersIDs);
            return view('order::frontend.orders.index', compact('orders'));
        } else {
            $orders = count($ordersIDs) > 0 ? $this->order->getAllGuestOrders($ordersIDs) : [];
            return view('order::frontend.orders.index', compact('orders'));
        }

    }

    public function invoice($id)
    {
        if (auth()->user()) {
            $order = $this->order->findByIdWithUserId($id);
        } else {
            $order = $this->order->findGuestOrderById($id);
        }

        if (!$order) {
            return abort(404);
        }

        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.details', compact('order'));
    }

    public function reOrder($id)
    {
        $order = $this->order->findByIdWithUserId($id);
        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.re-order', compact('order'));
    }

    public function guestInvoice()
    {
        if (isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')])) {
            $savedID = (array) \GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]);
        }
        $id = count($savedID) > 0 ? $savedID[count($savedID) - 1] : 0;
        $order = $this->order->findByIdWithGuestId($id);
        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);

        if ($order) {
            return view('order::frontend.orders.invoice', compact('order'))->with([
                'alert' => 'success', 'status' => __('order::frontend.orders.index.alerts.order_success')
            ]);
        }

        return abort(404);
    }

    public function createOrder(CreateOrderRequest $request)
    {
        $errors1 = [];
        $errors2 = [];
        $errors3 = [];
        $errors4 = [];


        $address = $request->address_type == 'selected_address' ? Address::find($request->selected_address_id): null;

        if($address){

            $this->setShippingTypeByAddress($address);
            $shippingValidateAddress = $this->shipping->validateAddress($request,$address);
        }else{

            $this->setShippingTypeByRequest($request);
            $shippingValidateAddress = $this->shipping->validateAddress($request);

        }

        if($shippingValidateAddress[0]){

            $errors = new MessageBag([
                'productCart' => [$shippingValidateAddress],
            ]);
            return Response()->json([false, 'errors' =>  $errors],400);

        }else{

            $request->merge(['address_type' => $shippingValidateAddress['addressType'], 'json_data' => $shippingValidateAddress['jsonData']]);
        }

        $payment = $request['payment'] != 'cash' ? PaymentTrait::getPaymentGateway($request['payment']) : 'cash';

        if ($payment != 'cash' && !$payment) {

            return Response()->json([false, 'errors' =>  ['status' => __('order::frontend.orders.index.alerts.payment_not_supported_now')]],400);

        }elseif($payment == 'cash' && $request->has('json_data') && isset($request->json_data['country_id'])
            && count((array)config('setting.payment_gateway.cache.supported_countries',[]))
            && !in_array($request->json_data['country_id'], (array)config('setting.payment_gateway.cache.supported_countries',[]))){

                return Response()->json([false, 'errors' =>  ['status' => __('order::frontend.orders.index.alerts.country_not_support_cache_payment')]],400);
        }


        foreach (getCartContent() as $key => $item) {

            if ($item->attributes->product->product_type == 'product') {
                $cartProduct = $item->attributes->product;
                $product = $this->product->findOneProduct($cartProduct->id);
                if (!$product) {
                    return Response()->json([false, 'errors' =>  ['status' =>  __('cart::api.cart.product.not_found').$cartProduct->id]],400);
                }

                $product->product_type = 'product';
            } else {
                $cartProduct = $item->attributes->product;
                $product = $this->product->findOneProductVariant($cartProduct->id);
                if (!$product) {
                    return Response()->json([false, 'errors' =>  ['status' =>  __('cart::api.cart.product.not_found').$cartProduct->id]],400);
                }

                $product->product_type = 'variation';
            }

            $productFound = $this->productFound($product, $item);
            if ($productFound) {
                $errors1[] = $productFound;
            }

            $activeStatus = $this->checkActiveStatus($product, $request);
            if ($activeStatus) {
                $errors2[] = $activeStatus;
            }

            if (!is_null($product->qty)) {

                $maxQtyInCheckout = $this->checkMaxQtyInCheckout($product, $item->quantity, $cartProduct->qty);

                if ($maxQtyInCheckout) {
                    $errors3[] = $maxQtyInCheckout;
                }
            }

        }


        if ($errors1 || $errors2 || $errors3 || $errors4) {
            $errors = new MessageBag([
                'productCart' => $errors1,
                'productCart2' => $errors2,
                'productCart3' => $errors3,
                'productCart4' => $errors4,
            ]);

            return Response()->json([false, 'errors' =>  $errors],400);
        }

        $order = $this->order->create($request);
        if (!$order) {
            return $this->redirectToFailedPayment();
        }


        if ($payment != 'cash') {
            $redirect = $payment->send($order, 'online', auth()->check() ? auth()->id() : null);

            if (isset($redirect['status'])) {

                if ($redirect['status'] == true) {

                    return Response()->json([
                        true, __('user::frontend.addresses.index.alert.success_'), 'desc' => __("You are being redirected to the payment page"), 'url' => $redirect['url'],
                    ]);
                } else {
                    return Response()->json([false, 'errors' =>  ['payment' => 'Online Payment not valid now']],400);
                }
            }

            return Response()->json([false, 'errors' =>  ['payment' => 'field']],400);
        }

        $this->shipping->createShipment($request,$order);
        return $this->redirectToPaymentOrOrderPage($request, $order,true);
    }

    public function webhooks(Request $request)
    {
        logger('here is the upayment webhook');
        logger($request->all());
        $this->order->updateOrder($request);
    }

    public function success(Request $request)
    {
        $order = $this->order->updateOrder($request);
        return $order ? $this->redirectToPaymentOrOrderPage($request) : $this->redirectToFailedPayment();
    }

    public function successTap(Request $request)
    {
        $data = (new TapPaymentService())->getTransactionDetails($request);

        $request = PaymentTrait::buildTapRequestData($data, $request);

        if ($request->Result == 'CAPTURED') {
            return $this->success($request);
        }
        return $this->failed($request);

    }

    public function myFatoorahCallBack(Request $request)
    {
        $data = (new MyFatoorahPaymentService())->GetPaymentStatus($request->paymentId , 'paymentId');

        $request = PaymentTrait::buildMyFatoorahRequestData($data, $request);

        if ($request->Result == 'CAPTURED') {
            return $this->success($request);
        }
        return $this->failed($request);

    }

    public function failed(Request $request)
    {
        $this->order->updateOrder($request);
        return $this->redirectToFailedPayment();
    }

    public function redirectToPaymentOrOrderPage($data, $order = null,$json = false)
    {
        $order = ($order == null) ? $this->order->findById($data['OrderID']) : $this->order->findById($order->id);
        try {

            if ($this->sendNotifications($order)) {

            }
        } catch (\Exception $e) {
            info($e);
        }
        $this->clearCart();
        return $this->redirectToInvoiceOrder($order,$json);
    }

    public function redirectToInvoiceOrder($order,$json = false)
    {
        ################# Start Store Guest Orders In Browser Cookie ######################
        if (isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')])) {
            $cookieArray = (array) \GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]);
        }
        $cookieArray[] = $order['id'];
        setcookie(config('core.config.constants.ORDERS_IDS'), \GuzzleHttp\json_encode($cookieArray),
            time() + (5 * 365 * 24 * 60 * 60), '/'); // expires at 5 year
        ################# End Store Guest Orders In Browser Cookie ######################

        if (auth()->user()) {

            if($json)
                return Response()->json([
                    true, __('order::frontend.orders.index.alerts.order_success'), 'url' => route('frontend.orders.invoice', $order->id), 'desc' => __("The invoice is being displayed")
                ]);
            else
                return redirect()->route('frontend.orders.invoice', $order->id)->with([
                    'alert' => 'success', 'status' => __('order::frontend.orders.index.alerts.order_success')
                ]);
        }


        if($json)
        return Response()->json([
            true, __('order::frontend.orders.index.alerts.order_success'), 'url' => route('frontend.orders.guest.invoice'),'desc' => '',
        ]);
        else
            return redirect()->route('frontend.orders.guest.invoice');
    }

    public function redirectToFailedPayment()
    {
        return redirect()->route('frontend.checkout.index')->with([
            'alert' => 'danger', 'status' => __('order::frontend.orders.index.alerts.order_failed')
        ]);
    }

    public function sendNotifications($order)
    {
        try {

            $this->fireLog($order);

            $email = null;
            $address = $order->orderAddress ?? $order->unknownOrderAddress;

            if($address){
                $attr = $address->attributes()->byType('email')->first();
                $email = $attr ? $attr->value : null;
            }

            if(!$email){
                $attr = $order->attributes()->byType('email')->first();
                $email = $attr ? $attr->value : null;
            }

            if ($email) {
                Notification::route('mail', $email)->notify(
                    (new UserNewOrderNotification($order))->locale(locale())
                );
            }

            Notification::route('mail', config('setting.contact_us.email'))->notify(
                (new AdminNewOrderNotification($order))->locale(locale())
            );

        } catch (\Exception $e) {
            info($e);
        }
    }

    public function fireLog($order)
    {
        try {

            $data = [
                'id' => $order->id,
                'type' => 'orders',
                'url' => route('dashboard.orders.show', $order->id),
                'description_en' => 'New Order',
                'description_ar' => 'طلب جديد ',
            ];
            event(new ActivityLog($data));

        } catch (\Exception $e) {
            info($e);
        }
    }

    public function HandleTabby(Request $request)
    {
        $tabby = new TabbyPaymentService();
        $TabbyID = session()->get('TabbyID');
        $tabbyPayment = $tabby->getSession($TabbyID);
        $order = $this->order->findById($tabbyPayment->payment->order->reference_id);
        $request = PaymentTrait::buildTabbyRequestData($tabbyPayment, $request);
       session()->put('TabbyID',null);
        if (isset($tabbyPayment->payment) && $tabbyPayment->payment->status == "AUTHORIZED") {
            $order['status'] == 'PAID';
            $order['paid'] == true;
            $order->save();
            return $this->success($request);
        } else {
            return $this->failed($request);
        }
    }
}
