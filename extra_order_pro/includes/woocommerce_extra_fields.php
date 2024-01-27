<?php
include_once(plugin_dir_path(__FILE__) . 'order_status_form_submission.php');
include_once(plugin_dir_path(__FILE__) . 'delivery_agent_form_submission.php');

function ev_orders_extra_add_custom_order_fields($order) {

    $order_id = $order->get_id();
    $delivery_agents = ev_orders_extra_get_delivery_agents();
    $selected_delivery_agent = get_post_meta($order_id, '_delivery_agent', true);
    $selected_payment_method = get_post_meta($order_id, '_payment_method', true);
    $selected_payment_status = get_post_meta($order_id, '_payment_status', true);

    echo "<br>";
    echo '<p class="form-field form-field-wide wc"><strong>' . __('Delivery Agent', 'ev-orders-extra') . ':</strong> ' . esc_html($selected_delivery_agent) . '</p>';
    echo '<select name="_delivery_agent" class="wc-select" style="width: 100%;">';
    echo '<option value="">' . __('Select a Delivery Agent', 'ev-orders-extra') . '</option>';
    foreach ($delivery_agents as $agent) {
        echo '<option value="' . esc_attr($agent->agent_name) . '" ' . selected($selected_delivery_agent, $agent->agent_name, false) . '>' . esc_html($agent->agent_name) . '</option>';
    }
    echo '</select>';

    $current_payment_method_key = get_post_meta($order_id, '_payment_method_x', true); 
    $current_payment_method = WC()->payment_gateways->get_available_payment_gateways();
    $current_payment_method = $current_payment_method[$current_payment_method_key]->method_title;

    $payment_required = 0;

    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_payment';
    
    $existing_record = $wpdb->get_results("SELECT * FROM $table_name");
    
    if ($existing_record) {
        $payment_required = $existing_record[0]->status;
    }

    echo "<br>";
    echo '<p class="form-field form-field-wide wc"><strong>' . __('Payment Method', 'ev-orders-extra') . ':</strong></p>';
    echo '<select name="_payment_method_x" class="wc-select" style="width: 100%;" ' . ($payment_required == 1 ? 'required' : '') . '>';
    echo '<option value="">' . __('Select a Payment method', 'ev-orders-extra') . '</option>';
    if (!empty($current_payment_method)) {
        echo '<option value="' . esc_attr($current_payment_method_key) . '" selected="selected">' . esc_html($current_payment_method) . '</option>';
    }

    $active_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
    foreach ($active_payment_methods as $payment_method) {
        if ($payment_method->id === $selected_payment_method) {
            continue;
        }
        if ($current_payment_method == $payment_method->get_title()) {
            continue;
        }
        echo '<option value="' . esc_attr($payment_method->id) . '">' . esc_html($payment_method->get_title()) . '</option>';
    }

    echo '</select>';

    echo "<br>";
    echo '<p class="form-field form-field-wide wc"><strong>' . __('Payment Status', 'ev-orders-extra') . ':</strong> ' . esc_html($selected_payment_status) . '</p>';
    echo '<select name="_payment_status" class="wc-select" style="width: 100%;">';

    $payment_status_options = array('Pending', 'Paid', 'Failed');
    foreach ($payment_status_options as $status_option) {
        echo '<option value="' . esc_attr($status_option) . '" ' . selected($selected_payment_status, $status_option, false) . '>' . esc_html($status_option) . '</option>';
    }
    echo '</select>';
}

add_action('woocommerce_admin_order_data_after_order_details', 'ev_orders_extra_add_custom_order_fields', 10, 1);

function ev_orders_extra_save_custom_order_fields($order_id) {
    $order = wc_get_order($order_id);
    $old_delivery_agent = get_post_meta($order_id, '_delivery_agent', true);

    if (isset($_POST['_delivery_agent'])) {
        $new_delivery_agent = sanitize_text_field($_POST['_delivery_agent']);

        if ($old_delivery_agent !== $new_delivery_agent) {
            $note = sprintf(__('Delivery Agent changed from %s to %s.', 'ev-orders-extra'), $old_delivery_agent, $new_delivery_agent);

            if (empty($old_delivery_agent)) {
                $note = sprintf(__('Delivery Agent set to %s.', 'ev-orders-extra'), $new_delivery_agent);
            }
            $order->add_order_note($note);
            update_post_meta($order_id, '_delivery_agent', $new_delivery_agent);
        }
    }

    if (isset($_POST['_payment_method'])) {
        $new_payment_method = sanitize_text_field($_POST['_payment_method_x']);
        $old_payment_method = get_post_meta($order_id, '_payment_method_x', true);

        $new_payment_method_key = $new_payment_method; 
        $new_payment_method_x= WC()->payment_gateways->get_available_payment_gateways();
        $new_payment_method_x = $new_payment_method_x[$new_payment_method_key]->method_title;
        
        if ($new_payment_method !== $old_payment_method) {
            $note = sprintf(__('Payment Method changed from %s to %s.', 'ev-orders-extra'), $old_payment_method, $new_payment_method);
            
            if (empty($old_payment_method)) {
                $note = sprintf(__('Payment Method: %s.', 'ev-orders-extra'), $new_payment_method);
            }
            $order->add_order_note($note);
            update_post_meta($order_id, '_payment_method_x', $new_payment_method);
        }
    }

    if (isset($_POST['_payment_status'])) {
        $new_payment_status = sanitize_text_field($_POST['_payment_status']);
        $old_payment_status = get_post_meta($order_id, '_payment_status', true);

        if ($new_payment_status !== $old_payment_status) {
            $note = sprintf(__('Payment Status changed from %s to %s.', 'ev-orders-extra'), $old_payment_status, $new_payment_status);
            if (empty($old_payment_status)) {
                $note = sprintf(__('Payment status set to %s.', 'ev-orders-extra'), $new_payment_status);
            }
            $order->add_order_note($note);
            update_post_meta($order_id, '_payment_status', $new_payment_status);
        }
    }

    if (isset($_POST['_order_status'])) {
        $new_order_status = sanitize_text_field($_POST['_order_status']);
        $old_order_status = get_post_meta($order_id, '_order_status', true);

        if ($new_order_status !== $old_order_status) {
            $note = sprintf(__('Order Status changed from %s to %s.', 'ev-orders-extra'), $old_order_status, $new_order_status);
            
            if (empty($old_order_status)) {
                $note = sprintf(__('Order Status set %s.', 'ev-orders-extra'), $new_payment_status);
            }
            $order->add_order_note($note);
            update_post_meta($order_id, '_order_status', $new_order_status);
        }
    }
}

add_action('woocommerce_process_shop_order_meta', 'ev_orders_extra_save_custom_order_fields', 10, 1);

function ev_orders_extra_set_default_order_status($order_id, $order) {
    if (empty(get_post_meta($order_id, '_order_status', true))) {
        update_post_meta($order_id, '_order_status', 'pending');
    }

    if (empty(get_post_meta($order_id, '_payment_status', true))) {
        update_post_meta($order_id, '_payment_status', 'pending');
    }
}

add_action('woocommerce_new_order', 'ev_orders_extra_set_default_order_status', 10, 2);

function ev_orders_extra_get_delivery_agents() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_delivery_agents';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

function ev_orders_extra_get_order_statuses() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_status';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

function ev_orders_extra_add_custom_order_status($order_statuses) {
    $custom_order_statuses = ev_orders_extra_get_order_statuses();
    foreach ($custom_order_statuses as $status) {
        $order_statuses['wc-' . sanitize_title($status->status_name)] = $status->status_name;
    }
    return $order_statuses;
}

add_filter('wc_order_statuses', 'ev_orders_extra_add_custom_order_status');

function ev_orders_extra_add_payment_confirmation_dropdown($order) {
    $order_id = $order->get_id();
    $payment_confirmed = get_post_meta($order_id, '_payment_confirmed', true);

    echo '<div class="order_data_column">';
    $confirmation_options = array(
        'unconfirmed' => __('Not Confirmed', 'ev-orders-extra'),
        'confirmed'   => __('Confirmed', 'ev-orders-extra'),
    );

    echo '<p class="form-field form-field-wide wc">';
    echo '<strong>' . __('Payment Confirmation', 'ev-orders-extra') . ':</strong>';

    echo '<select name="_payment_confirmed" class="wc-select" style="width: 100%;" onchange="handleConfirmationChange(this.value)">';
    foreach ($confirmation_options as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' . selected($payment_confirmed, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '</p>';

    echo '<div id="customConfirmationPopup" class="custom-popup" style="display: none;">
            <div class="popup-header">
                <span class="popup-icon">&#9888;</span> Warning
            </div>
            <div class="popup-content">
                <p>Once confirmed, the order cannot be edited. Do you want to proceed?</p>
                <button onclick="confirmPayment()">OK</button>
                <button onclick="cancelConfirmation()">Cancel</button>
            </div>
          </div>';

    echo '<style>
        .custom-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            max-width: 400px;
            width: 100%;
        }

        .popup-header {
            background-color: #39393D;
            color: #D63638;
            font-weight: bold;
            text-align: center;
            position: relative;
        }

        .popup-icon {
            font-size: 20px;
            margin-right: 5px;
        }

        .popup-content {
            text-align: center;
        }
    </style>';

    echo '<script>
        function handleConfirmationChange(value) {
            if (value === "confirmed") {
                document.getElementById("customConfirmationPopup").style.display = "block";
            }
        }

        function confirmPayment() {
            document.getElementsByName("_payment_confirmed")[0].value = "confirmed";
            closeConfirmationPopup();
        }

        function cancelConfirmation() {
            document.getElementsByName("_payment_confirmed")[0].value = "unconfirmed";
            closeConfirmationPopup();
        }

        function closeConfirmationPopup() {
            document.getElementById("customConfirmationPopup").style.display = "none";
        }
    </script>';

    echo '</div>';
}

add_action('woocommerce_admin_order_data_after_order_details', 'ev_orders_extra_add_payment_confirmation_dropdown', 11, 1);

function ev_orders_extra_save_payment_confirmation_field($order_id) {
    if (isset($_POST['_payment_confirmed'])) {
        $payment_confirmed = sanitize_text_field($_POST['_payment_confirmed']);
        update_post_meta($order_id, '_payment_confirmed', $payment_confirmed);
    }
}

add_action('woocommerce_process_shop_order_meta', 'ev_orders_extra_save_payment_confirmation_field', 10, 1);
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        disableActions();
    });

    function disableActions() {
        let selectElement = document.querySelector('select[name="_payment_confirmed"]');
        let selectedValue = selectElement.options[selectElement.selectedIndex].value;

        let deleteLink = document.querySelector('#delete-action .submitdelete');

        if (selectedValue == 'confirmed') {
            let updateButton = document.querySelector('.save_order');

            if (updateButton) {
                updateButton.disabled = true;
            }

            if (deleteLink) {
                deleteLink.href = "javascript:void(0)";
                deleteLink.classList.add('disabled');
                deleteLink.setAttribute('title', "Order cannot be deleted \nonce payment is confirmed");
            }
        } else {
            if (deleteLink) {
                deleteLink.href = deleteLink.getAttribute('data-href');
                deleteLink.classList.remove('disabled');
                deleteLink.removeAttribute('title');
            }
        }
    }

</script>


