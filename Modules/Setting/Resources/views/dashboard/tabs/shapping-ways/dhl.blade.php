<div class="row">
    <div class="col-md-6 col-md-offset-4">
        <div class="form-group">

            <div class="col-md-9">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline">
                        Test Mode
                        <input type="radio"
                               name="shiping[dhl][mode]" value="test_mode"
                               @if (Setting::get('shiping.dhl.mode') == 'test_mode')
                                   checked
                            @endif>
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline">
                        Live Mode
                        <input type="radio"
                               name="shiping[dhl][mode]" value="live_mode"
                               @if (Setting::get('shiping.dhl.mode') == 'live_mode')
                                   checked
                        @endif">
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-md-offset-2 pnm_switch" id="test_mode_101"
             style="{{ Setting::get('shiping.pnm.mode') == 'live_mode' ? 'display: none': 'display: block' }}">

            {{--            <h3 class="page-title text-center">Dhl Shipment Way ( Test Mode )</h3>--}}

            {!! field()->text('shiping[dhl][username]', 'Username', Setting::get('shiping.dhl.username') ?? '') !!}
            {!! field()->text('shiping[dhl][password]', 'Password', Setting::get('shiping.dhl.password') ?? '') !!}
            {!! field()->text('shiping[dhl][mobilePhone]', 'mobile Phone', Setting::get('shiping.dhl.mobilePhone') ?? '') !!}
            {!! field()->text('shiping[dhl][phone]', 'Phone', Setting::get('shiping.dhl.phone') ?? '') !!}
            {!! field()->text('shiping[dhl][companyName]', 'company Name', Setting::get('shiping.dhl.companyName') ?? '') !!}
            {!! field()->text('shiping[dhl][fullName]', 'full Name', Setting::get('shiping.dhl.fullName') ?? '') !!}
            {!! field()->text('shiping[dhl][cityName]', 'city Name', Setting::get('shiping.dhl.cityName') ?? '') !!}
            {!! field()->text('shiping[dhl][countryCode]', 'country Code', Setting::get('shiping.dhl.countryCode') ?? '') !!}
            {!! field()->text('shiping[dhl][addressLine1]', 'addressLine1', Setting::get('shiping.dhl.addressLine1') ?? '') !!}
            {!! field()->text('shiping[dhl][countyName]', 'county Name', Setting::get('shiping.dhl.countyName') ?? '') !!}
        </div>

        <div class="col-md-7 col-md-offset-2">
            <div class="form-group">
                <label class="col-md-2">
                    {{ __('setting::dashboard.settings.form.supported_countries') }}
                </label>
                <div class="col-md-9">
                    <select name="shiping[dhl][countries][]" class="form-control select2" multiple="">
                        @foreach ($countries as $code => $country)
                            <option value="{{ $code }}"
                                    @if (collect(Setting::get('shiping.dhl.countries'))->contains($code))
                                        selected=""
                                @endif>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            {!! field()->number('shiping[dhl][delivery_price]', 'Delivery Price',  Setting::get('shiping.dhl.delivery_price') ?? '') !!}
        </div>
    </div>
</div>
