<?php

// Dashboard ViewComposr
view()->composer([
    'catalog::dashboard.categories.*',
    'catalog::dashboard.products.*',
    'advertising::dashboard.advertising.*',
    'notification::dashboard.notifications.*',
    'slider::dashboard.sliders.*',
    'slider::dashboard.banner.*',
    'coupon::dashboard.*',
], \Modules\Catalog\ViewComposers\Dashboard\CategoryComposer::class);

// Dashboard ViewComposr
view()->composer([
    'advertising::dashboard.advertising.*',
    'notification::dashboard.notifications.*',
    'slider::dashboard.sliders.*',
    'slider::dashboard.banner.*',
], \Modules\Catalog\ViewComposers\Dashboard\ProductComposer::class);


view()->composer([
    'coupon::dashboard.*',
], \Modules\Catalog\ViewComposers\Dashboard\ProductComposer::class);


// FrontEnd ViewComposer
view()->composer([
//'apps::frontend.layouts.header-section',
//'apps::frontend.layouts.footer-section',
'apps::frontend.layouts.master',
], \Modules\Catalog\ViewComposers\FrontEnd\CategoryComposer::class);

// Dashboard View Composer
view()->composer([
    'catalog::dashboard.products.*',
], \Modules\Catalog\ViewComposers\Dashboard\SearchKeywordComposer::class);
