<?php

return [
    'attributes'  => [
        'datatable' => [
            'created_at'    => 'Created At',
            'date_range'    => 'Search By Dates',
            'options'       => 'Options',
            'status'        => 'Status',
            "type"         => "Type",
            'name'         => 'Name',

            "icon"      => "Icon",
            'sort'         => 'Sort',
            "order"     => "Orders",
            "options"     => "Options",
            "values"     => "Values",
            "show_in_search" => "Show in search",

        ],
        'form'      => [
            'name'         => 'name',
            'sort'         => 'Sort',
            'options'       => 'Options',
            'status'        => 'Status',
            'name'         => 'Name',
            "type"         => "Type",
            "show_in_search" => "Show in search",
            "price"         => "Price ",

            "icon"      => "Icon",
            "order"     => "Orders",
            "options"     => "Options",
            "values"     => "Values",
            "show"     => "Show",
            "hide"     => "Hide",
            "this_field_if"     => "this field if",
            "any"     => "any",
            "all"     => "all",
            "is"     => "Is",
            "is_not"     => "Is Not",
            "of_these_rules_match"     => "of these rules match",
            'tabs'              => [
                'general'   => 'General Info.',
                "validation" => "Validation",
                "products" => "Products",
            ],
            'slider_type' => [
                'categories'   => 'Categories',
                'products'   => 'Products',
                'addresses'   => 'Addresses',
                'checkout'   => 'Checkout',
                'childAttributes'   => 'Child Custom Attributes',
            ],
            'placeholders' => [
                'categories'   => 'Select Categories',
                'products'   => 'Select Products',
                'childAttributes'   => 'Child Custom Attributes',
            ],
            "validation"   => [
                "min" => "Min Amount",
                "max" => "Max Amount",
                "is_int" => "Intiger Value",
                "validate_min" =>"Validate Minmum ",
                "validate_max" =>" Validate Maxmum ",
                "required"     => "Required"
            ],
        ],
        'routes'    => [
            'create'    => 'Create Attribute',
            'index'     => 'Attributes',
            'update'    => 'Update Attribute',
        ],

    ],

];
