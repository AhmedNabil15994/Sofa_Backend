
<div class="row">
    <div class="col-md-6 col-md-offset-4">

        <div class="form-group">

            <div class="col-md-9">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline">
                        {{ __('setting::dashboard.settings.form.payment_gateway.payment_mode.test_mode') }}
                        <input onchange="paymentModeSwitcher('tabby_switch','testModelData_tabby')" type="radio" name="payment_gateway[tabby][payment_mode]" value="test_mode"
                               @if (config('setting.payment_gateway.tabby.payment_mode') != 'live_mode')
                                   checked
                            @endif>
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline">
                        {{ __('setting::dashboard.settings.form.payment_gateway.payment_mode.live_mode') }}
                        <input  onchange="paymentModeSwitcher('tabby_switch','liveModelData_tabby')" type="radio" name="payment_gateway[tabby][payment_mode]" value="live_mode"
                                @if (config('setting.payment_gateway.tabby.payment_mode') == 'live_mode')
                                    checked
                            @endif>
                        <span></span>
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-md-7 col-md-offset-2 tabby_switch" id="testModelData_tabby"
         style="{{ config('setting.payment_gateway.tabby.payment_mode') == 'live_mode' ? 'display: none': 'display: block' }}">

        <h3 class="page-title text-center">Tabby Gateway ( Test Mode )</h3>

        {!! field()->text('payment_gateway[tabby][test_mode][PUBLIC_KEY]', 'PUBLIC KEY', config('setting.payment_gateway.tabby.test_mode.PUBLIC_KEY') ?? '') !!}

        {!! field()->text('payment_gateway[tabby][test_mode][SECRET_KEY]', 'SECRET KEY', config('setting.payment_gateway.tabby.test_mode.SECRET_KEY') ?? '') !!}
    </div>

    <div class="col-md-7 col-md-offset-2 tabby_switch" id="liveModelData_tabby"
         style="{{ config('setting.payment_gateway.tabby.payment_mode') == 'live_mode' ? 'display: block': 'display: none' }}">

        <h3 class="page-title text-center">Tabby Gateway ( Live Mode )</h3>

        {!! field()->text('payment_gateway[tabby][live_mode][PUBLIC_KEY]', 'PUBLIC KEY',  config('setting.payment_gateway.tabby.live_mode.PUBLIC_KEY') ?? '') !!}

        {!! field()->text('payment_gateway[tabby][live_mode][SECRET_KEY]', 'SECRET KEY',  config('setting.payment_gateway.tabby.live_mode.SECRET_KEY') ?? '') !!}

    </div>
    <div class="col-md-7 col-md-offset-2">
        @foreach (config('translatable.locales') as $code)

            {!! field()->text('payment_gateway[tabby][title_'.$code.']', __('setting::dashboard.settings.form.payment_gateway.payment_types.payment_title').'-'.$code ,
            config('setting.payment_gateway.tabby.title_'.$code)) !!}

        @endforeach
        {!! field()->checkBox('payment_gateway[tabby][status]', __('setting::dashboard.settings.form.payment_gateway.payment_types.payment_status') , null , [
        (config('setting.payment_gateway.tabby.status') == 'on' ? 'checked' : '') => ''
        ]) !!}
    </div>
</div>
