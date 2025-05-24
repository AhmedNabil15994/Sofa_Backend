<?php

namespace Modules\Catalog\Http\Controllers\FrontEnd;

use Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Catalog\Http\Requests\FrontEnd\CheckoutInformationRequest;
use Modules\Catalog\Traits\ShoppingCartTrait;
use Modules\Catalog\Http\Requests\FrontEnd\CheckoutLimitationRequest;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository as Product;
use Modules\Company\Entities\DeliveryCharge;
use Modules\Catalog\Repositories\FrontEnd\PaymentRepository as PaymentMethods;
use Modules\Company\Repositories\FrontEnd\CompanyRepository as Company;
use Modules\Shipping\Traits\ShippingTrait;
use Modules\User\Entities\Address;
use Modules\Vendor\Entities\Vendor;
use Modules\User\Traits\AramexHelper;

class CheckoutController extends Controller
{
    use ShoppingCartTrait, ShippingTrait;

    protected $product;
    protected $payment;
    protected $company;

    function __construct(Product $product, PaymentMethods $payment, Company $company)
    {
        $this->product = $product;
        $this->payment = $payment;
        $this->company = $company;
    }

    public function index(Request $request)
    {
//        dd(getCartContent()->toArray(), Cart::getConditions()->toArray());
//        $address = Cart::getCondition('delivery_fees')->getAttributes()['address'];
//        $vendor = Vendor::find(Cart::getCondition('vendor')->getType());

//        Cart::removeCartCondition('company_delivery_fees');
//        dd(Cart::getCondition('company_delivery_fees'));

        ################# START Get Delivery Companies #################
        /*$vendors = [];
        foreach (getCartContent() as $key => $item) {
            $productSlug = $item->attributes->product->slug;
            $product = $this->product->findBySlug($productSlug);
            if ($product) {

                $vendorsIDs = array_column($vendors, 'vendor_id');
                if (!in_array($product->vendor->id, $vendorsIDs)) {
                    $vendors[$key]['vendor_id'] = $product->vendor->id;
                    $vendors[$key]['vendor_title'] = $product->vendor->title;
                    $vendors[$key]['vendor_object'] = $product->vendor;
                }

            } else
                $vendors[] = [];
        }
        ################# END Get Delivery Companies #################
        $vendors = array_values($vendors);*/

        $paymentMethods = $this->payment->getAll();

        $shippingCompanyId = config('setting.other.shipping_company') ?? 0;
        $shippingCompany = $this->company->findById($shippingCompanyId);
        if(is_null(Cart::getCondition('company_delivery_fees'))){
            $request = new Request();
            if(optional(auth()->user())->addresses){
                foreach (auth()->user()->addresses as $address){

                    $request->merge([
                        'state_id' => $address->state_id,
                        'address_id' => $address->id,
                        'type' => $address->selected_state,
                    ]);

                    $selectAddressResults = $this->getStateDeliveryPrice($request);

                    if ($selectAddressResults->getData()->success){
                        break;
                    }
                }
            }
        }
        return view('catalog::frontend.checkout.index', compact('paymentMethods', 'shippingCompany'));
    }

    public function saveCheckoutInformation(CheckoutInformationRequest $request)
    {
        // add cart conditions
        dd($request->all());
    }

    public function loadUserAddress()
    {
        $html = view('catalog::frontend.address.components.addresses')->render();
        return Response()->json(['html' => $html ,'container' => '#address_container']);
    }

    public function getContactInfo(Request $request)
    {
        $savedContactInfo = !empty(get_cookie_value(config('core.config.constants.CONTACT_INFO'))) ? (array)\GuzzleHttp\json_decode(get_cookie_value(config('core.config.constants.CONTACT_INFO'))) : [];
        return view('catalog::frontend.checkout.index', compact('savedContactInfo'));
    }

    public function getPaymentMethods(Request $request)
    {
        $cartAttributes = isset(Cart::getConditions()['delivery_fees']) && !empty(Cart::getConditions()['delivery_fees']) ? Cart::getConditions()['delivery_fees']->getAttributes() : null;

        if ($cartAttributes && $cartAttributes['address'] != null) {

            $address = Cart::getCondition('delivery_fees')->getAttributes()['address'];
            $vendor = Vendor::find(Cart::getCondition('vendor')->getType());

            return view('catalog::frontend.checkout.index', compact('address', 'vendor'));
        } else {
            return redirect()->back();
        }
    }

    public function getStateDeliveryPrice(Request $request)
    {
        if (auth()->check())
            $userToken = auth()->user()->id ?? null;
        else
            $userToken = get_cookie_value(config('core.config.constants.CART_KEY')) ?? null;

        if (is_null($userToken))
            return response()->json(["errors" => __('apps::frontend.general.user_token_not_found')], 422);

        if (isset($request->address_type) && in_array($request->address_type,['selected_address','known_address'])) {
            
            $request->company_id = config('setting.other.shipping_company') ?? 0;
            $address = null;
            
            if ($request->address_type == 'known_address') {

                $this->setShippingTypeByRequest($request);

            }elseif($request->address_type == 'selected_address'){

                $address = $request->selected_address_id ? Address::find($request->selected_address_id): null;
                
                if ($address)
                    $this->setShippingTypeByAddress($address);
                else
                    $this->setShippingTypeByRequest($request);

            } else {
                return response()->json(['success' => false, 'errors' => __('catalog::frontend.checkout.validation.please_choose_state')], 422);
            }
            
            return $this->shipping->getDeliveryPrice($request,$address,$userToken);

        } else {
            $data = [
                'price' => null,
                'totalDeliveryPrice' => 0,
                'total' => priceWithCurrenciesCode(number_format(getCartTotal(), 3)),
            ];
            return response()->json(['success' => true, 'data' => $data]);
        }

    }

    /* public function getStateDeliveryPrice(Request $request)
     {
         if (isset($request->type) && $request->type === 'selected_state') {

             if (Cart::getCondition('company_delivery_fees') != null) {
                 Cart::removeCartCondition('company_delivery_fees');
             }
             $data = [
                 'price' => null,
                 'totalDeliveryPrice' => 0,
                 'total' => getCartTotal(),
             ];
             return response()->json(['success' => true, 'data' => $data]);

         } else {

             if (isset($request->state_id) && $request->state_id != 0 && !empty($request->state_id)) {
                 $price = DeliveryCharge::where('state_id', $request->state_id)->where('company_id', $request->company_id)->value('delivery');
                 if ($price) {
                     $this->companyDeliveryChargeCondition($request, $price);
                     $deliveryPrice = Cart::getCondition('company_delivery_fees') != null ? Cart::getCondition('company_delivery_fees')->getValue() : 0;
                     $data = [
                         'price' => $price,
                         'totalDeliveryPrice' => $deliveryPrice,
                         'total' => getCartTotal(),
                     ];

                     return response()->json(['success' => true, 'data' => $data]);
                 } else {
                     if (Cart::getCondition('company_delivery_fees') != null) {
                         $this->companyDeliveryChargeCondition($request, null);
                     }

                     $deliveryPrice = Cart::getCondition('company_delivery_fees') != null ? Cart::getCondition('company_delivery_fees')->getValue() : 0;
                     $data = [
                         'price' => null,
                         'totalDeliveryPrice' => $deliveryPrice,
                         'total' => getCartTotal(),
                     ];
                     return response()->json(['success' => false, 'data' => $data, 'errors' => __('catalog::frontend.checkout.validation.state_not_supported_by_company')], 422);
                 }
             } else {
                 return response()->json(['success' => false, 'errors' => __('catalog::frontend.checkout.validation.please_choose_state')], 422);
             }

         }

     }*/

}
