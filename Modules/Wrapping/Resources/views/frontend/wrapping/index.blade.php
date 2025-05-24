@extends('apps::frontend.layouts.master')
@section('title', __('wrapping::frontend.wrapping.gift_wrapper'))

@section('externalStyle')
    <style>

        /* start loader style */

        .loaderDiv {
            display: none;
            margin: 15px 335px;
            justify-content: center;
        }

        #loaderCouponDiv {
            display: none;
            margin: 15px 100px;
            justify-content: center;
        }

        .loaderDiv .my-loader, #loaderCouponDiv .my-loader {
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
@endsection

@section('content')

    <div class="container">
        <div class="page-crumb mt-30">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.home') }}">
                            <i class="ti-home"></i> {{ __('apps::frontend.nav.home_page') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.shopping-cart.index') }}">{{ __('catalog::frontend.cart.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active text-muted"
                        aria-current="page">{{ __('wrapping::frontend.wrapping.gift_wrapper') }}</li>
                </ol>
            </nav>
        </div>
        <div class="inner-page">
            <div class="row">
                <div class="col-md-8">
                    <div class="cart-inner wrap-page">
                        <h3 class="slider-title">
                            <i class='ti-gift'></i> {{ __('wrapping::frontend.wrapping.gift_wrapper') }}</h3>
                        <div class="owl-carousel giftWarp-slider choose-warp mb-30">

                            @foreach($items['gifts'] as $k => $gift)
                                <div class="product-grid gift-wrap" data-toggle="modal"
                                     data-target="#warp-details-{{ $gift->id }}">
                                    <div class="product-image d-flex align-items-center">
                                        <img class="pic-1" src="{{ url($gift->image) }}">
                                    </div>
                                    <div class="product-content">
                                        <h3 class="title">{{ $gift->title }}</h3>
                                        <div class="d-flex">
                                            <span class="price d-inline-block right-side">{{ $gift->price }} {{ __('apps::frontend.master.kwd') }}</span>

                                            {{--<div class="warp-time d-inline-block left-side"> مدة التغليف:
                                                <span>48 ساعة</span>
                                            </div>--}}

                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <h3 class="slider-title">
                            <i class='ti-gallery'></i> {{ __('wrapping::frontend.wrapping.congratulation_card') }}
                        </h3>
                        <div class="owl-carousel giftWarp-slider choose-card mb-30">

                            @foreach($items['cards'] as $k => $card)
                                <div class="product-grid gift-wrap" data-toggle="modal"
                                     data-target="#card-details-{{ $card->id }}">
                                    <div class="product-image d-flex align-items-center">
                                        <img class="pic-1" src="{{ url($card->image) }}">
                                    </div>
                                    <div class="product-content">
                                        <h3 class="title"> {{ $card->title }}</h3>
                                        <span class="price">{{ $card->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <h3 class="slider-title">
                            <i class='ti-wand'></i> {{ __('wrapping::frontend.wrapping.additions') }} </h3>
                        <div class="owl-carousel giftWarp-slider choose-additions">

                            @foreach($items['addons'] as $k => $addons)
                                <div class="product-grid gift-wrap" data-toggle="modal"
                                     data-target="#addition-details-{{ $addons->id }}">
                                    <div class="product-image d-flex align-items-center">
                                        <img class="pic-1" src="{{ url($addons->image) }}">
                                    </div>
                                    <div class="product-content">
                                        <h3 class="title"> {{ $addons->title }}</h3>
                                        <span class="price">{{ $addons->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        {{--<div class="pt-40 mb-20 mt-10 text-left">
                            <a href="{{ route('frontend.checkout.index') }}"
                               class="btn btn-them"> {{ __('wrapping::frontend.wrapping.btn.complete_payment') }} </a>
                        </div>--}}
                    </div>
                </div>

                <div class="col-md-4">

                    @include('catalog::frontend.shopping-cart._total-side')

                </div>

            </div>
        </div>
    </div>

    @foreach($items['gifts'] as $k => $gift)
        @include('wrapping::frontend.wrapping._gift_wrap', ['giftObject'=> $gift])
    @endforeach

    @foreach($items['cards'] as $k => $card)
        @include('wrapping::frontend.wrapping._card_wrap', ['cardObject'=> $card])
    @endforeach

    @foreach($items['addons'] as $k => $addons)
        @include('wrapping::frontend.wrapping._addons_wrap', ['addonsObject'=> $addons])
    @endforeach

@endsection

@section('externalJs')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endsection
