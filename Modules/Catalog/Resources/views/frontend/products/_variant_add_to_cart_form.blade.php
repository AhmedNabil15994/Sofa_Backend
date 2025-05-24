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
    <input type="{{ $attribute->type }}" class="form-control productInputsAttributes" data-id="{{ $attribute->id }}" {{ $attribute->validation['required'] == 1 ? 'required' : '' }} name="productAttributes[{{ $attribute->id }}]" autocomplete="off" value="{{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) ? getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] : '' }}" />
    @elseif($attribute->type == 'drop_down')
    <select class="form-control productInputsAttributes" {{ $attribute->validation['required'] == 1 ? 'required' : '' }} data-id="{{ $attribute->id }}" name="productAttributes[{{ $attribute->id }}]">
        @foreach ($attribute->options as $option)
        <option value="{{ $option->id }}" {{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) && $option->id == getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] ? 'selected' : '' }}>
            {{ $option->value }}</option>
        @endforeach
    </select>
    @elseif($attribute->type == 'radio')
    <div class="row">
        @foreach ($attribute->options as $option)
        <div class="col-md-4">
            <label for="radi_{{ $option->id }}">{{ $option->value }}</label>
            <input type="radio" class="productInputsAttributes" name="productAttributes[{{ $attribute->id }}]" data-id="{{ $attribute->id }}" id="radi_{{ $option->id }}" {{ $attribute->validation['required'] == 1 ? 'required' : '' }} {{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) && $option->id == getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] ? 'checked' : '' }} value="{{ $option->id }}">
        </div>
        @endforeach
    </div>
    @elseif($attribute->type == 'boolean')
    <input type="checkbox" class="productInputsAttributes" name="productAttributes[{{ $attribute->id }}]" data-id="{{ $attribute->id }}" {{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) && getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] == 'on' ? 'checked' : '' }} {{ $attribute->validation['required'] == 1 ? 'required' : '' }} />
    @elseif($attribute->type == 'file')
    <input type="{{ $attribute->type }}" class="form-control productInputsAttributes" name="productAttributes[{{ $attribute->id }}]" data-id="{{ $attribute->id }}" onchange="readURL(this, 'imgUploadPreview-{{ $attribute->id }}', 'single');" @if (!key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? [])) {{ $attribute->validation['required'] == 1 ? 'required' : '' }} @endif
    value="" />
    @if (key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) &&
    !is_null(getCartItemById($product->id)->attributes['productAttributes'][$attribute->id]))
    <img src="{{ url(getCartItemById($product->id)->attributes['productAttributes'][$attribute->id]) }}" id="imgUploadPreview-{{ $attribute->id }}" class="img-thumbnail img-responsive img-preview" style="height: 150px; width: 250px;" alt="attribute image">
    @else
    <img src="#" id="imgUploadPreview-{{ $attribute->id }}" class="img-thumbnail img-responsive img-preview" style="height: 150px; width: 250px; display: none;" alt="attribute image">
    @endif
    @else
    <input type="{{ $attribute->type }}" class="form-control productInputsAttributes" name="productAttributes[{{ $attribute->id }}]" data-id="{{ $attribute->id }}" autocomplete="off" {{ $attribute->validation['required'] == 1 ? 'required' : '' }} value="{{ key_exists($attribute->id, getCartItemById($product->id)->attributes['productAttributes'] ?? []) ? getCartItemById($product->id)->attributes['productAttributes'][$attribute->id] : '' }}" />
    @endif
</div>
@endforeach
@endif

<input type="hidden" id="productImage-{{ $variantProduct->id }}" value="{{ url($variantProduct->image) }}">
<input type="hidden" id="productTitle-{{ $variantProduct->id }}" value="{{ $productTitle }}">
<input type="hidden" name="product_type" id="productType" value="variation">
<input type="hidden" id="selectedOptions" value="{{ json_encode($selectedOptions) }}">
<input type="hidden" id="selectedOptionsValue" value="{{ json_encode($selectedOptionsValue) }}">


    <span class="price have-discount">

        @if(isset($variantProduct->price) && $variantProduct->price)
            @if(isset($variantProduct->offer) && $variantProduct->offer)
            {{$variantProduct->offer->offer_price}} {{ __('apps::frontend.master.kwd') }}
            @else
            {{$variantProduct->price}} {{ __('apps::frontend.master.kwd') }}
            @endif
            @else
            @if($product->offer)
            @if(!is_null($product->offer->offer_price))
            <span class="price-before">{{ $product->price }} {{ __('apps::frontend.master.kwd') }}</span>
            {{ $product->offer->offer_price }} {{ __('apps::frontend.master.kwd') }}
            @else
            <span>{{ $product->price }} {{ __('apps::frontend.master.kwd') }}</span>
            /
            <span class="percentage-discount">
                {{ $product->offer->percentage . ' %' }}
                {{ __('apps::frontend.master.discount') }}
            </span>
            @endif
            @else
            {{ $product->price }}
            {{ __('apps::frontend.master.kwd') }}
            @endif
            @endif

    </span>

    <div class="align-items-center d-flex">
        <h5>
            {{ __('catalog::frontend.products.quantity') }}
        </h5>
        <div class="quantity">
            <div class="buttons-added single-product-buttons-added ">
                <button class="sign plus single-product-plus"><i class="fa fa-chevron-up"></i></button>
                <input type="text"
                       id="prodQuantity" name="qty"
                       value="{{ getCartItemById('var-' . $variantProduct->id) ? getCartItemById('var-' . $variantProduct->id)->quantity : '1' }}"
                       title="Qty" class="input-text qty text"
                       size="1">
                <button class="sign minus single-product-minus"><i class="fa fa-chevron-down"></i></button>
            </div>
        </div>
        <button
                id="btnAddToCart"

                type="submit"

                class="btn btn-them main-custom-btn"
        >
            <i class="ti-shopping-cart"></i>


            {{ __('catalog::frontend.products.add_to_cart') }}
        </button>
        <div id="loaderDiv" style="margin:0px 46px">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status"
                     style="width: 2rem; height: 2rem;">
                    <span class="sr-only">{{__('apps::frontend.Loading')}}</span>
                </div>
            </div>
        </div>
    </div>
