<?php

namespace Modules\Setting\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;

class SettingController extends WebServiceController
{
    public function index()
    {
        $settingExceptions = ['payment_gateway', 'custom_codes', 'order_status', 'products'];
        $settings = Arr::except(config('setting'), $settingExceptions);
        return $this->response($settings);
    }
}
