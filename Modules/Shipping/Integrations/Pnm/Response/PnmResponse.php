<?php
namespace Modules\Shipping\Integrations\Pnm\Response;

class PnmResponse
{
    private $data = [];
    private $errors = [];
    private $hasErrorFlag = false;


    function __construct($response,$status = 'success')
    {
        $this->responseHandler($response,$status);
    }

    public function get()
    {
        return $this->data;
    }

    protected function setErrors($errors)
    {
        return $this->errors = (array)$errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError()
    {
        return count($this->errors) ? reset($this->errors) : 'error in Pnm response';
    }

    public function hasErrors()
    {
        return $this->hasErrorFlag;
    }

    public function clearErrors()
    {
        $this->hasErrorFlag = false;
        $this->setErrors([]);
    }

    private function responseHandler($response,$status = 'success')
    {
        switch ($status) {
            case 'success':
                $this->data = json_decode($response->getBody(), true);
                break;
            default:
            $this->hasErrorFlag = true;
            $response = $response->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $response = json_decode($responseBodyAsString,true);
            isset($response['errors']) ? $this->setErrors($response['errors']) : null;
            break;
        }
    }
}
