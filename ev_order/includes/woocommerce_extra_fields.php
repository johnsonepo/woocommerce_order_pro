<?php
// Include necessary scripts
include_once(plugin_dir_path(__FILE__) . 'order_status_form_submission.php');
include_once(plugin_dir_path(__FILE__) . 'delivery_agent_form_submission.php');

// Add custom fields to WooCommerce order
function ev_orders_extra_add_custom_order_fields($order) {
    $order_id = $order->get_id();

    // Get delivery agents from the database
    $delivery_agents = ev_orders_extra_get_delivery_agents();

    // Get order statuses from the database
    $order_statuses = ev_orders_extra_get_order_statuses();

    // Get selected values from the order meta
    $selected_delivery_agent = get_post_meta($order_id, '_delivery_agent', true);
    $selected_order_status = get_post_meta($order_id, '_order_status', true);
    $selected_payment_method = get_post_meta($order_id, '_payment_method', true);
    $selected_payment_status = get_post_meta($order_id, '_payment_status', true);

    // Output Delivery Agent dropdown
    echo "<br>";
    echo '<p class="form-field form-field-wide wc"><strong>' . __('Delivery Agent', 'ev-orders-extra') . ':</strong> ' . esc_html($selected_delivery_agent) . '</p>';
    echo '<select name="_delivery_agent" class="wc-select" style="width: 100%;">';
    echo '<option value="">' . __('Select a Delivery Agent', 'ev-orders-extra') . '</option>';
    foreach ($delivery_agents as $agent) {
        echo '<option value="' . esc_attr($agent->agent_name) . '" ' . selected($selected_delivery_agent, $agent->agent_name, false) . '>' . esc_html($agent->agent_name) . '</option>';
    }
    echo '</select>';

    // Output Payment Method dropdown
    echo "<br>";
    echo '<p class="form-field form-field-wide wc"><strong>' . __('Payment Method', 'ev-orders-extra') . ':</strong> ' . esc_html($selected_payment_method) . '</p>';
    echo '<select name="_payment_method" class="wc-select" style="width: 100%;">';
    echo '<option value="">' . __('Select a Payment Method', 'ev-orders-extra') . '</option>';
    // Get active payment methods
    $active_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
    foreach ($active_payment_methods as $payment_method) {
        echo '<option value="' . esc_attr($payment_method->id) . '" ' . selected($selected_payment_method, $payment_method->id, false) . '>' . esc_html($payment_method->get_title()) . '</option>';
    }
    echo '</select>';

    // Output Payment Status dropdown
    echo "<br>";
    echo '<p class="form-field form-field-wide wc"><strong>' . __('Payment Status', 'ev-orders-extra') . ':</strong> ' . esc_html($selected_payment_status) . '</p>';
    echo '<select name="_payment_status" class="wc-select" style="width: 100%;">';
    echo '<option value="">' . __('Select a Payment Status', 'ev-orders-extra') . '</option>';
    // Define payment status options as needed (e.g., Paid, Pending, Failed)
    $payment_status_options = array('Paid', 'Pending', 'Failed');
    foreach ($payment_status_options as $status_option) {
        echo '<option value="' . esc_attr($status_option) . '" ' . selected($selected_payment_status, $status_option, false) . '>' . esc_html($status_option) . '</option>';
    }
    echo '</select>';
}

add_action('woocommerce_admin_order_data_after_order_details', 'ev_orders_extra_add_custom_order_fields', 10, 1);

// Save custom fields when the order is saved
function ev_orders_extra_save_custom_order_fields($order_id) {
    $order = wc_get_order($order_id);

    // Get previous delivery agent
    $old_delivery_agent = get_post_meta($order_id, '_delivery_agent', true);

    if (isset($_POST['_delivery_agent'])) {
        $new_delivery_agent = sanitize_text_field($_POST['_delivery_agent']);

        if ($old_delivery_agent !== $new_delivery_agent) {
            // Add order note for the delivery agent change
            $note = sprintf(__('Delivery Agent changed from %s to %s.', 'ev-orders-extra'), $old_delivery_agent, $new_delivery_agent);

            // If no previous delivery agent, add a specific order note
            if (empty($old_delivery_agent)) {
                $note = sprintf(__('Delivery Agent set to %s.', 'ev-orders-extra'), $new_delivery_agent);
            }

            $order->add_order_note($note);

            // Update the delivery agent meta
            update_post_meta($order_id, '_delivery_agent', $new_delivery_agent);
        }
    }

    if (isset($_POST['_payment_method'])) {
        $new_payment_method = sanitize_text_field($_POST['_payment_method']);
        $old_payment_method = get_post_meta($order_id, '_payment_method', true);

        if ($new_payment_method !== $old_payment_method) {
            // Add order note for the payment method change
            $note = sprintf(__('Payment Method changed from %s to %s.', 'ev-orders-extra'), $old_payment_method, $new_payment_method);
            $order->add_order_note($note);

            // Update the payment method meta
            update_post_meta($order_id, '_payment_method', $new_payment_method);
        }
    }

    if (isset($_POST['_payment_status'])) {
        $new_payment_status = sanitize_text_field($_POST['_payment_status']);
        $old_payment_status = get_post_meta($order_id, '_payment_status', true);

        if ($new_payment_status !== $old_payment_status) {
            // Add order note for the payment status change
            $note = sprintf(__('Payment Status changed from %s to %s.', 'ev-orders-extra'), $old_payment_status, $new_payment_status);
            $order->add_order_note($note);

            // Update the payment status meta
            update_post_meta($order_id, '_payment_status', $new_payment_status);
        }
    }

    if (isset($_POST['_order_status'])) {
        $new_order_status = sanitize_text_field($_POST['_order_status']);
        $old_order_status = get_post_meta($order_id, '_order_status', true);

        if ($new_order_status !== $old_order_status) {
            // Add order note for the order status change
            $note = sprintf(__('Order Status changed from %s to %s.', 'ev-orders-extra'), $old_order_status, $new_order_status);
            $order->add_order_note($note);

            // Update the order status meta
            update_post_meta($order_id, '_order_status', $new_order_status);
        }
    }
}

add_action('woocommerce_process_shop_order_meta', 'ev_orders_extra_save_custom_order_fields', 10, 1);

// Function to save payment data in ev_orders_extra_order_payment table
function ev_orders_extra_save_payment_data($order_id, $payment_status, $payment_method, $payment_amount) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_payment';
    $wpdb->insert(
        $table_name,
        array(
            'order_id' => $order_id,
            'payment_status' => $payment_status,
            'payment_method' => $payment_method,
            'payment_amount' => $payment_amount,
            'payment_date' => current_time('mysql'),
        ),
        array('%d', '%s', '%s', '%f', '%s')
    );
}

// Function to save data in ev_orders_extra_order_ref table
function ev_orders_extra_save_order_ref_data($order_id, $agent_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_ref';
    $wpdb->insert(
        $table_name,
        array(
            'order_id' => $order_id,
            'agent_name' => $agent_name,
        ),
        array('%d', '%s')
    );
}

// Set default order status and payment status if not selected
function ev_orders_extra_set_default_order_status($order_id, $order) {
    // Set default order status to 'Pending' if not selected
    if (empty(get_post_meta($order_id, '_order_status', true))) {
        update_post_meta($order_id, '_order_status', 'pending');
    }

    // Set default payment status to 'Pending' if not selected
    if (empty(get_post_meta($order_id, '_payment_status', true))) {
        update_post_meta($order_id, '_payment_status', 'pending');
    }
}

add_action('woocommerce_new_order', 'ev_orders_extra_set_default_order_status', 10, 2);

// Get delivery agents from the database
function ev_orders_extra_get_delivery_agents() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_delivery_agents';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

// Get order statuses from the database
function ev_orders_extra_get_order_statuses() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_status';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

// Add custom order status to WooCommerce order status dropdown
function ev_orders_extra_add_custom_order_status($order_statuses) {
    $custom_order_statuses = ev_orders_extra_get_order_statuses();
    foreach ($custom_order_statuses as $status) {
        $order_statuses['wc-' . sanitize_title($status->status_name)] = $status->status_name;
    }
    return $order_statuses;
}

add_filter('wc_order_statuses', 'ev_orders_extra_add_custom_order_status');
?>
