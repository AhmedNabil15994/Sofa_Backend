
<header class="scroll-fix">
    <div class="container">
        <div class="box-inner">
            <div class="box-inner-inner">
                <div class="pt-menu mobile-menu hidden-lg" id="pt-menu-4533">        
                <input type="hidden" id="menu-effect-4533" class="menu-effect" value="none">
                <div class="pt-menu-bar">
                    <i class="ion-android-menu" aria-hidden="true"></i>
                    <i class="ion-android-close" aria-hidden="true"></i>
                </div>
                <ul class="ul-top-items">
                    <li class="menu-mobile-title">
                        <h3> {{ config('app.name') }}</h3>
                    </li>                            
                    <li class="li-top-item">
                        <a class="a-top-link a-item"  href="{{ route('frontend.home') }}">
                            <span>{{ __('apps::frontend.master.home') }}</span>
                        </a>
                    </li>
                    @foreach($headerCategories as $k => $category)
                        <li class="li-top-item">
                            <a  class="a-top-link a-item" href="{{route('frontend.categories.'. ($category->show_children ? 'children' : 'products'), $category->slug)}}"
                            >
                                <span> {{ $category->title }} </span>
                            </a>
                        </li>
                    @endforeach

                    <hr style="border-bottom: 0.2rem solid var(--link-hover-color);">
                    
                    <li class="li-top-item">
                        <a class="a-top-link a-item" href="{{ route('frontend.contact_us') }}"> 
                            <span>{{ __('apps::frontend.master.contact_us') }} </span>
                        </a>
                    </li>
                    @if($aboutUs)
                        <li class="li-top-item">
                            <a class="a-top-link a-item" href="{{ $aboutUs ? route('frontend.pages.index', $aboutUs->slug) : '#' }}">
                                <span>{{ __('apps::frontend.master.about_us') }}</span>
                            </a>
                        </li>
                    @endif
                    @if($privacyPage)
                        <li class="li-top-item">
                            <a class="a-top-link a-item" href="{{ $privacyPage ? route('frontend.pages.index', $privacyPage->slug) : '#' }}">
                                <span>{{ __('apps::frontend.Privacy & Policy') }}</span>
                            </a>
                        </li>
                    @endif
                    
                </ul>
            </div>				
                <div id="logo">
                    <a href="{{ route('frontend.home') }}"><img src="{{ config('setting.logo') ? url(config('setting.logo')) : url('frontend/images/header-logo.png') }}" title="" alt="" class="img-responsive"></a>
                </div>
                <div class="col-cart">
                    <div class="inner" style="direction: ltr;">
                        <div class="box-setting btn-group">
                            <button class="dropdown-toggle" data-toggle="dropdown" onclick="
                            window.location.replace('{{auth()->check() ? route('frontend.profile.index') : route('frontend.login')}}');"
                            ></button>
                        </div>
                        <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL((locale() == 'en' ?
                        'ar' : 'en'), null, [], true) }}" id="wishlist-total" alt="{{locale() == 'en' ? 'عربي' : 'English'}}">
                            <span><span class="text-wishlist"></span></span>
                        </a>

                        <div id="cart" class="btn-group btn-block block-minicart dropdown">
                            <a href="#" class="minicart btn btn-inverse btn-block btn-lg dropdown-toggle">
                                <span id="cartIcon" style="
                                display:{{count(getCartContent(null,true)) > 0 ? 'block' : 'none'}}">
                                    
                                    <span class="txt-count" id="cartPrdCount">
                                        {{count(getCartContent()) > 0 ? count(getCartContent()) : ''}}
                                    </span>
                                    <span class="text-cart">
                                        {{ priceWithCurrenciesCode(getCartSubTotal()) }}
                                    </span>
                                </span>
                            </a>
                            
                            <div class="dropdown-menu">
                                <div class="minicart-content-wrapper">
                                    <div id="cartItemsInfo">
                                        @if(count(getCartContent()) > 0)
                                            <div class="subtitle">
                                                {{ __('catalog::frontend.cart.you_have') }}
                                                <b>( {{ count(getCartContent()) }} )</b>
                                                {{ __('catalog::frontend.cart.products_in_your_cart') }}
                                            </div>
                                        @else
                                            <div class="empty-subtitle">{{ __('catalog::frontend.cart.empty') }}</div>
                                        @endif
                                    </div>
                                    <div class="minicart-items-wrapper">
                                        <ol class="minicart-items">

                                            @foreach(getCartContent() as $k => $item)
                                                <li class="product-item"
                                                    id="prdList-{{ $item->attributes->product->id }}">
                                                    <div class="media align-items-center">
                                                        <div class="pro-img d-flex align-items-center">
                                                            <img class="img-fluid"
                                                                 src="{{ url($item->attributes->product->image) }}">
                                                        </div>
                                                        <div class="media-body">
                                                        <span class="product-name">
                                                            @if($item->attributes->product_type == 'variation')
                                                                <a
                                                                        href="{{ route('frontend.products.index', [$item->attributes->product->product->slug, generateVariantProductData($item->attributes->product->product, $item->attributes->product->id, $item->attributes->selectedOptionsValue)['slug']]) }}">
                                                                    {!! generateVariantProductData($item->attributes->product->product, $item->attributes->product->id,
                                                                     $item->attributes->selectedOptionsValue)['name'] !!}
                                                                </a>
                                                            @else
                                                                <a
                                                                        href="{{ route('frontend.products.index', [$item->attributes->product->slug]) }}">
                                                                    {{ $item->attributes->product->title }}
                                                                </a>
                                                            @endif
                                                        </span>
                                                            <div class="product-price d-block"><span
                                                                        class="text-muted">x {{ $item->quantity }}</span>
                                                                <span>
                                                                {{ priceWithCurrenciesCode($item->price) }}
                                                            </span>
                                                            </div>
                                                        </div>

                                                        @if(!in_array(request()->route()->getName(),
                                                                            ['frontend.shopping-cart.index', 'frontend.checkout.index']))
                                                            <button type="button" class="btn remove"
                                                                    onclick="deleteFromCartByAjax('{{ $item->attributes->product->id }}', '{{ $item->attributes->product->product_type }}')">

                                                                <i class="ti-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    <div class="minicart-footer" id="cart_footer_content">
                                        <div class="subtotal">
                                            <span class="label"> {{ __('catalog::frontend.cart.subtotal') }} :</span>
                                            <span class="price cartPrdTotal">
                                            {{ priceWithCurrenciesCode(getCartSubTotal()) }}
                                        </span>
                                        </div>
                                        <div class="actions">
                                            <a class="btn btn-viewcart secondary-custom-btn"
                                               href="{{ route('frontend.shopping-cart.index') }}">
                                                <i class="ti-shopping-cart-full"></i>
                                                {{ __('catalog::frontend.cart.cart_details') }}
                                            </a>
                                            <a class="btn btn-checkout main-custom-btn"
                                               href="{{ route('frontend.checkout.index') }}">
                                                <i class="ti-wallet"></i>
                                                {{ __('catalog::frontend.cart.checkout') }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-search">
                <div id="search-by-category">
                    <div class="dropdown-toggle search-button" data-toggle="dropdown"></div>
                    <div class="dropdown-menu search-content" style="height: 55px;">
                        <div class="search-container" style="background-color: white;border-bottom: 0.2px solid #00000047;">
                            
                            <form method="get" action="{{route('frontend.categories.products')}}" style="display: inline-flex;">
                                <input 
                                    type="text" 
                                    id="text-search" 
                                    class=""
                                    onkeyup="showResult(this.value,'{{route('frontend.home.filter')}}')" autocomplete="off"
                                    placeholder="{{__('apps::frontend.Search for a product')}}"
                                    name="s"
                                    value="{{request('s')}}">
                                <div id="sp-btn-search" class="">
                                    <button type="button" id="btn-search-category" class="btn btn-default btn-lg">
                                        <span class="hidden-xs">Search</span>
                                    </button>
                                </div>
                            </form>
                            
                        </div>
                        <div class="xdsoft_autocomplete">
                            <div id="livesearch" class="xdsoft_autocomplete_dropdown" style="top: -13px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-hoz">
            <div class="header-menu">
                <ul class="header-nav" style="font-weight: 700;"> 
                    <li>
                        <a href="{{ route('frontend.home') }}">{{ __('apps::frontend.master.home') }}</a>
                    </li>
                    @if(count($headerCategories) > 0)
                       
                        @foreach($headerCategories as $k => $category)

                            <li>
                                <a href="{{route('frontend.categories.'. ($category->show_children ? 'children' : 'products'), $category->slug)}}">
                                    {{ $category->title }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                    <li>
                        <a href="{{ route('frontend.contact_us') }}"> {{ __('apps::frontend.master.contact_us') }} </a>
                    </li>
                    @if($aboutUs)
                        <li>
                            <a href="{{ $aboutUs ? route('frontend.pages.index', $aboutUs->slug) : '#' }}">{{ __('apps::frontend.master.about_us') }}</a>
                        </li>
                    @endif
                    @if($privacyPage)
                        <li>
                            <a href="{{ $privacyPage ? route('frontend.pages.index', $privacyPage->slug) : '#' }}">{{ __('apps::frontend.Privacy & Policy') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>