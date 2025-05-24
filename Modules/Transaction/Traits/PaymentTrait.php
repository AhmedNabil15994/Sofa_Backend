<?php

namespace Modules\Transaction\Traits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Transaction\Services\MyFatoorahPaymentService;
use Modules\Transaction\Services\TabbyPaymentService;
use Modules\Transaction\Services\TapPaymentService;
use Modules\Transaction\Services\UPaymentService;

trait PaymentTrait
{
    static function getPaymentGateway($payment)
    {
        if (config('setting.payment_gateway.'.$payment) && config('setting.payment_gateway.'.$payment.'.status') == 'on') {

            switch ($payment) {
                case 'upayment':
                    return new UPaymentService();
                case 'tap':
                    return new TapPaymentService();
                case 'my_fatoorah':
                    return new MyFatoorahPaymentService();
                case 'tabby':
                    return new TabbyPaymentService();
            }
        }
        return false;
    }

    static function buildTapRequestData($data ,Request $request){

        $request->merge([
           'OrderID' => isset($data['metadata']['udf5']) ? $data['metadata']['udf5'] : null,
           'userToken' => isset($data['metadata']['udf6']) ? $data['metadata']['udf6'] : null,
           'Result' => isset($data['status']) ? $data['status'] : null,
           'Auth' => isset($data['transaction']['authorization_id']) ? $data['transaction']['authorization_id'] : null,
           'TranID' => isset($data['id']) ? $data['id'] : null,
           'PostDate' => isset($data['transaction']['created']) ? $data['transaction']['created'] : null,
           'Ref' => null,
           'TrackID' => isset($data['reference']['track']) ? $data['reference']['track'] : null,
           'PaymentID' => isset($data['reference']['payment']) ? $data['reference']['payment'] : null,
        ]);

        return $request;
    }

    static function buildUpaymentRequestData($data ,Request $request){

        $request->merge([
            'userToken' => $request->cust_ref ?? null,
        ]);

        return $request;
    }

    static function buildMyFatoorahRequestData($data ,Request $request){
        $data = (array)$data;
        $request->merge([
           'OrderID' => isset($data['CustomerReference']) ? $data['CustomerReference'] : null,
           'userToken' => isset($data['UserDefinedField']) ? $data['UserDefinedField'] : null,
           'Result' => isset($data['InvoiceStatus']) && $data['InvoiceStatus'] == 'Paid' ? 'CAPTURED' : null
        ]);

        if(isset($data['InvoiceTransactions']) && isset($data['InvoiceTransactions'][0])){
            $InvoiceTransactions = $data['InvoiceTransactions'][0];
            $request->merge([
                'Auth' => $InvoiceTransactions->AuthorizationId ?? null,
                'TranID' => $InvoiceTransactions->TransactionId ?? null,
                'PostDate' => $InvoiceTransactions->TransactionDate ?
                    Carbon::parse($InvoiceTransactions->TransactionDate)->toDateTimeString() : null,
                'Ref' => $InvoiceTransactions->ReferenceId ?? null,
                'TrackID' => $InvoiceTransactions->TrackId ?? null,
                'PaymentID' => $InvoiceTransactions->PaymentId ?? null,
             ]);
        }

        return $request;
    }

    static function buildTabbyRequestData($data ,Request $request){
//        dd($data);
        $request->merge([
            'OrderID' => isset($data->payment->order->reference_id) ? $data->payment->order->reference_id : null,
//            'userToken' => isset($data['metadata']['udf6']) ? $data['metadata']['udf6'] : null,
            'Result' => isset($data->payment->status) && $data->payment->status == 'AUTHORIZED' ? 'CAPTURED' : null,
//            'Auth' => isset($data['transaction']['authorization_id']) ? $data['transaction']['authorization_id'] : null,
            'TranID' => isset($data->payment->id) ? $data->payment->id : null,
            'PostDate' => isset($data->payment->created_at) ? $data->payment->created_at : null,
            'Ref' => null,
            'TrackID' => isset($data->id) ? $data->id : null,
            'PaymentID' => isset($data->payment->id) ? $data->payment->id : null,
        ]);

        return $request;
    }
}
