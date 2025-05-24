 <div class="row">            
    <div class="form-group">
        <label class="col-md-2">
            {{ __('setting::dashboard.settings.form.supported_countries') }}
        </label>
        <div class="col-md-9">
            <select name="shiping[local][countries][]" class="form-control select2" multiple="">
                @foreach ($countries as $code => $country)
                    <option value="{{ $code }}"
                            @if (collect(Setting::get('shiping.local.countries'))->contains($code))
                            selected=""
                        @endif>
                        {{ $country }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>