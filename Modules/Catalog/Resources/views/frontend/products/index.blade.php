@extends('apps::frontend.layouts.master')
@section('title', __('apps::frontend.products.details.title'))
@section('meta_description', $product->seo_description ?? '')
@section('meta_keywords', $product->seo_keywords ?? '')
@push('styles')
    <style>
        #loaderDiv {
            display: none;
            margin: 15px auto;
            justify-content: center;
        }

        .color-backet {
            padding: 13px;
            border: 1px solid black;
            border-radius: 17px;
            background-color: #ff000000;
            cursor: pointer;
        }

        .color-selected {
            border: 2px solid white !important;
        }
    </style>
@endpush
@section('content')

    <div class="container">
        <div class="page-crumb mt-30">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}"><i class="ti-home"></i>
                            {{ __('apps::frontend.master.home') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('frontend.categories.products', optional($product->categories()->first())->slug) }}">
                            {{ optional($product->categories()->first())->title }}
                        </a></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page"> {{ $product->title }} </li>
                </ol>
            </nav>
        </div>
        <div class="inner-page">
            <div class="row">
                <div class="col-md-5">
                    @if($product->qty == 0 && $product->qty != null)
                        <div class="ribbon"><span class="danger">{{__('apps::frontend.products.out_of_stock')}}</span></div>
                    @elseif($product->is_new)
                        <div class="ribbon"><span class="primary">{{__('apps::frontend.products.new')}}</span></div>
                    @endif
                    <div class="main-image sp-wrap">
                        
                        <a href="{{ asset($product->image) }}"><img src="{{ asset($product->image) }}"
                                class="img-responsive" alt="{{ $product->title }}"></a>
                        @if ($product->images->count())
                            @foreach ($product->images as $image)
                                <a href="{{ asset('uploads/products/' . $image->image) }}">
                                    <img src="{{ asset('uploads/products/' . $image->image) }}" class="img-responsive"
                                        alt="{{ $product->title }}"></a>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="product-detials">
                        <div class="product-head media align-items-center">
                            <div class="media-body">
                                <h1 id="pro-name">{{ $product->title }}</h1>
                            </div>
                            <div class="text-left">
                                @if (auth()->check() &&
                                    !in_array(
                                        $product->id,
                                        array_column(auth()->user()->favourites->toArray(),
                                            'id')))
                                    <form class="favourites-form" method="POST">
                                        @csrf
                                        <button type="button" class="btn favo-btn"
                                            onclick="generalAddToFavourites('{{ route('frontend.profile.favourites.store', [$product->id]) }}', '{{ $product->id }}')"
                                            id="btnAddToFavourites-{{ $product->id }}">
                                            <i class="fa fa-heart"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="product-summ-det">
                            {!! $product->description !!}
                        </div>
                        <div class="product-summ-det product_sku">
                            <p class="d-flex">
                                <span class="d-inline-block right-side">
                                    {{ __('catalog::frontend.products.sku') }}</span>
                                <span class="d-inline-block left-side">
                                    {{ !is_null($variantPrd) ? $variantPrd->sku : $product->sku }}
                                </span>
                            </p>

                            @if (!is_null($variantPrd))
                                @if ($variantPrd->qty <= (config('setting.products.products_qty_show_in_website') ?? 0))

                                    <p class="d-flex">
                                        <span class="d-inline-block right-side">
                                            {{ __('catalog::frontend.products.remaining_qty') }}</span>
                                        <span class="d-inline-block left-side">
                                            {{ $variantPrd->qty }}
                                        </span>
                                    </p>
                                @endif
                            @else

                                @if ($product->qty <= (config('setting.products.products_qty_show_in_website') ?? 0))
                                    <p class="d-flex">
                                        <span class="d-inline-block right-side">
                                            {{ __('catalog::frontend.products.remaining_qty') }}</span>
                                        <span class="d-inline-block left-side">
                                            {{ $product->qty }}
                                        </span>
                                    </p>
                                @endif
                            @endif
                        </div>

                        <div class="product-summ-det">
                            <div id="successMsg"></div>
                            <div id="responseMsg"></div>

                            @foreach ($product->options as $k => $opt)

                                @php
                                    $productValues = optional($opt->productValues)->unique('option_value_id');
                                    $first = 0;
                                    $max = count($productValues) - 1;
                                @endphp

                                @if (count($productValues))
                                    <input type="hidden" class="product-var-options"
                                        data-option-id="{{ optional($opt->option)->id }}"
                                        id="prdOption-{{ $opt->id }}">
                                    <div class="select select-color">
                                        <h6 class="quantity-title">
                                            {{ optional($opt->option)->title }}
                                        </h6>

                                        @if ($opt->option->value_type == 'color')
                                            @foreach ($productValues as $optValue)
                                                <label
                                                    class="color-backet product-var-options-values-{{ optional($opt->option)->id }}"
                                                    data-option-value-status="false"
                                                    data-option-value-id="{{ optional($optValue->optionValue)->id }}"
                                                    id="prdOptionValue-{{ $opt->id }}"
                                                    data-product-id="{{ $product->id }}"
                                                    data-option-id="{{ optional($opt->option)->id }}"
                                                    style="background-color: {{ optional($optValue->optionValue)->color }}"
                                                    onclick="selectVariantColor(this)"
                                                    title="{{ optional($optValue->optionValue)->title }}"></label>
                                            @endforeach
                                        @else
                                            @foreach ($productValues as $i => $optValue)
                                                @if ($first == 0)
                                                    <div class="form-group">
                                                        <select class="select2 select-size options-value-select"
                                                            onchange="callGetVariationInfo(this)">
                                                            <option disabled selected>
                                                                {{ __('apps::frontend.master.select') }}</option>
                                                @endif

                                                <option
                                                    class="form-control product-var-options-values-{{ optional($opt->option)->id }}"
                                                    data-option-value-status="false"
                                                    data-option-value-id="{{ optional($optValue->optionValue)->id }}"
                                                    id="prdOptionValue-{{ $opt->id }}"
                                                    data-product-id="{{ $product->id }}"
                                                    data-option-id="{{ optional($opt->option)->id }}">
                                                    {{ optional($optValue->optionValue)->title }}
                                                </option>

                                                @if ($first == $max)
                                                    </select>
                                    </div>
                                @endif

                                @php $first ++; @endphp
                            @endforeach
                            @endif

                        </div>
                        @endif
                        @endforeach
                    </div>
                    <div class="product-summ-price">
                        <form class="form" id="productDetailsForm" action="{{ $formAction }}" method="POST"
                            data-id="{{ $formDataId }}">
                            @csrf
                            <div id="addVariantPrdToCartSection">
                                @if (count($product->inputAttributes()) > 0)
                                    @foreach ($product->inputAttributes() as $key => $attribute)
                                        <div class="form-group">
                                            <label>
                                                {{ $attribute->translate('name', locale()) }}
                                                @if ($attribute->price)
                                                    <b> / {{ __('catalog::frontend.products.attribute_price') }}: </b>
                                                    {{ priceWithCurrenciesCode($attribute->price) }}
                                                @endif
                                            </label>
                                            @if ($attribute->type == 'text')
                                                <input type="{{ $attribute->type }}"
                                                    class="form-control productInputsAttributes"
                                                    data-id="{{ $attribute->id }}"
                                                    {{ $attribute->validation['required'] == 1 ? 'required' : '' }}
                                                    name="productAttributes[{{ $attribute->id }}]" autocomplete="off"
                                                    value="{{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) ? getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] : '' }}" />
                                            @elseif($attribute->type == 'drop_down')
                                                <select class="form-control productInputsAttributes"
                                                    {{ $attribute->validation['required'] == 1 ? 'required' : '' }}
                                                    data-id="{{ $attribute->id }}"
                                                    name="productAttributes[{{ $attribute->id }}]">
                                                    @foreach ($attribute->options as $option)
                                                        <option value="{{ $option->id }}"
                                                            {{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) && $option->id == getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] ? 'selected' : '' }}>
                                                            {{ $option->value }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($attribute->type == 'radio')
                                                <div class="row">
                                                    @foreach ($attribute->options as $option)
                                                        <div class="col-md-4">
                                                            <label
                                                                for="radi_{{ $option->id }}">{{ $option->value }}</label>
                                                            <input type="radio" class="productInputsAttributes"
                                                                name="productAttributes[{{ $attribute->id }}]"
                                                                data-id="{{ $attribute->id }}"
                                                                id="radi_{{ $option->id }}"
                                                                {{ $attribute->validation['required'] == 1 ? 'required' : '' }}
                                                                {{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) && $option->id == getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] ? 'checked' : '' }}
                                                                value="{{ $option->id }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($attribute->type == 'boolean')
                                                <input type="checkbox" class="productInputsAttributes"
                                                    name="productAttributes[{{ $attribute->id }}]"
                                                    data-id="{{ $attribute->id }}"
                                                    {{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) && getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] == 'on' ? 'checked' : '' }}
                                                    {{ $attribute->validation['required'] == 1 ? 'required' : '' }} />
                                            @elseif($attribute->type == 'file')
                                                <input type="{{ $attribute->type }}"
                                                    class="form-control productInputsAttributes"
                                                    name="productAttributes[{{ $attribute->id }}]"
                                                    data-id="{{ $attribute->id }}"
                                                    onchange="readURL(this, 'imgUploadPreview-{{ $attribute->id }}', 'single');"
                                                    @if (!key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? [])) {{ $attribute->validation['required'] == 1 ? 'required' : '' }} @endif
                                                    value="" />
                                                @if (key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) &&
                                                    !is_null(getCartItemById($product->id)->attributes['productAttributes'][$attribute->id]))
                                                    <img src="{{ url(getCartItemById($product->id)->attributes['productAttributes'][$attribute->id]) }}"
                                                        id="imgUploadPreview-{{ $attribute->id }}"
                                                        class="img-thumbnail img-responsive img-preview"
                                                        style="height: 150px; width: 250px;" alt="attribute image">
                                                @else
                                                    <img src="#" id="imgUploadPreview-{{ $attribute->id }}"
                                                        class="img-thumbnail img-responsive img-preview"
                                                        style="height: 150px; width: 250px; display: none;"
                                                        alt="attribute image">
                                                @endif
                                            @else
                                                <input type="{{ $attribute->type }}"
                                                    class="form-control productInputsAttributes"
                                                    name="productAttributes[{{ $attribute->id }}]"
                                                    data-id="{{ $attribute->id }}" autocomplete="off"
                                                    {{ $attribute->validation['required'] == 1 ? 'required' : '' }}
                                                    value="{{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) ? getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] : '' }}" />
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                                <span class="price">
                                </span>
                                <span class="price have-discount">
                                    
                                    @if($product->offer)
                                    @if(!is_null($product->offer->offer_price))
                                        <span
                                                class="price-before">{{ priceWithCurrenciesCode($product->price) }}</span>
                                        {{ priceWithCurrenciesCode($product->offer->offer_price) }}
                                    @else
                                        <span style="text-decoration: line-through;color: red">{{ priceWithCurrenciesCode($product->price) }}</span>
                                        /
                    
                                        {{ priceWithCurrenciesCode(calculateOfferAmountByPercentage($product->price, $product->offer->percentage)) }}
                                    @endif
                                @else
                                    {{ priceWithCurrenciesCode($product->price) }}
                                @endif
                                </span>

                                <input type="hidden" id="productImage-{{ $product->id }}"
                                    value="{{ url($product->image) }}">
                                <input type="hidden" id="productTitle-{{ $product->id }}"
                                    value="{{ $product->title }}">
                                <input type="hidden" id="productType" value="product">
                                <input type="hidden" id="selectedOptions" value="">
                                <input type="hidden" id="selectedOptionsValue" value="">
                                
                                @if(($product->qty > 0) || $product->qty == null)
                                    <div class="align-items-center d-flex">
                                        <h5>
                                            {{ __('catalog::frontend.products.quantity') }}
                                        </h5>
                                        <div class="quantity">
                                            <div class="buttons-added single-product-buttons-added ">
                                                <button class="sign plus single-product-plus"><i
                                                        class="fa fa-chevron-up"></i></button>
                                                <input type="text" id="prodQuantity" name="qty"
                                                    value="{{ getCartItemById($product->id) ? getCartItemById($product->id)->quantity : '1' }}"
                                                    title="Qty" class="input-text qty text" size="1">
                                                <button class="sign minus single-product-minus"><i
                                                        class="fa fa-chevron-down"></i></button>
                                            </div>
                                        </div>
                                        <button id="btnAddToCart" type="submit" class="btn btn-them main-custom-btn">
                                            <i class="ti-shopping-cart"></i>
                                            {{ __('catalog::frontend.products.add_to_cart') }}
                                        </button>
                                        <div id="loaderDiv" style="margin:0px 46px">
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border" role="status"
                                                    style="width: 2rem; height: 2rem;">
                                                    <span class="sr-only">{{ __('apps::frontend.Loading') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if (count($related_products))
            <div class="home-products mt-40 mb-0">
                <h3 class="slider-title">
                    {{ __('apps::frontend.Recently viewed') }}
                </h3>
                <div class="owl-carousel products-slider">
                    @foreach ($related_products as $k => $record)
                        @include('catalog::frontend.products.components.single-product', [
                            'product' => $record,
                        ])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        $(document).ready(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var submitActor = null;
            var $form = $('#productDetailsForm');
            // var $submitActors = $form.find('button[type=submit]');

            $form.submit(function(e) {
                e.preventDefault();

                let token = $(this).find('input[name="_token"]').val();
                let action = $(this).attr('action');
                let qty = $('#prodQuantity').val();
                let prodQuantity = $('#prodQuantity').val();
                let notes = $('#notes').val();
                let productId = $(this).attr('data-id');
                let productType = $(this).find('#productType').val();
                let productImage = $(this).find('#productImage-' + productId).val();
                let productTitle = $(this).find('#productTitle-' + productId).val();
                let selectedOptions = $(this).find('#selectedOptions').val();
                let selectedOptionsValue = $(this).find('#selectedOptionsValue').val();
                let outputproductAttributes = {};
                let productAttributes = $(".productInputsAttributes").map(function(){ 
                    outputproductAttributes[$(this).attr('data-id')] = $(this).val()
                    }).get();
                    
                if (parseInt(qty) > 0) {

                    $('#btnAddToCart').hide();
                    $('#btnAddToCartCheckout').hide();
                    $('#loaderDiv').show();

                    let data = {
                            "qty": qty,
                            "request_type": 'product',
                            "product_type": productType,
                            "selectedOptions": selectedOptions,
                            "selectedOptionsValue": selectedOptionsValue,
                            "productAttributes": outputproductAttributes,
                            "notes": notes ? notes : null,
                            "_token": token,
                        };

                    $.ajax({
                        method: "POST",
                        url: action,
                        data: data,
                        beforeSend: function() {},
                        success: function(data) {
                            var params = {
                                'productId': productId,
                                'productImage': productImage,
                                'productTitle': data.data.productTitle,
                                'productQuantity': data.data.productQuantity,
                                'productPrice': data.data.productPrice,
                                'productDetailsRoute': data.data.productDetailsRoute,
                                'cartCount': data.data.cartCount,
                                'cartSubTotal': data.data.subTotal,
                                'product_type': productType,
                            };

                            updateHeaderCart(params);

                            if (data.data.remainingQty != null && data.data.remainingQty <= 3) {
                                var qty = `
                                    <p class="d-flex">
                                        <span class="d-inline-block right-side">{{ __('catalog::frontend.products.remaining_qty') }}</span>
                                        <span class="d-inline-block left-side">${data.data.remainingQty}</span>
                                    </p>
                                `;
                                $('#remainingQtySection').html(qty);
                            }

                            var msg = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    ${data.message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#successMsg').html(msg);

                            if (submitActor === 'btn_add_to_cart_and_checkout')
                                window.location.replace(
                                    "{{ route('frontend.checkout.index') }}");

                            {{-- if (submitActor.name === 'btn_add_to_cart_and_checkout')
                                window.location.replace("{{ route('frontend.checkout.index') }}"); --}}
                        },
                        error: function(data) {
                            $('#loaderDiv').hide();
                            $('#btnAddToCart').show();
                            $('#btnAddToCartCheckout').show();
                            // displayErrorsMsg(data);

                            let getJSON = $.parseJSON(data.responseText);
                            let error = '';
                            if (getJSON.errors['notes'])
                                error = getJSON.errors['notes'];
                            else
                                error = getJSON.errors;

                            if (typeof error == 'object')
                                error = error[Object.keys(error)][0];

                            let msg = `
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    ${error}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `;
                            $('#responseMsg').html(msg);
                        },
                        complete: function(data) {
                            $('#loaderDiv').hide();
                            $('#btnAddToCart').show();
                            $('#btnAddToCartCheckout').show();
                        },
                    });
                }

            });

            /*$submitActors.click(function (event) {
                submitActor = this;
            });*/

            $(document).on('click', '#btnAddToCartCheckout, #btnAddToCart', function(e) {
                submitActor = e.target.name;
            });

        });

        function callGetVariationInfo(e) {

            var optionSelected = $("option:selected", e);
            getVariationInfo(optionSelected, optionSelected.attr('data-product-id'), optionSelected.attr('data-option-id'))
        }

        function selectVariantColor(e) {

            var optionSelected = $(e);
            $('.color-backet').removeClass('color-selected');
            optionSelected.addClass('color-selected');
            getVariationInfo(optionSelected, optionSelected.attr('data-product-id'), optionSelected.attr('data-option-id'))
        }

        function getVariationInfo(e, productId, ref) {

            var selectedOptions = [];
            var selectedOptionsValue = [];
            $('.product-var-options-values-' + ref).attr('data-option-value-status', 'false');
            $(e).attr('data-option-value-status', 'true');

            $('#remainingQtySection').empty();
            $('.product-var-options').each(function(i, item) {
                selectedOpt = $(this).attr('data-option-id');
                selectedOptions.push(selectedOpt);

                $('.product-var-options-values-' + selectedOpt).each(function(i, item) {

                    if ($(this).attr('data-option-value-status') === 'true') {
                        $(this).addClass('active');
                        selectedOptionsValue.push($(this).attr('data-option-value-id'));
                    } else {

                        $(this).removeClass('active');
                    }
                });
            });

            if (selectedOptions.length != 0 && !selectedOptionsValue.includes(undefined) && !selectedOptionsValue.includes(
                    "")) {
                $.ajax({
                    method: "GET",
                    url: '{{ route('frontend.get_prd_variation_info') }}',
                    data: {
                        "selectedOptions": selectedOptions,
                        "selectedOptionsValue": selectedOptionsValue,
                        "product_id": productId,
                        "_token": '{{ csrf_token() }}',
                    },
                    beforeSend: function() {
                        $('#loaderDiv').show();
                        $('#btnAddToCart').hide();
                    },
                    success: function(data) {

                        var variantProduct = data.data.variantProduct;
                        var productTitle = data.data.productTitle;

                        if (variantProduct.sku) {
                            $('#skuSection').text('').append(variantProduct.sku);
                        }

                        if (variantProduct.qty <= 3) {
                            var qty = `
                                <p class="d-flex">
                                    <span class="d-inline-block right-side">{{ __('catalog::frontend.products.remaining_qty') }}</span>
                                    <span class="d-inline-block left-side">${variantProduct.qty}</span>
                                </p>
                            `;
                            $('#remainingQtySection').html(qty);
                        }

                        if (productTitle)
                            $('#pro-name').text('').append(productTitle);

                        if (variantProduct.image) {
                            var selectedImg = `
                                <a href="${variantProduct.image}"
                                    id="first-product-img">
                                <img src="${variantProduct.image}" class="img-responsive"
                                        alt="${productTitle}">
                                </a>
                            `;
                            $('#first-product-img').remove();
                            $('#first-product-img').prepend(selectedImg);
                        }

                    },
                    error: function(data) {

                        $('#loaderDiv').hide();
                        $('#btnAddToCart').show();
                        if (data.status == 423 && selectedOptions.length > selectedOptionsValue.length) {

                        } else {

                            displayErrorsMsg(data);
                        }
                    },
                    complete: function(data) {
                        let form = $('#productDetailsForm');
                        var getJSON = $.parseJSON(data.responseText);
                        // console.log('getJSON::', getJSON);
                        form.attr('action',getJSON.data.formAction);
                        form.attr('data-id',getJSON.data.data_id);
                        $('#addVariantPrdToCartSection').html(getJSON.data.form_view);
                    },
                });
            } else {
                $('#addVariantPrdToCartSection').empty();
            }

        }
        $(document).ready(function() {
            // $('.select2').select2();
        });
    </script>
@endpush
