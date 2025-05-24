@php
    $current_currency = isset(session()->get('currency_data')['selected_currency']) ? optional(session()->get('currency_data')['selected_currency'])->id : null;
@endphp

<div class="modal fade bd-example-modal-xl" id="currenciesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header w-100 text-center">
                <h5 class="modal-title w-100 text-center">
{{--                    <img src="{{ config('setting.logo') ? url(config('setting.logo')) : url('frontend/images/header-logo.png') }}" alt="logo">--}}
                    {{__('apps::frontend.choose_country')}}
                </h5>
            </div>
            <div class="modal-body p-5">
                <div class="row countries">
                    @foreach ($activeCurrencies as $key => $currency)
                        <div class="col-4 country">
                            <div class="currencyItem {{$currency->id == $current_currency ? 'active' : ''}}" data-area="{{$currency->id}}">
                                <div class="row">
                                    <div class="col-3">
                                        <span class="flag">{{$currency->country->emoji}}</span>
                                    </div>
                                    <div class="col-9 p-0">
                                        <b>{{$currency->country->title}}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
