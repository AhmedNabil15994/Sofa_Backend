@extends('apps::frontend.layouts.master')
@section('title', __('catalog::frontend.cart.title'))

@push('styles')
    <style>
        .product-dim .btn, .product-dim .btn:hover {
            color: #000;
            background: transparent;
            border: 1px solid #000;
        }

        /* start loader style */

        .giftCartLoaderDiv, .cardCartLoaderDiv, .addonsCartLoaderDiv {
            display: none;
            margin: 15px auto;
            justify-content: center;
        }

        .loaderDiv {
            display: none;
            margin: 15px 35px;
            justify-content: center;
        }

        .loaderDiv .my-loader, .giftCartLoaderDiv .my-loader, .cardCartLoaderDiv .my-loader, .addonsCartLoaderDiv .my-loader {
            border: 10px solid #f3f3f3;
            border-radius: 50%;
            border-top: 10px solid #3498db;
            width: 70px;
            height: 70px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
        }

        /* end loader style */

        .empty-cart-title {
            text-align: center;
        }

    </style>
@endpush

@section('content')

    @if(count(getCartContent()) > 0)
        <div class="container">
            <div class="page-crumb mt-30">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><i
                                        class="ti-home"></i> {{__('apps::frontend.master.home')}}</a></li>
                        <li class="breadcrumb-item active text-muted"
                            aria-current="page"> {{ __('catalog::frontend.cart.title') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="inner-page">
                <div class="row">
                    <div class="col-md-8">
                        @include('apps::frontend.layouts._alerts')
                        <div class="cart-inner cart-page">
                            <div class="cart-items">
                                <h2 class="cart-title">المنتجات</h2>

                                @foreach ($items as $item)
                                    <div class="cart-item media align-items-center">
                                        <div class="pro-det d-flex align-items-center">
                                            <div class="pro-img">
                                                <img class="img-fluid"
                                                     src="{{ url($item->attributes->product->image) }}" alt="">
                                            </div>
                                            <div class="media-body">
                                                <span class="product-name">
                                                    @if($item->attributes->product_type == 'variation')
                                                        <a href="{{ route('frontend.products.index', [$item->attributes->product->product->slug, generateVariantProductData($item->attributes->product->product, $item->attributes->product->id, $item->attributes->selectedOptionsValue)['slug']]) }}">
                                                            {!! generateVariantProductData($item->attributes->product->product, $item->attributes->product->id, $item->attributes->selectedOptionsValue)['name'] !!}
                                                        </a>
                                                    @else
                                                        <a href="{{ url(route('frontend.products.index', [$item->attributes->product->slug])) }}">
                                                            {{ $item->attributes->product->title }}
                                                        </a>
                                                    @endif
                                                </span>

                                                <span class="price d-block">
                                                    {{ priceWithCurrenciesCode($item->price) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div id="loaderDiv-{{ $item->attributes->product->id }}" style="display: none">
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border" role="status"
                                                     style="width: 2rem; height: 2rem;">
                                                    <span class="sr-only">{{__('apps::frontend.Loading')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro-counting">
                                            <div class="quantity">
                                                <form class="form"
                                                      @if($item->attributes->product_type == 'product')
                                                      action="{{ url(route('frontend.shopping-cart.create-or-update', [ $item->attributes->product->slug ])) }}"
                                                      @else
                                                      action="{{ url(route('frontend.shopping-cart.create-or-update', [ $item->attributes->product->product->slug, $item->attributes->product->id])) }}"
                                                      @endif
                                                      method="POST"
                                                      data-id="{{ $item->attributes->product->id }}">
                                                    @csrf

                                                    <input type="hidden"
                                                           id="productImage-{{ $item->attributes->product->id }}"
                                                           value="{{ url($item->attributes->product->image) }}">
                                                    <input type="hidden"
                                                           id="productTitle-{{ $item->attributes->product->id }}"
                                                           value="{{ $item->attributes->product_type == 'product' ? $item->attributes->product->title : $item->attributes->product->product->title }}">
                                                    <input type="hidden"
                                                           id="productType-{{ $item->attributes->product->id }}"
                                                           value="{{ $item->attributes->product_type == 'product' ? 'product' : 'variation' }}">

                                                    <div class="buttons-added quantity"
                                                         id="quantityContainer-{{ $item->attributes->product->id }}">
                                                        <button class="sign plus btnIncDecQty"><i class="fa fa-plus"></i></button>
                                                        <input type="text" value="{{ $item->quantity }}" title="Qty"
                                                               class="input-text qty text"
                                                               size="1">
                                                        <button class="sign minus btnIncDecQty"><i class="fa fa-minus"></i></button>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="text-left">
                                            <a href="{{ url(route('frontend.shopping-cart.delete', [
                                                $item->attributes->product->id,
                                               'product_type' => $item->attributes->product_type])) }}"
                                               class="btn remove"><i class="ti-trash"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="order-summery cart-order-summery">
                            <div class="minicart-content-wrapper">
                                <div class="minicart-footer">
                                    <div class="subtotal d-flex text-center justify-content-center mb-20">
                                        <span class="label"> {{ __('catalog::frontend.cart.subtotal') }} </span>
                                        ( <span class="price cartPrdTotal">{{ priceWithCurrenciesCode(number_format(getCartSubTotal(), 2)) }}</span> )
                                    </div>
                                    <div class="actions">
                                        <a class="btn btn-checkout btn-them main-custom-btn btn-block" href="{{ route('frontend.checkout.index') }}">

                                            {{ __('catalog::frontend.cart.btn.checkout') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="page-content">
            <div class="container">
                <div class="align-items-center text-center">
                    <div class="order-done">
                        <img src="{{asset('frontend/images/empty-cart.png')}}" class="img-fluid"
                             style="max-width: 32%;margin: 34px;" alt="">
                        <h1 class="margin-top-20 margin-bottom-20">{{ __('catalog::frontend.cart.empty') }}!</h1>
                        <p>
                            {{__('apps::frontend.Before proceed to checkout you must add some products to your shopping cart. You will find a lot of interesting products on our "Shop" page.')}}
                        </p>
                        <a href="{{route('frontend.categories.products')}}"
                           class="btn btn-theme2 margin-top-20 main-custom-btn">{{__('apps::frontend.Start Shopping')}}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>

        $(document).on('click', '.btnIncDecQty', function (e) {

            var token = $(this).closest('.form').find('input[name="_token"]').val();
            var action = $(this).closest('.form').attr('action');
            var qty = parseInt($(this).closest('.form').find('.qty').val());

            var productId = $(this).closest('.form').attr('data-id');
            var productType = $(this).closest('.form').find('#productType-' + productId).val();
            var productImage = $(this).closest('.form').find('#productImage-' + productId).val();
            var productTitle = $(this).closest('.form').find('#productTitle-' + productId).val();

            if ($(this).is('.plus')) {
                qty += 1;
            } else {
                if (qty != 0) {
                    qty -= 1;
                }
            }

            e.preventDefault();

            if (parseInt(qty) > 0) {

                $('#loaderDiv-' + productId).show();
                $(this).closest('.form').find('.quantity').hide();

                $.ajax({
                    method: "POST",
                    url: action,
                    data: {
                        "qty": qty,
                        "request_type": 'cart',
                        "product_type": productType,
                        "_token": token,
                    },
                    beforeSend: function () {
                    },
                    success: function (data) {
                        var params = {
                            'productId': productId,
                            'productImage': productImage,
                            'productTitle': productTitle,
                            'productQuantity': qty,
                            'productPrice': data.data.productPrice,
                            'productDetailsRoute': data.data.productDetailsRoute,
                            'cartCount': data.data.cartCount,
                            'cartSubTotal': data.data.subTotal,
                        };

{{--                        @if(!in_array(request()->route()->getName(), ['frontend.shopping-cart.index', 'frontend.checkout.index']))--}}
                        updateHeaderCart(params);
{{--                        @endif--}}
                        // displaySuccessMsg(data);

                    },
                    error: function (data) {
                        $('#loaderDiv-' + productId).hide();
                        $('#quantityContainer-' + productId).show();

                        displayErrorsMsg(data);
                    },
                    complete: function (data) {

                        $('#loaderDiv-' + productId).hide();
                        $('#quantityContainer-' + productId).show();

                        var getJSON = $.parseJSON(data.responseText);

                        if (getJSON.data) {
                            $('#pro-price').html(getJSON.data.subTotal);
                            $('#cartTotalAmount').html(getJSON.data.total);
                        }

                    },
                });
            }

        });

    </script>

@endpush
