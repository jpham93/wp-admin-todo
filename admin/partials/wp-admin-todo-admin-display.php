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
                    'post_type' => 'wp-admin-todo'
                )
            );

        foreach( $posts as $post):
    ?>
        <li>
            <input type="text" disabled value="<?php echo $post->get_content() ?>"
        </li>
    <?php endforeach; ?>
</ul>

<!--ADD FUNCTION-->
<input type="text" name="content" id="add-content-input">
<button id="todo-add-button" class="btn btn-outline-primary btn-small">+</button>

<script>

    // AJAX URL
    const ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

    // ADD TODO
    const addButton = document.getElementById('todo-add-button');

    addButton.addEventListener('click', async function(e) {

        const content = document.getElementById('add-content-input').value;

        console.log(content);

        // send as form data instead of JSON (catch via $_POST)
        const formData  = new FormData();

        // AJAX route
        formData.append('action', 'add_todo');

        // attach payload
        formData.append('content', content)

        // don't worry about response for now
        await fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        });

        // refresh page to load new data
        // location.reload();
    });

</script>
