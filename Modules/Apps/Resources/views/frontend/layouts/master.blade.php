
@if(env("CUSTOM_LAYOUTS_PATH",null))
    @include(env("CUSTOM_LAYOUTS_PATH",false).".master")
@else
<!DOCTYPE html>
<html dir="{{ locale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ locale() == 'ar' ? 'ar' : 'en' }}">

@include('apps::frontend.layouts._header')

<body>
<div class="main-content">
    @include('apps::frontend.layouts.header-section',[
        'headerCategories' => $headerCategories,
        'aboutUs' => $aboutUs,

    ])
    <div class="site-main" id="main">
        @yield('content')
    </div>
    @include('apps::frontend.layouts.footer-section',compact('aboutUs','terms','privacyPage'))
</div>

@if(config('setting.contact_us.mobile'))
    <a href="https://wa.me/{{ config('setting.contact_us.whatsapp','') }}" class="whatsapp-chat no-print"
        data-toggle="tooltip" data-placement="top" target="_blank" style="z-index: 999"
        title="تواصل معنا" target="_blank">
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        <lottie-player src="https://assets2.lottiefiles.com/private_files/lf30_vfaddvqs.json" background="transparent"
            speed="1" style="width: 70px; height: 70px;" loop autoplay></lottie-player>
    </a>
@endif

@include('apps::frontend.layouts.currenciesModal')
@include('apps::frontend.layouts.scripts')

</body>
</html>
@endif
