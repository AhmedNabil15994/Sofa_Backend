<?php

namespace Modules\Order\Http\Controllers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\MessageBag;
use Modules\Cart\Traits\CartTrait;
use Modules\Catalog\Repositories\WebService\CatalogRepository as Catalog;
use Modules\Order\Entities\OrderStatusesHistory;
use Modules\Order\Entities\PaymentStatus;
use Modules\Order\Http\Requests\WebService\CreateOrderRequest;

use Modules\Order\Notifications\FrontEnd\UserNewOrderNotification;
use Modules\Order\Notifications\FrontEnd\AdminNewOrderNotification;
use Notification;
use Modules\Order\Transformers\WebService\OrderProductResource;
use Modules\Transaction\Services\MyFatoorahPaymentService;
use Modules\Transaction\Services\TapPaymentService;
use Modules\Transaction\Traits\PaymentTrait;
use Modules\User\Repositories\WebService\AddressRepository;
use Setting;
use Illuminate\Http\Request;
use Modules\Order\Events\ActivityLog;

use Modules\Catalog\Repositories\FrontEnd\PaymentRepository as PaymentMethods;
//use Modules\Transaction\Services\PaymentService;
use Modules\Transaction\Services\UPaymentService;
use Modules\Order\Transformers\WebService\OrderResource;
use Modules\Order\Repositories\WebService\OrderRepository as Order;

//use Modules\Order\Repositories\WebService\OrderRepositoryOld as Order;
use Modules\Wrapping\Repositories\WebService\WrappingRepository as Wrapping;
use Modules\Company\Repositories\WebService\CompanyRepository as Company;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;

class OrderController extends WebServiceController
{
    use CartTrait;

    protected $payment;
    protected $order;
    protected $company;
    protected $catalog;
    protected $address;

    function __construct(
        Order $order,
        PaymentMethods $payment,
        Company $company,
        Catalog $catalog,
        AddressRepository $address
    )
    {
        // if (request()->hasHeader('authorization'))
        //     $this->middleware('auth');
            
        $this->payment = $payment;
        $this->order = $order;
        $this->company = $company;
        $this->catalog = $catalog;
        $this->address = $address;
    }

    public function paymentMethods()
    {
        $response = [];

        foreach($this->payment->getAll() as $k => $payment){
            
            if(in_array($payment->code, config('setting.other.supported_payments',[]))){
                
                if($payment->code == 'online' && count(config('setting.payment_gateway.upayment'))){

                    foreach(config('setting.payment_gateway') as $k => $gateway){

                        if($gateway['status'] == 'on'){
                            array_push($response,[
                                'code' => $k,
                                'title' => isset($gateway['title_'.locale()]) ? $gateway['title_'.locale()] : $payment->title,
                            ]);
                        }
                    }
                }else{
                    array_push($response,[
                        'code' => $payment->code,
                        'title' => $payment->title,
                    ]);
                }
            }
        }
        
        return $this->response($response);
    }

    public function createOrder(CreateOrderRequest $request)
    {
        if (auth('api')->check())
            $userToken = auth('api')->user()->id;
        else
            $userToken = $request->user_id;

        // Check if address is not found
        if ($request->address_type == 'selected_address') {
            // get address by id
            $companyDeliveryFees = getCartConditionByName($userToken, 'company_delivery_fees');
            $addressId = isset($companyDeliveryFees->getAttributes()['address_id'])
                ? $companyDeliveryFees->getAttributes()['address_id']
                : null;
            $address = $this->address->findByIdWithoutAuth($addressId);
            if (!$address)
                return $this->error(__('user::webservice.address.errors.address_not_found'), [], 422);
        }

        $payment = $request['payment'] != 'cash' ? PaymentTrait::getPaymentGateway($request['payment']) : 'cash';
        
        if ($payment != 'cash' && !$payment) {

            return $this->error(__('order::frontend.orders.index.alerts.payment_not_supported_now'));

        }elseif($payment == 'cash' && $request->has('json_data') && isset($request->json_data['country_id']) 
            && count((array)config('setting.payment_gateway.cache.supported_countries',[])) 
            && !in_array($request->json_data['country_id'], (array)config('setting.payment_gateway.cache.supported_countries',[]))){

                return $this->error(__('order::frontend.orders.index.alerts.country_not_support_cache_payment'));
              
        }

        foreach (getCartContent($userToken) as $key => $item) {

            if ($item->attributes->product->product_type == 'product') {
                $cartProduct = $item->attributes->product;
                $product = $this->catalog->findOneProduct($cartProduct->id);
                if (!$product)
                    return $this->error(__('cart::api.cart.product.not_found') . $cartProduct->id, [], 422);

                $product->product_type = 'product';
            } else {
                $cartProduct = $item->attributes->product;
                $product = $this->catalog->findOneProductVariant($cartProduct->id);
                if (!$product)
                    return $this->error(__('cart::api.cart.product.not_found') . $cartProduct->id, [], 422);

                $product->product_type = 'variation';
            }

            $checkPrdFound = $this->productFound($product, $item);
            if ($checkPrdFound)
                return $this->error($checkPrdFound, [], 422);

            $checkPrdStatus = $this->checkProductActiveStatus($product, $request);
            if ($checkPrdStatus)
                return $this->error($checkPrdStatus, [], 422);

            if (!is_null($product->qty)) {
                $checkPrdMaxQty = $this->checkMaxQty($product, $item->quantity);
                if ($checkPrdMaxQty)
                    return $this->error($checkPrdMaxQty, [], 422);
            }
        }

        $order = $this->order->create($request, $userToken);
        if (!$order)
            return $this->error('error', [], 422);

        if ($payment != 'cash') {
            $redirect = $payment->send($order,'online' , $userToken,'api-order');

            if (isset($redirect['status'])) {

                if ($redirect['status'] == true) {
                    return $this->response([
                        'order_id' => $order->id,
                        'paymentUrl' => $redirect['url']
                    ]);
                } else {
                    return $this->error('Online Payment not valid now');
                }
            }

            return $this->error('field');
        }

        return $this->redirectToPaymentOrOrderPage($request, $order,$userToken);
    }

    public function redirectToPaymentOrOrderPage($data, $order = null,$userToken)
    {
        $order = ($order == null) ? $this->order->findById($data['OrderID']) : $this->order->findById($order->id);
        try {

            if ($this->sendNotifications($order)) {

            }
        } catch (\Exception $e) {
            info($e);
        }

        $this->clearCart($userToken);

        return $this->response(new OrderResource($order));
    }

    public function redirectToFailedPayment()
    {
        return $this->error(__('order::frontend.orders.index.alerts.order_failed'));
    }

    public function webhooks(Request $request)
    {
        $this->order->updateOrder($request);
    }

    public function userOrdersList(Request $request)
    {
        $orders = $this->order->getAllByUser();
        return $this->response(OrderResource::collection($orders));
    }

    public function getOrderDetails(Request $request, $id)
    {
        $order = $this->order->findById($id);

        if (!$order)
            return $this->error(__('order::api.orders.validations.order_not_found'), [], 422);

        $allOrderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return $this->response(OrderProductResource::collection($allOrderProducts));
    }

    public function fireLog($order)
    {
        $data = [
            'id' => $order->id,
            'type' => 'orders',
            'url' => url(route('dashboard.orders.show', $order->id)),
            'description_en' => 'New Order',
            'description_ar' => 'طلب جديد ',
        ];

        event(new ActivityLog($data));
    }

    public function success(Request $request)
    {
        $order = $this->order->updateOrder($request);
        $userToken = auth('api')->check() ? auth('api')->id() : ($request->userToken ?? null);
        return $order ? $this->redirectToPaymentOrOrderPage($request,null,$userToken) : $this->redirectToFailedPayment();
    }

    public function failed(Request $request)
    {
        $this->order->updateOrder($request);
        return $this->redirectToFailedPayment();
    }

    public function successUpayment(Request $request)
    {
        $request = PaymentTrait::buildUpaymentRequestData([], $request);

        if ($request->Result == 'CAPTURED') {
            return $this->success($request);
        }
        return $this->failed($request);

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



    private function returnHtmlOrder($order)
    {
        $order->allProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        $htmlOrder['html_order'] = view('order::api.html-order', compact('order'))->render();
        return $htmlOrder;
    }

    public function cancelOrderPayment(Request $request, $id)
    {
        if (auth('api')->check()) {
            $userData['column'] = 'user_id';
            $userData['value'] = auth('api')->id();
        } else {
            $userData['column'] = 'user_token';
            $userData['value'] = $request->user_token;
        }
        
        $order = $this->order->checkOrderPendingPayment($id, $userData);
        if ($order) {
            $orderStatusId = $this->order->getOrderStatusByFlag('failed')->id;
            $paymentStatusId = optional(PaymentStatus::where('flag', 'failed')->first())->id ?? $order->payment_status_id;

            $order->update([
                'order_status_id' => $orderStatusId, // failed
                'payment_status_id' => $paymentStatusId, // failed
                'payment_confirmed_at' => null,
                'increment_qty' => true,
            ]);

            // Add Order Status History
            OrderStatusesHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $orderStatusId, // failed
                'user_id' => null,
            ]);

            if ($order->orderProducts) {
                foreach ($order->orderProducts as $i => $orderProduct) {
                    if (!is_null($orderProduct->product->qty)) {
                        $orderProduct->product->increment('qty', $orderProduct->qty);
                    }
                }
            }

            if ($order->orderVariations) {
                foreach ($order->orderVariations as $i => $orderProduct) {
                    if (!is_null($orderProduct->variant->qty)) {
                        $orderProduct->variant->increment('qty', $orderProduct->qty);
                    }
                }
            }

        }
        return $this->response(null);
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

}
