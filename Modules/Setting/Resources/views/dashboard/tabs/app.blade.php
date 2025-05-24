<div class="tab-pane fade" id="app">
{{--    <h3 class="page-title">{{ __('setting::dashboard.settings.form.tabs.app') }}</h3>--}}
    <div class="col-md-10">

        {{--  tab for lang --}}
        <ul class="nav nav-tabs">
            @foreach (config('translatable.locales') as $code)
                <li class="@if($loop->first) active @endif">
                    <a data-toggle="tab"
                       href="#first_{{$code}}">{{__('catalog::dashboard.products.form.tabs.input_lang',["lang"=>$code])}}</a>
                </li>
            @endforeach
        </ul>

        {{--  tab for content --}}
        <div class="tab-content">

            @foreach (config('translatable.locales') as $code)
                <div id="first_{{$code}}" class="tab-pane fade @if($loop->first) in active @endif">

                    <div class="form-group">
                        <label class="col-md-2">
                            {{ __('setting::dashboard.settings.form.app_name') }} - {{ $code }}
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="app_name[{{$code}}]"
                                   value="{{ config('setting.app_name.' . $code) }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2">
                            {{ __('setting::dashboard.settings.form.app_description') }} - {{ $code }}
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="app_description[{{$code}}]"
                                   value="{{ config('setting.app_description.' . $code) }}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2">
                            {{ __('setting::dashboard.settings.form.address') }} - {{ $code }}
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="address[{{$code}}]"
                                   value="{{ config('setting.address.' . $code) }}"/>
                        </div>
                    </div>

                </div>
            @endforeach

        </div>


        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.contacts_email') }}
            </label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="contact_us[email]"
                       value="{{config('setting.contact_us.email') ? config('setting.contact_us.email') : ''}}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.contacts_whatsapp') }}
            </label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="contact_us[whatsapp]"
                       value="{{config('setting.contact_us.whatsapp') ? config('setting.contact_us.whatsapp') : ''}}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.contacts_mobile') }}
            </label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="contact_us[mobile]"
                       value="{{config('setting.contact_us.mobile') ? config('setting.contact_us.mobile') : ''}}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.contacts_technical_support') }}
            </label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="contact_us[technical_support]"
                       value="{{config('setting.contact_us.technical_support') ? config('setting.contact_us.technical_support') : ''}}"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.address') }}
            </label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="contact_us[address]"
                       value="{{config('setting.contact_us.address') ? config('setting.contact_us.address') : ''}}"/>
            </div>
        </div>
        @if(env("HAS_MOBILE_APPS",false))
            <div class="form-group">
                <label class="col-md-2">
                    {{ __('Android App Url') }}
                </label>
                <div class="col-md-9">
                    
                    <input type="text" class="form-control" name="apps[android_url]"
                    value="{{ Setting::get('apps.android_url') }}"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2">
                    {{ __('Ios App Url') }}
                </label>
                <div class="col-md-9">
                    
                    <input type="text" class="form-control" name="apps[ios_url]"
                    value="{{ Setting::get('apps.ios_url') }}"/>
                </div>
            </div>
        @endif
        

        <div class="form-group">
            <label class="col-md-2">
                {{ __('Site activation') }}
            </label>
            <div class="col-md-9">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline">
                        {{ __('setting::dashboard.settings.form.yes') }}
                        <input type="radio" name="other[site_activation]" value="1"
                               @if (config('setting.other.site_activation',1) == 1)
                               checked
                            @endif>
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline">
                        {{ __('setting::dashboard.settings.form.no') }}
                        <input type="radio" name="other[site_activation]" value="0"
                               @if (config('setting.other.site_activation',1) == 0)
                               checked
                            @endif>
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        {!! field()->file('images[qr_code]' , __('QR code') , Setting::get('qr_code') ? asset(Setting::get('qr_code')) : null) !!}
    </div>
</div>
