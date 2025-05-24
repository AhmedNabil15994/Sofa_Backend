@if(isset(Setting::get('theme_sections')['top_footer']) && Setting::get('theme_sections')['top_footer'])
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-12 footer-logo-icon">
                    <img class="footer-logo"
                         src="{{ config('setting.logo') ? url(config('setting.logo')) : url('frontend/images/header-logo.png') }}"/>
                    

                    
                    <div class="links">
                        <ul>
                            @if(config('setting.contact_us.address'))
                                <li>{{ config('setting.contact_us.address') }}</li>

                            @endif
                        </ul>
                    </div>
                    
                    @if(isset(Setting::get('theme_sections')['footer_social_media']) && Setting::get('theme_sections')['footer_social_media'])
                        <div class="footer-social">
                            @if(isset(Setting::get('social')['facebook']) && Setting::get('social')['facebook'] && Setting::get('social')['facebook'] != '#')
                                <a href="{{Setting::get('social')['facebook']}}" class="social-icon"><i
                                            class="ti-facebook"></i></a>
                            @endif
                            @if(isset(Setting::get('social')['instagram']) && Setting::get('social')['instagram'] && Setting::get('social')['instagram'] != '#')
                                <a href="{{Setting::get('social')['instagram']}}" class="social-icon"><i
                                            class="ti-instagram"></i></a>
                            @endif
                            @if(isset(Setting::get('social')['linkedin']) && Setting::get('social')['linkedin'] && Setting::get('social')['linkedin']!= '#')
                                <a href="{{Setting::get('social')['linkedin']}}" class="social-icon"><i
                                            class="ti-linkedin"></i></a>
                            @endif
                            @if(isset(Setting::get('social')['twitter']) && Setting::get('social')['twitter'] && Setting::get('social')['twitter'] != '#')
                                <a href="{{Setting::get('social')['twitter']}}" class="social-icon"><i
                                            class="ti-twitter-alt"></i></a>
                            @endif
                            @if(isset(Setting::get('social')['tiktok']) && Setting::get('social')['tiktok'] && Setting::get('social')['tiktok'] != '#')
                                <a href="{{Setting::get('social')['tiktok']}}" class="social-icon"><i
                                            class="ti-tumblr-alt"></i></a>
                            @endif
                        </div>
                    @endif
                </div>

                @if(!$terms || !$privacyPage)
                    <div class="col-md-2 col-6">
                        <h3 class="title-of-footer"> {{ __('apps::frontend.master.important_links') }}</h3>
                        <div class="links">
                            <ul>
                                @if($terms)
                                    <li>
                                        <a href="{{ $terms ? route('frontend.pages.index', $terms->slug) : '#' }}">{{ __('apps::frontend.Terms & Conditions') }}</a>
                                    </li>
                                @endif

                                @if($privacyPage)
                                    <li>
                                        <a href="{{ $privacyPage ? route('frontend.pages.index', $privacyPage->slug) : '#' }}">{{ __('apps::frontend.Privacy & Policy') }}</a>
                                    </li>
                                @endif

                                @guest()
                                    <li>
                                        <a href="{{ route('frontend.login') }}"> {{ __('authentication::frontend.login.title') }}</a>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="col-md-2 col-6">
                    <h3 class="title-of-footer">{{ __('apps::frontend.master.website_links') }}</h3>
                    <div class="links">
                        <ul>
                            <li><a href="{{ route('frontend.home') }}">{{ __('apps::frontend.master.home') }}</a></li>
                            @if($aboutUs)
                                <li>
                                    <a href="{{ $aboutUs ? route('frontend.pages.index', $aboutUs->slug) : '#' }}">{{ __('apps::frontend.master.about_us') }}</a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('frontend.contact_us') }}">{{ __('apps::frontend.master.contact_us') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 col-12 footer-subscribe">
                    @if(env("HAS_MOBILE_APPS",false) || Setting::get('qr_code'))
                        <div class="links row">
                            @if(env("HAS_MOBILE_APPS",false))
                                <div class="col-lg-6">
                                    @if(Setting::get('apps.android_url'))
                                        <a href="{{Setting::get('apps.android_url')}}">
                                            <img style="width: 160px;border-radius:0px" class="footer-logo" src="{{ asset('images/icons/android_logo.svg') }}"/>
                                        </a>
                                    @endif
                                    <br>
                                    @if(Setting::get('apps.android_url'))
                                        <a href="{{Setting::get('apps.ios_url')}}">
                                            <img style="width: 160px;border-radius:0px" class="footer-logo" src="{{ asset('images/icons/ios_logo.svg') }}"/>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            @if(Setting::get('qr_code'))
                                <div class="col-lg-6">

                                    <img style="border-radius:0px" class="footer-logo" src="{{ asset(Setting::get('qr_code')) }}"/>
                                </div>
                            @endif
                            
                        </div>
                    @else
                        <h3 class="title-of-footer"> {{__('apps::frontend.Subscribe to get offers')}}</h3>
                        <div class="subscribe-form">
                            {!! Form::open([
                                    'url'=> route('frontend.subscribe'),
                                    'id'=>'subscribe-form',
                                    'role'=>'form',
                                    'method'=>'POST',
                                    'class'=>'subscribe-form',
                                    ])!!}
                            <input type="email" class="form-control" name="subscribe_email"
                                placeholder="{{ __('apps::frontend.contact_us.form.email')}}"/>
                            <button class="btn main-custom-btn"
                                    type="submit">{{ __('apps::frontend.master.subscribe') }}</button>
                            {!! Form::close()!!}
                        </div>
                    @endif
                    
                    <h3 class="title-of-footer">{{__('apps::frontend.Payment Method')}}</h3>
                    <div class="pay-men">
                        <a href="#"><img src="{{asset('frontend/images/payment.svg')}}" alt="pay1"></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endif


@if(isset(Setting::get('social')['whatsapp']) && Setting::get('social')['whatsapp'])
    <!--WhatsApp-->
    <div id="wmobile" class="d-sm-none">
        <!-- Mobile -->
        <a href="https://wa.me/{{Setting::get('social')['whatsapp']}}"><img src="{{url('uploads/whatspulse.gif')}}" alt="" style="    height: 120px;"></a>
    </div>

    <div id="wdesktop" class="d-none d-sm-block">
        <!-- Desktop only-->
        <a href="https://wa.me/{{Setting::get('social')['whatsapp']}}"><img src="{{url('uploads/whatspulse.gif')}}" alt="" style="    height: 120px;"/></a>
    </div>
@endif

@if(isset(Setting::get('theme_sections')['bottom_footer']) && Setting::get('theme_sections')['bottom_footer'])
    <div class="footer-copyright text-center">
        <p> <a href="https://trydukan.com">{{__('apps::frontend.Powered By Dukan',['domain' => config('setting.app_name.' . locale())])}}</a></p>
    </div>
@endif
