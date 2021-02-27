<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jamespham.io
 * @since             1.0.0
 * @package           Wp_Admin_Todo
 *
 * @wordpress-plugin
 * Plugin Name:       WP Admin TODO
 * Plugin URI:        https://github.com/jpham93/wp-admin-todo
 * Description:       This is a simple TODO plugin for WordPress admins.
 * Version:           1.0.0
 * Author:            James Pham
 * Author URI:        https://jamespham.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-admin-todo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_ADMIN_TODO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-admin-todo-activator.php
 */
function activate_wp_admin_todo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-admin-todo-activator.php';
	Wp_Admin_Todo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-admin-todo-deactivator.php
 */
function deactivate_wp_admin_todo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-admin-todo-deactivator.php';
	Wp_Admin_Todo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_admin_todo' );
register_deactivation_hook( __FILE__, 'deactivate_wp_admin_todo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-admin-todo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_admin_todo() {

	$plugin = new Wp_Admin_Todo();
	$plugin->run();

}
run_wp_admin_todo();
