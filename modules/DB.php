<?php

require_once __DIR__ . '/../main-config.php';
require_once __DIR__ . '/../logs/log.php';

trait MigrationsTrait {

    public static function migrateListingEditorsTable() {
        // SQL query to create the wp_listing_editors table
        $query = "
            CREATE TABLE IF NOT EXISTS `wp_listing_editors` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `post_id` BIGINT UNSIGNED,
                `author_id` BIGINT UNSIGNED,
                `user_id` BIGINT UNSIGNED,
                `editor_ids` VARCHAR(400),
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                FOREIGN KEY (`post_id`) REFERENCES `wp_posts`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        // Execute the query
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public static function dropTable(string $table) {
        // Check for foreign key constraints
        $foreignKeyQuery = "SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL";
        $stmt = self::connect()->prepare($foreignKeyQuery);
        $stmt->execute([$table]);
        
        if ($stmt->fetchColumn() > 0) {
            // Ignore foreign key constraints and force drop
            $query = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `{$table}`; SET FOREIGN_KEY_CHECKS = 1;";
        } else {
            $query = "DROP TABLE IF EXISTS `{$table}`"; 
        }

        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }

}

class DB {
    use MigrationsTrait;

    private $pdo;

    public const COMMENT_META = 'wp_commentmeta';
    public const COMMENTS = 'wp_comments';
    public const E_EVENTS = 'wp_e_events';
    public const FAVETHEMES_CURRENCY_CONVERTER = 'wp_favethemes_currency_converter';
    public const FAVETHEMES_INSIGHTS = 'wp_favethemes_insights';
    public const HOUZEZ_CRM_ACTIVITIES = 'wp_houzez_crm_activities';
    public const HOUZEZ_CRM_DEALS = 'wp_houzez_crm_deals';
    public const HOUZEZ_CRM_ENQUIRIES = 'wp_houzez_crm_enquiries';
    public const HOUZEZ_CRM_LEADS = 'wp_houzez_crm_leads';
    public const HOUZEZ_CRM_NOTES = 'wp_houzez_crm_notes';
    public const HOUZEZ_CRM_VIEWED_LISTINGS = 'wp_houzez_crm_viewed_listings';
    public const HOUZEZ_CURRENCIES = 'wp_houzez_currencies';
    public const HOUZEZ_FIELDS_BUILDER = 'wp_houzez_fields_builder';
    public const HOUZEZ_SEARCH = 'wp_houzez_search';
    public const HOUZEZ_THREAD_MESSAGES = 'wp_houzez_thread_messages';
    public const HOUZEZ_THREADS = 'wp_houzez_threads';
    public const LINKS = 'wp_links';
    public const MLS_ON_THE_FLY_CACHE = 'wp_mls_on_the_fly_cache';
    public const OPTIONS = 'wp_options';
    public const POSTMETA = 'wp_postmeta';
    public const POSTS = 'wp_posts';
    public const REALTYNA_RF_MAPPINGS = 'wp_realtyna_rf_mappings';
    public const REVSLIDER_CSS = 'wp_revslider_css';
    public const REVSLIDER_LAYER_ANIMATIONS = 'wp_revslider_layer_animations';
    public const REVSLIDER_NAVIGATIONS = 'wp_revslider_navigations';
    public const REVSLIDER_SLIDERS = 'wp_revslider_sliders';
    public const REVSLIDER_SLIDERS7 = 'wp_revslider_sliders7';
    public const REVSLIDER_SLIDES = 'wp_revslider_slides';
    public const REVSLIDER_SLIDES7 = 'wp_revslider_slides7';
    public const REVSLIDER_STATIC_SLIDES = 'wp_revslider_static_slides';
    public const TERM_RELATIONSHIPS = 'wp_term_relationships';
    public const TERM_TAXONOMY = 'wp_term_taxonomy';
    public const TERM_META = 'wp_termmeta';
    public const TERMS = 'wp_terms';
    public const USER_META = 'wp_usermeta';
    public const USERS = 'wp_users';
    public const LISTING_EDITORS = 'wp_listing_editors';

    public const MAIN_ADMINISTRATOR_ID = null;

    public function __construct() {
        $this->pdo = self::connect();
    }

    public static function showAllTables() {
        $query = "SHOW TABLES";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function connect() {
        return new PDO(DATABASE_CONFIGURATION['driver'] . ':host=' . DATABASE_CONFIGURATION['host'] . ';dbname=' . DATABASE_CONFIGURATION['database'], DATABASE_CONFIGURATION['username'], DATABASE_CONFIGURATION['password']);
    }

    public static function getAllUsers(array $columns = ['*']) {
        $query = "SELECT " . implode(', ', $columns) . " FROM wp_users";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(string $table, int $id, string $by = 'latest') {
        $query = "SELECT * FROM `{$table}` WHERE `id` = ? LIMIT 1";
        if ($by === 'latest') {
            $query = "SELECT * FROM `{$table}` ORDER BY `id` DESC LIMIT 1";
        }
        $stmt = self::connect()->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
    * Find data by columns
    * @param string $table
    * @param array $conditions
    * @param string $by
    * @return array|false
    */
    public static function findByColumns(string $table, array $conditions, string $by = 'latest') {
        $whereQuery = implode(' AND ', array_map(function($column) {
            return "`{$column}` = ?";
        }, array_keys($conditions)));

        $query = "SELECT * FROM `{$table}` WHERE {$whereQuery}";

        if ($by === 'latest') {
            $query .= " ORDER BY `id` DESC LIMIT 1";
        }

        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($conditions));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
    * Update data by columns
    * @param string $table
    * @param array $columns
    * @param array $data
    * @param bool $timestamps
    * @return int|false
    */
    public static function updateByColumns(string $table, array $columns, array $data, bool $timestamps = true) {
        if ($timestamps) $data = array_merge($data, ['updated_at' => self::getTimestamps()['updated_at']]);
        $setQuery = implode(', ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($data)));
        
        $whereQuery = implode(' AND ', array_map(function($column) { return "`{$column}` = ?"; }, array_keys($columns)));
        $query = "UPDATE `{$table}` SET {$setQuery} WHERE {$whereQuery}";
        
        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_merge(array_values($data), array_values($columns)));

        if ($stmt->errorCode() !== '00000') {
            $errorInfo = $stmt->errorInfo();
            Logger::error("SQL Error: {$errorInfo[2]} - Query: {$query} - Data: " , compact('data', 'columns'));
            return false;
        }

        return $stmt->rowCount();
    }

    /**
    * Create data
    * @param string $table
    * @param array $data
    * @param bool $timestamps
    * @return int|false
    */
    public static function create(string $table, array $data, bool $timestamps = true): ?int {
        if (empty($data)) {
            Logger::error("Data array cannot be empty.");
            return null;
        }

        $conn = self::connect();
        $columns = [];
        $placeholders = [];

        foreach ($data as $key => $value) {
            $columns[] = "`{$key}`";
            $placeholders[] = "?";
        }

        if ($timestamps) {
            $timestamps = self::getTimestamps();
            $data['created_at'] = $timestamps['created_at'];
            $data['updated_at'] = $timestamps['updated_at'];

            $columns[] = "`created_at`";
            $columns[] = "`updated_at`";
            $placeholders[] = "?";
            $placeholders[] = "?";
        }

        $q1 = implode(', ', $columns);
        $q2 = implode(', ', $placeholders);
        $query = "INSERT INTO `{$table}` ({$q1}) VALUES ({$q2})";
        
        $stmt = $conn->prepare($query); 

        // Execute with bound parameters
        if (!$stmt->execute(array_values($data))) {
            $errorInfo = $stmt->errorInfo();
            Logger::error("SQL Error: {$errorInfo[2]} - Query: {$query} - Data: " . json_encode($data));
            return null;  
        }

        return (int) $conn->lastInsertId();
    }

    public static function update(string $table, int $id, array $data, bool $timestamps = true): ?int {
        if (empty($data)) {
            Logger::error("Data array cannot be empty.");
            return null; 
        }

        $setQuery = [];
        foreach ($data as $key => $value) {
            $setQuery[] = "`{$key}` = ?";
        }
        
        $setQueryString = implode(', ', $setQuery);
        $query = "UPDATE `{$table}` SET {$setQueryString} WHERE `id` = ?";

        if ($timestamps) {
            $data['updated_at'] = self::getTimestamps()['updated_at'];
        }

        $stmt = self::connect()->prepare($query);
        $params = array_merge(array_values($data), [$id]);

        if (!$stmt->execute($params)) {
            $errorInfo = $stmt->errorInfo();
            Logger::error("SQL Error: {$errorInfo[2]} - Query: {$query} - Data: " . json_encode($params));
            return null;
        }

        return $stmt->rowCount(); // Return the number of affected rows
    }

    public static function delete(string $table, int $id) {
        $query = "DELETE FROM `{$table}` WHERE `id` = ?";
        $stmt = self::connect()->prepare($query);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public static function getLastInsertId() {
        return self::connect()->lastInsertId();
    }

    public static function getRowCount(string $table) {
        $query = "SELECT COUNT(*) FROM `{$table}`";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getTableColumns(string $table) {
        $query = "SHOW COLUMNS FROM `{$table}`";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function get(string $table, array $columns = ['*'], string $by = 'latest') {
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}`";
        if ($by === 'latest') {
            $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` ORDER BY `id` DESC";
        }
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getWhere(string $table, array $columns = ['*'], array $where = [], string $by = 'latest') {
        $whereQuery = implode(' AND ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($where)));
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` WHERE {$whereQuery}";

        if ($by === 'latest') {
            $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` WHERE {$whereQuery} ORDER BY `id` DESC LIMIT 1";
        }

        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($where));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrderBy(string $table, array $columns = ['*'], array $where = [], string $orderBy = 'id', string $order = 'ASC') {
        $whereQuery = implode(' AND ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($where)));
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` WHERE {$whereQuery} ORDER BY `{$orderBy}` {$order}";
        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($where));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLimit(string $table, array $columns = ['*'], array $where = [], int $limit = 10, int $offset = 0) {
        $whereQuery = implode(' AND ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($where)));
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` WHERE {$whereQuery} LIMIT {$limit} OFFSET {$offset}";
        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($where));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPagination(string $table, array $columns = ['*'], array $where = [], int $limit = 10, int $offset = 0) {
        $whereQuery = implode(' AND ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($where)));
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` WHERE {$whereQuery} LIMIT {$limit} OFFSET {$offset}";
        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($where));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPaginationCount(string $table, array $where = []) {
        $whereQuery = implode(' AND ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($where)));
        $query = "SELECT COUNT(*) FROM `{$table}` WHERE {$whereQuery}";
        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($where));
        return $stmt->fetchColumn();
    }

    public static function getPaginationWithTotal(string $table, array $columns = ['*'], array $where = [], int $limit = 10, int $offset = 0) {
        $whereQuery = implode(' AND ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($where)));
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` WHERE {$whereQuery} LIMIT {$limit} OFFSET {$offset}";
        $stmt = self::connect()->prepare($query);
        $stmt->execute(array_values($where));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTimestamps(string $timestamp = 'now') {
        $timestamp = $timestamp === 'now' ? time() : $timestamp;
        $timestamp = date('Y-m-d H:i:s', $timestamp);

        return [
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    /**
     * Get all post IDs by editor ID
     * @param int $user_id
     * @return array
     */
    public static function getPostIdsByEditorId(int $user_id) {
        // Prepare the query to select post IDs where the user ID is in the editor_ids JSON array
        $table = self::LISTING_EDITORS;
        $table2 = self::POSTS;
        $query = "SELECT DISTINCT `post_id` FROM `{$table}` WHERE JSON_CONTAINS(editor_ids, ?, '$')";
        $query2 = "SELECT `ID` FROM `{$table2}` WHERE `post_author` = ?";

        // Prepare and execute the statement
        $stmt = self::connect()->prepare($query);
        $stmt->execute(["{$user_id}"]); // Bind the user ID as a JSON string
        $stmt2 = self::connect()->prepare($query2);
        $stmt2->execute([$user_id]);

        // Fetch the results and return as an array of post IDs
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $results2 = $stmt2->fetchAll(PDO::FETCH_COLUMN, 0);

        // remove duplicate post ids
        return array_unique(array_merge($results, $results2));
    }

    /**
     * Get assigned editors for a post
     * @param int $post_id
     * @param bool $editorsIdsOnly
     * @param bool $isDecodedEditorIds
     * @return array|string
     */
    public static function getAssignedEditorByPostId(int $post_id, bool $editorsIdsOnly = true, bool $isDecodedEditorIds = false) {
        $assigned_editors = DB::findByColumns(DB::LISTING_EDITORS, ['post_id' => $post_id]);

        if($assigned_editors) {
            if($isDecodedEditorIds) {
                $editor_ids = json_decode($assigned_editors['editor_ids'], true);
                if(json_last_error() !== JSON_ERROR_NONE) $editor_ids = [];
            } else {
                $editor_ids = $assigned_editors['editor_ids'] ?? '[]';
            }

            if($editorsIdsOnly) return $editor_ids;

            return [
                'id' => $assigned_editors['id'],
                'author_id' => $assigned_editors['author_id'],
                'user_id' => $assigned_editors['user_id'],
                'post_id' => $assigned_editors['post_id'],
                'editor_ids' => $editor_ids,
                'created_at' => $assigned_editors['created_at'],
                'updated_at' => $assigned_editors['updated_at'],
            ];
        }

        $editor_ids = '[]';
        if($isDecodedEditorIds) $editor_ids = [];
        if($editorsIdsOnly) return $editor_ids;
        
        return [
            'id' => null,
            'author_id' => null,
            'user_id' => null,
            'post_id' => null,
            'editor_ids' => $editor_ids,
            'created_at' => null,
            'updated_at' => null,
        ];
    }

    public static function hasAssignedEditor(int $user_id, int $post_id) {
        $assigned_editors = self::getAssignedEditorByPostId($post_id, true, true);
        
        if(!is_array($assigned_editors)) return false;
        
        return in_array($user_id, $assigned_editors, true);
    }
}
?>