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

    public function createOrderRefTable() {
        $table_name = $this->table_prefix . 'ev_orders_extra_order_ref';
    
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            agent_name varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function createOrderPaymentTable() {
        $table_name = $this->table_prefix . 'ev_orders_extra_order_payment';
    
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            payment_amount decimal(10,2) NOT NULL,
            payment_date datetime NOT NULL,
            payment_status varchar(50) NOT NULL DEFAULT 'Pending',
            PRIMARY KEY (id)
        ) ENGINE=InnoDB;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    
    
}
