
<script src="{{asset('frontend/js/jquery.min.js')}}"></script>
<script src="{{asset('frontend/js/jquery-ui.min.js')}}"></script>
<script src="{{asset('frontend/js/popper.min.js')}}"></script>
<script src="{{asset('frontend/js/bootstrap.min.js')}}"></script>
<script src="{{asset('frontend/js/wow.min.js')}}"></script>
<script src="{{asset('frontend/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('frontend/js/smoothproducts.min.js')}}"></script>
<script src="{{asset('frontend/js/select2.min.js')}}"></script>

<script src="{{asset('frontend/plugins/live-search/jquery.autocomplete.js')}}"></script>
<script src="https://unpkg.com/vue@3"></script>
@stack('plugins_scripts')

<script src="{{asset('frontend/plugins/sweetalert2.all.js')}}"></script>

@include('apps::frontend.layouts._js')

{{-- Start - Bind Js Code From Dashboard Daynamic --}}
{!! config('setting.custom_codes.js_before_body') ?? null !!}
{{-- End - Bind Js Code From Dashboard Daynamic --}}

<script src="{{asset('frontend/js/script-'.locale().'.js')}}"></script>
<script src="{{ url('frontend/js/actions.js') }}"></script>
<script src="{{ url('frontend/js/script.js') }}"></script>

@php
    $current_country = isset(session()->get('currency_data')['selected_currency']) ? optional(session()->get('currency_data')['selected_currency'])->country : null;
@endphp
<script>
    $(function (){
        @if(!$current_country)
        $('.currency_selector').click()
        @endif
        $(document).on('click','#currenciesModal .currencyItem',function (){
            let currencyId = $(this).data('area');
            $('#currenciesModal .currencyItem.active').removeClass('active');
            $(this).addClass('active');
            $.ajax({
                type:'POST',
                url: "{{ route('frontend.home.update_currency') }}",
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'currency_id' : currencyId,
                },
                success: function (data){
                    $('#currenciesModal').modal('hide')
                    setTimeout(function (){
                        location.reload()
                    },500)
                }
            });
        });
    });
</script>
@stack('scripts')
