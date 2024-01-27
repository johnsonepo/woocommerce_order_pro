<?php
include_once(plugin_dir_path(__FILE__) . '../includes/order_status_form_submission.php');

global $wpdb;
$table_name_order_status = $wpdb->prefix . 'ev_orders_extra_order_status';

$order_statuses = $wpdb->get_results("SELECT * FROM $table_name_order_status");
?>

<h2><?php _e('Order Status', 'ev-orders-extra'); ?></h2>

<button class="button ev-add-new-status-button" id="ev-add-new-order-status-button"><?php _e('Add New Order Status', 'ev-orders-extra'); ?></button>

<div id="ev-add-new-order-status-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="ev-add-new-order-status-close">&times;</span>
        <form id="add-new-order-status-form" method="post">
            <input type="hidden" name="action" value="ev_orders_extra_add_order_status">
            <label for="order-status-name"><?php _e('Order Status Name', 'ev-orders-extra'); ?>:</label>
            <input type="text" id="order-status-name" name="order_status_name" required>
            <input type="submit" class="button" value="<?php _e('Add Order Status', 'ev-orders-extra'); ?>">
        </form>
    </div>
</div>

<table class="wp-list-table widefat fixed striped" style="width: 95%; margin: 20px;">
    <thead>
        <tr>
            <th class="manage-column"><?php _e('ID', 'ev-orders-extra'); ?></th>
            <th class="manage-column"><?php _e('Order Status Name', 'ev-orders-extra'); ?></th>
            <th class="manage-column"><?php _e('Actions', 'ev-orders-extra'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order_statuses as $order_status) : ?>
            <tr>
                <td><?php echo esc_html($order_status->id); ?></td>
                <td>
                    <?php if (isset($_POST['action']) && $_POST['action'] === 'ev_orders_extra_edit_order_status' && $_POST['order_status_id'] === $order_status->id) : ?>
                        <form method="post">
                            <input type="hidden" name="action" value="ev_orders_extra_edit_order_status">
                            <input type="hidden" name="order_status_id" value="<?php echo esc_attr($order_status->id); ?>">
                            <input type="hidden" name="order_status_name" value="<?php echo esc_attr($order_status->status_name); ?>">
                            <label for="edit-order-status-name"><?php _e('Edit Order Status Name', 'ev-orders-extra'); ?>:</label>
                            <input type="text" id="edit-order-status-name" name="order_status_name" value="<?php echo esc_attr($order_status->status_name); ?>" required>
                            <input name="btn_edit_order_status" type="submit" class="button" value="<?php _e('Save', 'ev-orders-extra'); ?>">
                        </form>
                    <?php else : ?>
                        <?php echo esc_html($order_status->status_name); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($_POST['action']) && $_POST['action'] === 'ev_orders_extra_edit_order_status' && $_POST['order_status_id'] === $order_status->id) : ?>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="action" value="cancel_edit_order_status">
                            <input type="submit" class="button" value="<?php _e('Cancel', 'ev-orders-extra'); ?>">
                        </form>
                    <?php else : ?>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="action" value="ev_orders_extra_edit_order_status">
                            <input type="hidden" name="order_status_id" value="<?php echo esc_attr($order_status->id); ?>">
                            <input type="hidden" id="edit-order-status-name" name="order_status_name" value="<?php echo esc_attr($order_status->status_name); ?>" required>
                            <input type="submit" class="button" value="<?php _e('Edit', 'ev-orders-extra'); ?>">
                        </form>

                        <form method="post" style="display: inline-block; margin-left: 5px;">
                            <input type="hidden" name="action" value="ev_orders_extra_delete_order_status">
                            <input type="hidden" name="order_status_id" value="<?php echo esc_attr($order_status->id); ?>">
                            <input type="submit" class="button" value="<?php _e('Delete', 'ev-orders-extra'); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete '. $order_status->status_name.' status?', 'ev-orders-extra'); ?>');">
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    const addNewModal = document.querySelector("#ev-add-new-order-status-modal");
    const addNewButton = document.querySelector("#ev-add-new-order-status-button");
    const closeButton = document.querySelector("#ev-add-new-order-status-close");

    addNewButton.addEventListener("click", () => addNewModal.style.display = "block");
    closeButton.addEventListener("click", () => addNewModal.style.display = "none");

    window.addEventListener("click", function(e) {
        if (e.target === addNewModal || e.target === closeButton) {
            addNewModal.style.display = "none";
        }
    });
</script>