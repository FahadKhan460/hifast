# Cash on Delivery Payment Plugin

## Overview
The Cash on Delivery (COD) payment plugin allows customers to pay for their orders with cash when the delivery is made. This is a simple, offline payment method that doesn't require any payment gateway integration.

## Features
- Simple payment method without API integration
- Customizable payment instructions
- Support for multiple languages (English, Chinese)
- Automatic order status handling
- Mobile-friendly payment interface

## Installation

The plugin has been installed and enabled in your system. It's located in the `plugins/CashOnDelivery` directory.

### Database Setup
The plugin has been registered in the database:
- Plugin entry added to `plugins` table
- Plugin status set to enabled in `settings` table
- Plugin code added to `free_plugin_codes` in `config/app.php` (prevents automatic disabling)

**Important**: The plugin code `cash_on_delivery` has been added to the `free_plugin_codes` array in `config/app.php`. This is essential because the BeikeShop system automatically disables plugins that are not in this list when it performs license checks. Without this configuration, the plugin would be disabled automatically.

## Configuration

### Admin Panel Settings
You can configure the following options from the admin panel:

1. **Title** - Custom payment method title (optional, defaults to "Cash on Delivery")
2. **Description** - Payment method description shown to customers
3. **Instructions** - Custom instructions displayed on the payment page

### Accessing Settings
1. Log in to the admin panel
2. Navigate to Plugins → Payment Methods
3. Find "Cash on Delivery" in the list
4. Click Edit to configure the settings

## How It Works

### Customer Flow
1. Customer selects "Cash on Delivery" as the payment method during checkout
2. Order is created with status "unpaid"
3. Customer is redirected to the payment page
4. Customer confirms they will pay cash on delivery
5. Order status changes to "paid" (with a note that payment will be collected on delivery)
6. Customer sees the order confirmation page

### Admin Flow
1. Admin receives order with COD payment method
2. Admin processes and ships the order
3. Delivery person collects cash payment from customer
4. Admin can manually update order status if needed

## File Structure

```
plugins/CashOnDelivery/
├── Bootstrap.php                          # Plugin initialization
├── config.json                            # Plugin metadata
├── columns.php                            # Configuration fields
├── Controllers/
│   └── CashOnDeliveryController.php      # Payment confirmation handler
├── Lang/
│   ├── en/
│   │   ├── common.php                    # English translations
│   │   └── setting.php                   # English setting labels
│   └── zh_cn/
│       ├── common.php                    # Chinese translations
│       └── setting.php                   # Chinese setting labels
├── Routes/
│   └── shop.php                          # Shop routes
└── Views/
    └── checkout/
        └── payment.blade.php             # Payment page template
```

## Technical Details

### Payment Processing
Unlike PayPal or Stripe, COD doesn't require external API calls. The payment flow is:

1. **Order Creation**: Standard checkout process creates the order
2. **Payment Confirmation**: Customer confirms they will pay on delivery
3. **Status Update**: Order status changes to "paid" with a note
4. **Success**: Customer is redirected to success page

### API Endpoints
- `POST /cash-on-delivery/confirm` - Confirms COD payment

### Hooks
The plugin uses Laravel hooks to integrate with the BeikeShop system:

- `service.payment.mobile_pay.data` - Provides mobile payment data
- `payment.cod.confirmed` - Triggered when COD payment is confirmed

## Customization

### Changing Payment Instructions
Edit the instructions in the admin panel or modify the default instructions in:
```
plugins/CashOnDelivery/Lang/en/common.php
```

### Styling
You can customize the payment page appearance by editing:
```
plugins/CashOnDelivery/Views/checkout/payment.blade.php
```

### Adding Languages
To add a new language:
1. Create a new directory in `Lang/` (e.g., `Lang/es` for Spanish)
2. Copy `common.php` and `setting.php` from `Lang/en/`
3. Translate all the strings to the new language

## Testing

### Test the Plugin
1. Add products to cart
2. Proceed to checkout
3. Select "Cash on Delivery" as payment method
4. Complete the order
5. Verify order status is updated correctly

## Comparison with PayPal/Stripe

| Feature | PayPal/Stripe | Cash on Delivery |
|---------|--------------|------------------|
| API Integration | Required | Not required |
| External Credentials | API keys needed | None |
| Payment Processing | Online | Offline |
| Order Status | Updates after payment | Updates on confirmation |
| Complexity | High | Low |
| Setup Time | Medium | Quick |

## Troubleshooting

### Plugin Gets Disabled Automatically
**Cause**: The BeikeShop system performs automatic license checks on plugins. If a plugin is not in the `free_plugin_codes` list, it will be disabled.

**Solution**:
1. Open `config/app.php`
2. Find the `free_plugin_codes` array
3. Add `'cash_on_delivery'` to the array
4. Run `php artisan config:clear`
5. Re-enable the plugin in the database:
   ```sql
   UPDATE settings SET value='1' WHERE space='cash_on_delivery' AND name='status';
   ```

### Plugin Not Showing
1. Clear cache: `php artisan cache:clear && php artisan config:clear`
2. Check plugin is enabled in database: `settings` table
3. Verify plugin is listed in `plugins` table
4. Ensure `cash_on_delivery` is in `free_plugin_codes` array in `config/app.php`

### Payment Not Processing
1. Check routes are loaded
2. Verify CSRF token is present
3. Check JavaScript console for errors
4. Verify Bootstrap.php exists and has correct namespace

## Support
For issues or questions, refer to the BeikeShop documentation or contact support.

## Version History
- v1.0.0 (2024-02-22) - Initial release
