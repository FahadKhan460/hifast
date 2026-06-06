# Cash on Delivery Plugin - Installation Guide

## Quick Installation

The plugin has already been installed. This guide is for reference or reinstallation.

## Installation Steps

### 1. Database Registration

```sql
-- Add plugin to plugins table
INSERT INTO plugins (type, code, created_at, updated_at)
VALUES ('payment', 'cash_on_delivery', NOW(), NOW());

-- Enable the plugin
INSERT INTO settings (type, space, name, value, json, created_at, updated_at)
VALUES ('plugin', 'cash_on_delivery', 'status', '1', 0, NOW(), NOW());
```

### 2. Add to Free Plugin Codes

Edit `config/app.php` and add `'cash_on_delivery'` to the `free_plugin_codes` array:

```php
'free_plugin_codes' => [
    'bestseller',
    'cash_on_delivery',  // Add this line
    'flat_shipping',
    'latest_products',
    'openai',
    'paypal',
    'social',
    'stripe',
    'wintopay',
    'youdao'
],
```

**Why is this needed?**
The BeikeShop system performs automatic license checks on all plugins. Plugins not in the `free_plugin_codes` array will be automatically disabled by the license verification system. Adding the plugin to this array prevents it from being disabled.

### 3. Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 4. Verification

Check if the plugin is properly installed and enabled:

```sql
-- Check plugin exists
SELECT * FROM plugins WHERE code = 'cash_on_delivery';

-- Check plugin is enabled
SELECT * FROM settings WHERE space = 'cash_on_delivery' AND name = 'status';
```

The status value should be `1` for enabled.

### 5. Testing

1. Go to your shop frontend
2. Add a product to cart
3. Proceed to checkout
4. "Cash on Delivery" should appear as a payment option
5. Select it and complete the order

## Uninstallation

If you need to remove the plugin:

```sql
-- Disable the plugin
UPDATE settings SET value = '0' WHERE space = 'cash_on_delivery' AND name = 'status';

-- Or completely remove it
DELETE FROM settings WHERE space = 'cash_on_delivery';
DELETE FROM plugins WHERE code = 'cash_on_delivery';
```

Then remove `'cash_on_delivery'` from the `free_plugin_codes` array in `config/app.php`.

## Common Issues

### Issue: Plugin gets disabled after page refresh

**Cause**: Plugin not in `free_plugin_codes` array

**Solution**: Follow Step 2 above

### Issue: Plugin doesn't appear in payment methods

**Cause**: Cache not cleared or plugin not enabled

**Solution**:
1. Run cache clear commands (Step 3)
2. Verify status is `1` in database (Step 4)

## Support

For issues, check the main README.md file or the troubleshooting section.
