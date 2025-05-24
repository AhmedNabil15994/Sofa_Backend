<?php

view()->composer(
    [
        'apps::dashboard.index',
        'order::dashboard.shared._filter',
        'order::dashboard.shared._bulk_order_actions',
    ],
    \Modules\Order\ViewComposers\Dashboard\OrderStatusComposer::class
);

view()->composer(
    [
        'setting::dashboard.index',
    ],
    \Modules\Order\ViewComposers\Dashboard\OrderStatusSettingComposer::class
);
