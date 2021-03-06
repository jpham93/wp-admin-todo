<?php
/**
* Create New TODO List view
*
* @link       https://jamespham.io
* @since      2.0.0
*
* @package    Wp_Admin_Todo
* @subpackage Wp_Admin_Todo/admin/partials
*/
?>
<h1>Create a New List</h1>

<div class="container-fluid ml-0">
    <div class="col-md-2">
        <label for="todo-new-list" class="form-label">New List Name</label>
        <input type="text" id="todo-new-list-input" class="form-control">

        <button id="todo-new-list-create" class="btn btn-outline-success mt-3 mr-0">
            Create
        </button>
    </div>
</div>

<script>
    const ajaxUrl = '<?php echo admin_url('admin-ajax.php') ?>';

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

        await fetch(ajaxUrl, {
           method:  'POST',
           body:    formData
        });

    });

</script>
