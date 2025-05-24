<?php

namespace Modules\Shipping\Companies;

use JanisKelemen\Setting\Facades\Setting;
use Modules\Area\Entities\Country;
use Modules\Shipping\Interfaces\ShippingTransactionsInterface;
use Illuminate\Http\Request;
use Modules\User\Entities\Address;
use Cart;
use Modules\Catalog\Traits\ShoppingCartTrait;
use Modules\Order\Entities\Order;
use Modules\Area\Repositories\FrontEnd\AreaRepository as Area;
use Modules\Area\Transformers\FrontEnd\AreaSelectorResource;
use Modules\User\Entities\Address as AddressModel;
use Illuminate\Support\Facades\Http;


class Dhl implements ShippingTransactionsInterface
{
    use ShoppingCartTrait;

    public $country;
    public $address = null;
    private $dhl;

    public function getCities(Request $request)
    {
        return AreaSelectorResource::collection((new Area)->getChildAreaByParent($request));
    }

    public function validateAddress(Request $request, $address = null): array
    {
        $addressType = 'dhl';
        $this->address = $address;
        $jsonData = $this->getAddressObjectData($request, $address);

        return [false, 'addressType' => $addressType, 'jsonData' => $jsonData];
    }

    public function getAddressObjectData(Request $request, $object): array
    {
        $response = $object && optional($object)->json_data && count(optional($object)->json_data) ? (array)optional($object)->json_data : [];

        if ($this->country && !array_key_exists('country_id', $response))
            $response['country_id'] = $this->country->id;

        return $response;
    }

    public function getDeliveryPrice(Request $request, AddressModel $address = null, $userToken): object
    {
        if (!is_null($request['selected_address_id'])) {
            $address = Address::find($request['selected_address_id']);
            $country = Country::find($address['json_data']['country_id']);
            $request['destinationCountryCode'] = $country->iso2;
            $request['destinationCityName'] = $address->state->getTranslation('title', 'en');
        } else {
            $country = Country::find($request['country_id']);
            $request['destinationCountryCode'] = $country->iso2;
            $request['destinationCityName'] = $request['city_name'];
        }
        $request['weight'] = $this->getItemsDensity($this->getCartItems(), 'weight');
        $request['length'] = $this->getItemsDensity($this->getCartItems(), 'length');
        $request['width'] = $this->getItemsDensity($this->getCartItems(), 'width');
        $request['height'] = $this->getItemsDensity($this->getCartItems(), 'height');

        $price = \App\Services\DHL\DHLProvidersService::rate($request->all());

        $this->companyDeliveryChargeCondition($request, $price, $userToken);
        $data = [
            'price' => priceWithCurrenciesCode($price),
            'delivery_time' => '',
            'totalDeliveryPrice' => priceWithCurrenciesCode($price),
            'total' => priceWithCurrenciesCode(number_format(getCartTotal(), 3)),
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function createShipment(Request $request, Order $order): void
    {
        if (!is_null($request['selected_address_id'])) {
            $address = Address::find($request['selected_address_id']);
            $country = Country::find($address['json_data']['country_id']);
            $request['username'] = $address['username'];
            $request['mobile'] = $address['username'];
            $request['city_name'] = $address->state->getTranslation('title', 'en');
            $request['countryCode'] = $country->iso2 ?? '';
            $request['countyName'] = $country->getTranslation('title', 'en');
        } else {
            $country = Country::find($order->OrderAddress['json_data']['country_id']);
            $request['username'] = $order->OrderAddress->username;
            $request['mobile']   = $order->OrderAddress->mobile;
            $request['city_name'] = $order->OrderAddress->state->getTranslation('title', 'en');
            $request['countryCode'] = $country->iso2 ?? '';
            $request['countyName'] = $country->getTranslation('title', 'en');
        }

        $request['weight'] = $this->getItemsDensity($this->getCartItems(), 'weight');
        $request['length'] = $this->getItemsDensity($this->getCartItems(), 'length');
        $request['width'] = $this->getItemsDensity($this->getCartItems(), 'width');
        $request['height'] = $this->getItemsDensity($this->getCartItems(), 'height');

        $data = \App\Services\DHL\DHLProvidersService::shipment($order, $request->all());
        if (isset($data['shipmentTrackingNumber'])) {
            $order->shipmentTransactions()->create([
                'shipment_id' => $data['shipmentTrackingNumber'],
                'status' => 'new',
                'json_data' => $data,
                'type' => 'dhl',
            ]);
        }
    }

    protected function getCartItems()
    {
        $items = [];
        foreach (getCartContent() as $item) {
            $weight = optional(optional($item->attributes->product)->shipment)['weight'] ?? 0;
            array_push($items, [
                'hs_code' => optional($item->attributes->product)->sku,
                'quantity' => $item['quantity'],
                'qty' => $item['quantity'],
                'rate' => 5,
                'price' => (float)priceWithCurrenciesCode($item['price'], true, false),
                'name' => $item['name'],
                'weight' => $weight > 0 ? $weight / 1000 : 3.16,
                'length' => optional(optional($item->attributes->product)->shipment)['length'] ?? 41.7,
                'width' => optional(optional($item->attributes->product)->shipment)['width'] ?? 35.9,
                'height' => optional(optional($item->attributes->product)->shipment)['height'] ?? 36.9,
                'package_type' => 'Box',
            ]);
        }

        return $items;
    }

    protected function getItemsDensity($items, $name)
    {
        $weight = 0;
        foreach ($items as $item) {
            $weight += $item[$name] * $item['qty'];
        }

        return $weight;
    }
}
