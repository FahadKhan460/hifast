<?php
/**
 * shop.php - Cash on Delivery Shop Routes
 *
 * @copyright  2024 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     HiFast Development Team
 * @created    2024-02-22
 */

use Illuminate\Support\Facades\Route;
use Plugin\CashOnDelivery\Controllers\CashOnDeliveryController;

Route::post('/cash-on-delivery/confirm', [CashOnDeliveryController::class, 'confirm'])->name('cash_on_delivery.confirm');
