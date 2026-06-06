<div class="cod-payment-container mt-4">
    <div class="checkout-black">
        <h5 class="checkout-title">{{ __('CashOnDelivery::common.payment_title') }}</h5>

        <div class="cod-instructions mb-4">
            @if(plugin_setting('cash_on_delivery.instructions'))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    {{ plugin_setting('cash_on_delivery.instructions') }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-cash"></i>
                    <strong>{{ __('CashOnDelivery::common.default_instructions_title') }}</strong>
                    <p class="mb-0 mt-2">{{ __('CashOnDelivery::common.default_instructions') }}</p>
                </div>
            @endif
        </div>

        <div class="order-summary mb-4">
            <h6>{{ __('CashOnDelivery::common.order_summary') }}</h6>
            <table class="table table-borderless">
                <tr>
                    <td>{{ __('CashOnDelivery::common.order_number') }}:</td>
                    <td class="text-end"><strong>{{ $order->number }}</strong></td>
                </tr>
                <tr>
                    <td>{{ __('CashOnDelivery::common.total_amount') }}:</td>
                    <td class="text-end"><strong>{{ currency_format($order->total, $order->currency_code) }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="cod-confirmation mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="codConfirm" required>
                <label class="form-check-label" for="codConfirm">
                    {{ __('CashOnDelivery::common.confirm_text') }}
                </label>
            </div>
        </div>

        <div class="cod-actions">
            <button type="button" class="btn btn-primary btn-lg" id="confirmCodPayment" disabled>
                <i class="bi bi-check-circle"></i> {{ __('CashOnDelivery::common.confirm_order') }}
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Enable/disable confirm button based on checkbox
        $('#codConfirm').on('change', function() {
            $('#confirmCodPayment').prop('disabled', !this.checked);
        });

        // Handle order confirmation
        $('#confirmCodPayment').on('click', function() {
            const button = $(this);
            const originalText = button.html();

            // Disable button and show loading state
            button.prop('disabled', true);
            button.html('<span class="spinner-border spinner-border-sm me-2"></span>{{ __('CashOnDelivery::common.processing') }}');

            // Submit the COD payment
            const token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/cash-on-delivery/confirm',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': token
                },
                data: JSON.stringify({
                    order_number: "{{ $order->number }}"
                }),
                contentType: 'application/json',
                success: function(response) {
                    if (response.status === 'success' || response.success) {
                        // Redirect to success page
                        const lang = "{{ locale() === system_setting('base.locale') ? 'null' : session()->get('locale') }}";
                        let path = "/" + "{{ session()->get('locale') }}" + "/checkout/success?order_number={{ $order->number }}";
                        if(lang === "null") {
                            path = "/checkout/success?order_number={{ $order->number }}";
                        }
                        window.location.href = path;
                    } else {
                        layer.msg(response.message || '{{ __('CashOnDelivery::common.error') }}');
                        button.prop('disabled', false);
                        button.html(originalText);
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || '{{ __('CashOnDelivery::common.error') }}';
                    layer.msg(errorMessage);
                    button.prop('disabled', false);
                    button.html(originalText);
                }
            });
        });
    });
</script>

<style>
    .cod-payment-container {
        max-width: 600px;
    }

    .cod-instructions .alert {
        border-left: 4px solid #0d6efd;
    }

    .cod-confirmation {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .order-summary table td {
        padding: 8px 0;
    }
</style>
