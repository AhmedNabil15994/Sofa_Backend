<div class="tab-pane fade" id="currencies">
    {{--    <h3 class="page-title">{{ __('setting::dashboard.settings.form.tabs.general') }}</h3>--}}
        <div class="col-md-10">
        <?php $default_currency_code = Modules\Area\Entities\CurrencyCode::find(Setting::get('default_currency'))->code??'KWD';?>
        @foreach($currencies as $currency)
            @if ( $default_currency != $currency->id && in_array($currency->id,$supported_currencies ?? []) )
            <div class="form-group">
                <label class="col-md-2">
                    {{ __('setting::dashboard.settings.form.from') }} {{ $default_currency_code }} {{ __('setting::dashboard.settings.form.to') }} {{ $currency->translate('name','ar') }}
                </label>
                <div class="col-md-9">
                    <input type="number" name="{{$default_currency_code.'to'.$currency->id}}" value="{{ Setting::get($default_currency_code.'to'.$currency->id) }}" step="0.01" class="form-control">
                </div>
            </div>
           @endif
        @endforeach



        </div>
    </div>
