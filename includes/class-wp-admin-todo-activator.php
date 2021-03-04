<?php

/**
 * Fired during plugin activation
 *
 * @link       https://jamespham.io
 * @since      2.0.0
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/includes
 */

require_once( plugin_dir_path( __DIR__ ) . '/includes/class-wp-admin-todo-database.php' );

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/includes
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Admin_Todo_Activator {

	/**
	 * Create the necessary tables in MySQL upon activating the
	 *
	 * @since    2.0.0
	 */
	public static function activate()
    {
	    $db = new Wp_Admin_Todo_Database();
	    $db->init_tables();
	}

}
