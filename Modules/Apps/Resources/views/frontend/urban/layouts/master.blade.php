<!DOCTYPE html>
    <html dir="{{ locale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ locale() == 'ar' ? 'ar' : 'en' }}">

    @include(env("CUSTOM_LAYOUTS_PATH",false).'._header')

    <body class="common-home desktop" style="padding-top: 0px;">

        <div class="main-content">
            @include(env("CUSTOM_LAYOUTS_PATH",false).'.header-section',[
                'headerCategories' => $headerCategories,
                'aboutUs' => $aboutUs
            ])
            <div class="site-main" id="main">
                @yield('content')
            </div>
            @include(env("CUSTOM_LAYOUTS_PATH",false).'.footer-section',compact('aboutUs','terms','privacyPage'))
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

        @include(env("CUSTOM_LAYOUTS_PATH",false).'.scripts')

    </body>
</html>