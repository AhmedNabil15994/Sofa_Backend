<div class="tab-pane fade" id="payment_gateway">

    @if(auth()->user()->tocaan_perm)
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#cache">Cash</a>
            </li>
            <li>
                <a data-toggle="tab" href="#UPayment">UPayment</a>
            </li>
            <li>
                <a data-toggle="tab" href="#Tap">Tap</a>
            </li>
            <li>
                <a data-toggle="tab" href="#Myfatoorah">Myfatoorah</a>
            </li>
            <li>
                <a data-toggle="tab" href="#Tabby">Tabby</a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="cache" class="tab-pane fade in active">
                @include('setting::dashboard.tabs.gatways.cache')
            </div>
            <div id="UPayment" class="tab-pane fade">
                @include('setting::dashboard.tabs.gatways.upayment')
            </div>

            <div id="Tap" class="tab-pane fade">
                @include('setting::dashboard.tabs.gatways.tab')
            </div>

            <div id="Myfatoorah" class="tab-pane fade">
                @include('setting::dashboard.tabs.gatways.fatoorah')
            </div>

            <div id="Tabby" class="tab-pane fade">
                @include('setting::dashboard.tabs.gatways.tabby')
            </div>

        </div>
        <hr>
    @endif
    <div class="row">
        <div class="col-md-7 col-md-offset-3">
            <div class="form-group">
                <label
                        class="col-md-3">{{ __('setting::dashboard.settings.form.payment_gateway.payment_types.title') }}</label>
                <div class="col-md-8">
                    <label class="checkbox-inline">
                        <input type="checkbox"
                               name="other[supported_payments][]"
                               @if (in_array('cash',config('setting.other.supported_payments',[])))
                               checked
                               @endif
                               value="cash"> {{ __('setting::dashboard.settings.form.payment_gateway.payment_types.cash') }}
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox"
                               name="other[supported_payments][]"
                               @if (in_array('online',config('setting.other.supported_payments',[])))
                               checked
                               @endif
                               value="online"> {{ __('setting::dashboard.settings.form.payment_gateway.payment_types.online') }}
                    </label>
                </div>
            </div>

        </div>
    </div>

</div>
