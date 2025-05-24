<?php

namespace Modules\Apps\Http\Controllers\FrontEnd;

use Illuminate\Support\Facades\Cache;
use Modules\Apps\Repositories\Dashboard\AppHomeRepository;
use Modules\Apps\Transformers\Frontend\HomeFilterResource;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository;
use Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Apps\Http\Requests\FrontEnd\ContactUsRequest;
use Modules\Apps\Notifications\FrontEnd\ContactUsNotification;
use Modules\Apps\Repositories\Frontend\AppHomeRepository as Home;
use Cart;
use function Sodium\add;
use Modules\Area\Entities\CurrencyCode as CurrencyModel;
use Setting;

class HomeController extends Controller
{
    protected $home;
    protected $slider;
    protected $product;

    public function __construct(ProductRepository $product)
    {
        $this->product = $product;
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $this->home = new Home;
        $home_sections = $this->home->getAll($request);
        $home_sections = view('apps::frontend.home-sections.section-builder', compact('home_sections'))->render();
        return view('apps::frontend.index',compact('home_sections'));
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function filter(Request $request)
    {
        $results = HomeFilterResource::collection($this->product->autoCompleteSearch($request)->take(30)->get(['title','id','slug']))->jsonSerialize();
        $response = view('apps::frontend.components.live-search-menu',compact('results'))->render();
        return response()->json(['html'=> $response]);
    }

    public function contactUs()
    {
        return view('apps::frontend.contact-us');
    }

    public function sendContactUs(ContactUsRequest $request)
    {
        $request->merge([
            'username' => $request->contact_username,
            'email' => $request->contact_email,
            'mobile' => $request->contact_mobile,
            'message' => $request->contact_message,
        ]);

        Notification::route('mail', config('setting.contact_us.email'))
            ->notify((new ContactUsNotification($request))->locale(locale()));

        return Response()->json([true, __('apps::frontend.contact_us.alerts.send_message')]);
    }

    public function updateCurrency(Request $request)
    {
        if ($request->currency_id) {
            $selected_currency = CurrencyModel::find($request->currency_id);
            if ($selected_currency) {
                $default_currency = optional(session()->get('default_currency'))->code ?? CurrencyModel::find(Setting::get('default_currency'))->code;
                $settingKey = $default_currency.'to'.$selected_currency->id;
                $currency = Setting::get($settingKey) ?? 1;

                if ($currency) {
                    session()->put('currency_data', [
                        'currencies_value' => floatval($currency), // rate
                        'selected_currency' => $selected_currency,
                    ]);
                }
            }
        }
        return redirect()->back();
    }
}
