<?php
include_once(plugin_dir_path(__FILE__) . '../includes/delivery_agent_form_submission.php');

global $wpdb;
$table_name = $wpdb->prefix . 'ev_orders_extra_delivery_agents';

$agents = $wpdb->get_results("SELECT * FROM $table_name");
?>

<h2><?php _e('Delivery Agent', 'ev-orders-extra'); ?></h2>

<button class="button ev-add-new-button" id="ev-add-new-delivery-agent-button"><?php _e('Add New', 'ev-orders-extra'); ?></button>

<div id="ev-add-new-delivery-agent-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="ev-add-new-delivery-agent-close">&times;</span>
        <form id="add-new-agent-form" method="post">
            <input type="hidden" name="action" value="ev_orders_extra_add_agent">
            <label for="agent-name"><?php _e('Agent Name', 'ev-orders-extra'); ?>:</label>
            <input type="text" id="agent-name" name="agent_name" required>
            <input type="submit" class="button" value="<?php _e('Add Agent', 'ev-orders-extra'); ?>">
        </form>
    </div>
</div>

<table class="wp-list-table widefat fixed striped" style="width: 95%; margin: 20px;">
    <thead>
        <tr>
            <th class="manage-column"><?php _e('ID', 'ev-orders-extra'); ?></th>
            <th class="manage-column"><?php _e('Agent Name', 'ev-orders-extra'); ?></th>
            <th class="manage-column"><?php _e('Actions', 'ev-orders-extra'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agents as $agent) : ?>
            <tr>
                <td><?php echo esc_html($agent->id); ?></td>
                <td>
                    <?php if (isset($_POST['action']) && $_POST['action'] === 'ev_orders_extra_edit_agent' && $_POST['agent_id'] === $agent->id) : ?>
                        <form method="post">
                            <input type="hidden" name="action" value="ev_orders_extra_edit_agent">
                            <input type="hidden" name="agent_id" value="<?php echo esc_attr($agent->id); ?>">
                            <input type="hidden" name="agent_name" value="<?php echo esc_attr($agent->agent_name); ?>">
                            <label for="edit-agent-name"><?php _e('Edit Agent Name', 'ev-orders-extra'); ?>:</label>
                            <input type="text" id="edit-agent-name" name="agent_name" value="<?php echo esc_attr($agent->agent_name); ?>" required>
                            <input name = "btn_edit" type="submit" class="button" value="<?php _e('Save', 'ev-orders-extra'); ?>">
                        </form>
                    <?php else : ?>
                        <?php echo esc_html($agent->agent_name); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($_POST['action']) && $_POST['action'] === 'ev_orders_extra_edit_agent' && $_POST['agent_id'] === $agent->id) : ?>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="action" value="cancel_edit">
                            <input type="submit" class="button" value="<?php _e('Cancel', 'ev-orders-extra'); ?>">
                        </form>
                    <?php else : ?>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="action" value="ev_orders_extra_edit_agent">
                            <input type="hidden" name="agent_id" value="<?php echo esc_attr($agent->id); ?>">
                            <input type="submit" class="button" value="<?php _e('Edit', 'ev-orders-extra'); ?>">
                        </form>

                        <form method="post" style="display: inline-block; margin-left: 5px;">
                            <input type="hidden" name="action" value="ev_orders_extra_delete_agent">
                            <input type="hidden" name="agent_id" value="<?php echo esc_attr($agent->id); ?>">
                            <input type="submit" class="button" value="<?php _e('Delete', 'ev-orders-extra'); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete ' . $agent->agent_name .' ? ', 'ev-orders-extra'); ?>');">
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    const addNewModal = document.querySelector("#ev-add-new-delivery-agent-modal");
    const addNewButton = document.querySelector("#ev-add-new-delivery-agent-button");
    const closeButton = document.querySelector("#ev-add-new-delivery-agent-close");

    addNewButton.addEventListener("click", () => addNewModal.style.display = "block");
    closeButton.addEventListener("click", () => addNewModal.style.display = "none");

    window.addEventListener("click", function(e) {
        if (e.target == addNewModal) {
            addNewModal.style.display = "none";
        }
    });
</script>