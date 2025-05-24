@extends('apps::dashboard.layouts.app')
@section('title', __('setting::dashboard.settings.routes.index'))

@section('css')
    <style>
        .select2{
            width: 100% !important;
        }
    </style>
@stop
@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('setting::dashboard.settings.routes.index') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            @include('apps::dashboard.layouts._msg')

            <div class="row">
                <form role="form" class="form-horizontal form-row-seperated" method="post"
                      action="{{route('dashboard.setting.update')}}" enctype="multipart/form-data">
                    <div class="col-md-12">
                        @csrf
                        <div class="col-md-3">
                            <div class="panel-group accordion scrollable" id="accordion2">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle">
                                                {{__('setting::dashboard.settings.form.tabs.info')}}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_2_1" class="panel-collapse in">
                                        <div class="panel-body">
                                            <ul class="nav nav-pills nav-stacked">
                                                <li class="active">
                                                    <a href="#global_setting" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.general') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#currencies" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.currencies') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#app" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.app') }}
                                                    </a>
                                                </li>

                                                @if(auth()->user()->tocaan_perm)
                                                    <li>
                                                        <a href="#mail" data-toggle="tab">
                                                            {{ __('setting::dashboard.settings.form.tabs.mail') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="#logo" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.logo') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#social_media" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.social_media') }}
                                                    </a>
                                                </li>

                                                @if(auth()->user()->tocaan_perm)
                                                    <li>
                                                        <a href="#products" data-toggle="tab">
                                                            {{ __('setting::dashboard.settings.form.tabs.products') }}
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#order_status" data-toggle="tab">
                                                            {{ __('setting::dashboard.settings.form.tabs.order_status') }}
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#custom_codes" data-toggle="tab">
                                                            {{ __('setting::dashboard.settings.form.tabs.custom_codes') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="#payment_gateway" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.payment_gateway') }}
                                                    </a>
                                                </li>

                                                @if(auth()->user()->tocaan_perm)
                                                    <li>
                                                        <a href="#shipping_orders" data-toggle="tab">
                                                            {{ __('setting::dashboard.settings.form.tabs.shipping_orders') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#other" data-toggle="tab">
                                                            {{ __('setting::dashboard.settings.form.tabs.other') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @inject('countries' ,'Modules\Area\Entities\Country')
                        @php
                            $countries = $countries->pluck('title','id')->toArray();
                        @endphp
                        <div class="col-md-9">
                            <div class="tab-content">
                                @include('setting::dashboard.tabs.general')
                                @include('setting::dashboard.tabs.currencies')
                                @include('setting::dashboard.tabs.app')

                                @if(auth()->user()->tocaan_perm)
                                    @include('setting::dashboard.tabs.mail')
                                @endif

                                @include('setting::dashboard.tabs.logo')
                                @include('setting::dashboard.tabs.social')

                                @if(auth()->user()->tocaan_perm)
                                    @include('setting::dashboard.tabs.products')
                                    @include('setting::dashboard.tabs.order_status')
                                    @include('setting::dashboard.tabs.custom_codes')
                                @endif

                                @include('setting::dashboard.tabs.payment_gateway')

                                @if(auth()->user()->tocaan_perm)
                                    @include('setting::dashboard.tabs.shipping-orders')
                                    @include('setting::dashboard.tabs.other')
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" id="submit" class="btn btn-lg blue">
                                    {{__('apps::dashboard.general.edit_btn')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')

    <script>
        var sideMenuReviewProducts = $('#sideMenuReviewProducts');


        function paymentModeSwitcher(hide_class,show_id){
            $('.'+hide_class).hide();
            $('#'+show_id).show();
        }

    let aramexCountryId = @json(Setting::get('shiping.aramex.source.country_id'));
    let aramexStateId = @json(Setting::get('shiping.aramex.source.state_id'));

    if(aramexCountryId){
        $( document ).ready(function() {
            getCitiesByCountryId('#aramex_country_selector',aramexStateId);
        });
    }

    function getCitiesByCountryId(country,selectedState = null) {

        country = $(country);
        var container = country.closest('.address_selector');
        var area_selector = container.find('.area_selector');
        var id = country.val();

        $.ajax({
            method: "GET",
            url: '{{route('frontend.area.get_child_area_by_parent')}}?type=city&parent_id=' + id,
            beforeSend: function () {
                area_selector.empty();
                container.find('.state_selector_content').hide();
                container.find('.state_selector_content_loader').show();
            },
            success: function (data) {
                area_selector.append('<option selected>{{__('user::frontend.addresses.form.states')}}</option>');
                var optgroup = '';
                $.each(data.data, function (index, city) {
                    var options = '';
                    $.each(city.states, function (index, state) {
                        options += '<option value="' + state.id + '">' + state.title + '</option>';
                    });

                    optgroup = '<optgroup label="'+city.title+'">'+options+'</optgroup>';
                    area_selector.append(optgroup);
                });
                container.find('.state_selector_content').show();
                container.find('.state_selector_content_loader').hide();

                if(selectedState){
                    area_selector.val(selectedState);
                    area_selector.select2();
                }
            }
        });
    }
    </script>

@stop
