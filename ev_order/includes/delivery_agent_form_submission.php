<?php
$wp_root_path = realpath(dirname(__FILE__) . '/../../../../');
include_once($wp_root_path . '/wp-load.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $agent_id = isset($_POST['agent_id']) ? intval($_POST['agent_id']) : 0;

        switch ($action) {
            case 'ev_orders_extra_add_agent':
                $agent_name = sanitize_text_field($_POST['agent_name']);
                ev_orders_extra_add_agent($agent_name);
                break;
            case 'ev_orders_extra_edit_agent':
                if (isset($_POST['btn_edit'])) {
                    $agent_name = sanitize_text_field($_POST['agent_name']);
                    ev_orders_extra_edit_agent($agent_id, $agent_name);
                    break;
                }
                break;
            case 'ev_orders_extra_delete_agent':
                ev_orders_extra_delete_agent($agent_id);
                break;
            case 'cancel_edit':
                break;
            default:
                break;
        }
    }
}

function ev_orders_extra_add_agent($agent_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_delivery_agents';
    $wpdb->insert(
        $table_name,
        array('agent_name' => $agent_name),
        array('%s')
    );
}

function ev_orders_extra_edit_agent($agent_id, $agent_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_delivery_agents';
    $wpdb->update(
        $table_name,
        array('agent_name' => $agent_name),
        array('id' => $agent_id),
        array('%s'),
        array('%d')
    );
}

function ev_orders_extra_delete_agent($agent_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ev_orders_extra_delivery_agents';
    $wpdb->delete(
        $table_name,
        array('id' => $agent_id),
        array('%d')
    );
}

?>

