<div class="tab-pane fade" id="other">
    {{--    <h3 class="page-title">{{ __('setting::dashboard.settings.form.tabs.other') }}</h3>--}}
    <div class="col-md-10">
        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.privacy_policy') }}
            </label>
            <div class="col-md-9">
                <select name="other[privacy_policy]" class="form-control select2">
                    <option value=""></option>
                    @foreach ($pages as $page)
                        <option
                            value="{{ $page['id'] }}" {{isset(config('setting.other')['privacy_policy']) && (config('setting.other')['privacy_policy'] == $page->id) ? ' selected="" ' : ''}}>
                            {{ $page->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.terms') }}
            </label>
            <div class="col-md-9">
                <select name="other[terms]" class="form-control select2">
                    <option value=""></option>
                    @foreach ($pages as $page)
                        <option
                            value="{{ $page['id'] }}" {{isset(config('setting.other')['terms']) && (config('setting.other')['terms'] == $page->id) ? ' selected="" ' : ''}}>
                            {{ $page->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.about_us') }}
            </label>
            <div class="col-md-9">
                <select name="other[about_us]" class="form-control select2">
                    <option value=""></option>
                    @foreach ($pages as $page)
                        <option
                            value="{{ $page['id'] }}" {{( isset(config('setting.other')['about_us']) && config('setting.other')['about_us'] == $page->id) ? ' selected="" ' : ''}}>
                            {{ $page->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.our_branches') }}
            </label>
            <div class="col-md-9">
                <select name="other[our_branches]" class="form-control select2">
                    <option value=""></option>
                    @foreach ($pages as $page)
                        <option
                            value="{{ $page['id'] }}" {{( isset(config('setting.other')['our_branches']) && config('setting.other')['our_branches'] == $page->id) ? ' selected="" ' : ''}}>
                            {{ $page->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.before_buy') }}
            </label>
            <div class="col-md-9">
                <select name="other[before_buy]" class="form-control select2">
                    <option value=""></option>
                    @foreach ($pages as $page)
                        <option
                            value="{{ $page['id'] }}" {{( isset(config('setting.other')['before_buy']) && config('setting.other')['before_buy'] == $page->id) ? ' selected="" ' : ''}}>
                            {{ $page->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.shipping_company') }}
            </label>
            <div class="col-md-9">
                <select name="other[shipping_company]" class="form-control select2">
                    <option value=""></option>
                    @foreach ($companies as $company)
                        <option
                            value="{{ $company['id'] }}" {{( isset(config('setting.other')['shipping_company']) && config('setting.other')['shipping_company'] == $company->id) ? ' selected="" ' : ''}}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2">
                {{ __('setting::dashboard.settings.form.add_shipping_company') }}
            </label>
            <div class="col-md-9">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline">
                        {{ __('setting::dashboard.settings.form.yes') }}
                        <input type="radio" name="other[add_shipping_company]" value="1"
                               @if (config('setting.other.add_shipping_company') == 1)
                               checked
                            @endif>
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline">
                        {{ __('setting::dashboard.settings.form.no') }}
                        <input type="radio" name="other[add_shipping_company]" value="0"
                               @if (config('setting.other.add_shipping_company') == 0)
                               checked
                            @endif>
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        {!! field()->file('images[default_banner_categories]' , __('setting::dashboard.settings.form.default_banner_categories') , Setting::get('default_banner_categories') ? asset(Setting::get('default_banner_categories')) : null) !!}
        {!! field()->file('images[default_banner_pages]' , __('setting::dashboard.settings.form.default_banner_pages') , Setting::get('default_banner_pages') ? asset(Setting::get('default_banner_pages')) : null) !!}


    </div>
</div>
