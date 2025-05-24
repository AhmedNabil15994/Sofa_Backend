@extends('apps::frontend.layouts.master')
@section('title', __('order::frontend.orders.invoice.details_title') )
@section('content')
    <div class="container">
        <div class="invoice-page invoice-style2">
            <div class="invoice-conent">
                <h1 class="invoice-head"> {{ __('order::frontend.orders.invoice.title') }}</h1>
                <div class="invoice-head-rec">
                    <div class="row">
                        <div class="col-md-8 col-4">
                            <img
                                    src="{{ config('setting.logo') ? url(config('setting.logo')) : url('frontend/images/logo.png') }}"
                                    class="img-fluid">
                        </div>
                        <div class="col-md-4 col-8">
                            <address class="norm">
                                <p class="d-flex"><b class="flex-1">
                                        {{ __('order::frontend.orders.invoice.order_id') }}
                                    </b>{{ $order->id }}</p>
                                <p class="d-flex"><b
                                            class="flex-1">{{ __('order::frontend.orders.invoice.date') }}</b>{{ $order->created_at }}
                                <p>
                                <p class="d-flex"><b
                                            class="flex-1">{{ __('order::frontend.orders.invoice.method') }}</b>
                                    @if($order->transactions->method == 'cash')
                                        {{ __('order::frontend.orders.invoice.cash') }}
                                    @else
                                        {{ __('order::frontend.orders.invoice.online') }}
                                    @endif
                                </p>
                            </address>
                        </div>
                    </div>
                </div>

                <div class="invoice-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h1>{{ __('order::frontend.orders.invoice.client_address.receiver') }}</h1>


                            @php $address = $order->orderAddress ?? $order->unknownOrderAddress; @endphp
                            @if ($address)
                            <address class="norm">
                                <p class="d-flex">
                                    <b class="flex-1"><span class="bold uppercase">
                                        {{ optional(optional(optional($address)->state)->city)->title }}
                                        /
                                        {{ optional(optional($address)->state)->title }}
                                    </span>
                                </p>
                                <p class="d-flex">
                                    <b class="flex-1">

                                    @if(optional($address)->address_type != 'local')
                                        <span class="bold uppercase">
                                            @if(optional($address)->json_data['country_id'])
                                                {{Modules\Area\Entities\Country::find(optional($address)->json_data['country_id'])->title}}
                                                /
                                            @endif
                                            @if(@optional($address)->json_data['city'])
                                                {{@optional($address)->json_data['city']}}
                                            @endif
                                        </span>
                                    @endif
                                <p>
                                @if (optional($address)->address_type)
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::dashboard.orders.show.address.shipping_type') }}
                                            :</b>
                                            {{ optional($address)->address_type }}
                                    </p>
                                @endif
                                <p class="d-flex">
                                    <b class="flex-1">{{ __('order::dashboard.orders.show.address.details') }}
                                        :</b>
                                        {{ optional($address)->address ?? '---' }}
                                </p>
                                @php $addresstAttrs = optional(optional(optional($address))->attributes())->get(['name','value','type']); @endphp

                                @foreach($addresstAttrs as $attr)
                                    @if($attr->type == 'file')
                                        <p class="d-flex">
                                            <b class="flex-1">
                                                {{$attr->name}} :</b>
                                                <a href="{{asset($attr->value)}}"><i class="fa fa-file"></i></a>
                                        </p>
                                    @elseif($attr->type == 'countryAndStates' && optional($address)->address_type == 'local')
                                        <p class="d-flex">
                                            <b class="flex-1">
                                                {{$attr->name}} :</b>

                                            @inject('states','Modules\Area\Entities\State')
                                            @php $state = optional(optional($states)->find($attr->value)) @endphp

                                            @if(optional($address)->address_type == 'local')
                                                {{ optional(optional(optional($state)->city)->country)->title }}
                                                /
                                                {{ optional(optional($state)->city)->title }}
                                                /
                                                {{ optional($state)->title }}
                                            @endif
                                        </p>
                                    @else
                                        <p class="d-flex">
                                            <b class="flex-1">
                                                {{$attr->name}} :</b>
                                                {{$attr->value}}
                                        </p>
                                    @endif
                                @endforeach
                            </address>
                            @endif
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <table class="inventory">
                        <thead>
                        <tr>
                            <th><span> #</span></th>
                            <th><span>{{ __('order::frontend.orders.invoice.product_title') }}</span></th>
                            <th><span>{{ __('order::frontend.orders.invoice.product_qty') }}</span></th>
                            <th><span>{{ __('order::frontend.orders.invoice.product_price') }}</span></th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($order->orderProducts) > 0)
                            @foreach ($order->orderProducts as $key => $orderProduct)
                                <tr class="{{ ++$key % 2 == 0 ? 'even' : '' }}">
                                    <td><span>{{ $key }}</span></td>
                                    @if (isset($orderProduct->product_variant_id) || $orderProduct->product_variant_title)
                                        <td>
                                            <span>
                                                {!!
                                                    $orderProduct->product_variant_id ?
                                                        generateVariantProductData($orderProduct->variant->product,
                                                        $orderProduct->product_variant_id,
                                                        $orderProduct->variant->productValues->pluck('option_value_id')->toArray())['name']
                                                    :
                                                        $orderProduct->product_variant_title
                                                !!}
                                            </span>
                                        </td>
                                    @else
                                        <td><span>{{ $orderProduct->product_id ? $orderProduct->product->title : $orderProduct->product_title }}</span></td>
                                    @endif
                                    <td><span>{{ $orderProduct->qty }}</span></td>
                                    <td><span>{{ $orderProduct->price }}</span>
                                        <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                    <table class="balance">
                        <tr>
                            <th><span>{{ __('order::frontend.orders.invoice.subtotal') }}</span></th>
                            <td>
                                <span>{{ $order->subtotal }}</span>
                                <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th><span>{{ __('order::frontend.orders.invoice.shipping') }}</span></th>
                            <td>
                                <span>{{ $order->shipping }}</span>
                                <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                            </td>
                        </tr>
                        <tr class="price">
                            <th><span>{{ __('order::frontend.orders.invoice.total') }}</span></th>
                            <td>
                                <span>{{ $order->total }}</span>
                                <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="invoice-footer">
                <button class="btn btn-them print-invoice main-custom-btn"><i class="ti-printer"></i> {{ __('order::frontend.orders.invoice.btn.print') }}</button>
            </div>
        </div>
    </div>
@endsection
