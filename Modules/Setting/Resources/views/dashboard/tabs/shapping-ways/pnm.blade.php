 <div class="row">
    <div class="col-md-6 col-md-offset-4">
        <div class="form-group">

            <div class="col-md-9">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline">
                        Test Mode
                        <input onchange="paymentModeSwitcher('pnm_switch','test_mode')" type="radio" name="shiping[pnm][mode]" value="test_mode"  
                        @if (Setting::get('shiping.pnm.mode') == 'test_mode')
                                checked
                                @endif>
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline">
                        Live Mode
                        <input type="radio" onchange="paymentModeSwitcher('pnm_switch','live_mode')" name="shiping[pnm][mode]" value="live_mode"  
                        @if (Setting::get('shiping.pnm.mode') == 'live_mode')
                                checked
                                @endif">
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-md-offset-2 pnm_switch" id="test_mode"
                style="{{ Setting::get('shiping.pnm.mode') == 'live_mode' ? 'display: none': 'display: block' }}">

            <h3 class="page-title text-center">Pnm Shipment Way ( Test Mode )</h3>
            
            {!! field()->text('shiping[pnm][test_mode][API_KEY]', 'API Key', Setting::get('shiping.pnm.test_mode.API_KEY') ?? '') !!}
            {!! field()->number('shiping[pnm][test_mode][shipper_id]', 'Shipper ID', Setting::get('shiping.pnm.test_mode.shipper_id') ?? '') !!}
        </div>

        <div class="col-md-7 col-md-offset-2 pnm_switch" id="live_mode"
                style="{{ Setting::get('shiping.pnm.mode') == 'live_mode' ? 'display: block': 'display: none' }}">

            <h3 class="page-title text-center">Pnm Shipment Way ( Live Mode )</h3>

            {!! field()->text('shiping[pnm][live_mode][API_KEY]', 'API Key',  Setting::get('shiping.pnm.live_mode.API_KEY') ?? '') !!}
            {!! field()->number('shiping[pnm][live_mode][shipper_id]', 'Shipper ID',  Setting::get('shiping.pnm.live_mode.shipper_id') ?? '') !!}

        </div>
        <div class="col-md-7 col-md-offset-2">
            <div class="form-group">
                <label class="col-md-2">
                    {{ __('setting::dashboard.settings.form.supported_countries') }}
                </label>
                <div class="col-md-9">
                    <select name="shiping[pnm][countries][]" class="form-control select2" multiple="">
                        @foreach ($countries as $code => $country)
                            <option value="{{ $code }}"
                                    @if (collect(Setting::get('shiping.pnm.countries'))->contains($code))
                                    selected=""
                                @endif>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div> 
            {!! field()->number('shiping[pnm][delivery_price]', 'Delivery Price',  Setting::get('shiping.pnm.delivery_price') ?? '') !!}
        </div> 
    </div>
</div>
