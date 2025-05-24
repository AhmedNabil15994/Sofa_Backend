
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
@stack('scripts')
