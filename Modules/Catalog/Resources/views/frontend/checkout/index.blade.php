@extends('apps::frontend.layouts.master')
@section('title', __('catalog::frontend.checkout.index.title') )
@push('plugins_styles')
<link rel="stylesheet" href="{{asset('frontend/css/intlTelInput.min.css')}}">
@endpush
@section('content')
<div class="container">
    <div class="page-crumb mt-30">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('frontend.home')}}">
                        <i class="ti-home"></i>
                        {{ __('apps::frontend.nav.home_page') }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('frontend.shopping-cart.index') }}">
                        {{ __('catalog::frontend.cart.title') }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    {{ __('catalog::frontend.checkout.index.title') }}
                </li>
            </ol>
        </nav>
    </div>

    @include('apps::frontend.layouts._alerts')
    <div class="inner-page">
        <form method="post"  enctype="multipart/form-data" action="{{ route('frontend.orders.create_order') }}" id="checkout-form">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="cart-inner">

                        @if(auth()->user())
                            <input type="hidden" name="address_type" id="checkoutAddressType" value="selected_address">


                            <div class="address-types">
                                <h2 class="cart-title"> {{ __('catalog::frontend.checkout.address.title') }} </h2>
                                <div class="panel-group">
                                    <div class="panel panel-default">

                                        <div class="panel-body">
                                            <div class="previous-address choose-add">
                                                <div id="address_container">
                                                    @include('catalog::frontend.address.components.addresses')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="cart-footer pt-40 mb-20 mt-20 text-left">
                            <a href="#" class="btn btn-them main-custom-btn" onclick="openAddressModal()">
                                {{ __('user::frontend.addresses.create.title') }}
                            </a>
                        </div>
                        @else
                        <input type="hidden" name="address_type" id="checkoutAddressType" value="known_address">

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {!!
                                    field('frontend_no_label')->text('username',__('user::frontend.addresses.form.username'))
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    {!!
                                    field('frontend_no_label')->number('mobile',__('user::frontend.addresses.form.mobile'),null,['style'
                                    => 'height: 45px; !important']) !!}
                                </div>
                            </div>

                            @php
                                $addressesAttributesData = [
                                    'container_class' => 'col-md-6 col-12',
                                ];
                            @endphp
                            <x-attributes-inputs type="addresses" inputTheme="address_no_label" :data="$addressesAttributesData"/>
                        </div>
                        @endif

                        <x-attributes-inputs type="checkout" inputTheme="address_no_label"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="order-summery cart-order-summery">
                        <h4 class="order-summ-title">تفاصيل الطلب</h4>
                        <div class="minicart-content-wrapper">
                            <div class="minicart-items-wrapper">
                                <ol class="minicart-items">

                                    @foreach(getCartContent() as $k => $item)
                                        <li class="product-item">
                                            <div class="media align-items-center">
                                                <div class="pro-img d-flex align-items-center">
                                                    <img class="img-fluid"
                                                        src="{{ url($item->attributes->product->image) }}" alt="Author">
                                                </div>
                                                <div class="media-body">
                                                    <span class="product-name">
                                                        @if($item->attributes->product_type == 'product')
                                                        <a
                                                            href="{{ route('frontend.products.index', [$item->attributes->product->slug]) }}">
                                                            {{ $item->attributes->product->title }}
                                                        </a>
                                                        @else
                                                        <a
                                                            href="{{ route('frontend.products.index', [$item->attributes->product->product->slug, generateVariantProductData($item->attributes->product->product, $item->attributes->product->id, $item->attributes->selectedOptionsValue)['slug']]) }}">
                                                            {!!
                                                            generateVariantProductData($item->attributes->product->product,
                                                            $item->attributes->product->id,
                                                            $item->attributes->selectedOptionsValue)['name'] !!}
                                                        </a>
                                                        @endif
                                                    </span>
                                                    <div class="product-price d-block"><span class="text-muted">x {{
                                                            $item->quantity }}</span>
                                                        <span>{{ priceWithCurrenciesCode($item->price) }} </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                            <div class="d-flex mb-20 align-items-center">
                                <span class="d-inline-block right-side flex-1">
                                    {{ __('catalog::frontend.cart.subtotal') }}
                                </span>
                                <span class="d-inline-block left-side">
                                    {{ priceWithCurrenciesCode(number_format(getCartSubTotal(), 3)) }}
                                </span>
                            </div>
                            <div id="deliveryPriceLoaderDiv" style="display: none">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border" role="status" style="width: 2rem; height: 2rem;">
                                        <span class="sr-only">{{__('apps::frontend.Loading')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div id="totalCompaniesDeliveryPrice">

                                @if(Cart::getCondition('company_delivery_fees'))

                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1">
                                        {{ __('catalog::frontend.checkout.shipping') }}
                                    </span>
                                    <span class="d-inline-block left-side">
                                        <span id="totalDeliveryPrice">
                                            {{
                                            priceWithCurrenciesCode(number_format(Cart::getCondition('company_delivery_fees')->getValue(),
                                            3)) }}
                                        </span>
                                    </span>
                                </div>
                                @else
                                <span id="totalDeliveryPrice"></span>
                                @endif
                            </div>

                            <div id="couponContainer">
                                @if(\Cart::getCondition('coupon_discount') != null &&
                                \Cart::getCondition('coupon_discount')->getValue() != 0)

                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1">{{
                                        __('catalog::frontend.cart.coupon_value') }}</span>
                                    <span class="d-inline-block left-side">

                                        {{
                                        priceWithCurrenciesCode(number_format(abs(Cart::getCondition('coupon_discount')->getValue()),
                                        3)) }}
                                    </span>
                                </div>
                                @endif

                                @if(!is_null(getCartItemsCouponValue()) && getCartItemsCouponValue() != 0)
                                    <div class="d-flex mb-20 align-items-center">
                                        <span class="d-inline-block right-side flex-1">
                                            {{ __('catalog::frontend.cart.coupon_value') }}</span>
                                        <span class="d-inline-block left-side">
                                            {{ priceWithCurrenciesCode(number_format(getCartItemsCouponValue(), 3))}}
                                        </span>
                                    </div>
                                @endif

                            </div>

                            <div id="couponForm">
                                <form class="coupon-form" method="POST">
                                    @csrf
                                    <div id="loaderCouponDiv" style="display: none">
                                        <div class="d-flex justify-content-center">
                                            <div class="spinner-border" role="status"
                                                style="width: 2rem; height: 2rem;">
                                                <span class="sr-only">{{__('apps::frontend.Loading')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex promo-code align-items-center justify-content-between">
                                        <div class="d-flex mb-20 promo-code align-items-center">

                                            <input type="hidden" value="" id="coupon_discount_id"
                                                name="coupon_discount_id">
                                            <input type="hidden" value="" id="coupon_discount_value"
                                                name="coupon_discount_value">
                                            <span class="d-inline-block right-side flex-1">
                                                <input type="text" id="txtCouponCode" name=""
                                                    placeholder=" {{ __('catalog::frontend.cart.enter_discount_number') }}">
                                            </span>
                                            <span class="d-inline-block left-side">
                                                <button class="btn btn-add" id="btnCheckCoupon" type="button">
                                                    {{__('apps::frontend.Add')}}
                                                </button>
                                            </span>
                                            <span class="d-inline-block left-side remove" title="ازالة الكوبون"><i
                                                    class="ti-close"></i></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="minicart-footer">
                                @include('catalog::frontend.checkout.components.checkout-payments')
                                <div class="subtotal d-flex text-center justify-content-center mb-20">
                                    <span class="label"> {{ __('catalog::frontend.checkout.total') }} :</span>
                                    <span class="price" id="cartTotalAmount">
                                        {{ priceWithCurrenciesCode(number_format(getCartTotal(), 3)) }}
                                    </span>
                                </div>
                                <div class="actions">
                                    @include('apps::dashboard.layouts._ajax-msg')
                                    <button type="submit" id="submit" class="btn btn-checkout btn-them btn-block main-custom-btn">
                                        {{ __('catalog::frontend.cart.btn.checkout') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@if(auth()->check())
@include('user::frontend.profile.addresses.components.address-model',[
'route' => route('frontend.profile.address.store'),
'view_type' => 'checkout'
])
@endif
@endsection

@push('scripts')
<script src="{{ url('frontend/js/intlTelInput.min.js') }}"></script>
@include('user::frontend.profile.addresses.components.address-model-scripts')

<script>
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function onStateChanged(val) {
            $('#selectedStateFromAddress').val(val);
            getDeliveryPriceOnStateChanged($('#selectedStateFromAddress').val());
        }

        function checkoutSelectCompany(vendorId, companyId) {
            var thisID = '#checkVendorCompany-' + vendorId + '-' + companyId;
            var stateId = $('#selectedStateFromAddress').val();

            // START TO make radio button selected
            $(`.check-${vendorId}`).prop('checked', false);
            $('.vendor-company-' + vendorId + '-' + companyId).toggleClass("cut-radio-style");
            $(`.checkout-company-${vendorId}:not(${thisID})`).removeClass("cut-radio-style");
            // END TO make radio button selected

            if ($('#checkVendorCompany-' + vendorId + '-' + companyId).attr('data-state') == 0) {
                $('.checkout-company-' + vendorId).attr('data-state', 0);
                $('#checkVendorCompany-' + vendorId + '-' + companyId).attr('data-state', 1);
                // $(`.checkout-company:not(${thisID})`).attr('data-state', 0);
                $("input[name='vendor_company[" + vendorId + "]']").val(companyId);

                getStateDeliveryPrice(vendorId, companyId, stateId, 'checked');

            } else {
                $('.checkout-company-' + vendorId).attr('data-state', 0);
                $("input[name='vendor_company[" + vendorId + "]']").val('');
                getStateDeliveryPrice(vendorId, companyId, stateId, 'un_checked');
            }

        }

        function chooseCompanyDeliveryDay(companyId, dayCode) {

            // $('.day-block-company').not('.deliveryDay-' + dayCode).removeClass('active');
            // $('.deliveryDay-' + dayCode).toggleClass("active");
            // console.log('toggle::', $('.deliveryDay-' + dayCode).toggleClass("active"));

            $(this).toggleClass("active");

            if ($('.deliveryDay-' + dayCode).attr('data-state-value') == 0) {
                $('.day-block-company').attr('data-state-value', 0);
                $('.deliveryDay-' + dayCode).attr('data-state-value', 1);
                $("input[name='shipping_company[day]']").val(dayCode);
            } else {
                $('.day-block-company').attr('data-state-value', 0);
                $("input[name='shipping_company[day]']").val('');
            }

        }

        @guest()
        $('.area_selector').on('select2:select', function (e) {
            var data = e.params.data;
            @if(request()->route()->getName() == 'frontend.checkout.index' && auth()->guest())
            getDeliveryPriceOnStateChanged(data.id);
            @endif
        });
        @endguest


        $(document).on('click', '#btnCheckCoupon', function (e) {

            var action = '{{route('frontend.check_coupon')}}';
            var code = $('#txtCouponCode').val();

            e.preventDefault();

            if (code !== '') {

                $('#loaderCouponDiv').show();

                $.ajax({
                    method: "POST",
                    url: action,
                    data: {
                        "code": code,
                    },
                    beforeSend: function () {
                    },
                    success: function (data) {

                        displaySuccessMsg(data['message']);
                    },
                    error: function (data) {
                        displayErrorsMsg(data);
                    },
                    complete: function (data) {

                        $('#loaderCouponDiv').hide();
                        var getJSON = $.parseJSON(data.responseText);
                        if (getJSON.data) {
                            showCouponContainer(getJSON.data.coupon_value, getJSON.data.total);
                        }

                    },
                });
            } else {
                $('#txtCouponCode').focus();
            }

        });

        function getStateDeliveryPrice(vendorId, companyId, stateId, type) {
            var data = {
                'vendor_id': vendorId,
                'company_id': companyId,
                'state_id': stateId,
                'type': type,
            };
            getDeliveryPrice(data, stateId, type, vendorId, companyId);
        }

        function getDeliveryPriceOnStateChanged(stateId, addressId = null) {
            var type = 'selected_state',
                data = {
                    'state_id': stateId,
                    'address_id': addressId,
                    'company_id': $("input[name='shipping_company[id]']").val(),
                    'type': type,
                };
            getDeliveryPrice(data, stateId, type);
        }

        function getDeliveryPrice(data, stateId, type, vendorId = null, companyId = null) {

            $('#deliveryPriceLoaderDiv').show();
            var deliveryPriceRow;

            $.ajax({
                    method: "POST",
                    url: '{{ route('frontend.checkout.get_state_delivery_price') }}',
                    data: $('#checkout-form').serialize(),
                    dataType: 'JSON',
                    cache: false,
                    processData: true,
                    beforeSend: function () {
                    },
                    success: function (data) {
                        var totalCompaniesDeliveryPrice = $('#totalCompaniesDeliveryPrice');

                        if (type === 'selected_state') {

                            $('.checkedCompanyInput').prop('checked', false);
                            $('.checkedCompany').removeClass("cut-radio-style");
                            $('.checkedCompany').attr('data-state', 0);
                            $(".vendor-company-input").val('');

                            deliveryPriceRow = `
                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1"> {{ __('catalog::frontend.checkout.shipping') }}</span>
                                    <span class="d-inline-block left-side"
                                          id="totalDeliveryPrice">${data.data.totalDeliveryPrice}</span>
                                </div>
                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1">
                                        ${data.data.delivery_time}
                                    </span>
                                </div>
                                `;
                            totalCompaniesDeliveryPrice.html(deliveryPriceRow);

                        } else {

                            if (data.data.price != null) {
                                deliveryPriceRow = `
                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1"> {{ __('catalog::frontend.checkout.shipping') }}</span>
                                    <span class="d-inline-block left-side"
                                          id="totalDeliveryPrice">${data.data.totalDeliveryPrice}</span>
                                </div>

                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1">
                                        ${data.data.delivery_time}
                                    </span>
                                </div>
                                `;
                                totalCompaniesDeliveryPrice.html(deliveryPriceRow);
                            }

                        }

                    },
                    error: function (data) {
                        $('#deliveryPriceLoaderDiv').hide();
                        displayErrorsMsg(data);

                        @guest()
                            $(".area_selector").val("");
                            $(".area_selector").select2();
                        @else
                            $('input[name="selected_address_id"]').prop('checked', false);
                        @endguest
                        var getJSON = $.parseJSON(data.responseText);

                        if (getJSON.data.price == null) {

                            if (type !== 'selected_state') {
                                $('.checkout-company-' + vendorId).removeClass("cut-radio-style");
                                $("input[name='vendor_company[" + vendorId + "]']").val('');
                            }

                            var totalCompaniesDeliveryPrice = $('#totalCompaniesDeliveryPrice');
                            deliveryPriceRow = `
                                <div class="d-flex mb-20 align-items-center">
                                    <span class="d-inline-block right-side flex-1"> {{ __('catalog::frontend.checkout.shipping') }}</span>
                                    <span class="d-inline-block left-side"
                                          id="totalDeliveryPrice">${data.data.totalDeliveryPrice}</span>
                                </div>
                                `;
                            totalCompaniesDeliveryPrice.html(deliveryPriceRow);
                        }
                    },
                    complete: function (data) {
                        $('#deliveryPriceLoaderDiv').hide();
                        var getJSON = $.parseJSON(data.responseText);
                        if (getJSON.data) {
                            $('#cartTotalAmount').html(getJSON.data.total);
                        }
                    },
                }
            );
        }
</script>

<script>
    $('#checkout-form').on('submit', function (e) {

        e.preventDefault();

        var url = $(this).attr('action');
        var method = $(this).attr('method');

        $.ajax({

            url: url,
            type: method,
            dataType: 'JSON',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,

            beforeSend: function () {
                $('#submit').prop('disabled', true);
                resetErrors();
            },
            success: function (data) {

                $('#submit').prop('disabled', false);
                $('#submit').text();

                if (data[0] == true) {
                    redirectAfterOrderSaved(data);
                } else {
                    displayMissing(data);
                }

            },
            error: function (data) {

                $('#submit').prop('disabled', false);
                displayErrors(data);

            },
        });

    });


    function redirectAfterOrderSaved(data) {
        if (data['url']) {
            var url = data['url'];

            if (url) {

                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data[1],
                    text: data['desc'],
                    showConfirmButton: false,
                });
                if (data['blank'] && data['blank'] == true) {

                    window.open(url, '_blank');
                } else {
                    window.location.replace(url);
                }
            }
        }
    }

// function successfully(data) {
//     toastr["success"](data[1]);
//     $('.progress-info').hide();
//     $('.progress-bar').width('0%');
//     $('#dataTable').DataTable().ajax.reload();

// }
</script>
@endpush
