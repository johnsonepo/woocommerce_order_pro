<?php
/*
Plugin Name: WooCommerce Order Extras
Description: This plugin adds extra fields to WooCommerce order such as order status and delivery agents.
Version: 1.1
Author: Johnson Epo (envoos)
Author URI: https://www.linkedin.com/in/envoos/
Plugin URI: https://wordpress.org/plugins/woocommerce-order-extras/
Text Domain: woocommerce-order-extras
Tags: woocommerce, orders, extras, delivery, status
*/

include_once(plugin_dir_path(__FILE__) . 'includes/database_manager.php');
include_once(plugin_dir_path(__FILE__) . 'includes/woocommerce_extra_fields.php');
include_once(plugin_dir_path(__FILE__) . 'includes/payment_method._setting.php');

function ev_orders_extra_activate() {
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Sorry, but this plugin requires WooCommerce to be installed and active. Please activate WooCommerce and try again.');
    }

    $database_manager = new DatabaseManager();
    $database_manager->createDeliveryAgentsTable();
    $database_manager->createExtraOrderStatusTable(); 
    $database_manager->createOrderPaymentTMethodRequiredable(); 
}

register_activation_hook(__FILE__, 'ev_orders_extra_activate');

function ev_orders_extra_enqueue_styles_and_scripts() {
    wp_enqueue_style('ev-orders-extra-styles', plugin_dir_url(__FILE__) . 'zcss/style.css');
}
add_action('admin_enqueue_scripts', 'ev_orders_extra_enqueue_styles_and_scripts');
function ev_orders_extra_add_to_woocommerce_menu() {
    add_submenu_page(
        'woocommerce',
        __('Custom Order Status', 'ev-orders-extra'),
        __('Custom Order Status', 'ev-orders-extra'),
        'manage_options',
        'ev_orders_extra_order_status',
        'ev_orders_extra_display_order_status_tab'
    );

    add_submenu_page(
        'woocommerce',
        __('Delivery Agent', 'ev-orders-extra'),
        __('Delivery Agent', 'ev-orders-extra'),
        'manage_options',
        'ev_orders_extra_delivery_agent',
        'ev_orders_extra_display_delivery_agent_tab'
    );
}
add_action('admin_menu', 'ev_orders_extra_add_to_woocommerce_menu');

function ev_orders_extra_display_order_status_tab() {
    include(plugin_dir_path(__FILE__) . 'views/order_status.php');
}
function ev_orders_extra_display_delivery_agent_tab() {
    include(plugin_dir_path(__FILE__) . 'views/delivery_agent.php');
}
