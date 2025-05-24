<?php

namespace Modules\Shipping\Integrations\Pnm;

use Setting;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use Modules\Shipping\Integrations\Pnm\Response\PnmResponse;

trait ConfigTrait
{
    //stg url : https://dev.pnm-ics.com/api
    // live url : https://portal.packandmove.com.kw/api , https://dev.pnm-ics.com/api
    private $api_key;
    private $mode;
    private $url;

    function __construct()
    {
        $this->model = Setting::get('shiping.pnm.mode');
        $this->api_key = Setting::get("shiping.pnm.{$this->model}.API_KEY");
        $this->url = $this->model == 'live_mode' ? 'https://portal.packandmove.com.kw/api' : 'https://dev.pnm-ics.com/api';
    }

    private function request($type, $action, $data)
    {
        $client = new Client();

        $params = [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => $this->api_key
            ],
        ];

        switch ($type) {
            case 'get':
            case 'post':
                $params[RequestOptions::JSON] = $data;
                break;
        }

        try {

            $res = $client->$type("{$this->url}/{$action}", $params);

            return new PnmResponse($res);

        } catch (\GuzzleHttp\Exception\ClientException $e) {

            return new PnmResponse($e,'error');
        }
    }

    protected function post($action, $data)
    {
        return $this->request('post', $action, $data);
    }

    protected function get($action, $data)
    {
        return $this->request('get', $action, $data);
    }
}
