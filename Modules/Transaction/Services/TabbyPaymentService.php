<?php

namespace Modules\Transaction\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Modules\Area\Entities\Country;

class TabbyPaymentService
{
    protected string $PaymentMode = 'test_mode';
    protected string $BaseUrl = 'https://api.tabby.ai/api/v2';
    protected string $PublicKey = 'pk_test_d266e39b-02d5-4930-b160-a676dae83ea2';
    protected string $SecretKey = 'sk_test_09e072a4-a07f-4fd8-8292-d84e8f6b4978';
    protected string $merchantCode = 'SZH';
    protected string $Currency = 'AED';

    public function __construct()
    {
        if (config('setting.payment_gateway.tabby.payment_mode') == 'live_mode') {
            $this->PaymentMode = 'live_mode';
            $this->PublicKey = config('setting.payment_gateway.tabby.live_mode.PUBLIC_KEY');
            $this->SecretKey = config('setting.payment_gateway.tabby.live_mode.SECRET_KEY');
            $this->Currency = 'KWD';
        }
    }

    public function send($order, $payment, $userToken = '', $type = 'order')
    {
        $body = $this->getConfig($order);
        $http = Http::withToken($this->PublicKey)->baseUrl($this->BaseUrl);
        $response = $http->post('checkout', $body)->object();
        $redirect_url = @$response->configuration->available_products->installments[0]->web_url;
        if (!$redirect_url) {
            return ['status' => false];
        }
        session()->put('TabbyID', $response->id);
        return ['status' => true, 'url' => $redirect_url];
    }

    public function getSession($payment_id)
    {
        $http = Http::withToken($this->SecretKey)->baseUrl($this->BaseUrl);
        $url = 'checkout/' . $payment_id;
        $response = $http->get($url);
        return $response->object();
    }

    public function getConfig($order): array
    {
        if (auth()->check()) {
            $user = auth()->user();
            $data['registered_since'] = Carbon::parse($user['created_at']);
        } else {
            $data['registered_since'] = Carbon::now();
        }
        $data['loyalty_level'] = 0;
        $data['full_name'] = $order->orderAddress->username ?? '';
        $data['buyer_phone'] = $order->orderAddress->mobile ?? '';
        $data['buyer_email'] = $order->orderAddress->email ?? '';
        $data['city'] = $order->orderAddress->json_data->city ?? '';
        $country = Country::find(@$order->orderAddress->json_data['country_id']);
        $data['address'] = $country->getTranslation('title', 'en') ?? 'kuwait';
        $items = collect([]);

        foreach ($order->orderProducts as $orderProducts) {
            $items->push([
                'title' => $orderProducts->product_title ?? '',
                'quantity' => $orderProducts->qty,
                'unit_price' => $orderProducts->price,
                'category' => $orderProducts->Product->short_description ?? '',
            ]);
        }

        return [
            "payment" => [
                "amount" => (double)number_format($order['total'], 2),
                "currency" => $this->Currency,
                "description" => $order['notes'] ?? '',
                "buyer" => [
                    "phone" => $data['buyer_phone'],
                    "email" => $data['buyer_email'],
                    "name" => $data['full_name']
                ],
                "shipping_address" => [
                    "city" => $data['city'],
                    "address" => $data['address'],
                    "zip" => '1234',
                ],
                "order" => [
                    "tax_amount" => "0.00",
                    "shipping_amount" => "0.00",
                    "discount_amount" => "0.00",
                    "updated_at" => now(),
                    "reference_id" => (string)$order->id,
                    "items" =>
                        $items
                    ,
                ],
                "buyer_history" => [
                    "registered_since" => $data['registered_since'],
                    "loyalty_level" => $data['loyalty_level'],
                ],
            ],
            "lang" => app()->getLocale(),
            "merchant_code" => $this->merchantCode,
            "merchant_urls" => [
                "success" => route('frontend.orders.success.tabby'),
                "cancel" => route('frontend.orders.cancel.tabby'),
                "failure" => route('frontend.orders.failure.tabby'),
            ]
        ];
    }

}
