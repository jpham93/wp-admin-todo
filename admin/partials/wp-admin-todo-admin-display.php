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

<h1 class="text-primary">WordPress Admin TODO</h1>

<!-- CREATE LISTS -->
<button id="todo-new-list-begin" class="btn btn-outline-primary btn-small">
    Create New list
</button>

<div id="todo-new-list-container" class="my-3" hidden>
    <h2>Create a New List</h2>
    <div class="container-fluid ml-0">
        <div class="col-md-2">
            <label for="todo-new-list-name" class="form-label">New List Name</label>
            <input type="text" id="todo-new-list-name" class="form-control">

            <button id="todo-new-list-create" class="btn btn-outline-success mt-3 mr-0">
                Create
            </button>
            <button id="todo-new-list-cancel" class="btn btn-outline-dark mt-3 mr-0">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- LIST DISPLAY -->
<div id="todo-list-container" class="my-3">

    <?php
    $database   = new Wp_Admin_Todo_Database();
    $todo_lists = $database->get_lists();

    // display empty if retrieval is empty
    if ( ! count( $todo_lists ) ) { ?>

        <h4>No lists available</h4>

    <?php } else {

        // display each list
        foreach ( $todo_lists as $list )
        {
            // BEGIN LIST
            ?>
                <ul id="todo-list-<?php echo $list->id; ?>" class="list-group col-md-4 my-3">
                    <li class="d-inline-flex justify-content-between align-items-center">
                        <h4 class="d-inline mb-0">
                            <?php echo $list->list_name; ?>
                        </h4>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-warning">
                                <span class="dashicons dashicons-edit-large"></span>
                            </button>
                            <button class="btn btn-sm btn-danger">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </li>
            <?php
            $list_items = $database->read_list( $list->id )->items;

            // display empty if no items
            if ( !count( $list_items ) ) {

                ?>
                    <li class="todo-list-item-row list-group-item">
                        <h5>No items available</h5>
                    </li>
                <?php

            } else {

                foreach ( $list_items as $item )
                {
                    // id, content, completed
                    ?>
                        <li class="todo-list-item list-group-item input-group"
                            id="todo-list-item-<?php echo $item->id; ?>">
                            <input type="checkbox"
                                   class="todo-list-item-checkbox"
                            >
                            <input type="text"
                                   value="<?php echo $item->content; ?>"
                                   disabled
                                   class="todo-list-item-input"
                            >
                        </li>
                    <?php
                }

            }

            // END LIST
            ?>
                </ul>
            <?php
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
    const newListForm         = document.getElementById('todo-new-list-container');

    // SHOW CREATE NEW LIST
    const createNewListButton = document.getElementById('todo-new-list-begin');

    createNewListButton.addEventListener('click', function() {

        // hide button & show create list form
        this.hidden = true;
        newListForm.hidden = false;

    });

    // HIDE CREATE NEW LIST
    const cancelNewListButton = document.getElementById('todo-new-list-cancel');

    cancelNewListButton.addEventListener('click', function() {

        newListForm.hidden          = true;
        createNewListButton.hidden  = false;

    });

    // CREATE NEW LIST
    const createListButton = document.getElementById('todo-new-list-create');

    createListButton.addEventListener('click', async function() {

        // extract value
        const newListInput  = document.getElementById('todo-new-list-name');
        const listName      = newListInput.value;

        // create payload
        const formData      = new FormData();
        formData.append('action', 'create_list');
        formData.append('list-name', listName);

        const res = await fetch(ajaxUrl, {
            method:  'POST',
            body:    formData
        });

        try {
            const json = await res.text();
            console.log('Create new list res:', json);

            // @todo - flash w/ hide spinner list created (success message)
            if (json.success) {
                console.log(json);
            }

            // show previous forms
        } catch(e) {

            console.error('WP Todo Admin Error Line 178:', e);

        }
        newListForm.hidden          = true;
        createNewListButton.hidden  = false;

    });

    /**
     * Injects a newly created list to avoid refreshing the page
     * @param id            {number}
     * @param list_name     {string}
     * @param items         { { id: number, content: string, completed: boolean, list_id: number }[] }
     * @TODO - may be deprecated
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
     * @TODO - may be deprecated
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

    // addNewItemButton.addEventListener('click', function() {
    //
    //     const newItemInput = `
    //         <ol>
    //             <div class="input-group mt-3">
    //                 <button class="btn btn-danger item-cancel-buttons" onclick="removeListItem(this);">
    //                     <span class="dashicons dashicons-no-alt"></span>
    //                 </button>
    //                 <input type="text" placeholder="New TODO Item...">
    //                 <button class="btn btn-outline-success item-create-button" onclick="createListItem(this);">
    //                     Create
    //                 </button>
    //             </div>
    //         </ol>
    //     `;
    //
    //     const list = document.getElementById('list-items');
    //     list.insertAdjacentHTML('beforeend', newItemInput);
    //
    // });

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
