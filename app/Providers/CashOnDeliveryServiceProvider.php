<?php
/**
 * CashOnDeliveryServiceProvider.php
 *
 * Registers the built-in Cash on Delivery payment method.
 * Works without any plugin installation in the database.
 *
 * Hooks used:
 *  1. 'service.checkout.data'      — Injects COD into the payment_methods array
 *                                    displayed on the checkout page.
 *  2. 'repo.plugin.payment_methods'— Injects a proper Plugin model so that
 *                                    PluginRepo::paymentEnabled('cash_on_delivery')
 *                                    returns true during order confirmation.
 */

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CashOnDeliveryServiceProvider extends ServiceProvider
{
    private const PLUGIN_DIR  = 'CashOnDelivery';
    private const PLUGIN_CODE = 'cash_on_delivery';

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (! installed()) {
            return;
        }

        $pluginPath = base_path('plugins/' . self::PLUGIN_DIR);

        // Register the COD payment confirmation route
        $shopRoutePath = $pluginPath . '/Routes/shop.php';
        if (file_exists($shopRoutePath)) {
            Route::name('shop.')
                ->middleware('shop')
                ->group(function () use ($shopRoutePath) {
                    $this->loadRoutesFrom($shopRoutePath);
                });
        }

        // ─────────────────────────────────────────────────────────────
        // HOOK 1: Inject COD into the checkout DATA array (for display).
        //
        // This hook runs AFTER PaymentMethodItem::collection() has already
        // serialized DB-installed plugins into plain arrays. We simply
        // prepend a plain COD array entry — no BPlugin processing needed.
        // ─────────────────────────────────────────────────────────────
        add_hook_filter('service.checkout.data', function ($data) {
            if (! is_array($data)) {
                return $data;
            }

            $payments = $data['payment_methods'] ?? [];

            if (! is_array($payments)) {
                $payments = [];
            }

            // Avoid duplicate injection
            foreach ($payments as $payment) {
                if (is_array($payment) && ($payment['code'] ?? '') === self::PLUGIN_CODE) {
                    return $data;
                }
            }

            // Resolve locale for localized name/description
            try {
                $locale = session()->get('locale', config('app.locale', 'en'));
            } catch (\Exception $e) {
                $locale = 'en';
            }
            $locale = str_replace('-', '_', (string) $locale);

            $names = [
                'en'    => 'Cash on Delivery',
                'zh_cn' => '货到付款',
                'zh_hk' => '貨到付款',
            ];
            $descriptions = [
                'en'    => 'Pay with cash when your order arrives.',
                'zh_cn' => '收到货物后支付现金',
                'zh_hk' => '收到貨物後支付現金',
            ];

            // Prepend COD so it appears first in the payment method list
            array_unshift($payments, [
                'type'        => 'payment',
                'code'        => self::PLUGIN_CODE,
                'name'        => $names[$locale] ?? $names['en'],
                'description' => $descriptions[$locale] ?? $descriptions['en'],
                'icon'        => '/image/cod-icon.png',
            ]);

            $data['payment_methods'] = $payments;

            return $data;
        }, 20);

        // ─────────────────────────────────────────────────────────────
        // HOOK 2: Allow COD to pass PluginRepo::paymentEnabled() check.
        //
        // During order confirmation, validateConfirm() calls:
        //   PluginRepo::paymentEnabled('cash_on_delivery')
        // which filters getPaymentMethods() for items with code === 'cash_on_delivery'.
        //
        // We inject a proper Plugin Eloquent model (with the BPlugin already
        // loaded by PluginServiceProvider from config.json) so the check passes.
        // ─────────────────────────────────────────────────────────────
        add_hook_filter('repo.plugin.payment_methods', function ($methods) {
            // Avoid duplicate
            foreach ($methods as $method) {
                if (isset($method->code) && $method->code === self::PLUGIN_CODE) {
                    return $methods;
                }
            }

            // Get the BPlugin loaded by Manager (from CashOnDelivery/config.json)
            $bPlugin = plugin(self::PLUGIN_CODE);

            if (! $bPlugin) {
                return $methods; // config.json missing — skip
            }

            // Override enabled/installed flags so validation passes
            $bPlugin->setEnabled(true);
            $bPlugin->setInstalled(true);
            $bPlugin->setCanUpdate(false);

            // Build a minimal Plugin model that PaymentMethodItem can process
            $plugin         = new \Beike\Models\Plugin();
            $plugin->type   = 'payment';
            $plugin->code   = self::PLUGIN_CODE;
            $plugin->plugin = $bPlugin;  // Required by PaymentMethodItem::toArray()

            $methods->push($plugin);

            return $methods;
        }, 20);
    }
}
