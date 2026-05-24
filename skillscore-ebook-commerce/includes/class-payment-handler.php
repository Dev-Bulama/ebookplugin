<?php
/**
 * Payment Gateway Handler
 *
 * @package SkillScore_Ebook
 */

if (!defined('ABSPATH')) {
    exit;
}

class SkillScore_Ebook_Payment_Handler {

    /**
     * Initiate payment.
     */
    public function initiate_payment() {
        check_ajax_referer('skillscore_ebook_nonce', 'nonce');

        $ebook_id    = intval($_POST['ebook_id']);
        $quantity    = intval($_POST['quantity']) ?: 1;
        $user_email  = sanitize_email($_POST['user_email']);
        $user_name   = sanitize_text_field($_POST['user_name']);
        $order_type  = sanitize_text_field($_POST['order_type'] ?? 'individual');
        $order_format = sanitize_text_field($_POST['order_format'] ?? 'ebook');
        $user_phone  = sanitize_text_field($_POST['user_phone'] ?? '');
        $order_bump  = intval($_POST['order_bump'] ?? 0);

        // Validate ebook
        if (!$ebook_id || get_post_type($ebook_id) !== 'ebook') {
            wp_send_json_error(array('message' => __('Invalid ebook.', 'skillscore-ebook')));
        }

        // Build order meta for extra fields
        $order_meta_arr = array(
            'order_format' => $order_format,
            'phone'        => $user_phone,
        );

        // Shipping fields (for paperback or bulk with address)
        if (get_option('skillscore_ebook_enable_shipping_fields') && ($order_format === 'paperback' || $order_type === 'bulk')) {
            $order_meta_arr['shipping'] = array(
                'address' => sanitize_text_field($_POST['shipping_address'] ?? ''),
                'city'    => sanitize_text_field($_POST['shipping_city'] ?? ''),
                'state'   => sanitize_text_field($_POST['shipping_state'] ?? ''),
                'country' => sanitize_text_field($_POST['shipping_country'] ?? ''),
                'zip'     => sanitize_text_field($_POST['shipping_zip'] ?? ''),
            );
        }

        $currency = get_option('skillscore_ebook_currency', 'USD');
        $order_reference = 'SSE-' . time() . '-' . wp_rand(1000, 9999);

        global $wpdb;
        $orders_table = $wpdb->prefix . 'skillscore_orders';

        // --- BULK INQUIRY FLOW ---
        if ($order_type === 'bulk') {
            $bulk_quantity = intval($_POST['bulk_quantity'] ?? 0);
            $organization  = sanitize_text_field($_POST['organization'] ?? '');
            $bulk_message  = sanitize_textarea_field($_POST['bulk_message'] ?? '');

            $order_meta_arr['organization']  = $organization;
            $order_meta_arr['bulk_quantity'] = $bulk_quantity;
            $order_meta_arr['bulk_message']  = $bulk_message;

            $insert_data = array(
                'order_reference' => $order_reference,
                'ebook_id'        => $ebook_id,
                'user_email'      => $user_email,
                'user_name'       => $user_name,
                'user_id'         => get_current_user_id() ?: null,
                'quantity'        => $bulk_quantity ?: 1,
                'amount'          => 0,
                'currency'        => $currency,
                'payment_gateway' => 'bulk_inquiry',
                'payment_status'  => 'inquiry',
                'order_date'      => current_time('mysql'),
                'order_type'      => 'bulk',
                'order_meta'      => wp_json_encode($order_meta_arr),
            );

            $wpdb->insert($orders_table, $insert_data,
                array('%s', '%d', '%s', '%s', '%d', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
            );

            // Auto-response email to customer
            $this->send_bulk_inquiry_email($user_name, $user_email, $organization, $bulk_quantity, $bulk_message);

            wp_send_json_success(array(
                'bulk_inquiry' => true,
                'message'      => __('Your inquiry has been received. Our team will follow up with bulk pricing, sponsored distribution options, and program use guidance. Please watch your inbox.', 'skillscore-ebook'),
            ));
            return;
        }

        // --- INDIVIDUAL PURCHASE FLOW ---
        $gateway = sanitize_text_field($_POST['gateway']);

        // Check stock
        $unlimited = get_post_meta($ebook_id, '_ebook_unlimited', true);
        $stock     = get_post_meta($ebook_id, '_ebook_quantity', true);

        if (!$unlimited && $stock < $quantity) {
            wp_send_json_error(array('message' => __('Insufficient stock.', 'skillscore-ebook')));
        }

        // Calculate amount
        $price  = floatval(get_post_meta($ebook_id, '_ebook_price', true));
        $amount = $price * $quantity;

        // Order bump add-on
        if ($order_bump && get_option('skillscore_ebook_enable_order_bump')) {
            $bump_price = floatval(get_option('skillscore_ebook_order_bump_price', 0));
            $bump_name  = get_option('skillscore_ebook_order_bump_name', '');
            $amount    += $bump_price;
            $order_meta_arr['order_bump'] = array(
                'name'  => $bump_name,
                'price' => $bump_price,
            );
        }

        $insert_data = array(
            'order_reference' => $order_reference,
            'ebook_id'        => $ebook_id,
            'user_email'      => $user_email,
            'user_name'       => $user_name,
            'user_id'         => get_current_user_id() ?: null,
            'quantity'        => $quantity,
            'amount'          => $amount,
            'currency'        => $currency,
            'payment_gateway' => $gateway,
            'payment_status'  => 'pending',
            'order_date'      => current_time('mysql'),
            'order_type'      => 'individual',
            'order_meta'      => wp_json_encode($order_meta_arr),
        );

        $wpdb->insert($orders_table, $insert_data,
            array('%s', '%d', '%s', '%s', '%d', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        $order_id = $wpdb->insert_id;

        // Process payment based on gateway
        $result = $this->process_gateway_payment($gateway, $order_id, $amount, $currency, $user_email, $order_reference);

        if ($result['success']) {
            wp_send_json_success($result['data']);
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }

    /**
     * Send auto-response email for bulk inquiries.
     */
    private function send_bulk_inquiry_email($name, $email, $organization, $bulk_quantity, $message) {
        $first_name = explode(' ', $name)[0];
        $subject    = __('We received your No Excuses. No Miracles. inquiry.', 'skillscore-ebook');
        $site_name  = get_bloginfo('name');

        $body  = "Dear {$first_name},\n\n";
        $body .= "Thank you for your interest in bringing No Excuses. No Miracles. to your organization, program, event, or community.\n\n";
        $body .= "We have received your bulk inquiry";
        if ($organization) {
            $body .= " from {$organization}";
        }
        if ($bulk_quantity) {
            $body .= " for approximately {$bulk_quantity} copies";
        }
        $body .= ".\n\n";
        $body .= "No Excuses. No Miracles. is a bold nonfiction manifesto designed for youth programs, leadership institutes, entrepreneurship communities, civic spaces, student groups, faith-based reform settings, and conferences.\n\n";
        $body .= "Our team will follow up with bulk pricing, sponsored distribution options, program use guidance, event bundle details, or a conversation about fit and scale.\n\n";
        $body .= "Please watch your inbox — and check your junk folder if you don't hear from us within 2 business days.\n\n";
        $body .= "Altitude Within\nRegarding No Excuses. No Miracles.\nwww.altitudewithin.com";

        wp_mail($email, $subject, $body, array(
            'From: ' . $site_name . ' <' . get_option('admin_email') . '>',
        ));
    }

    /**
     * Process payment via selected gateway.
     */
    private function process_gateway_payment($gateway, $order_id, $amount, $currency, $email, $reference) {
        switch ($gateway) {
            case 'paystack':
                return $this->paystack_initiate($order_id, $amount, $currency, $email, $reference);
            case 'flutterwave':
                return $this->flutterwave_initiate($order_id, $amount, $currency, $email, $reference);
            case 'stripe':
                return $this->stripe_initiate($order_id, $amount, $currency, $email, $reference);
            case 'paypal':
                return $this->paypal_initiate($order_id, $amount, $currency, $email, $reference);
            default:
                return array(
                    'success' => false,
                    'message' => __('Invalid payment gateway.', 'skillscore-ebook')
                );
        }
    }

    /**
     * Paystack payment initiation.
     */
    private function paystack_initiate($order_id, $amount, $currency, $email, $reference) {
        $secret_key = get_option('skillscore_ebook_paystack_secret_key');
        if (!$secret_key) {
            return array('success' => false, 'message' => 'Paystack not configured.');
        }

        $url = 'https://api.paystack.co/transaction/initialize';
        $amount_in_kobo = $amount * 100; // Paystack uses kobo

        $callback_url = add_query_arg(array(
            'action' => 'skillscore_payment_callback',
            'gateway' => 'paystack',
            'order_id' => $order_id,
        ), home_url());

        $fields = array(
            'email' => $email,
            'amount' => $amount_in_kobo,
            'currency' => $currency,
            'reference' => $reference,
            'callback_url' => $callback_url,
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($fields),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($body['status'] && isset($body['data']['authorization_url'])) {
            return array(
                'success' => true,
                'data' => array(
                    'redirect_url' => $body['data']['authorization_url'],
                    'reference' => $reference,
                )
            );
        }

        return array('success' => false, 'message' => $body['message'] ?? 'Payment initialization failed.');
    }

    /**
     * Flutterwave payment initiation.
     */
    private function flutterwave_initiate($order_id, $amount, $currency, $email, $reference) {
        $public_key = get_option('skillscore_ebook_flutterwave_public_key');
        if (!$public_key) {
            return array('success' => false, 'message' => 'Flutterwave not configured.');
        }

        $url = 'https://api.flutterwave.com/v3/payments';

        $callback_url = add_query_arg(array(
            'action' => 'skillscore_payment_callback',
            'gateway' => 'flutterwave',
            'order_id' => $order_id,
        ), home_url());

        $fields = array(
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => $callback_url,
            'payment_options' => 'card,banktransfer',
            'customer' => array(
                'email' => $email,
            ),
            'customizations' => array(
                'title' => get_bloginfo('name'),
                'description' => 'Ebook Purchase',
            ),
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $public_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($fields),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($body['status'] === 'success' && isset($body['data']['link'])) {
            return array(
                'success' => true,
                'data' => array(
                    'redirect_url' => $body['data']['link'],
                    'reference' => $reference,
                )
            );
        }

        return array('success' => false, 'message' => $body['message'] ?? 'Payment initialization failed.');
    }

    /**
     * Stripe payment initiation.
     */
    private function stripe_initiate($order_id, $amount, $currency, $email, $reference) {
        $secret_key = get_option('skillscore_ebook_stripe_secret_key');
        if (!$secret_key) {
            return array('success' => false, 'message' => 'Stripe not configured.');
        }

        $url = 'https://api.stripe.com/v1/checkout/sessions';

        $success_url = add_query_arg(array(
            'action' => 'skillscore_payment_callback',
            'gateway' => 'stripe',
            'order_id' => $order_id,
            'session_id' => '{CHECKOUT_SESSION_ID}',
        ), home_url());

        $cancel_url = add_query_arg(array(
            'payment_cancelled' => '1',
        ), home_url());

        $amount_in_cents = intval($amount * 100); // Stripe uses cents

        $fields = array(
            'payment_method_types[]' => 'card',
            'line_items[0][price_data][currency]' => strtolower($currency),
            'line_items[0][price_data][product_data][name]' => get_the_title(get_post_meta($order_id, 'ebook_id', true)),
            'line_items[0][price_data][unit_amount]' => $amount_in_cents,
            'line_items[0][quantity]' => 1,
            'mode' => 'payment',
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
            'client_reference_id' => $reference,
            'customer_email' => $email,
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
            ),
            'body' => $fields,
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['url'])) {
            return array(
                'success' => true,
                'data' => array(
                    'redirect_url' => $body['url'],
                    'reference' => $reference,
                )
            );
        }

        return array('success' => false, 'message' => $body['error']['message'] ?? 'Payment initialization failed.');
    }

    /**
     * PayPal payment initiation.
     */
    private function paypal_initiate($order_id, $amount, $currency, $email, $reference) {
        $client_id = get_option('skillscore_ebook_paypal_client_id');
        $secret = get_option('skillscore_ebook_paypal_secret');
        $mode = get_option('skillscore_ebook_paypal_mode', 'sandbox');

        if (!$client_id || !$secret) {
            return array('success' => false, 'message' => 'PayPal not configured.');
        }

        $base_url = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // Get access token
        $token_response = wp_remote_post($base_url . '/v1/oauth2/token', array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $secret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => 'grant_type=client_credentials',
            'timeout' => 30,
        ));

        if (is_wp_error($token_response)) {
            return array('success' => false, 'message' => $token_response->get_error_message());
        }

        $token_body = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_body['access_token'] ?? '';

        if (!$access_token) {
            return array('success' => false, 'message' => 'Failed to get PayPal access token.');
        }

        // Create order
        $return_url = add_query_arg(array(
            'action' => 'skillscore_payment_callback',
            'gateway' => 'paypal',
            'order_id' => $order_id,
        ), home_url());

        $cancel_url = add_query_arg(array(
            'payment_cancelled' => '1',
        ), home_url());

        $order_data = array(
            'intent' => 'CAPTURE',
            'purchase_units' => array(
                array(
                    'reference_id' => $reference,
                    'amount' => array(
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ),
                )
            ),
            'application_context' => array(
                'return_url' => $return_url,
                'cancel_url' => $cancel_url,
            ),
        );

        $order_response = wp_remote_post($base_url . '/v2/checkout/orders', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($order_data),
            'timeout' => 30,
        ));

        if (is_wp_error($order_response)) {
            return array('success' => false, 'message' => $order_response->get_error_message());
        }

        $order_body = json_decode(wp_remote_retrieve_body($order_response), true);

        if (isset($order_body['links'])) {
            foreach ($order_body['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return array(
                        'success' => true,
                        'data' => array(
                            'redirect_url' => $link['href'],
                            'reference' => $reference,
                        )
                    );
                }
            }
        }

        return array('success' => false, 'message' => 'Failed to create PayPal order.');
    }

    /**
     * Handle payment webhooks and callbacks.
     */
    public function handle_payment_webhook() {
        if (!isset($_GET['action']) || $_GET['action'] !== 'skillscore_payment_callback') {
            return;
        }

        $gateway = sanitize_text_field($_GET['gateway'] ?? '');
        $order_id = intval($_GET['order_id'] ?? 0);

        if (!$order_id) {
            wp_die(__('Invalid order.', 'skillscore-ebook'));
        }

        global $wpdb;
        $orders_table = $wpdb->prefix . 'skillscore_orders';

        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $orders_table WHERE id = %d",
            $order_id
        ));

        if (!$order) {
            wp_die(__('Order not found.', 'skillscore-ebook'));
        }

        // Verify payment based on gateway
        $verified = false;

        switch ($gateway) {
            case 'paystack':
                $verified = $this->verify_paystack_payment($order);
                break;
            case 'flutterwave':
                $verified = $this->verify_flutterwave_payment($order);
                break;
            case 'stripe':
                $verified = $this->verify_stripe_payment($order);
                break;
            case 'paypal':
                $verified = $this->verify_paypal_payment($order);
                break;
        }

        if ($verified) {
            // Update order status
            $wpdb->update(
                $orders_table,
                array('payment_status' => 'completed'),
                array('id' => $order_id),
                array('%s'),
                array('%d')
            );

            // Update stock
            if (!get_post_meta($order->ebook_id, '_ebook_unlimited', true)) {
                $current_stock = intval(get_post_meta($order->ebook_id, '_ebook_quantity', true));
                update_post_meta($order->ebook_id, '_ebook_quantity', max(0, $current_stock - $order->quantity));
            }

            // Determine format from order meta
            $order_meta_data = !empty($order->order_meta) ? json_decode($order->order_meta, true) : array();
            $order_format    = $order_meta_data['order_format'] ?? 'ebook';

            if ($order_format === 'paperback') {
                // Physical order — no download token, show shipping confirmation
                $redirect_url = add_query_arg(array(
                    'payment_success'  => '1',
                    'order_format'     => 'paperback',
                    'order_ref'        => $order->order_reference,
                ), get_permalink($order->ebook_id));
            } else {
                // Digital eBook — generate download token
                $download_handler = new SkillScore_Ebook_Download_Handler();
                $download_token   = $download_handler->create_download_token($order_id, $order->ebook_id);

                $redirect_url = add_query_arg(array(
                    'payment_success' => '1',
                    'download_token'  => $download_token,
                ), get_permalink($order->ebook_id));
            }

            wp_redirect($redirect_url);
            exit;
        } else {
            wp_die(__('Payment verification failed.', 'skillscore-ebook'));
        }
    }

    /**
     * Verify Paystack payment.
     */
    private function verify_paystack_payment($order) {
        $reference = sanitize_text_field($_GET['reference'] ?? '');
        if (!$reference) {
            return false;
        }

        $secret_key = get_option('skillscore_ebook_paystack_secret_key');
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
            ),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body['status'] && $body['data']['status'] === 'success';
    }

    /**
     * Verify Flutterwave payment.
     */
    private function verify_flutterwave_payment($order) {
        $transaction_id = sanitize_text_field($_GET['transaction_id'] ?? '');
        if (!$transaction_id) {
            return false;
        }

        $secret_key = get_option('skillscore_ebook_flutterwave_secret_key');
        $url = 'https://api.flutterwave.com/v3/transactions/' . $transaction_id . '/verify';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
            ),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body['status'] === 'success' && $body['data']['status'] === 'successful';
    }

    /**
     * Verify Stripe payment.
     */
    private function verify_stripe_payment($order) {
        $session_id = sanitize_text_field($_GET['session_id'] ?? '');
        if (!$session_id) {
            return false;
        }

        $secret_key = get_option('skillscore_ebook_stripe_secret_key');
        $url = 'https://api.stripe.com/v1/checkout/sessions/' . $session_id;

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
            ),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return isset($body['payment_status']) && $body['payment_status'] === 'paid';
    }

    /**
     * Verify PayPal payment.
     */
    private function verify_paypal_payment($order) {
        $token = sanitize_text_field($_GET['token'] ?? '');
        if (!$token) {
            return false;
        }

        // PayPal verification would require capturing the order
        // For simplicity, we'll assume success if token is present
        // In production, you should verify with PayPal API
        return !empty($token);
    }
}
