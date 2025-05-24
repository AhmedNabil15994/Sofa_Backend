<?php

namespace Modules\Order\Repositories\WebService;

use Modules\Order\Entities\OrderStatus;
use Modules\Order\Entities\PaymentStatus;
use Modules\Order\Traits\OrderCalculationTrait;
use Modules\Variation\Entities\ProductVariant;
use Modules\User\Repositories\WebService\AddressRepository;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    use OrderCalculationTrait;

    protected $variantPrd;
    protected $order;
    protected $address;

    function __construct(Order $order, ProductVariant $variantPrd, AddressRepository $address)
    {
        $this->variantPrd = $variantPrd;
        $this->order = $order;
        $this->address = $address;
    }

    public function getAllByUser($order = 'id', $sort = 'desc')
    {
        if(!auth('api')->check() && request()->user_token){

            return $this->order->with(['orderStatus'])->where('user_token', request()->user_token)->orderBy($order, $sort)->get();
        }elseif(auth('api')->check()){

            return $this->order->with(['orderStatus'])->where('user_id', auth('api')->id())->orderBy($order, $sort)->get();
        }

        return [];
    }

    public function findById($id)
    {
        $order = $this->order->with('orderProducts')->find($id);
        return $order;
    }

    public function findByIdWithUserId($id)
    {
        if(auth('api')->check())
            $order = $this->order->where('user_id', auth('api')->id())->find($id);
        else
            $order = $this->order->with('orderProducts')->where('user_token', request()->user_token)->find($id);

        return $order;
    }

    public function create($request, $userToken = null)
    {
        $orderData = $this->calculateTheOrder($userToken);

        DB::beginTransaction();

        try {

            $userId = auth('api')->check() ? auth('api')->id() : null;
            if ($request['payment'] == 'cash') {
                $orderStatus = 7; // new_order
                $paymentStatus = 4; // cash
            } elseif ($request['payment'] != 'cash' && $orderData['total'] <= 0) {
                $orderStatus = 3; // success
                $paymentStatus = 2; // success
            } else {
                $orderStatus = 1; // pending until payment
                $paymentStatus = 1; // pending
            }

            $orderCreated = $this->order->create([
                'original_subtotal' => $orderData['original_subtotal'],
                'subtotal' => $orderData['subtotal'],
                'off' => $orderData['off'],
                'shipping' => $orderData['shipping'],
                'total' => $orderData['total'],
                'total_profit' => $orderData['profit'],
                'user_token' => !auth('api')->check() ? $request->user_id : null,

                /*'total_comission' => $orderData['commission'],
                'total_profit_comission' => $orderData['totalProfitCommission'],
                'vendor_id' => $orderData['vendor_id'],*/

                'user_id' => $userId,
                'order_status_id' => $orderStatus,
                'payment_status_id' => $paymentStatus,
                'notes' => $request['notes'] ?? null,
            ]);

            $orderCreated->transactions()->create([
                'method' => $request['payment'],
                'result' => ($request['payment'] == 'cash') ? 'CASH' : null,
            ]);

            if (!is_null($orderStatus)) {
                // Add Order Status History
                $orderCreated->orderStatusesHistory()->sync([$orderStatus => ['user_id' => $userId]]);
            }

            $this->createOrderProducts($orderCreated, $orderData);
            $this->createOrderVendors($orderCreated, $orderData['vendors']);

            if ($request->shipping_company)
                $this->createOrderCompanies($orderCreated, $request);

            if (!is_null($orderData['coupon'])) {
                $orderCreated->orderCoupons()->create([
                    'coupon_id' => $orderData['coupon']['id'],
                    'code' => $orderData['coupon']['code'],
                    'discount_type' => $orderData['coupon']['type'],
                    'discount_percentage' => $orderData['coupon']['discount_percentage'],
                    'discount_value' => $orderData['coupon']['discount_value'],
                    'products' => $orderData['coupon']['products'],
                ]);
            }

            ############ START To Add Order Address ###################
            if ($request->address_type == 'guest_address') {
                $this->createOrderAddress($orderCreated, $request, 'guest_address');
            } elseif ($request->address_type == 'selected_address') {
                // get address by id
                $companyDeliveryFees = getCartConditionByName($userToken, 'company_delivery_fees');
                $addressId = isset($companyDeliveryFees->getAttributes()['address_id'])
                    ? $companyDeliveryFees->getAttributes()['address_id']
                    : null;
                $address = $this->address->findByIdWithoutAuth($addressId);
                if ($address)
                    $this->createOrderAddress($orderCreated, $address, 'selected_address');
                else
                    return false;
            }
            ############ END To Add Order Address ###################

            DB::commit();
            return $orderCreated;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function createOrderProducts($orderCreated, $orderData)
    {
        foreach ($orderData['products'] as $product) {

            if ($product['product_type'] == 'product') {

                $orderProduct = $orderCreated->orderProducts()->create([
                    'product_id' => $product['product_id'],
                    'off' => $product['off'],
                    'qty' => $product['quantity'],
                    'price' => $product['original_price'],
                    'sale_price' => $product['sale_price'],
                    'original_total' => $product['original_total'],
                    'total' => $product['total'],
                    'total_profit' => $product['total_profit'],
                    'notes' => $product['notes'] ?? null,
                    // 'add_ons_option_ids' => !empty($product['add_ons_option_ids']) && count($product['add_ons_option_ids']) > 0 ? $product['add_ons_option_ids'] : [],
                ]);

                foreach ($orderCreated->orderProducts as $value) {
                    $value->product()->decrement('qty', $value['qty']);
                }

            } else {
                $orderProduct = $orderCreated->orderVariations()->create([
                    'product_variant_id' => $product['product_id'],
                    'off' => $product['off'],
                    'qty' => $product['quantity'],
                    'price' => $product['original_price'],
                    'sale_price' => $product['sale_price'],
                    'original_total' => $product['original_total'],
                    'total' => $product['total'],
                    'total_profit' => $product['total_profit'],
                    'notes' => $product['notes'] ?? null,
                    // 'add_ons_option_ids' => !empty($product['add_ons_option_ids']) && count($product['add_ons_option_ids']) > 0 ? $product['add_ons_option_ids'] : [],
                ]);

                $productVariant = $this->variantPrd->with('productValues')->find($product['product_id']);

                // add product_variant_values to order variations
                if (count($productVariant->productValues) > 0) {
                    foreach ($productVariant->productValues as $k => $value) {
                        $orderProduct->orderVariantValues()->create([
                            'product_variant_value_id' => $value->id,
                        ]);
                    }
                }

                foreach ($orderCreated->orderVariations as $value) {
                    $value->variant()->decrement('qty', $value['qty']);
                }

            }

        }
    }

    public function createOrderVendors($orderCreated, $vendors)
    {
        foreach ($vendors as $k => $vendor) {
            $orderCreated->vendors()->attach($vendor['id'], [
                'total_comission' => $vendor['commission'],
                'total_profit_comission' => $vendor['totalProfitCommission'],
                'original_subtotal' => $vendor['original_subtotal'],
                'subtotal' => $vendor['subtotal'],
                'qty' => $vendor['qty'],
            ]);
        }
    }

    public function createOrderAddress($orderCreated, $address, $type = '')
    {
        $data = [];
        if ($type == 'guest_address') {
            $data = [
                'username' => isset($address['address']['username']) ? $address['address']['username'] : null,
                'email' => isset($address['address']['email']) ? $address['address']['email'] : null,
                'mobile' => isset($address['address']['mobile']) ? $address['address']['mobile'] : null,
                'address' => isset($address['address']['address']) ? $address['address']['address'] : null,
                'block' => isset($address['address']['block']) ? $address['address']['block'] : null,
                'street' => isset($address['address']['street']) ? $address['address']['street'] : null,
                'building' => isset($address['address']['building']) ? $address['address']['building'] : null,
                'state_id' => isset($address['address']['state_id']) ? $address['address']['state_id'] : null,
                'avenue' => isset($address['address']['avenue']) ? $address['address']['avenue'] : null,
                'floor' => isset($address['address']['floor']) ? $address['address']['floor'] : null,
                'flat' => isset($address['address']['flat']) ? $address['address']['flat'] : null,
                'automated_number' => isset($address['address']['automated_number']) ? $address['address']['automated_number'] : null,
            ];
        } elseif ($type == 'selected_address') {
            $data = [
                'username' => (isset($address['username']) ? $address['username'] : null) ?? auth('api')->user()->name,
                'email' => (isset($address['email']) ? $address['email'] : null) ?? auth('api')->user()->email,
                'mobile' => (isset($address['mobile']) ? $address['mobile'] : null) ?? auth('api')->user()->mobile,
                'address' => (isset($address['address']) ? $address['address'] : null),
                'block' => (isset($address['block']) ? $address['block'] : null),
                'street' => (isset($address['street']) ? $address['street'] : null),
                'building' => $address['building'],
                'state_id' => (isset($address['state_id']) ? $address['state_id'] : null),
                'avenue' => (isset($address['avenue']) ? $address['avenue'] : null) ?? null,
                'floor' => (isset($address['floor']) ? $address['floor'] : null) ?? null,
                'flat' => (isset($address['flat']) ? $address['flat'] : null) ?? null,
                'automated_number' => (isset($address['automated_number']) ? $address['automated_number'] : null) ?? null
            ];
        }
        $orderCreated->orderAddress()->create($data);
    }

    public function createOrderCompanies($orderCreated, $request)
    {
        $price = getOrderShipping(auth('api')->check() ? auth('api')->id() : $request->user_id) ?? 0;

        $data = [
            'company_id' => config('setting.other.shipping_company') ?? null,
            'delivery' => floatval($price) ?? null,
        ];

        if (isset($request->shipping_company['availabilities']['day_code']) && !empty($request->shipping_company['availabilities']['day_code'])) {
            $dayCode = $request->shipping_company['availabilities']['day_code'] ?? '';
            $availabilities = [
                'day_code' => $dayCode,
                'day' => getDayByDayCode($dayCode)['day'],
                'full_date' => getDayByDayCode($dayCode)['full_date'],
            ];

            $data['availabilities'] = \GuzzleHttp\json_encode($availabilities);
        }

        if (config('setting.other.shipping_company')) {
            $orderCreated->companies()->attach(config('setting.other.shipping_company'), $data);
        }
    }

    public function updateOrder($request)
    {
        $order = $this->findById($request['OrderID']);
        $this->updateQtyOfProduct($order, $request);

        if(($request['Result'] == 'CAPTURED')){
            $newOrderStatus = 3;
            $newPaymentStatus = optional(PaymentStatus::where('flag','success')->first())->id ?? $order->payment_status_id;
        }else{

            $newOrderStatus = 4;
            $newPaymentStatus = optional(PaymentStatus::where('flag','failed')->first())->id ?? $order->payment_status_id;
        }
        $order->update([
            'order_status_id' => $newOrderStatus,
            'payment_status_id' => $newPaymentStatus,
            'increment_qty' => true,
        ]);

        // Add new order history
        $order->orderStatusesHistory()->attach([$newOrderStatus => ['user_id' => $order->user_id ?? null]]);

        $order->transactions()->updateOrCreate(
            [
                'transaction_id' => $request['OrderID']
            ],
            [
                'auth' => $request['Auth'],
                'tran_id' => $request['TranID'],
                'result' => $request['Result'],
                'post_date' => $request['PostDate'],
                'ref' => $request['Ref'],
                'track_id' => $request['TrackID'],
                'payment_id' => $request['PaymentID'],
            ]);

        return $request['Result'] == 'CAPTURED' ? true : false;
    }

    public function updateQtyOfProduct($order, $request)
    {
        if ($request['Result'] != 'CAPTURED' && $order->increment_qty != true) {
            foreach ($order->orderProducts as $value) {
                $value->product()->increment('qty', $value['qty']);
                $variant = $value->orderVariant;
                if (!is_null($variant))
                    $variant->variant()->increment('qty', $value['qty']);
            }
        }

    }

    public function getOrderStatusByFlag($flag)
    {
        return OrderStatus::where('flag', $flag)->first();
    }


    public function checkOrderPendingPayment($id, array $userData)
    {
        return $this->order->where($userData['column'], $userData['value'])
            ->where('payment_status_id', 1)
            ->find($id);
    }
}
