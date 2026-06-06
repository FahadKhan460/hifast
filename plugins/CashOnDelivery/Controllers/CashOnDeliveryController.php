<?php
/**
 * CashOnDeliveryController.php
 *
 * @copyright  2024 beikeshop.com - All Rights Reserved
 * @link       https://beikeshop.com
 * @author     HiFast Development Team
 * @created    2024-02-22
 */

namespace Plugin\CashOnDelivery\Controllers;

use Beike\Models\Order;
use Beike\Repositories\OrderRepo;
use Beike\Services\StateMachineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CashOnDeliveryController extends Controller
{
    /**
     * Confirm Cash on Delivery payment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function confirm(Request $request): JsonResponse
    {
        try {
            $orderNumber = $request->input('order_number');

            if (empty($orderNumber)) {
                return json_fail(trans('CashOnDelivery::common.invalid_order'));
            }

            $customer = current_customer();
            $order = OrderRepo::getOrderByNumber($orderNumber, $customer);

            if (empty($order)) {
                return json_fail(trans('CashOnDelivery::common.order_not_found'));
            }

            if ($order->payment_method_code !== 'cash_on_delivery') {
                return json_fail(trans('CashOnDelivery::common.invalid_payment_method'));
            }

            if ($order->status !== 'unpaid') {
                return json_fail(trans('CashOnDelivery::common.order_already_paid'));
            }

            // For COD, we mark the order as paid but note that payment is pending
            // The actual payment will be collected upon delivery
            $stateMachineService = StateMachineService::getInstance($order);

            // Change order status to paid
            $stateMachineService->changeStatus(StateMachineService::PAID, trans('CashOnDelivery::common.cod_payment_note'), true);

            // Log the COD payment
            hook_action('payment.cod.confirmed', ['order' => $order]);

            return json_success([
                'message' => trans('CashOnDelivery::common.order_confirmed'),
                'order_number' => $order->number,
            ]);

        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
