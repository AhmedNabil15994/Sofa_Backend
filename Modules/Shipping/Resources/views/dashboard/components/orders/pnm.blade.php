
@php 
    $data = [
        'ID' => $shipmentStatus->shipment_id,
        __('shipping::dashboard.shipping') => strtoupper($order->address_type),
        __('shipping::dashboard.status') => isset($shipmentStatus->json_data['tracking_url']) ? 
        '<a target="blank" href="'.$shipmentStatus->json_data['tracking_url'].'"><i class="fa fa-eye"></i></a>' : '',
        __('shipping::dashboard.airway_bill') => isset($shipmentStatus->json_data['label_url']) ? 
        '<a target="blank" href="'.$shipmentStatus->json_data['label_url'].'"><i class="fa fa-eye"></i></a>' : '',
        __('shipping::dashboard.price') => $order->shipping ,
        __('shipping::dashboard.shipping_date') => $shipmentStatus->date,
    ];
@endphp

<div class="portlet light bordered" style="    border: 1px solid #e7ecf1!important">
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="fa fa-shopping-cart font-red-sunglo"></i>
            <span class="caption-subject bold uppercase">
                    {{ __('shipping::dashboard.order_shipping_details') }}
                    </span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="no-print">
            <div class="row">
                <div class="col-md-12">

                    <div class="col-xs-12 table-responsive">
                        <table class="table table-hover">
                            <thead>
                                @foreach($data as $title => $value)
                                    <tr>
                                        <th class="text-left bold">
                                            {{ $title }}
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-left bold"> 
                                            {!! $value !!}
                                        </th>
                                    </tr>
                                @endforeach
                            </thead>
                        </table>
                    </div>
                    @permission('cancel_shipment_request')
           
                        @if(!$order->shipmentCancel)
                            <a class="btn btn-lg btn-danger hidden-print margin-bottom-5" href="#request_cancel_shipping" data-toggle="modal"  style="margin: 0px 77px;">
                                {{ __('shipping::dashboard.request_cancel_shipping') }}
                            </a>
                            <div class="modal fade" id="request_cancel_shipping" tabindex="-1" role="basic" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            
                                        </div>

                                        {!! Form::open([
                                            'url'=> route('dashboard.shipment.cancel',$order->id),
                                            'role'=>'form',
                                            'page'=>'form',
                                            'class'=>'updateForm',
                                            'method'=>'PUT',
                                            'files' => true
                                            ])!!}
                                            <div class="modal-body"> 
                                                <div class="form-group">
                                                    <label>
                                                        {{ __('shipping::dashboard.cancel_reson') }}
                                                    </label>
                                                    <textarea class="form-control" name="reason" rows="8" cols="80"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="form-actions">
                                                    @include('apps::dashboard.layouts._ajax-msg')
                                                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">
                                                    
                                                                        {{ __('order::dashboard.orders.show.close') }}
                                                                        </button>
                                                    <button type="submit" class="btn red submit">
                                                        {{ __('shipping::dashboard.request_cancel_shipping') }}
                                                    </button>
                                                </div>
                                            </div>
                                        {!! Form::close()!!}
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                        @else
                            <span style="background-color: #D4EDDA;padding: 2px 14px; color: #000000; border-radius: 25px; float: none;"> 
                                {{ __('shipping::dashboard.cancel_shipping_requested') }}
                            </span>
                        @endif
                    @endpermission
                </div>
            </div>
        </div>
    </div>
</div>