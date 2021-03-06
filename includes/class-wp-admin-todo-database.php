<?php
/**
* The core plugin class.
*
* This class is responsible for all database related operations including creating the plugin tables. The most important
* Please refer to https://codex.wordpress.org/Creating_Tables_with_Plugins for more information.
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
    private $lists_table;
    private $items_table;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb         = $wpdb;
        $this->lists_table  = $this->wpdb->prefix . 'admin_todo_lists';
        $this->items_table  = $this->wpdb->prefix . 'admin_todo_items';

        $this->wpdb->get_charset_collate();
    }

    /**
     * INITIALIZE DATABASE TABLES
     * creates plugin database tables if it doesn't exist yet
     * @see - https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function init_tables()
    {
        $lists_table_sql  = sprintf("
            CREATE TABLE IF NOT EXISTS %s (
                id INT NOT NULL AUTO_INCREMENT,
                list_name varchar(255) NOT NULL,
                PRIMARY KEY  (id)
            )
        ", $this->lists_table);

        $items_table_sql  = sprintf("
            CREATE TABLE IF NOT EXISTS %s (
                id INT NOT NULL AUTO_INCREMENT,
                content VARCHAR (300),
                completed BIT NOT NULL,
                list_id INT NOT NULL,
                PRIMARY KEY  (id),
                FOREIGN KEY  (list_id) REFERENCES wp_admin_todo_lists(id)
            ) 
        ", $this->items_table);

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $lists_table_sql );
        dbDelta( $items_table_sql );
    }

    /*********
     * LISTS *
     *********/

    /**
     * Insert a new list record into wp_admin_todo_lists
     * @param $list_name    string
     */
    public function create_list( $list_name )
    {
        try
        {
            $this->wpdb->insert(
                $this->lists_table,
                array(
                    'list_name' => $list_name,
                )
            );
        }
        catch (Exception $e)
        {
            error_log($e);
            echo $e;
        }
    }


    /*********
     * ITEMS *
     *********/

    /**
     * @param $list_ID          int
     * @param $item_content     string
     */
    public function create_todo( $list_ID, $item_content )
    {
        try
        {
            $this->wpdb->insert(
                $this->items_table,
                array(
                    'content' => $item_content,
                    'list_id' => $list_ID
                )
            );
        }
        catch (Exception $e)
        {
            error_log($e);
            echo $e;
        }
    }

}
