

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '--') || {{ config('app.name') }} </title>
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="keywords" content="@yield('meta_keywords', '')">
    <meta name="author" content="{{ config('setting.app_name.'.locale()) ?? '' }}">

    @foreach(config("setting.social.markting", [] ) as $val)
       {!! isset($val) ? $val : '' !!}
    @endforeach

    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="icon" href="{{ config('setting.favicon') ? url(config('setting.favicon')) : url('frontend/favicon.png') }}"/>
    <link rel="stylesheet" href="{{asset('frontend/css/font-awesome.css')}}">
    <link href="{{ url('admin/assets/global/plugins/grapick/grapick.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('frontend/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/select2.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/smoothproducts.css')}}" type="text/css">
    <link href="{{asset('SewidanField/plugins/ck-editor-5/css/ckeditor.css')}}" rel="stylesheet" id="style_components" type="text/css" />
    <link rel="stylesheet" href="{{asset('frontend/css/vars.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/style-'.locale().'.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/css/style.css')}}">
    <link href="{{asset('frontend/plugins/live-search/jquery.autocomplete.css')}}" rel="stylesheet" id="style_components" type="text/css" />

    @stack('plugins_styles')

    {{-- Start - Bind Css Code From Dashboard Daynamic --}}
    {!! config('setting.custom_codes.css_in_head') ?? null !!}
    {{-- End - Bind Css Code From Dashboard Daynamic --}}

    @if(isset(Setting::get('theme_sections')['css']))
    <style>
    {!! Setting::get('theme_sections')['css'] !!}
    </style>
    @endif

    <link rel="stylesheet" href="{{asset('frontend/css/style.css')}}">

    <style>
        #wmobile{
            position: fixed;
            bottom: 55px;
            right: -12;
            height:60px;

        }

        #wdesktop{
            position: fixed;
            bottom: 10px;
            right: 15px;
            z-index: 99999;
        }

        #wmobile img,#wdesktop img{
            height:60px;
        }
        #currenciesModal .modal-header{
            background: transparent;
        }
        #currenciesModal .modal-header .modal-title,
        #currenciesModal .currencyItem .modal-content {
            color: #555;
        }
        #currenciesModal .modal-header img{
            height: 85px;
        }
        #currenciesModal .col-4{
            padding-right: 8px;
            padding-left: 8px;
        }
        #currenciesModal .currencyItem{
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 35px 10px 25px;
            transition: all ease-in-out .25s;
            -webkit-transition: all ease-in-out .25s;
            -moz-transition: all ease-in-out .25s;
            -o-transition: all ease-in-out .25s;
            cursor: pointer;
        }
        #currenciesModal .currencyItem .flag{
            font-size: 50px;
        }
        #currenciesModal .currencyItem.active{
            background: linear-gradient(to right, #fbcbd5 1%, #fbcbd5 1%);
        }
        .hidden{
            display: none !important;
        }
        @media (max-width:991px){
            #currenciesModal .countries{
                display: block;
            }
            #currenciesModal .country{
                max-width: 50% !important;
                width: 50% !important;
                float: left;
            }
        }
        @media (max-width: 1199px) {
            #currenciesModal .currencyItem{
                padding-top: 25px;
                padding-bottom: 5px;
            }
            .currencyItem .row .col-3,
            .currencyItem .row .col-9{
                max-width: 100% !important;
                text-align: center;
                flex: unset;
                display: block;
                margin-bottom: 10px;
            }
        }
    </style>
    @stack('styles')

    {{-- Start - Bind Js Code From Dashboard Daynamic --}}
    {!! config('setting.custom_codes.js_before_head') ?? null !!}
    {{-- End - Bind Js Code From Dashboard Daynamic --}}

    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1067916697130636');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=1067916697130636&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Meta Pixel Code -->


</head>
