<?php
namespace Modules\Shipping\Integrations\Pnm;

use Modules\Shipping\Integrations\Pnm\Addresses\Addresses;
use Modules\Shipping\Integrations\Pnm\Shipments\Shipments;
use Modules\Shipping\Integrations\Pnm\Rates\CalculateRates;
use Modules\Shipping\Integrations\Pnm\Requests\fetchCities;

class PnmShipmentWay
{
    use 
    ConfigTrait, 
    fetchCities, 
    CalculateRates,
    Addresses,
    Shipments
    ;


    /**
     * Class PnmShipmentWay
     *
     * @method public cities fetchCities
     * @method public calculateRate CalculateRates
     * @method public createAddress Addresses
     * @method public createShipments Shipments
     * @method public cancelShipment Shipments
     */
}
