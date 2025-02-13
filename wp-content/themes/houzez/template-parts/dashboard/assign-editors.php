<?php
// Template Name: Assign Editors
get_header(); ?>

<div class="assign-editors">
    <h1><?php esc_html_e('Assign Editors', 'houzez'); ?></h1>
    <form method="post" action="">
        <label for="editor"><?php esc_html_e('Select Editor:', 'houzez'); ?></label>
        <select name="editor" id="editor">
            <?php
            // Fetch users with editor role
            $editors = get_users(array('role' => 'editor'));
            foreach ($editors as $editor) {
                echo '<option value="' . esc_attr($editor->ID) . '">' . esc_html($editor->display_name) . '</option>';
            }
            ?>
        </select>
        <input type="hidden" name="property_id" value="<?php echo esc_attr($post_id); ?>">
        <input type="submit" name="assign_editor" value="<?php esc_html_e('Assign Editor', 'houzez'); ?>">
    </form>

    <?php
    // Handle form submission
    if (isset($_POST['assign_editor'])) {
        $editor_id = intval($_POST['editor']);
        $property_id = intval($_POST['property_id']);
        update_post_meta($property_id, 'assigned_editor', $editor_id);
        echo '<p>' . esc_html__('Editor assigned successfully!', 'houzez') . '</p>';
    }
    ?>
</div>

<?php get_footer(); ?> 