<?php
/**
* The core plugin class.
*
* This is used to define internationalization, admin-specific hooks, and
* public-facing site hooks.
*
* Also maintains the unique identifier of this plugin as well as the current
* version of the plugin.
*
* @since        2.0.0
* @package      Wp_Admin_Todo
* @subpackage   Wp_Admin_Todo/includes
* @author       James Pham <jamespham93@yahoo.com>
* @lastUpdated  3/4/2021
*/

class Wp_Admin_Todo_Database
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * INITIALIZE DATABASE TABLES
     * creates plugin database tables if it doesn't exist yet
     * @see - https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function init_tables()
    {
        $this->wpdb->get_charset_collate();

        $lists_table_name = $this->wpdb->prefix . 'admin_todo_lists';

        $lists_table_sql  = sprintf('
            CREATE TABLE IF NOT EXISTS %s (
                id INT NOT NULL AUTO_INCREMENT,
                list_name varchar(255) NOT NULL,
                PRIMARY KEY  (id)
            )
        ', $lists_table_name);

        $items_table_name = $this->wpdb->prefix . 'admin_todo_items';

        $items_table_sql  = sprintf('
            CREATE TABLE IF NOT EXISTS %s (
                id INT NOT NULL AUTO_INCREMENT,
                content VARCHAR (300),
                completed BIT NOT NULL,
                list_id INT NOT NULL,
                PRIMARY KEY  (id),
                FOREIGN KEY  (list_id) REFERENCES wp_admin_todo_lists(id)
            ) 
        ', $items_table_name);

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $lists_table_sql );
        dbDelta( $items_table_sql );
    }

}
