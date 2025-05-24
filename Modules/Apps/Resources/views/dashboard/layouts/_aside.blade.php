<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed" data-keep-expanded="false" data-auto-scroll="true"
            data-slide-speed="200" style="padding-top: 20px">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item {{ active_menu(['home', '']) }}">
                <a href="{{ url(route('dashboard.home')) }}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">{{ __('apps::dashboard.home.title') }}</span>
                    <span class="selected"></span>
                </a>
            </li>

            <li class="nav-item open">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users"></i>
                    <span class="title">{{ __('apps::dashboard.aside.tab.users') }}</span>
                    <span class="arrow open"></span>
                    <span class="selected"></span>
                </a>
                <ul class="sub-menu" style="display: block;">

                    @permission('roles')
                    <li class="nav-item {{ active_menu('roles') }}">
                        <a href="{{ url(route('dashboard.roles.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-briefcase"></i>
                            <span class="title">{{ __('apps::dashboard._layout.aside.roles') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_users')
                    <li class="nav-item {{ active_menu('users') }}">
                        <a href="{{ url(route('dashboard.users.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.users') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_admins')
                    <li class="nav-item {{ active_menu('admins') }}">
                        <a href="{{ url(route('dashboard.admins.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.admins') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>

            @if (Module::isEnabled('Order'))
                <li class="nav-item  open">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">{{ __('apps::dashboard.aside.tab.orders') }}</span>
                        <span class="arrow open"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu" style="display: block;">


                        @permission('show_orders')

                        <li class="nav-item {{ active_menu('current-orders') }}">
                            <a href="{{ url(route('dashboard.current_orders.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.current_orders') }}</span>
                                @if (isset($ordersCount['current_orders']) && $ordersCount['current_orders'] > 0)
                                    <span class="badge badge-danger">{{ $ordersCount['current_orders'] }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('completed-orders') }}">
                            <a href="{{ url(route('dashboard.completed_orders.index')) }}"
                                class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span
                                    class="title">{{ __('apps::dashboard.aside.completed_orders') }}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('not-completed-orders') }}">
                            <a href="{{ url(route('dashboard.not_completed_orders.index')) }}"
                                class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span
                                    class="title">{{ __('apps::dashboard.aside.not_completed_orders') }}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('refunded-orders') }}">
                            <a href="{{ url(route('dashboard.refunded_orders.index')) }}"
                                class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.refunded_orders') }}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('all-orders') }}">
                            <a href="{{ url(route('dashboard.all_orders.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.all_orders') }}</span>
                            </a>
                        </li>

                        @endpermission
                    </ul>
                </li>
            @endif

            @if (Module::isEnabled('Catalog'))
                <li class="nav-item  open">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">{{ __('apps::dashboard.aside.tab.catalog') }}</span>
                        <span class="arrow open"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu" style="display: block;">
                        @permission('show_products')
                        <li class="nav-item {{ active_menu('products') }}">
                            <a href="{{ url(route('dashboard.products.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.products') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_categories')
                        <li class="nav-item {{ active_menu('categories') }}">
                            <a href="{{ url(route('dashboard.categories.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.categories') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @if(config('setting.products.toggle_variations') == 1)
                            @permission('show_options')
                            <li class="nav-item {{ active_menu('options') }}">
                                <a href="{{ url(route('dashboard.options.index')) }}" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">{{ __('apps::dashboard.aside.options') }}</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            @endpermission
                        @endif

                        @permission('show_tags')
                        <li class="nav-item {{ active_menu('tags') }}">
                            <a href="{{ url(route('dashboard.tags.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-tag"></i>
                                <span class="title">{{ __('apps::dashboard.aside.tags') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_search_keywords')
                        <li class="nav-item {{ active_menu('search_keywords') }}">
                            <a href="{{ url(route('dashboard.search_keywords.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.search_keywords') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_attributes')
                            <li class="nav-item {{ active_menu('attributes') }}">
                                <a href="{{ url(route('dashboard.attributes.index')) }}" class="nav-link nav-toggle">
                                    <i class="icon-settings"></i>
                                    <span class="title">{{ __('apps::dashboard.aside.attributes') }}</span>
                                </a>
                            </li>
                        @endpermission

                        <li class="nav-item {{ active_menu('ages') }}">
                            <a href="{{ url(route('dashboard.ages.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('Ages') }}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('brands') }}">
                            <a href="{{ url(route('dashboard.brands.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('Brands') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif


            <li class="nav-item  open">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.tab.marketing') }}</span>
                    <span class="arrow open"></span>
                    <span class="selected"></span>
                </a>
                <ul class="sub-menu" style="display: block;">

                    @permission('show_coupon')
                    <li class="nav-item {{ active_menu('coupons') }}">
                        <a href="{{ url(route('dashboard.coupons.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-calculator"></i>
                            <span class="title">{{ __('apps::dashboard.aside.coupons') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_banner')
                    <li class="nav-item {{ active_menu('banner') }}">
                        <a href="{{ url(route('dashboard.banner.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.banner') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_notifications')
                    <li class="nav-item {{ active_menu('notifications') }}">
                        <a href="{{ url(route('dashboard.notifications.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.notifications') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_apphomes')
                    <li class="nav-item {{ active_menu('app-homes') }}">
                        <a href="{{ url(route('dashboard.apphomes.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.apphomes') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_advertising')
                    <li class="nav-item {{ active_menu('advertising-groups') }}">
                        <a href="{{ url(route('dashboard.advertising_groups.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.advertising_groups') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_subscriptions')
                    <li class="nav-item {{ active_menu('subscribe') }}">
                        <a href="{{ url(route('dashboard.subscribe.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.subscriptions') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission

                    @permission('show_social_marketing')
                    <li class="nav-item {{ active_menu('social') }}">
                        <a href="{{ url(route('dashboard.social.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.social_media') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>


            {{-- ############################################################################# --}}

            @if (Module::isEnabled('Company'))

                <li class="nav-item  {{active_slide_menu(['companies','delivery-charges','drivers'])}}">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">{{ __('apps::dashboard.aside.tab.companies') }}</span>
                        <span class="arrow {{active_slide_menu(['companies','delivery-charges','drivers'])}}"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">

                        @permission('show_companies')
                        <li class="nav-item {{ active_menu('companies') }}">
                            <a href="{{ url(route('dashboard.companies.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.companies') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_delivery_charges')
                        <li class="nav-item {{ active_menu('delivery-charges') }}">
                            <a href="{{ url(route('dashboard.delivery-charges.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.delivery_charges') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_drivers')
                        <li class="nav-item {{ active_menu('drivers') }}">
                            <a href="{{ url(route('dashboard.drivers.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.drivers') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
            @endif


            @if (Module::isEnabled('Report'))

                <li class="nav-item  {{active_slide_menu(['product-sales-reports','product-stock-reports','order-sales-reports'])}}">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">{{ __('apps::dashboard.aside.tab.reports') }}</span>
                        <span class="arrow {{active_slide_menu(['product-sales-reports','product-stock-reports','order-sales-reports'])}}"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">
                        @permission('show_product_sale_reports')
                        <li class="nav-item {{ active_menu('product-sales-reports') }}">
                            <a href="{{ url(route('dashboard.reports.product_sale')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.product_sales') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_product_stock_reports')
                        <li class="nav-item {{ active_menu('product-stock-reports') }}">
                            <a href="{{ url(route('dashboard.reports.product_stock')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.product_stock') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_order_sale_reports')
                        <li class="nav-item {{ active_menu('order-sales-reports') }}">
                            <a href="{{ url(route('dashboard.reports.order_sale')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.order_sales') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
            @endif

            @if (Module::isEnabled('Area'))
                <li class="nav-item  {{active_slide_menu(['countries','cities','states'])}}">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">{{ __('apps::dashboard.aside.tab.areas') }}</span>
                        <span class="arrow {{active_slide_menu(['countries','cities','states'])}}"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">

                        @permission('show_countries')
                        <li class="nav-item {{ active_menu('countries') }}">
                            <a href="{{ url(route('dashboard.countries.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.countries') }}</span>
                        <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_cities')
                        <li class="nav-item {{ active_menu('cities') }}">
                            <a href="{{ url(route('dashboard.cities.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.cities') }}</span>
                        <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission

                        @permission('show_states')
                        <li class="nav-item {{ active_menu('states') }}">
                            <a href="{{ url(route('dashboard.states.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.states') }}</span>
                        <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
            @endif

            <li class="nav-item  {{active_slide_menu(['pages','setting'])}}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.tab.setting') }}</span>
                    <span class="arrow {{active_slide_menu(['pages','setting'])}}"></span>
                    <span class="selected"></span>
                </a>
                <ul class="sub-menu" style="display:{{active_slide_menu(['pages','setting']) == 'open' ? 'block' : 'none'}}">


                    <li class="nav-item {{ active_menu('setting') }}">
                        <a href="{{ url(route('dashboard.setting.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">@lang('apps::dashboard.aside.tab.setting')</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @permission('show_pages')
                    <li class="nav-item {{ active_menu('pages') }}">
                        <a href="{{ url(route('dashboard.pages.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::dashboard.aside.pages') }}</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>

            @if(auth()->user()->tocaan_perm)
                <li class="nav-item  {{active_slide_menu(['setting','permissions','themes'])}}">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">Tocaan Tab</span>
                        <span class="arrow {{active_slide_menu(['setting','permissions','themes'])}}"></span>
                        <span class="selected"></span>
                    </a>
                    <ul class="sub-menu">

                        <li class="nav-item {{ active_menu('setting') }}">
                            <a href="{{ url(route('dashboard.setting.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">Settings</span>
                                <span class="selected"></span>
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('permissions') }}">
                            <a href="{{ url(route('dashboard.permissions.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">Permissions</span>
                                <span class="selected"></span>
                            </a>
                        </li>

                        <li class="nav-item {{ active_menu('themes') }}">
                            <a href="{{ url(route('developer.themes.colors.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">Themes Colors</span>
                                <span class="selected"></span>
                            </a>
                        </li>

                        @permission('show_order_statuses')
                        <li class="nav-item {{ active_menu('order-statuses') }}">
                            <a href="{{ url(route('dashboard.order-statuses.index')) }}" class="nav-link nav-toggle">
                                <i class="icon-settings"></i>
                                <span class="title">{{ __('apps::dashboard.aside.order_statuses') }}</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
