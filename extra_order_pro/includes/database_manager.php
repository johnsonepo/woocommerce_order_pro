<?php
class DatabaseManager {
    private $table_prefix;
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $wpdb->prefix;
    }

    public function createDeliveryAgentsTable() {
        $table_name = $this->table_prefix . 'ev_orders_extra_delivery_agents';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            agent_name varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function createExtraOrderStatusTable() {
        $table_name = $this->table_prefix . 'ev_orders_extra_order_status';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            status_name varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }  
    
    public function createOrderPaymentTMethodRequiredable() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ev_orders_extra_order_payment';
        $existing_record = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    
        if ($existing_record == 0) {
            $sql = "INSERT INTO $table_name (status) VALUES (0)";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    
}
