<?php
$wp_root_path = realpath(dirname(__FILE__) . '/../../../../');
include_once($wp_root_path . '/wp-load.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $order_status_id = isset($_POST['order_status_id']) ? intval($_POST['order_status_id']) : 0;

        switch ($action) {
            case 'ev_orders_extra_add_order_status':
                $order_status_name = sanitize_text_field($_POST['order_status_name']);
                ev_orders_extra_add_order_status($order_status_name);
                break;
            case 'ev_orders_extra_edit_order_status':
                $order_status_name = sanitize_text_field($_POST['order_status_name']);
                ev_orders_extra_edit_order_status($order_status_id, $order_status_name);
                break;
            case 'ev_orders_extra_delete_order_status':
                ev_orders_extra_delete_order_status($order_status_id);
                break;
            default:
                break;
        }
    }
}

function ev_orders_extra_add_order_status($order_status_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_status';
    $wpdb->insert(
        $table_name,
        array('status_name' => $order_status_name),
        array('%s')
    );
}

function ev_orders_extra_edit_order_status($order_status_id, $order_status_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_status';
    $wpdb->update(
        $table_name,
        array('status_name' => $order_status_name),
        array('id' => $order_status_id),
        array('%s'),
        array('%d')
    );
}

function ev_orders_extra_delete_order_status($order_status_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_order_status';
    $wpdb->delete(
        $table_name,
        array('id' => $order_status_id),
        array('%d')
    );
}

?>
