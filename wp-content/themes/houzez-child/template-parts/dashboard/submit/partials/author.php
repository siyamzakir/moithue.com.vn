<?php
$user_id = get_current_user_id();
$post_id = filter_input(INPUT_GET, 'edit_property', FILTER_VALIDATE_INT);

// find property by id
$property = DB::findById(DB::POSTS, $post_id, ['post_author', 'ID']);
$has_permission = ($property && $property['post_author'] == $user_id) || houzez_can_manage() || houzez_is_editor();

if ($has_permission): 
    echo Section::userSelectBox(
        'manage_author',
        'Manage Author',
        'Select An Author',
        ['selectClass' => 'bs-select-hidden', 'select'=> 'data-size="5" id="property-author-js"'],
        [$property['post_author']],
    );
?>
<?php endif; ?>