<?php

namespace App\Services\DHL;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use JanisKelemen\Setting\Facades\Setting;

class DHLProvider implements DHLInterface
{

    /**
     * @var string
     */
    private $userName;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private string $Url;

    /**
     * @array array
     */
    private $setting;

    /**
     * @var string
     */
    private string $company;

    public function __construct()
    {
        $this->setting = Setting::get('shiping.dhl') ?? [];
        $this->userName = $this->setting['username'];
        $this->password = $this->setting['password'];
        $this->Url = $this->setting['mode'] == 'test_mode' ? 'https://express.api.dhl.com/mydhlapi/test' :
            'https://express.api.dhl.com/mydhlapi';
        $this->company = 'EXPRESS WORLDWIDE NONDOC';
    }

    public function rate($data)
    {
        $response = Http::withBasicAuth($this->userName, $this->password)
            ->get("{$this->Url}/rates", [
                'accountNumber' => '451805801',
                'originCountryCode' => @$this->setting['countryCode'] ?? '',
                'originCityName' => @$this->setting['cityName'] ?? '',
                'destinationCountryCode' => $data['destinationCountryCode'] ?? 'SA',
                'destinationCityName' => $data['destinationCityName'] ?? 'dubai',
                'weight' => $data['weight'] ?? '3.16',
                'length' => $data['length'] ?? '41.7',
                'width' => $data['width'] ?? '35.9',
                'height' => $data['height'] ?? '36.9',
                'plannedShippingDate' => Carbon::today()->format('Y-m-d'),
                'isCustomsDeclarable' => 'true',
                'unitOfMeasurement' => 'metric',
                'nextBusinessDay' => 'true',
                'strictValidation' => 'false',
                'getAllValueAddedServices' => 'false',
                'requestEstimatedDeliveryDate' => 'true',
                'estimatedDeliveryDateType' => 'QDDF',
            ]);
//        dd($response->json(),$data);
        $company = collect(@$response->json()['products'])->where('productName', $this->company)->first();
        return @collect($company['totalPrice'])->where('currencyType', 'BILLC')->first()['price'] ??
            $this->setting['delivery_price'];
    }

    public function shipment($order, $data)
    {
        $response = Http::withBasicAuth($this->userName, $this->password)
            ->post("{$this->Url}/shipments", [
                "getOptionalInformation" => false,
                "customerReferences" => [
                    [
                        "value" => (string)@$order->user->id ?? "1010499814",
                        "typeCode" => "CU"
                    ]
                ],
                "pickup" => [
                    "isRequested" => true
                ],
                "productCode" => "P",
                "localProductCode" => "P",
                "plannedShippingDateAndTime" => Carbon::tomorrow()->toDateTimeLocalString() . "GMT+03:00",
                "requestOndemandDeliveryURL" => false,
                "getRateEstimates" => false,
                "accounts" => [
                    [
                        "number" => "451805801",
                        "typeCode" => "shipper",
                    ]
                ],
                "customerDetails" => [
                    "shipperDetails" => [
                        "postalAddress" => [
                            "cityName" => @$this->setting['cityName'] ?? '',
                            "countryCode" => @$this->setting['countryCode'] ?? '',
                            "postalCode" => "NULL",
                            "addressLine1" => @$this->setting['addressLine1'] ?? '',
                            "countyName" => @$this->setting['countyName'] ?? ''
                        ],
                        'contactInformation' => [
                            "mobilePhone" => @$this->setting['mobilePhone'] ?? '',
                            "phone" => @$this->setting['phone'] ?? '',
                            "companyName" => @$this->setting['companyName'] ?? '',
                            "fullName" => @$this->setting['fullName'] ?? ''
                        ],
                        "typeCode" => "business"
                    ],
                    "receiverDetails" => [
                        "postalAddress" => [
                            "cityName" => @$data['city_name'] ?? '',
                            "countryCode" => @$data['countryCode'] ?? '',
                            "provinceCode" => "Null",
                            "postalCode" => "Null",
                            "addressLine1" => @$data['attributes']['7'] ?? 'addressLine',
                            "addressLine2" => "Null",
                            "countyName" => @$data['countyName'] ?? ''
                        ],
                        "contactInformation" => [
                            "mobilePhone" => @$data['mobile'] ?? '',
                            "phone" => @$data['mobile'] ?? '',
                            "companyName" => @$data['username'] ?? 'companyName',
                            "fullName" => @$data['username'] ?? 'fullName'
                        ],
                        "typeCode" => "business"
                    ]
                ],
                "content" => [
                    "exportDeclaration" => [
                        "lineItems" => json_decode($this->products($order)),
                        "invoice" => [
                            "date" => Carbon::today()->format('Y-m-d'),
                            "customerDataTextEntries" => [
                                "string"
                            ],
                            "number" => (string)$order['id'],
                            "instructions" => [
                                "string"
                            ],
                            "totalGrossWeight" => @$data['weight'] ?? 1,
                            "signatureName" => "",
                            "function" => "both",
                            "totalNetWeight" => @$data['weight'] ?? 1,
                            "signatureTitle" => ""
                        ],
                        "placeOfIncoterm" => "DAP"
                    ],
                    "unitOfMeasurement" => "metric",
                    "isCustomsDeclarable" => true,
                    "incoterm" => "DAP",
                    "description" => "Desc",
                    "packages" => [
                        [
                            "customerReferences" => [
                                [
                                    "value" => (string)@$order->user->id ?? "1010499814",
                                    "typeCode" => "CU"
                                ]
                            ],
                            "identifiers" => [
                                [
                                    "value" => (string)@$order['id'],
                                    "typeCode" => "shipmentId"
                                ]
                            ],
                            "weight" => @$data['weight'] ?? 0.5,
                            "description" => "SOFFA ZONE PRODUCTS",
                            "dimensions" => [
                                "length" => @$data['length'] ?? 1,
                                "width" => @$data['width'] ?? 1,
                                "height" => @$data['height'] ?? 1
                            ],
                        ]
                    ],
                    "declaredValueCurrency" => "KWD",
                    "declaredValue" => (double)@$order['total'] ?? 2
                ],
                "valueAddedServices" => [
                    [
                        "serviceCode" => "WY"
                    ]
                ],
                "outputImageProperties" => [
                    "splitInvoiceAndReceipt" => false,
                    "splitDocumentsByPages" => false,
                    "splitTransportAndWaybillDocLabels" => false,
                    "printerDPI" => 300,
                    "encodingFormat" => "pdf",
                    "imageOptions" => [
                        [
                            "templateName" => "COMMERCIAL_INVOICE_P_10",
                            "invoiceType" => "commercial",
                            "languageCode" => "ENG",
                            "isRequested" => true,
                            "typeCode" => "invoice"
                        ],
                        [
                            "hideAccountNumber" => false,
                            "templateName" => "ARCH_8X4_A4_002",
                            "numberOfCopies" => 1,
                            "isRequested" => true,
                            "typeCode" => "waybillDoc"
                        ],
                        [
                            "templateName" => "ECOM26_84_A4_001",
                            "typeCode" => "label"
                        ]
                    ]
                ]
            ]);
        return $response->json();

    }

    public function details($id)
    {
        $response = Http::withBasicAuth($this->userName, $this->password)
            ->get("{$this->Url}/shipments/{$id} /tracking");
        return @$response->json()['shipments'][0];
    }

    public function validate($cityName, $countryCode)
    {
        $response = Http::withBasicAuth($this->userName, $this->password)
            ->get("{$this->Url}/address-validate", [
                'cityName' => $cityName,
                'countryCode' => $countryCode,
                'type' => 'delivery'
            ]);
        return @$response->json()['address'][0];
    }


    public function products($order)
    {
        $i = 1;
        $data = array();
        foreach ($order->orderProducts as $orderProducts) {
            $width = @$orderProducts->product->shipment['width'] ?? '7117' ;
            $length = @$orderProducts->product->shipment['length'] ?? '19';
            $height = @$orderProducts->product->shipment['height'] ?? '00';
            $data[] = [
                "number" => $i,
                "commodityCodes" => [
                    [
                        "value" => "$width.$length.$height",
                        "typeCode" => "outbound"
                    ],
                    [
                        "value" => "$width.$length.$height",
                        "typeCode" => "inbound"
                    ],
                ],
                "additionalInformation" => [
                    @$orderProducts->product->getTranslation('title', 'en'),
                ],
                "quantity" => [
                    "unitOfMeasurement" => "PCS",
                    "value" => $orderProducts->qty,
                ],
                "price" => (double)$orderProducts->total,
                "description" => $orderProducts->getTranslation('product_title', 'en'),
                "weight" => [
                    "netValue" => @$orderProducts->product->shipment['weight'] * $orderProducts->qty ?? 1,
                    "grossValue" => @$orderProducts->product->shipment['weight'] * $orderProducts->qty ?? 1,
                ],
                "isTaxesPaid" => false,
                "exportReasonType" => "permanent",
                "manufacturerCountry" => "RO",
            ];
            $i++;
        }
        return json_encode($data);
    }
}
