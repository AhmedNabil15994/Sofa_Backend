<?php

$ConstProjectName = 'single_store';

return [
    'name' => 'Core',
    'image_mimes' => 'jpeg,png,jpg,gif,svg',
    'image_max' => '2048',
    'special_images' => ['default.png', 'user.png'],
    'tag_img_path' => 'uploads/tags',
    'vendor_img_path' => 'uploads/vendors',
    'brand_img_path' => 'uploads/brands',
    'category_img_path' => 'uploads/categories',
    'product_img_path' => 'uploads/products',
    'adverts_img_path' => 'uploads/adverts',
    'popup_ads_img_path' => 'uploads/popup_ads',
    'slider_img_path' => 'uploads/sliders',
    'settings_img_path' => 'uploads/settings',

    'constants' => [
        'SHIPPING_BRANCH' => $ConstProjectName . '_SHIPPING_BRANCH',
        'DELIVERY_CHARGE' => $ConstProjectName . '_DELIVERY_CHARGE',
        'DASHBOARD_CHANNEL' => $ConstProjectName . '_DASHBOARD_CHANNEL',
        'DASHBOARD_ACTIVITY_LOG' => $ConstProjectName . '_DASHBOARD_ACTIVITY_LOG',
        'VENDOR_DASHBOARD_CHANNEL' => $ConstProjectName . '_VENDOR_DASHBOARD_CHANNEL',
        'VENDOR_DASHBOARD_ACTIVITY_LOG' => $ConstProjectName . '_VENDOR_DASHBOARD_ACTIVITY_LOG',
        'DRIVER_DASHBOARD_CHANNEL' => $ConstProjectName . '_DRIVER_DASHBOARD_CHANNEL',
        'DRIVER_DASHBOARD_ACTIVITY_LOG' => $ConstProjectName . '_DRIVER_DASHBOARD_ACTIVITY_LOG',
        'CART_KEY' => $ConstProjectName . '_CART_KEY',
        'ORDERS_IDS' => $ConstProjectName . '_ORDERS_IDS',
        'CONTACT_INFO' => $ConstProjectName . '_CONTACT_INFO',
        'ORDER_STATE_ID' => $ConstProjectName . '_ORDER_STATE_ID',
        'ORDER_STATE_NAME' => $ConstProjectName . '_ORDER_STATE_NAME',
        'ORDER_DELIVERY_TIME' => $ConstProjectName . '_ORDER_DELIVERY_TIME',
        'ORDER_DELIVERY_PRICE' => $ConstProjectName . '_ORDER_DELIVERY_PRICE',
    ],
];
