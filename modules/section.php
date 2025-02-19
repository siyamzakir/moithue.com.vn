<?php
require_once __DIR__ . '/DB.php';

final class Section extends DB {
    
    public static function userSelectBox(
        string $name = 'user_id', 
        string $title = "Author", 
        string $placeholder = "Select Author", 
        array $attributes = [], 
        array $selectedIds = [],
        string $search = 'true'    
    ) {        
        $users = static::getAllUsers();
        $optionsString = "<option value='' hidden></option>";

        foreach ($users as $user) {
            if(in_array($user['ID'], $selectedIds)) {
                $optionsString .= "<option value='{$user['ID']}' selected>{$user['display_name']}</option>";
            } else {
                $optionsString .= "<option value='{$user['ID']}'>{$user['display_name']}</option>";
            }
        }

        $divAttr = isset($attributes['div']) ? $attributes['div'] : '';
        $labelAttr = isset($attributes['label']) ? $attributes['label'] : '';
        $selectAttr = isset($attributes['select']) ? $attributes['select'] : '';

        return "
            <div class='form-group' {$divAttr}>
                <label for='{$name}' {$labelAttr}>{$title}</label>
                <select name='{$name}' class='selectpicker form-control bs-select-hidden' title='{$placeholder}' data-live-search='{$search}' {$selectAttr}>
                    {$optionsString}
                </select>
            </div>
        ";
    }

    public static function postSelectBox(
        string $type = 'property',
        string $name = 'post_id', 
        string $title = "Property", 
        string $placeholder = "Select A Property", 
        array $attributes = [], 
        array $selectedIds = [],
        string $search = 'true',
        int $userId = 0
    ) {        
        if($userId && !static::DEALS_LEADS_MANAGE_BY_SELF) {
            $getPostIds = static::getPostIdsByEditorId($userId);
            $posts = static::getByIdsColumns(static::POSTS, $getPostIds, ['post_type' => $type], ['ID', 'post_title']);
        } else {
            $posts = static::getByColumns(static::POSTS, ['post_type' => $type], ['ID', 'post_title']);
        }

        if(!$posts) return "<div class='alert alert-danger'>No {$title} found</div>";

        $optionsString = "<option value='' hidden></option>";

        foreach ($posts as $post) {
            if(in_array($post['ID'], $selectedIds)) {
                $optionsString .= "<option value='{$post['ID']}' selected>{$post['post_title']}</option>";
            } else {
                $optionsString .= "<option value='{$post['ID']}'>{$post['post_title']}</option>";
            }
        }

        $divAttr = isset($attributes['div']) ? $attributes['div'] : '';
        $labelAttr = isset($attributes['label']) ? $attributes['label'] : '';
        $selectAttr = isset($attributes['select']) ? $attributes['select'] : '';

        return "
            <div class='form-group relative' {$divAttr}>
                <label for='{$name}' {$labelAttr}>{$title}</label>
                <select name='{$name}' class='selectpicker form-control bs-select-hidden' title='{$placeholder}' data-live-search='{$search}' {$selectAttr}>
                    {$optionsString}
                </select>
            </div>
        ";
    }

    public static function echoDefaultUrl(?string $endpoint = null, ?string $url = null) {
        if($endpoint && $url) {
            return "<script>window.history.pushState({}, '', '{$url}{$endpoint}');</script>";
        }
        if($url) {
            return "<script>window.history.pushState({}, '', '{$url}');</script>";
        }

        if($endpoint) {
            return "<script>
                window.history.pushState({}, '', window.location.href.split('?')[0] + '{$endpoint}');
            </script>";
        }

        return "<script>window.history.pushState({}, '', window.location.href.split('?')[0]);</script>";
    }
}



?>