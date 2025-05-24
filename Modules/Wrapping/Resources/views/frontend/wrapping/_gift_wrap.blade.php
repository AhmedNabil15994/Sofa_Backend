<div class="modal fade" id="warp-details-{{ $giftObject->id }}" tabindex="-1" role="dialog"
     aria-hidden="true" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body wrap-det">
                <div class="img-box text-center mb-20">
                    <img src="{{ url($giftObject->image) }}" alt="{{ $giftObject->title }}"
                         style="width: 300px; height: 200px;"/>
                </div>
                <h4>{{ __('wrapping::frontend.wrapping.gift_wrapper') }}</h4>
                <span class="warp-price d-block mb-20">{{ $giftObject->price }} {{ __('apps::frontend.master.kwd') }}</span>
                <p>{{ $giftObject->title }}</p>

                <div class="choose-products-wrap mt-20">
                    <h5>{{ __('wrapping::frontend.wrapping.select_at_least_one_element') }}</h5>

                    <div class="row">

                        @if(count(getCartContent()) > 0)
                            @foreach (getCartContent() as $cartItem)

                                <div class="col-md-4 col-6">
                                    <div class="giftWrap-{{ $giftObject->id }} product-grid gift-wrap {{ checkSelectedCartGiftProducts($cartItem->attributes->product->id, $giftObject->id) ? 'active' : '' }}"
                                         data-id="{{ $cartItem->attributes->product->id }}">
                                        <div class="product-image d-flex align-items-center">
                                            <img class="pic-1" src="{{ url($cartItem->attributes->image) }}">
                                        </div>
                                        <div class="product-content">
                                            <h3 class="title">{{ $cartItem->attributes->product->title }}</h3>
                                            <span class="price">{{ $cartItem->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        @endif

                    </div>
                </div>

                <form class="form" method="POST">
                    @csrf
                    <div class="mb-20 mt-30 text-center">

                        <div class="loaderDiv">
                            <div class="my-loader"></div>
                        </div>

                        <button type="button"
                                id="btnCheckGift-{{$giftObject->id}}"
                                onclick="checkGiftProducts('{{ route('frontend.shopping-cart.add_gift', $giftObject->id) }}', '{{ $giftObject->id }}')"
                                class="btn btn-them w200 btnCheckGift main-custom-btn"> {{ __('wrapping::frontend.wrapping.btn.choose') }}</button>

                        {{--<button type="button" class="btn btn-danger" class="close" data-dismiss="modal" aria-label="Close">
                            Close
                        </button>--}}

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


@section('externalJs')

    <script>

        var productsIds = [];

        $('.choose-warp .gift-wrap').on('click', function (e) {
            productsIds = [];
        });

        function checkGiftProducts(action, giftId) {

            $(".choose-products-wrap .giftWrap-" + giftId).each(function () {

                var prdId = $(this).attr('data-id');

                // var classString = $(this).attr("class");
                // console.log('classes::', classString);
                // console.log('active::', $(this).hasClass("active"));
                // console.log('prdId::', $(this).attr('data-id'));

                if ($(this).hasClass("active") === true && prdId !== undefined) {
                    if (productsIds.indexOf(prdId) === -1) {
                        productsIds.push(prdId);
                    }
                }

                if ($(this).hasClass("active") === false && productsIds.includes(prdId)) {
                    var index = productsIds.indexOf(prdId);
                    productsIds.splice(index, 1);
                }

            });

            $('#btnCheckGift-' + giftId).hide();
            $('.loaderDiv').show();

            $.ajax({
                method: "POST",
                url: action,
                data: {
                    "products_ids": productsIds,
                    "_token": "{{ csrf_token() }}",
                },
                beforeSend: function () {
                },
                success: function (data) {
                    displaySuccessMsg(data.message);
                },
                error: function (data) {
                    $('.loaderDiv').hide();
                    $('#btnCheckGift-' + giftId).show();
                    displayErrorsMsg(data);
                },
                complete: function (data) {

                    productsIds = [];
                    $('.loaderDiv').hide();
                    $('#btnCheckGift-' + giftId).show();

                    var getJSON = $.parseJSON(data.responseText);
                    if (getJSON.data) {
                        $('#cartTotalAmount').html(getJSON.data.total + " {{ __('apps::frontend.master.kwd') }}");
                    }

                },
            });

        }

        function addOrUpdateCartCard(action, cardId) {

            var senderName = $('#card_sender_name_' + cardId).val();
            var receiverName = $('#card_receiver_name_' + cardId).val();
            var msgName = $('#card_message_' + cardId).val();

            $('#btnCardCart-' + cardId).hide();
            $('.loaderDiv').show();

            $.ajax({
                method: "POST",
                url: action,
                data: {
                    "sender_name": senderName,
                    "receiver_name": receiverName,
                    "message": msgName,
                    "_token": "{{ csrf_token() }}",
                },
                beforeSend: function () {
                },
                success: function (data) {
                    displaySuccessMsg(data.message);

                    /*$('#card_sender_name_' + cardId).val('');
                    $('#card_receiver_name_' + cardId).val('');
                    $('#card_message_' + cardId).val('');*/
                },
                error: function (data) {
                    $('.loaderDiv').hide();
                    $('#btnCardCart-' + cardId).show();
                    displayErrorsMsg(data);
                },
                complete: function (data) {

                    $('.loaderDiv').hide();
                    $('#btnCardCart-' + cardId).show();

                    var getJSON = $.parseJSON(data.responseText);
                    if (getJSON.data) {
                        $('#cartTotalAmount').html(getJSON.data.total + " {{ __('apps::frontend.master.kwd') }}");
                    }

                },
            });

        }

        function addOrUpdateCartAddons(action, addonsId) {

            var qty = $('#qty_' + addonsId).val();

            $('#btnAddonsCart-' + addonsId).hide();
            $('.loaderDiv').show();

            $.ajax({
                method: "POST",
                url: action,
                data: {
                    "qty": qty,
                    "_token": "{{ csrf_token() }}",
                },
                beforeSend: function () {
                },
                success: function (data) {
                    displaySuccessMsg(data.message);
                },
                error: function (data) {
                    $('.loaderDiv').hide();
                    $('#btnAddonsCart-' + addonsId).show();
                    displayErrorsMsg(data);
                },
                complete: function (data) {

                    $('.loaderDiv').hide();
                    $('#btnAddonsCart-' + addonsId).show();

                    var getJSON = $.parseJSON(data.responseText);
                    if (getJSON.data) {
                        $('#cartTotalAmount').html(getJSON.data.total + " {{ __('apps::frontend.master.kwd') }}");
                    }

                },
            });

        }

    </script>

@endsection
