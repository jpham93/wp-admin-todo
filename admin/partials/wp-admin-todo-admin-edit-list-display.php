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

<div id="list-edit" class="mt-3" hidden>

    <h2 id="list-name"></h2>
    <ul id="list-items">

    </ul>
</div>

<script>
    const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';

    // show edit form if list is selected
    const listSelector = document.getElementById('list-edit-dropdown');

    listSelector.addEventListener('change', async function() {

        const listID = Number(this.value);

        if (listID > 0) {
            // payload
            const formData = new FormData();
            formData.append('action', 'read_list');
            formData.append('list-ID', listID);

            const res = await fetch(ajaxUrl, {
                method: 'POST',
                body:   formData
            });

            const { success, data } = await res.json();

            // if successful then send
            if (success) {
                renderList(data);
            } else {
                // send error
            }
        } else {
            const listEdit = document.getElementById('list-edit');
            listEdit.setAttribute('hidden', true);
            clearList();
        }

    });


    /**
     * Injects the list data into editing
     * @param id            {number}
     * @param list_name     {string}
     * @param items         { { id: number, content: string, completed: boolean, list_id: number }[] }
     */
    const renderList = ({ id, list_name, items }) => {

        // show form
        const listEdit  = document.getElementById('list-edit');
        listEdit.removeAttribute('hidden');

        const listName  = document.getElementById('list-name');
        listName.innerText = list_name;

        const listItems = document.getElementById('list-items');

        if (items.length) {
            for (const item of items) {

            }
        } else {
            const noItemsHTML = '<li>List has no current item</li>';
            listItems.insertAdjacentHTML('afterbegin', noItemsHTML);
        }

    };

    /**
     * Clears list items
     */
    const clearList = () => {

        const listItems = document.querySelectorAll('#list-items li');
        listItems.forEach(listItem => {
            listItem.remove();
        });

    };

</script>
