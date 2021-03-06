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

<!-- Dropdown of lists to edit -->
<select id="list-edit-dropdown" class="form-select">
    <?php
        $database   = new Wp_Admin_Todo_Database();
        $todo_lists = $database->get_lists();

        // initial default value
        printf( '<option value="0">Select a List</option>' );

        // display empty if retrieval is empty
        if ( ! count( $todo_lists ) ) {

            printf( '<option disabled>%s</option>', 'No lists available' );

        } else {

            // display each row as an option
            foreach ( $todo_lists as $list )
            {
                printf( '<option value="%d">%s</option>', $list->id, $list->list_name );
            }

        }
    ?>
</select>

<script>
    const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';

    // show edit form if list is selected
    const listSelector = document.getElementById('list-edit-dropdown');

    listSelector.addEventListener('change', async function() {

        const listID = this.value;

        // payload
        const formData = new FormData();
        formData.append('action', 'read_list');
        formData.append('list-ID', listID);

        const res = await fetch(ajaxUrl, {
            method: 'POST',
            body:   formData
        });

        const { success, data } = await res.json();

        // if successful payload, return data
        if (success) {
            console.log(data);
        }

    });

</script>
