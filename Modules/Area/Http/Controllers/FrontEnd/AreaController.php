<?php

namespace Modules\Area\Http\Controllers\FrontEnd;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Area\Entities\Country;
use Modules\Area\Transformers\FrontEnd\AreaSelectorResource;
use Setting;
use ExtremeSa\Aramex\Facades\Aramex;
use Modules\Area\Transformers\FrontEnd\AramexAreaSelectorResource;
use Modules\Shipping\Traits\ShippingTrait;

class AreaController extends Controller
{
    use ShippingTrait;

    public function getChildAreaByParent(Request $request)
    {
        if ($request->type == 'city') {
            $request->merge(['country_id' => $request->parent_id]);
            $this->setShippingTypeByRequest($request);
            $items = $this->shipping->getCities($request);
        }

        return response()->json(['success' => true, 'data' => $items, 'country' => optional(optional($this->shipping)->country)->iso2]);
    }
}