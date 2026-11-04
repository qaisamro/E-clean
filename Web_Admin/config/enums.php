<?php

return [
    'media_types' => ['image', 'audio', 'video', 'docs', 'excel', 'pdf', 'other'],

    // Empty = postcode restriction disabled (service available everywhere).
    'post_code' => [],

    'currency' => '₪',

    'coupons' => [
        'discount_types' => [
            'percent' => 'percent',
            'amount' => 'amount'
        ]
    ],

    'payment_status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'unpaid' => 'Unpaid',
    ],

    'payment_types' => [
        'cash_on_delivery' => 'Cash on Delivery',
        'online_payment' => 'Online Payment'
    ],

    'order_status' => [
        'pending' => 'Pending',
        'order_confirmed' => 'Order confirmed',
        'picked_order' => 'Picked your order',
        'processing' => 'Processing',
        'cancelled' => 'Cancelled',
        'delivered' => 'Delivered',
    ],

    'variants' => [
        'Men', 'Women', 'Kids', 'House Hold', 'Others'
    ],

    'settings' => [
        'privacy-policy' => 'Privacy Policy',
        'trams-of-service' => 'Terms of Service',
        'contact-us' => 'Contact us',
        'about-us' => 'About Us'
    ],

    'ganders' => [
        'Male', 'Female', 'Others'
    ]
];
