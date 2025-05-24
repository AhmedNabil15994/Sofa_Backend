<div class="tab-pane fade" id="shipping_orders">

    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#local_shiping">
                {{ __('setting::dashboard.settings.form.local') }}
            </a>
        </li>
        
        @foreach(config("shipping.supported_companies") as $company)
            <li>
                <a data-toggle="tab" href="#{{$company}}">{{ucfirst($company)}}</a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        <div id="local_shiping" class="tab-pane fade in active">
            @include('setting::dashboard.tabs.shapping-ways.local')
        </div>
        @foreach(config("shipping.supported_companies") as $company)
            <div id="{{$company}}" class="tab-pane fade">
                @include("setting::dashboard.tabs.shapping-ways.{$company}")
            </div>
        @endforeach
    </div>
</div>
