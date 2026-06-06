<?php
/**
 * Cash on Delivery Configuration Fields
 *
 * @copyright  2024 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     HiFast Development Team
 * @created    2024-02-22
 */

return [
    [
        'name'        => 'title',
        'label_key'   => 'setting.title',
        'type'        => 'string',
        'required'    => false,
        'rules'       => 'nullable|max:255',
        'description' => 'Payment method title (leave empty to use default)',
    ],
    [
        'name'        => 'description',
        'label_key'   => 'setting.description',
        'type'        => 'text',
        'required'    => false,
        'rules'       => 'nullable|max:500',
        'description' => 'Payment method description',
    ],
    [
        'name'        => 'instructions',
        'label_key'   => 'setting.instructions',
        'type'        => 'text',
        'required'    => false,
        'rules'       => 'nullable|max:1000',
        'description' => 'Instructions that will be shown on the payment page',
    ],
];
