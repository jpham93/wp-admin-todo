<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jamespham.io
 * @since      1.0.0
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/admin
 * @author     James Pham <jamespham93@yahoo.com>
 */
class Wp_Admin_Todo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Admin_Todo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Admin_Todo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

//		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-admin-todo-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'bootstrap/bootstrap.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Admin_Todo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Admin_Todo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-admin-todo-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Generate the main "Settings" page for this plugin
     *
     * @since 1.0.0
     */
	public function create_admin_page()
    {
        add_menu_page('TODO Main', 'Admin TODO', 'manage_options', 'wp-admin-todo-main', [$this, 'main_page_init'], 'dashicons-edit');
    }

    /**
     * PHP View for TODO MAIN
     *
     * @since 1.0.0
     */
    public function main_page_init()
    {
        include_once 'partials/wp-admin-todo-admin-display.php';
    }

    public function register_custom_posts()
    {
        $args = array(
            // For full range of label controls, see TemplatesDownloadWidget.php for more information
            'labels'              => 'WP Admin TODO',
            'description'         => 'TODO post storage',
            'public'              => false,
            'hierarchical'        => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_in_rest'        => true,
        );

        register_post_type( 'wp-admin-todo', $args );
    }


    /**
     * CRUD - CUSTOM REST FUNCTIONS
     */

    /**
     * FUNCTION IS OVERLOADED (IF USER PROVIDES $POST_ID, THEN EDIT POST)
     */
    public function add_todo()
    {
        $content    = $_POST['todo-content'];
        $status     = $_POST['todo-status'];

        $postarr = array(
            'post_type'    => 'wp-admin-todo',
            'post_content' => $content,
            'post_status'  => $status
        );

        // overload w/ ID for Editing
        if ( isset( $_POST['todo-ID'] ) && ! empty( $_POST['todo-ID'] ) ) {
            $postarr['ID'] = $_POST['todo-ID'];
        }

        wp_insert_post( $postarr );

//        echo $content;
//        echo $status;
    }

    /**
     * DELETES POST (TODO)
     */
    public function remove_todo()
    {
        $post_ID    = $_POST['todo-ID'];

        wp_delete_post( $post_ID );
    }

}
