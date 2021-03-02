<?php

/**
 * Provide a admin area view for the plugin
 *
 * MAIN TODO APP
 *
 * @link       https://jamespham.io
 * @since      1.0.0
 *
 * @package    Wp_Admin_Todo
 * @subpackage Wp_Admin_Todo/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1 class="text-primary">TODO</h1>

<!--DISPLAY CURRENT TODO LIST-->
<ul>
    <?php
        $posts = get_posts(
                array(
                    'post_type'        => 'wp-admin-todo',
                    'post_status'      => array( 'open', 'finished' ),
                    'orderby'          => 'date',
                    'order'            => 'DESC',
                    'suppress_filters' => true,
                )
            );

        if ( count($posts) ) {
            foreach( $posts as $post) {
    ?>

        <li>
            <input type="checkbox"
                   class="todo-item-checkbox"
                   id="todo-item-checkbox-<?php echo $post->ID ?>"
                   data-id="<?php echo $post->ID ?>"
                   <?php echo $post->post_status === 'finished' ? 'checked' : ''; ?>
            >
            <input type="text"
                   disabled
                   value="<?php echo $post->post_content; ?>"
                   id="todo-item-<?php echo $post->ID ?>"
                   class="todo-item"
                   data-id="<?php echo $post->ID ?>"
            >
            <button id="todo-item-edit-<?php echo $post->ID ?>"
                    class="btn btn-outline-warning btn-small todo-item-edit-button"
                    data-toggle="inactive"
                    data-id="<?php echo $post->ID ?>"
            >
                <span class="dashicons dashicons-edit"></span>
            </button>
            <button id="todo-item-remove-<?php echo $post->ID ?>"
                    class="btn btn-outline-danger btn-small todo-item-delete-button"
                    data-id="<?php echo $post->ID ?>"
            >
                <span class="dashicons dashicons-trash"></span>
            </button>
        </li>

    <?php
            }
        }
    ?>
</ul>

<!--ADD FUNCTION-->
<input type="text" id="add-content-input">
<button id="todo-add-button" class="btn btn-outline-primary btn-small">+</button>

<script>

    // AJAX URL
    const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

    // ADD ITEM
    const addButton = document.getElementById('todo-add-button');

    addButton.addEventListener('click', async function(e) {

        const content = document.getElementById('add-content-input').value;

        // send as form data instead of JSON (catch via $_POST)
        const formData  = new FormData();

        // AJAX route
        formData.append('action', 'add_todo');

        // attach payload
        formData.append('todo-content', content);
        formData.append('todo-status', 'open');

        // don't worry about response for now
        await fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        });

        // // refresh page to load new data
        location.reload();
    });

    // EDIT ITEM (CHECKBOXES)
    const checkboxes = document.querySelectorAll('input.todo-item-checkbox');

    checkboxes.forEach( checkbox => {

        checkbox.addEventListener('click', async function() {

            const status = this.checked ? 'finished' : 'open';
            const postID = this.getAttribute('data-id')
            const content = document.getElementById(`todo-item-${postID}`).value;     // repass content. Not enough time to do dynamic updating in AJAX route

            const formData  = new FormData();

            // AJAX route
            formData.append('action', 'edit_todo');

            // attach payload
            formData.append('todo-status', status);
            formData.append('todo-content', content);
            formData.append('todo-ID', postID);

            // don't worry about response for now
            await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });

        });
    });

    // EDIT ITEM (CONTENT)
    const editButtons = document.querySelectorAll('button.todo-item-edit-button');

    editButtons.forEach(editButton => {

        editButton.addEventListener('click', function() {

            const postID        = this.getAttribute('data-id');
            const toggle        = this.getAttribute('data-toggle');
            const todoInput     = document.getElementById(`todo-item-${postID}`);

            // if inactive, change toggle status & allow for inline editing.
            if (toggle === 'inactive') {

                this.setAttribute('data-toggle', 'active');
                todoInput.disabled  = false;

                // change colors with state change
                this.classList.add('btn-outline-success')
                this.classList.remove('btn-outline-warning');

            }
            // if active, save current values to DB and disable input
            else {

                this.setAttribute('data-toggle', 'inactive');
                todoInput.disabled  = true;

                // change colors with state change
                this.classList.add('btn-outline-warning');
                this.classList.remove('btn-outline-success');

            }

        });

    });

    // DELETE ITEM
    const deleteButton = document.querySelectorAll('.todo-item-delete-button');

    deleteButton.forEach(deleteButton => {

        deleteButton.addEventListener('click', async function() {

            const postID = this.getAttribute('data-id')

            const formData  = new FormData();

            // AJAX route
            formData.append('action', 'delete_todo');

            // attach payload
            formData.append('todo-ID', postID);

            // don't worry about response for now
            await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });

            // refresh page to load new data
            location.reload();

        });

    });

</script>
