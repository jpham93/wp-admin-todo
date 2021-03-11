<?php
/**
 * Provide a admin area view for the plugin
 *
 * MAIN TODO APP
 *
 * @link       https://jamespham.io
 * @since      2.0.0
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/admin/partials
 */

require_once( ABSPATH . 'wp-content/plugins/wp-admin-todo/includes/class-wp-admin-todo-database.php' );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1 class="text-primary">WordPress Admin TODO</h1>

<!--Create Lists-->

<!--CREATE NEW LIST TOGGLE-->
<button id="todo-new-list" class="btn btn-outline-primary btn-small">
    Create New list
</button>

<div id="todo-new-list-container" class="my-3">
    <h2>Create a New List</h2>
    <div class="container-fluid ml-0">
        <div class="col-md-2">
            <label for="todo-new-list" class="form-label">New List Name</label>
            <input type="text" id="todo-new-list-input" class="form-control">

            <button id="todo-new-list-create" class="btn btn-outline-success mt-3 mr-0">
                Create
            </button>
        </div>
    </div>
</div>

<!-- Dropdown of lists to edit -->
<div id="todo-list-container" class="my-3">

    <?php
    $database   = new Wp_Admin_Todo_Database();
    $todo_lists = $database->get_lists();



    // display empty if retrieval is empty
    if ( ! count( $todo_lists ) ) {

        printf( '<h4>%s</h4>', 'No lists available' );

    } else {

        // display each row as an option
        foreach ( $todo_lists as $list )
        {
            printf( '<option value="%d">%s</option>', $list->id, $list->list_name );
        }

    }
    ?>

</div>

<!--<div id="list-edit" class="mt-3" hidden>-->
<!---->
<!--    <h2 id="list-name"></h2>-->
<!--    <ul id="list-items">-->
<!---->
<!--    </ul>-->
<!--    <button class="btn btn-outline-primary" id="list-edit-add-item">-->
<!--        Add New Item-->
<!--    </button>-->
<!--    <input type="hidden" id="list-id">-->
<!--</div>-->

<script>
    const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';

    /*********
     * LISTS *
     *********/

        // show edit form if list is selected
    const listSelector = document.getElementById('list-edit-dropdown');

    listSelector.addEventListener('change', async function() {

        const listID = Number(this.value);

        clearList();

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
        }

    });

    // CREATE LIST
    const createButton = document.getElementById('todo-new-list-create');

    createButton.addEventListener('click', async function() {

        // extract value
        const newListInput  = document.getElementById('todo-new-list-input');
        const listName      = newListInput.value;

        // create payload
        const formData      = new FormData();
        formData.append('action', 'create_list');
        formData.append('list-name', listName);

        const res = await fetch(ajaxUrl, {
            method:  'POST',
            body:    formData
        });

        // @todo - flash list created (success message)

    });

    /**
     * Injects the list data into editing
     * @param id            {number}
     * @param list_name     {string}
     * @param items         { { id: number, content: string, completed: boolean, list_id: number }[] }
     */
    const renderList = ({ id, list_name, items }) => {
        // show form
        const listEdit      = document.getElementById('list-edit');
        listEdit.removeAttribute('hidden');

        const listName      = document.getElementById('list-name');
        listName.innerText  = list_name;

        const hiddenInput   = document.getElementById('list-id');
        hiddenInput.value   = id;

        const list = document.getElementById('list-items');

        if (items.length) {
            // remove message if previously exist
            const noItemsMessage = document.getElementById('no-list-items');
            noItemsMessage && noItemsMessage.remove();

            for (const { id, content, completed } of items) {
                injectListItem(content);
            }
        } else {
            const noItemsHTML = '<li id="no-list-items">List has no current item</li>';
            list.insertAdjacentHTML('afterbegin', noItemsHTML);
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

    /*********
     * ITEMS *
     *********/

        // Inject Add New Item input
    const addNewItemButton = document.getElementById('list-edit-add-item');

    addNewItemButton.addEventListener('click', function() {

        const newItemInput = `
            <ol>
                <div class="input-group mt-3">
                    <button class="btn btn-danger item-cancel-buttons" onclick="removeListItem(this);">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                    <input type="text" placeholder="New TODO Item...">
                    <button class="btn btn-outline-success item-create-button" onclick="createListItem(this);">
                        Create
                    </button>
                </div>
            </ol>
        `;

        const list = document.getElementById('list-items');
        list.insertAdjacentHTML('beforeend', newItemInput);

    });

    /**
     * Removes current <li>> from <ul> using a nested element
     * @param element {HTMLElement} - should be cancel or create button
     */
    function removeListItem(element) {
        const liElem = element.closest('li');
        liElem.remove();
    }

    /**
     * Creates a new list item
     * @param createButton
     */
    async function createListItem(createButton) {
        createButton.innerHTML = `
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
        `;

        const hiddenInput = document.getElementById('list-id');
        const listID      = hiddenInput.value;

        // extract new contents
        const newItemInput = createButton.previousElementSibling;
        const content      = newItemInput.value;

        // payload
        const formData = new FormData();
        formData.append('action', 'create_item');
        formData.append('list-ID', listID);
        formData.append('content', content);

        const res = await fetch(ajaxUrl, {
            method: 'POST',
            body:   formData
        })

        const { success } = await res.json();

        if (success) {

            removeListItem(createButton);
            injectListItem(content);

        } else {
            // error message

        }

    }

    /**
     * This is used after a list item has been created in the database
     */
    function injectListItem(content) {

        // remove message if previously exist
        const noItemsMessage = document.getElementById('no-list-items');
        noItemsMessage && noItemsMessage.remove();

        const list = document.getElementById('list-items');
        list.insertAdjacentHTML('beforeend', `
            <li>
                <input value="${content}" class="col-4" disabled>
            </li>
        `);

    }

</script>
