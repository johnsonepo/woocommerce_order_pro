<?php
function ev_orders_extra_add_custom_tab($settings_tabs) {
    $settings_tabs['ev_orders_extra_tab'] = __('Payment Extra', 'ev-orders-extra');
    return $settings_tabs;
}

add_filter('woocommerce_settings_tabs_array', 'ev_orders_extra_add_custom_tab', 50);

function ev_orders_extra_get_payment_required_setting() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_payment';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

function ev_orders_extra_custom_tab_content() {
    $payment_is_required = ev_orders_extra_get_payment_required_setting();
    if ($payment_is_required) {
        $payment_is_required = $payment_is_required[0]->status;
    }

    echo '<h2>' . esc_html__('Payment Extra', 'ev-orders-extra') . '</h2>';
    woocommerce_admin_fields(ev_orders_extra_payment_settings($payment_is_required));
}

add_action('woocommerce_settings_tabs_ev_orders_extra_tab', 'ev_orders_extra_custom_tab_content');

function ev_orders_extra_payment_settings($payment_is_required) {
    $settings = array();

    $settings[] = array(
        'name'     => __('Require Payment Method', 'ev-orders-extra'),
        'desc'     => __('Enable this to make the payment method required for each order.', 'ev-orders-extra'),
        'id'       => 'ev_orders_extra_require_payment',
        'type'     => 'checkbox',
        'css'      => 'min-width:300px;',
        'desc_tip' => true,
        'default'  => $payment_is_required,
    );

    return $settings;
}

function ev_orders_extra_save_payment_settings() {
    $payment_required = isset($_POST['ev_orders_extra_require_payment']) ? 1 : 0;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_payment';
    
    $existing_record = $wpdb->get_results("SELECT * FROM $table_name");
    
    if ($existing_record) {
        $wpdb->update(
            $table_name,
            array('status' => $payment_required),
            array('id' => $existing_record[0]->id)
        );
    } else {
        $wpdb->insert(
            $table_name,
            array('status' => $payment_required),
            array('%d')
        );
    }

    woocommerce_update_options(ev_orders_extra_payment_settings($payment_required));
}

add_action('woocommerce_update_options_ev_orders_extra_tab', 'ev_orders_extra_save_payment_settings');
