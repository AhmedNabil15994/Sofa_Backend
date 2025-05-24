<?php

namespace Modules\Area\ViewComposers\FrontEnd;

use Modules\Area\Entities\CurrencyCode;
use Modules\Area\Repositories\FrontEnd\StateRepository as State;
use Illuminate\View\View;
use Cache;
use Setting;

class currencyComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $supported_currencies = CurrencyCode::whereIn('id', Setting::get('supported_currencies'))->groupBy('country_id')->pluck('name','id')->toArray();
        $activeCurrencies = CurrencyCode::whereIn('id', Setting::get('supported_currencies'))->groupBy('country_id')->get();
        $currencies = CurrencyCode::whereIn('id', Setting::get('supported_currencies'))->get();
        $view->with('supported_currencies', $supported_currencies);
        $view->with('currencies', $currencies);
        $view->with('activeCurrencies', $activeCurrencies);
    }
}
