<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that defines the plugin's REST API via WordPress's admin ajax.
 *
 * @link       https://jamespham.io
 * @since      2.0.0
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/includes
 */
/**
 * Class Wp_Admin_Todo_Admin_API
 *
 * REST API (admin facing only) that serves the plugin.
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/admin
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Admin_Todo_Admin_API {

    private $database;

    function __construct()
    {
        $this->load_dependencies();
        $this->database = new Wp_Admin_Todo_Database();
    }

    /**
     * LOAD ANY IMPORTS
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-admin-todo-database.php';
    }

    /****************
     * ITEM ACTIONS *
     ****************/

    /**
     * ADD A TODO ITEM
     */
    public function create_item()
    {
        $content    = $_POST['content'];
        $list_ID    = (int) $_POST['list-ID'];

        $res = $this->database->create_item( $list_ID, $content );

        if ( $res ) {
            wp_send_json_success( $res );
        } else {
            wp_send_json_error();
        }
    }

    /**
     * EDIT A TODO ITEM
     * Overloaded:
     *  1. Change status of the item (if status is defined)
     *  2. Change the content of the item (if content is defined)
     */
    public function edit_item()
    {
//        $content    = $_POST['todo-content'];
//        $status     = $_POST['todo-status'];
        $ID         = $_POST['todo-ID'];

        // 1. status update
        if ( isset( $_POST['todo-status'] ) ) {

            $postarr = array(
                'post_type'    => 'wp-admin-todo',
                'ID'           => $ID,
                'post_status'  => $_POST['todo-status']
            );

        }
        // 2. content update
        elseif ( isset( $_POST['todo-content'] ) ) {

            $postarr = array(
                'post_type'    => 'wp-admin-todo',
                'ID'           => $ID,
                'post_content' => $_POST['todo-content']
            );

        }

        wp_update_post( $postarr );
    }

    /**
     * DELETES TODO ITEM
     */
    public function delete_item()
    {
        $post_ID    = $_POST['todo-ID'];

        wp_delete_post( $post_ID );
    }

    /****************
     * LIST ACTIONS *
     ****************/

    /**
     * CREATES TODO LISTS
     */
    public function create_list()
    {
        $list_name = $_POST['list-name'];

        $res = $this->database->create_list( $list_name );

        $res ? wp_send_json_success( $res ) : wp_send_json_error();
    }

    /**
     * READS A SINGLE LIST W/ LIST ID
     */
    public function read_list()
    {
        $list_ID = (int) $_POST['list-ID'];

        $res = $this->database->read_list( $list_ID );

        $res ? wp_send_json_success( $res ) : wp_send_json_error();
    }
}
