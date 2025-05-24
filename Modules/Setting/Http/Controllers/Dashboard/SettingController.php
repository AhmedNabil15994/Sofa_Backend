<?php

namespace Modules\Setting\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Area\Entities\Country;
use Modules\Setting\Http\Requests\Dashboard\SettingRequest;
use Modules\Area\Repositories\Dashboard\CountryRepository;
use Modules\Order\Repositories\Dashboard\OrderStatusRepository;
use Modules\Setting\Repositories\Dashboard\SettingRepository as Setting;
use Monarobase\CountryList\CountryListFacade as Countries;

class SettingController extends Controller
{
    protected $setting;
    protected $country;
    protected $orderStatus;

    function __construct(Setting $setting, CountryRepository $country, OrderStatusRepository $orderStatus)
    {
        $this->setting = $setting;
        $this->country = $country;
        $this->orderStatus = $orderStatus;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setting::dashboard.index');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     */
    public function update(SettingRequest $request)
    {
        DB::beginTransaction();

        try {
            
            if($request->payment_gateway){

                foreach ($request->payment_gateway as $key => $gateway){

                    if(!isset($gateway['status']))
                        $gateway['status'] = 'off';
    
                    $payment_gateway[$key] = $gateway;
                }
                
                $request->merge([
                    'payment_gateway' => $payment_gateway
                ]);
            }

            ### Start - Update Order Status In Model ###
            if ($request->order_status) {
                $this->orderStatus->updateColorInSettings($request->order_status);
            }
            ### End - Update Order Status In Model ###

            $this->setting->set($request);

            DB::commit();
            return redirect()->back()->with(['msg' => __('setting::dashboard.settings.form.messages.settings_updated_successfully'), 'alert' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function syncRelation($model, $incomingValues = null)
    {
        $oldIds = $model->pluck('code')->toArray();
        $data['deleted'] = array_values(array_diff($oldIds, $incomingValues));
        $data['updated'] = array_values(array_intersect($oldIds, $incomingValues));
        return $data;
    }
}
