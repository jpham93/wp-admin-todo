<?php
/**
 * Edit TODO List view
 *
 * @link       https://jamespham.io
 * @since      2.0.0
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/admin/partials
 */

require_once( ABSPATH . 'wp-content/plugins/wp-admin-todo/includes/class-wp-admin-todo-database.php' );
?>

<h1>Edit List</h1>

<select id="list-edit-dropdown" class="form-select">
    <?php
        $database   = new Wp_Admin_Todo_Database();
        $todo_lists = $database->get_lists();

        foreach ( $todo_lists as $list )
        {
            printf('<option value="%d">%s</option>', $list->id, $list->list_name );
        }
    ?>
</select>
