<?php
require_once __DIR__ . '/DB.php';

final class Section extends DB {
    
    public static function userSelectBox(
        string $name = 'user_id', 
        string $title = "Author", 
        string $placeholder = "Select Author", 
        array $attributes = [], 
        array $selectedIds = [],
        string $search = 'true',
        string $firstOption = "<option value='' hidden></option>",
    ) {        
        $optionsString = $firstOption;
        $users = static::getAllUsers();

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
        int $userId = 0,
        string $firstOption = "<option value='' hidden></option>"
    ) {        
        if($userId && !static::DEALS_LEADS_MANAGE_BY_SELF) {
            $getPostIds = static::getPostIdsByEditorId($userId);
            $posts = static::getByIdsColumns(static::POSTS, $getPostIds, ['post_type' => $type], ['ID', 'post_title']);
        } else {
            $posts = static::getByColumns(static::POSTS, ['post_type' => $type], ['ID', 'post_title']);
        }

        if(!$posts) return "<div class='alert alert-danger'>No {$title} found</div>";

        $optionsString = $firstOption;

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

    public static function leadSelectBox(
        string $name = 'lead_id', 
        string $title = "Lead", 
        string $placeholder = "Select A Lead", 
        array $attributes = [], 
        array $selectedIds = [],
        string $search = 'true',
        int $userId = 0,
        string $firstOption = "<option value='' hidden></option>"
    ) {        
        $table = self::HOUZEZ_CRM_LEADS;
        $conditions = [];
        
        if($userId && !static::DEALS_LEADS_MANAGE_BY_SELF) {
            $conditions['user_id'] = $userId;
        }

        $query = "SELECT lead_id, display_name FROM `{$table}`";
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "`{$key}` = ?";
            }
            $query .= " WHERE " . implode(' AND ', $whereClause);
        }
        $query .= " ORDER BY lead_id DESC";

        $stmt = self::connect()->prepare($query);
        if (!empty($conditions)) {
            $stmt->execute(array_values($conditions));
        } else {
            $stmt->execute();
        }
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$leads) return "<div class='alert alert-danger'>No {$title} found</div>";

        $optionsString = $firstOption;

        foreach ($leads as $lead) {
            if(in_array($lead['lead_id'], $selectedIds)) {
                $optionsString .= "<option value='{$lead['lead_id']}' selected>{$lead['display_name']}</option>";
            } else {
                $optionsString .= "<option value='{$lead['lead_id']}'>{$lead['display_name']}</option>";
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

    public static function nextActionSelector(
        ?string $selected = '', 
        string $extraCn = '', 
        string $attributes = '', 
        string $placeholder = 'Select a Action',
        string $firstOption = "<option value='' hidden></option>"
    ){
        $actions = [
            'Qualification',
            'Demo',
            'Call',
            'Send a Proposal',
            'Send an Email',
            'Follow Up',
            'Meeting'
        ];

        $optionsString = $firstOption;

        foreach ($actions as $action) {
            $isSelected = trim($selected) == trim($action) ? 'selected' : '';
            $optionsString .= "<option value='{$action}' {$isSelected}>{$action}</option>";
        }

        return "<select {$attributes} class='selectpicker {$extraCn} form-control bs-select-hidden' title='{$placeholder}'>{$optionsString}</select>";
    }

    public static function dealStatusSelector(
        ?string $selected = '', 
        string $extraCn = '',
        string $attributes = '',
        string $placeholder = "Select A Deal Status",
        string $firstOption = "<option value='' hidden></option>"
    ) {
        $statusTypes = [
            'New Lead',
            'Meeting Scheduled',
            'Qualified',
            'Proposal Sent',
            'Called',
            'Negotiation',
            'Email Sent'
        ];
        
        $optionsString = $firstOption;

        foreach ($statusTypes as $status) {
            $isSelected = trim($selected) == trim($status) ? 'selected' : '';
            $optionsString .= "<option value='{$status}' {$isSelected}>{$status}</option>";
        }   

        return "<select {$attributes} class='selectpicker {$extraCn} form-control bs-select-hidden' title='{$placeholder}'>{$optionsString}</select>";
    }

    public static function referrerSelector(
        ?string $selected = '', 
        string $extraCn = '',
        string $attributes = '',
        string $placeholder = "Select A Referrer",
        string $firstOption = "<option value='' hidden></option>",
        string $sources = 'Website, Newspaper, Friend, Google, Facebook'
    ) {
        $optionsString = $firstOption;
        $sourceArray = !empty($sources) ? explode(',', $sources) : [];

        foreach ($sourceArray as $source) {
            $source = trim($source);
            $isSelected = trim($selected) == $source ? 'selected' : '';
            $optionsString .= "<option value='{$source}' {$isSelected}>{$source}</option>";
        }   

        return "<select {$attributes} class='selectpicker {$extraCn} form-control bs-select-hidden' title='{$placeholder}'>{$optionsString}</select>";
    }

    
}



?>