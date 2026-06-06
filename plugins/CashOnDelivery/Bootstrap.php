<?php
/**
 * Bootstrap.php
 *
 * @copyright  2024 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     HiFast Development Team
 * @created    2024-02-22
 */

namespace Plugin\CashOnDelivery;

class Bootstrap
{
    /**
     * Boot the Cash on Delivery payment plugin
     *
     * @throws \Exception
     */
    public function boot(): void
    {
        // Hook for mobile payment data
        add_hook_filter('service.payment.mobile_pay.data', function ($data) {
            $order = $data['order'];
            if ($order->payment_method_code != 'cash_on_delivery') {
                return $data;
            }

            // For COD, we return simple params indicating it's a COD order
            $data['params'] = [
                'payment_method' => 'cash_on_delivery',
                'payment_type' => 'offline',
                'message' => trans('CashOnDelivery::common.mobile_message'),
            ];

            return $data;
        });

        // Hook for order status after COD payment is selected
        add_hook_filter('service.payment.pay.view_path', function ($viewPath) {
            if (strpos($viewPath, 'CashOnDelivery') !== false) {
                return $viewPath;
            }
            return $viewPath;
        });
    }
}
