<?php

require_once __DIR__ . '/../main-config.php';
require_once __DIR__ . '/../logs/log.php';

trait MigrationsTrait {
    public static function migrateListingEditorsTable() {
        // SQL query to create the wp_listing_editors table
        $table = DB::LISTING_EDITORS;
        $query = "
            CREATE TABLE IF NOT EXISTS `{$table}` (
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

    // add a new column 'is_locked' to the wp_houzez_crm_leads table and default true
    public static function updateCRMLeadsTable() {
        $table = DB::HOUZEZ_CRM_LEADS;

        // check if the column exists
        $query = "SHOW COLUMNS FROM {$table} LIKE 'is_locked'";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        $columnExists = $stmt->fetchColumn();

        if(!$columnExists) {
            $query = "ALTER TABLE {$table} ADD COLUMN `is_locked` TINYINT(1) NOT NULL DEFAULT '0' AFTER `status`";
            $stmt = self::connect()->prepare($query);
            $stmt->execute();
        }
    } 
}

trait DBConstantVars {
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
    public const LISTING_SELF_APPROVED = false;
    public const DEALS_LEADS_MANAGE_BY_SELF = false;
}

class DB {
    use MigrationsTrait, OlderDB, DBConstantVars;

    /**
     * Retrieves a list of all tables in the database.
     *
     * This method constructs a SQL query to show all tables present in the database.
     * It prepares and executes the query using the PDO connection established in the
     * class. The results are fetched as an associative array, which contains the names
     * of the tables. This method is useful for obtaining a comprehensive overview of
     * the database structure, allowing developers to understand what tables are available
     * for querying or manipulation.
     *
     * @return array Returns an associative array of all tables in the database.
     *               Each entry in the array corresponds to a table name. If there are
     *               no tables in the database, an empty array is returned. In case of
     *               an error during the execution of the query, an exception will be thrown.
     */
    public static function showAllTables() {
        $query = "SHOW TABLES";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Establishes a connection to the database using PDO.
     *
     * This method creates a new PDO instance, which represents a connection to the database.
     * It uses the configuration parameters defined in the DATABASE_CONFIGURATION constant
     * to specify the database driver, host, database name, username, and password.
     * 
     * The connection is essential for executing SQL queries and interacting with the database.
     * It is important to handle any potential exceptions that may arise during the connection
     * process, such as incorrect credentials or unreachable database server.
     *
     * @return PDO Returns a PDO instance representing the connection to the database.
     *              This instance can be used to prepare and execute SQL statements.
     *              If the connection fails, an exception will be thrown.
     *
     * @throws PDOException If the connection to the database fails, a PDOException will be thrown,
     *                      providing details about the error encountered.
     */
    public static function connect() {
        try {
            return new PDO(DATABASE_CONFIGURATION['driver'] . ':host=' . DATABASE_CONFIGURATION['host'] . ';dbname=' . DATABASE_CONFIGURATION['database'], DATABASE_CONFIGURATION['username'], DATABASE_CONFIGURATION['password']);
        } catch (PDOException $e) {
            Logger::error("SQL Error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Retrieves all records from the users table.
     *
     * This method constructs a SQL query to select all columns from the given table.
     * It prepares and executes the query using the PDO connection established in the
     * class. The results are fetched as an associative array, allowing easy access
     * to the data. This method is useful for obtaining a complete list of records
     * from a table, which can be used for display or further processing.
     *
     * @param string $table The name of the table to query.
     * @return array Returns an associative array of all records from the specified table.
     *               If the table is empty, an empty array is returned. In case of an error,
     *               an exception will be thrown.
     */
    public static function getAllUsers(array $columns = ['*']) {
        try {
            $table = self::USERS;
            $metaTable = self::USER_META;
            
            // If columns array only contains '*', replace with specific user table columns
            if ($columns === ['*']) {
                $cols = "u.*";
            } else {
                $cols = "u." . implode(', u.', $columns);
            }
            
            // in here u=user & um=user_meta
            $query = "SELECT {$cols},
                MAX(CASE WHEN um.meta_key = 'first_name' THEN um.meta_value END) as first_name,
                MAX(CASE WHEN um.meta_key = 'last_name' THEN um.meta_value END) as last_name
            FROM {$table} u
            LEFT JOIN {$metaTable} um ON u.ID = um.user_id
            WHERE um.meta_key IN ('first_name', 'last_name')
            GROUP BY u.ID
            ORDER BY u.display_name ASC";
            
            $stmt = self::connect()->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error("getAllUsers: SQL Error: {$e->getMessage()} - Query: {$query}");
            return [];
        }
    }

    /**
     * Find data by columns in a specified table.
     *
     * This method constructs a SQL query to retrieve a single record from the specified
     * table based on the provided conditions. It allows for filtering results using
     * an associative array of conditions, where the keys represent column names
     * and the values represent the values to match. The method also supports selecting
     * specific columns and can order the results based on the specified criteria.
     *
     * @param string $table The name of the table to query.
     * @param array $conditions An associative array of conditions to filter the results.
     *                          Each key is a column name, and each value is the value to match.
     * @param array $columns An optional array of specific columns to select. Defaults to ['*'].
     * @param string $by Determines the order of the results (default is 'latest').
     *                   If set to 'latest', the results will be ordered by the 'id' column in descending order.
     * @return array|false Returns an associative array of the first matching record or false on failure.
     *                     If no records match the conditions, an empty array is returned.
     */
    public static function findById(string $table, array|int $id, array $columns = ['*'], string $by = 'latest') {
        try {
            $idCol = 'ID';
            $idVal = $id;

            if(is_array($id)) {
                $idCol = array_keys($id)[0];
                $idVal = array_values($id)[0];
            }

            $cols = implode(', ', $columns);
            $query = "SELECT {$cols} FROM `{$table}` WHERE `{$idCol}` = ? LIMIT 1";

            if ($by === 'latest') {
                $query = "SELECT {$cols} FROM `{$table}` WHERE `{$idCol}` = ? ORDER BY `{$idCol}` DESC LIMIT 1";
            }

            $stmt = self::connect()->prepare($query);
            $stmt->execute([$idVal]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            Logger::info("SQL Error ~ Query: {$e->getMessage()}", compact('table', 'id', 'columns', 'by', 'query'));
            return false;
        }
    }

    /**
     * Find data by columns in a specified table.
     *
     * This method constructs a SQL query to retrieve a single record from the specified
     * table based on the provided conditions. It allows for filtering results using
     * an associative array of conditions, where the keys represent column names
     * and the values represent the values to match. The method also supports selecting
     * specific columns and can order the results based on the specified criteria.
     *
     * @param string $table The name of the table to query.
     * @param array $conditions An associative array of conditions to filter the results.
     *                          Each key is a column name, and each value is the value to match.
     * @param string $by Determines the order of the results (default is 'latest').
     *                   If set to 'latest', the results will be ordered by the 'id' column in descending order.
     * @return array|false Returns an associative array of the first matching record or false on failure.
     *                     If no records match the conditions, an empty array is returned.
     */
    public static function findByColumns(string $table, array $conditions, array $columns = ['*'], string $by = 'latest') {
        try {
            $whereQuery = implode(' AND ', array_map(function($column) {
                return "`{$column}` = ?";
            }, array_keys($conditions)));
    
            $cols = implode(', ', $columns);
            $query = "SELECT {$cols} FROM `{$table}` WHERE {$whereQuery}";
    
            if ($by === 'latest') {
                $query .= " ORDER BY `id` DESC ";
            }

            $query .= "LIMIT 1";
    
            $stmt = self::connect()->prepare($query);
            $stmt->execute(array_values($conditions));
    
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error("findByColumns: SQL Error: {$e->getMessage()} - Query: {$query} - Data: " . json_encode($conditions));
            return false;
        }
    }

    /**
      * Get records from a specified table based on an array of conditions.
      *
      * This function constructs a SQL query to retrieve records from the specified table
      * using the provided conditions. It allows for selecting specific columns and can
      * order the results based on the specified criteria.
      *
      * @param string $table The name of the table to query.
      * @param array $conditions The conditions to filter the results.
      * @param array $columns The columns to select from the table. Default is ['*'] to select all columns.
      * @param string $by Determines the order of the results (default is 'latest').
      * @return array|false Returns an array of results or false on failure.
      */
    public static function getByColumns(string $table, array $conditions, array $columns = ['*'], string $by = 'latest') {
        try {
            $whereQuery = implode(' AND ', array_map(function($column) {
                return "`{$column}` = ?";
            }, array_keys($conditions)));
    
            $cols = implode(', ', $columns);
            $query = "SELECT {$cols} FROM `{$table}` WHERE {$whereQuery}";
    
            if ($by === 'latest') {
                $query .= " ORDER BY `id` DESC";
            }
    
            $stmt = self::connect()->prepare($query);
            $stmt->execute(array_values($conditions));
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error("getByColumns: SQL Error: {$e->getMessage()} - Query: {$query} - Data: " . json_encode($conditions));
            return false;
        }
    }

    /**
     * Get data by ids and columns
     * This function retrieves records from the specified table based on an array of IDs and additional conditions.
     * It allows for selecting specific columns and can order the results by the latest entry.
     * 
     * @param string $table The name of the table to query.
     * @param array $ids An array of IDs to filter the results.
     * @param array $conditions Additional conditions for the query.
     * @param array $columns The columns to select from the table.
     * @param string $by Determines the order of the results (default is 'latest').
     * @return array|false Returns an array of results or false on failure.
     */
    public static function getByIdsColumns(string $table, array $ids, array $conditions = [], array $columns = ['*'], string $by = 'latest') {
        try {
            // Create placeholders for each ID
            $cols = implode(', ', $columns);
            $idPlaceholders = implode(', ', array_fill(0, count($ids), '?'));
    
            $params = []; // Array to hold all parameters for the SQL execution
            $whereParts = []; // Array to hold parts of the WHERE clause
    
            // Handle additional conditions
            foreach ($conditions as $key => $value) {
                $whereParts[] = "`$key` = ?";
                $params[] = $value; // Add condition values to parameters array
            }
    
            // Add IDs condition
            $whereParts[] = "`ID` IN ($idPlaceholders)";
            $params = array_merge($params, $ids); // Merge condition values with IDs
    
            $where = implode(' AND ', $whereParts);
            $query = "SELECT {$cols} FROM `{$table}` WHERE {$where}";
    
            if ($by === 'latest') {
                $query .= " ORDER BY `id` DESC";
            }
            
            $stmt = self::connect()->prepare($query);
            $stmt->execute($params);  // Execute with combined parameters
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error("getByIdsColumns: SQL Error: {$e->getMessage()} \n- Query: {$query} \n- Data: " . json_encode($params));
            return false;
        }
    }

    /**
     * Updates records in a table based on column conditions.
     * 
     * This function updates records in the specified table where the given column conditions match.
     * It supports automatic timestamp updates and handles SQL errors gracefully.
     *
     * @param string $table The name of the table to update
     * @param array $columns Associative array of column conditions to match in WHERE clause
     *                      Example: ['user_id' => 5, 'status' => 'active']
     * @param array $data Associative array of column-value pairs to update
     *                   Example: ['name' => 'John', 'email' => 'john@example.com'] 
     * @param bool $timestamps Whether to automatically update the updated_at timestamp (default true)
     * 
     * @return int|false Returns number of affected rows on success, false on failure
     *                   Note: Returns 0 if no rows were updated (conditions didn't match any records)
     * 
     * @throws PDOException on database errors, which are caught and logged
     * 
     * Example usage:
     * updateByColumns('users', ['id' => 5], ['name' => 'John', 'active' => 1]);
     * // UPDATE `users` SET `name` = 'John', `active` = 1, `updated_at` = NOW() WHERE `id` = 5
     */
    public static function updateByColumns(string $table, array $columns, array $data, bool $timestamps = true) {
        try {
            if ($timestamps) {
                $data = array_merge($data, ['updated_at' => self::getTimestamps()['updated_at']]);
            }
            
            $setQuery = implode(', ', array_map(function($key) { return "`{$key}` = ?"; }, array_keys($data)));
            $whereQuery = implode(' AND ', array_map(function($column) { return "`{$column}` = ?"; }, array_keys($columns)));
            $query = "UPDATE `{$table}` SET {$setQuery} WHERE {$whereQuery}";

            $stmt = self::connect()->prepare($query);
            $merge = array_merge(array_values($data), array_values($columns));

            // Execute the query
            $stmt->execute($merge);

            if ($stmt->errorCode() !== '00000') {
                $errorInfo = $stmt->errorInfo();
                Logger::error("updateByColumns: SQL Error: {$errorInfo[2]} - Query: {$query} - Data: ", compact('data', 'columns'));
                return false;
            }

            return $stmt->rowCount();
        } catch (PDOException $e) {
            Logger::error("updateByColumns: SQL Error: {$e->getMessage()} - Query: {$query} - Data: ", compact('data', 'columns'));
            return false;
        }
    }

    /**
     * Creates a new record in the specified database table.
     * 
     * This function inserts a new record into the given table with the provided data.
     * It supports automatic timestamp creation and handles SQL errors gracefully.
     *
     * @param string $table The name of the table to insert into
     * @param array $data Associative array of column-value pairs to insert
     *                   Example: ['name' => 'John', 'email' => 'john@example.com']
     * @param bool $timestamps Whether to automatically add created_at and updated_at timestamps (default true)
     * 
     * @return int|null Returns the ID of the newly inserted record on success, null on failure
     *                  Note: Returns null if data array is empty or if insert fails
     * 
     * @throws PDOException on database errors, which are caught and logged
     * 
     * Example usage:
     * create('users', ['name' => 'John', 'email' => 'john@example.com']);
     * // INSERT INTO `users` (`name`, `email`, `created_at`, `updated_at`) 
     * // VALUES ('John', 'john@example.com', '2023-01-01 00:00:00', '2023-01-01 00:00:00')
     */
    public static function create(string $table, array $data, bool $timestamps = true): ?int {
        try {
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
                Logger::error("create: SQL Error: {$errorInfo[2]} - Query: {$query} - Data: " . json_encode($data));
                return null;  
            }

            return (int) $conn->lastInsertId();
        } catch (PDOException $e) {
            Logger::error("create: SQL Error: {$e->getMessage()} - Query: {$query} - Data: " . json_encode($data));
            return null;  
        }
    }

    /**
     * Update a record in a specified table by ID.
     *
     * @param string $table The name of the table to update.
     * @param int $id The ID of the record to update.
     * @param array $data The data to update.
     * @param bool $timestamps Whether to update the timestamps.
     * @return int|null The number of rows affected by the update operation, or null if the data array is empty.
     */
    public static function update(string $table, int $id, array $data, bool $timestamps = true): ?int {
        try {
            if (empty($data)) {
                Logger::error("Data array cannot be empty.");
                return null; 
            }
    
            if ($timestamps) {
                $data['updated_at'] = self::getTimestamps()['updated_at'];
            }
    
            $setQuery = [];
            foreach ($data as $key => $value) {
                $setQuery[] = "`{$key}` = ?";
            }
            
            $setQueryString = implode(', ', $setQuery);
            $query = "UPDATE `{$table}` SET {$setQueryString} WHERE `ID` = ?";
    
            $stmt = self::connect()->prepare($query);
            $params = array_merge(array_values($data), [$id]);
    
            if (!$stmt->execute($params)) {
                $errorInfo = $stmt->errorInfo();
                Logger::error("update: SQL Error: {$errorInfo[2]} - Query: {$query} - Data: " . json_encode($params));
                return null;
            }
    
            return $stmt->rowCount(); // Return the number of affected rows
        } catch (PDOException $e) {
            Logger::error("update: SQL Error: {$e->getMessage()} - Query: {$query} - Data: " . json_encode($params));
            return null;
        }
    }

    /**
     * Delete a record from a specified table by ID.
     *
     * @param string $table The name of the table to delete from.
     * @param int $id The ID of the record to delete.
     * @return int The number of rows affected by the delete operation.
     */
    public static function delete(string $table, int $id) {
        $query = "DELETE FROM `{$table}` WHERE `id` = ?";
        $stmt = self::connect()->prepare($query);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    /**
     * Get the last inserted ID.
     *
     * @return int The last inserted ID.
     */
    public static function getLastInsertId() {
        return self::connect()->lastInsertId();
    }

    /**
     * Get the number of rows in a specified table.
     *
     * @param string $table The name of the table to count rows from.
     * @return int The number of rows in the table.
     */
    public static function getRowCount(string $table) {
        $query = "SELECT COUNT(*) FROM `{$table}`";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Get the columns of a specified table.
     *
     * @param string $table The name of the table to retrieve columns from.
     * @return array An associative array containing the column details.
     */
    public static function getTableColumns(string $table) {
        $query = "SHOW COLUMNS FROM `{$table}`";
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get records from a specified table.
     *
     * @param string $table The name of the table to query.
     * @param array $columns The columns to select. Defaults to all columns.
     * @param string $by Determines the order of the results. Defaults to 'latest', which orders by ID descending.
     * @return array The fetched records as an associative array.
     */
    public static function get(string $table, array $columns = ['*'], string $by = 'latest') {
        $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}`";
        if ($by === 'latest') {
            $query = "SELECT " . implode(', ', $columns) . " FROM `{$table}` ORDER BY `id` DESC";
        }
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
 
    /**
     * Get timestamps
     * @param string $timestamp
     * @return array
     */
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

    /**
     * Checks if a user is assigned as an editor to a specific post.
     *
     * This function retrieves the list of assigned editors for a given post and checks if the specified user
     * is included in that list. It first calls the `getAssignedEditorByPostId` method to get the editor IDs
     * associated with the post. If the list of editor IDs is an array, it then checks if the user ID is present
     * in that array.
     *
     * @param int $user_id The ID of the user to check.
     * @param int $post_id The ID of the post to check.
     * @return bool Returns true if the user is assigned as an editor to the post, false otherwise.
     */
    public static function hasAssignedEditor(int $user_id, int $post_id) {
        $assigned_editors = self::getAssignedEditorByPostId($post_id, true, true);
        
        if(!is_array($assigned_editors)) return false;
        
        return in_array($user_id, $assigned_editors, true);
    }

    /**
     * Removes an editor from a post.
     *
     * This function removes a specific user (editor) from the list of assigned editors for a given post.
     * It first retrieves the current list of assigned editors for the post, then removes the specified user
     * from that list, and finally updates the database with the new list of editors.
     *
     * @param int $user_id The ID of the user (editor) to be removed.
     * @param int $post_id The ID of the post from which the editor should be removed.
     * @return bool Returns true if the editor was successfully removed and the database was updated, false otherwise.
     */
    public static function removeEditorFromPost(int $user_id, int $post_id) {
        $assigned_editors = self::getAssignedEditorByPostId($post_id, true, true);
        
        if(!is_array($assigned_editors)) return false;
        $assigned_editors = array_diff($assigned_editors, [$user_id]);

        return self::updateByColumns(self::LISTING_EDITORS, ['post_id' => $post_id], ['editor_ids' => json_encode($assigned_editors)]);
    }

    public static function inArray(string $needle, $haystack): bool {
        if(!is_array($haystack)) return false;

        if(isset($haystack[$needle]) && !empty($haystack[$needle]) && $haystack[$needle]) {
            return true;
        }
        return false;
    }

    /**
     * Get deals with filtering, pagination and related data from leads and properties
     * 
     * @param int    $userId       User ID to filter deals by. If 0, returns all deals
     * @param array  $filters      Array of filter options:
     *                            - property_id (int): Filter by property/listing ID
     *                            - lead_id (int): Filter by lead ID
     *                            - agent_id (int): Filter by agent ID
     *                            - deal_title (string): Search deals by title
     *                            - next_action (string): Filter by next action
     *                            - due_date (string): Filter by due date (YYYY-MM-DD format)
     *                            - deal_group (string): Filter by deal group ('active'|'won'|'lost')
     *                            - lead_email (string): Filter by lead's email
     *                            - lead_mobile (string): Filter by lead's mobile number
     *                            - status (string): Filter by deal status (e.g. 'New Lead', 'Meeting Scheduled', etc)
     * @param int    $itemsPerPage Number of items to return per page
     * @param int    $currentPage  Current page number
     * 
     * @return array|null Returns array containing:
     *                    - results: Array of deal objects with joined lead and property data
     *                    - total_records: Total number of deals matching filters
     *                    - items_per_page: Number of items per page
     *                    - page: Current page number
     *                    - totals: Count of deals by deal_group (active/won/lost)
     *                    Returns null on error
     */
    public static function getDeals(
        int $userId = 0,
        array $filters = [
            'property_id' => 0,
            'lead_id' => 0,
            'agent_id' => 0,
            'deal_title' => '',
            'next_action' => '',
            'start_due_date' => '',
            'end_due_date' => '',
            'deal_group'=> '',
            'lead_email'=> '',
            'lead_mobile'=> '',
            'status'=> '',
        ], 
        $itemsPerPage = 10,
        $currentPage = 1, 
    ) {
        // validate filter property
        $validateFilterProperty = fn (string $key) => self::inArray($key, $filters);

        // Calculate offset based on current page and items per page
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Get deal IDs that this user can access as an editor
        $inIDs = self::getDealIdsByAssignedPostEditors($userId);

        // Define table names
        $table = self::HOUZEZ_CRM_DEALS;
        $leadsTable = self::HOUZEZ_CRM_LEADS;
        $postsTable = self::POSTS;
        
        // Build main query joining deals, leads and posts tables
        $query = "SELECT d.*, 
                l.lead_id AS lead_id,
                l.first_name AS lead_first_name,
                l.last_name AS lead_last_name, 
                l.email AS lead_email,
                l.mobile AS lead_mobile,
                l.display_name AS lead_display_name,
                p.post_title AS property_title,
                p.post_name AS property_slug 
            FROM `{$table}` AS d 
            LEFT JOIN `{$leadsTable}` AS l ON d.lead_id = l.lead_id 
            LEFT JOIN `{$postsTable}` AS p ON d.listing_id = p.ID";
        
        // Build count query to get totals by deal group
        $countQuery = "SELECT 
            COUNT(CASE WHEN deal_group = 'active' THEN 1 END) AS active,
            COUNT(CASE WHEN deal_group = 'lost' THEN 1 END) AS lost,
            COUNT(CASE WHEN deal_group = 'won' THEN 1 END) AS won
        FROM `{$table}` AS d 
        LEFT JOIN `{$leadsTable}` AS l ON d.lead_id = l.lead_id";

        // Add user filter condition
        if($userId) {
            $query .= " WHERE d.user_id = :user_id ";
            $countQuery .= " WHERE d.user_id = :user_id ";
        } else {
            $query .= " WHERE 0 = :user_id ";
            $countQuery .= " WHERE 0 = :user_id ";
        }

        // Initialize arrays for conditions and parameters
        $conditions = [];
        $parameters = [':offset' => $offset, ':items_per_page' => $itemsPerPage, ':user_id' => $userId];

        // Add condition for assigned editor IDs if any exist
        if(!empty($inIDs)) {
            $ids = implode(',', $inIDs);
            $conditions[] = "OR d.deal_id IN ({$ids}) ";
        }

        // Add filter conditions based on provided filters
        if ($validateFilterProperty('property_id')) {
            $conditions[] = "AND d.listing_id = :property_id";
            $parameters[':property_id'] = $filters['property_id'];
        }
        if ($validateFilterProperty('lead_id')) {
            $conditions[] = "AND d.lead_id = :lead_id";
            $parameters[':lead_id'] = $filters['lead_id'];
        }
        if ($validateFilterProperty('agent_id')) {
            $conditions[] = "AND d.agent_id = :agent_id";
            $parameters[':agent_id'] = $filters['agent_id'];
        }
        if ($validateFilterProperty('deal_title')) {
            $conditions[] = "AND d.title LIKE CONCAT('%', :deal_title, '%')";
            $parameters[':deal_title'] = $filters['deal_title'];
        }
        if ($validateFilterProperty('next_action')) {
            $conditions[] = "AND d.next_action LIKE CONCAT('%', :next_action, '%')";
            $parameters[':next_action'] = $filters['next_action'];
        }
        if ($validateFilterProperty('start_due_date') && $validateFilterProperty('end_due_date')) {
            $conditions[] = "AND d.action_due_date BETWEEN :start_due_date AND :end_due_date";
            $parameters[':start_due_date'] = $filters['start_due_date'] . ' 00:00:00';
            $parameters[':end_due_date'] = $filters['end_due_date'] . ' 23:59:59';
        }
        if ($validateFilterProperty('status')) {
            $conditions[] = "AND d.status LIKE CONCAT('%', :status, '%')";
            $parameters[':status'] = $filters['status'];
        }
        if ($validateFilterProperty('lead_email')) {
            $conditions[] = "AND l.email LIKE CONCAT('%', :lead_email, '%')";
            $parameters[':lead_email'] = $filters['lead_email'];
        }
        if($validateFilterProperty('lead_mobile')) {
            $conditions[] = "AND l.mobile LIKE CONCAT('%', :lead_mobile, '%')";
            $parameters[':lead_mobile'] = $filters['lead_mobile'];
        }

        // Combine all conditions
        $q1 = implode(' ', $conditions);
        $countQuery .= "{$q1}";

        // Add deal group filter and pagination if specified
        if($validateFilterProperty('deal_group')) {
            $query .= "{$q1} AND d.deal_group = :deal_group ORDER BY d.deal_id DESC LIMIT :offset, :items_per_page";
            $parameters[':deal_group'] = $filters['deal_group'];
        } else {
            $query .= "{$q1} ORDER BY d.deal_id DESC LIMIT :offset, :items_per_page";
        }
        
        try {
            // Get database connection
            $connect = self::connect();

            // Prepare statements
            $groupCountStmt = $connect->prepare($countQuery);
            $stmt = $connect->prepare($query);
            
            // Parameters to exclude from count query
            $ignoreParamsInCountQuery = [':offset', ':items_per_page', ':deal_group'];

            // Bind parameters to both queries
            foreach ($parameters as $key => $value) {
                $param = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                
                $stmt->bindValue($key, $value, $param);
                $d = compact('key', 'value', 'param'); 

                if (!in_array($key, $ignoreParamsInCountQuery)) {
                    $groupCountStmt->bindValue($key, $value, $param);
                }
            }
            
            // Execute queries
            $stmt->execute();
            $groupCountStmt->execute();
            
            // Get results
            $groupCounts = $groupCountStmt->fetch(PDO::FETCH_ASSOC);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ); 

            // Count total records
            $total = $stmt->rowCount();

            // Return formatted response
            return [
                'data' => [
                    'results' => $results,
                    'total_records' => $total,
                    'items_per_page' => $itemsPerPage,
                    'page' => $currentPage,
                ],
                'group_counts'=> $groupCounts,
                'filtered_by' => $filters,
                'deal_ids_from_assigned_editors' => $inIDs,
                'message' => 'Deals retrieved successfully', 
            ];
            
        } catch (PDOException $e) {
            // Log error and return null on failure
            $DB = [
                'MAIN_QUERY' => $query,
                'COUNT_QUERY' => $countQuery,
                'CONDITIONS' => $conditions,
                'PARAMETERS' => $parameters,
            ];
            Logger::error(
                "getDeals: SQL Error: {$e->getMessage()} - Query:\n\t {$query}\n\n", 
                compact('userId', 'filters', 'inIDs', 'conditions', 'parameters', 'query', 'countQuery', 'DB')
            );
            return null;
        }
    }

    /**
     * Retrieve leads with filtering and pagination support.
     * 
     * @param int $userId The ID of the user whose leads are to be retrieved. If 0, retrieves all leads.
     * @param array $filters Array of filter parameters:
     *      - keyword (string): Search across display_name, mobile, email, first_name, last_name fields
     *      - name (string): Filter by display_name field
     *      - phone (string): Filter by mobile, home_phone or work_phone fields
     *      - date (string): Filter by exact date in time field
     *      - referrer (string): Filter by source field
     * @param int $itemsPerPage Number of leads to return per page
     * @param int $page Current page number
     * @return array|null Returns array of leads with pagination data or null on failure
     */
    public static function getLeads(
        int $userId, 
        array $filters = [
            'keyword' => '',
            'name' => '',
            'phone' => '',
            'start_date' => '',
            'end_date' => '',
            'referrer' => '',
        ], 
        int $itemsPerPage = 10, 
        int $page = 1
    ) {
        $validateFilterProperty = fn (string $key) => self::inArray($key, $filters);

        try {
            $table = self::HOUZEZ_CRM_LEADS;
            $offset = ($page * $itemsPerPage) - $itemsPerPage;

            // Start with a basic query
            $query = "SELECT * FROM `{$table}`";

            // Add user condition if userId is not zero
            if ($userId) {
                $query .= " WHERE user_id = :user_id";
            } else {
                $query .= " WHERE 0 = :user_id";
            }

            $parameters = [':user_id' => $userId, ':offset' => $offset, ':items_per_page' => $itemsPerPage];

            // If keyword is present, modify the query to include search condition
            if ($validateFilterProperty('keyword')) {
                $query .= " AND (display_name LIKE :keyword OR mobile LIKE :keyword OR email LIKE :keyword OR first_name LIKE :keyword OR last_name LIKE :keyword)";
                $parameters[':keyword'] = "%{$filters['keyword']}%";
            }
            if ($validateFilterProperty('name')) {
                $query .= " AND (display_name LIKE :name)";
                $parameters[':name'] = "%{$filters['name']}%"; 
            }
            if ($validateFilterProperty('phone')) {
                $query .= " AND (mobile LIKE :phone OR home_phone LIKE :phone OR work_phone LIKE :phone)";
                $parameters[':phone'] = "%{$filters['phone']}%";
            }
            if ($validateFilterProperty('referrer')) {
                $query .= " AND (source LIKE :referrer)";
                $parameters[':referrer'] = "%{$filters['referrer']}%";
            }
            if ($validateFilterProperty('start_date') && $validateFilterProperty('end_date')) {
                $query .= " AND time BETWEEN :start_date AND :end_date";
                $parameters[':start_date'] = $filters['start_date'];
                $parameters[':end_date'] = $filters['end_date'];
            }

            // Prepare results query with limit and offset
            $query .= " ORDER BY lead_id DESC LIMIT :offset, :items_per_page";
            $stmt = self::connect()->prepare($query);
            
            foreach ($parameters as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);

            $returnArray = [
                'data' => [
                    'results' => $results,
                    'total_records' => $stmt->rowCount(),
                    'items_per_page' => $itemsPerPage,
                    'page' => $page,
                ]
            ];

            return $returnArray;
        } catch (PDOException $e) {
            Logger::error("getAllLeads: SQL Error: {$e->getMessage()} - Query: {$query}", compact('userId', 'keyword', 'itemsPerPage', 'page'));
            return null;
        }
    }

    /**
     * Retrieves deal IDs associated with posts that a specific editor is assigned to.
     *
     * This method performs the following steps:
     * 1. Gets all post IDs that the given editor user is assigned to
     * 2. Uses those post IDs to find corresponding deals in the CRM deals table
     * 3. Returns an array of deal IDs that match the editor's assigned posts
     *
     * @param int $userId The ID of the editor user to check assignments for
     * @return array An array of deal IDs that correspond to the editor's assigned posts.
     *               Returns empty array if no matches found or on error.
     *
     * @throws PDOException If there is an error executing the database query
     * @uses DB::getPostIdsByEditorId() To get posts assigned to the editor
     * @uses Logger::error() To log any database errors that occur
     */

    public static function getDealIdsByAssignedPostEditors(int $userId) {
        $postIds = DB::getPostIdsByEditorId($userId);

        try {
            if(!empty($postIds)) {
                $dealsTable = DB::HOUZEZ_CRM_DEALS;
                $strPostIds = implode(',', $postIds);
                $query = "SELECT `deal_id` FROM {$dealsTable} WHERE `listing_id` IN ($strPostIds)";
                $getDealIdsByPostIds = self::connect()->query($query);
                $dealIds = array_column($getDealIdsByPostIds->fetchAll(PDO::FETCH_ASSOC), 'deal_id', 'numeric');
                return $dealIds;
            }
        } catch (PDOException $e) {
            Logger::error("getDealIdsByAssignedPostEditors: SQL Error: {$e->getMessage()} - Query: {$query}", compact('userId', 'postIds'));
            return [];
        }
    }

    /**
     * Get the total number of deals by group.
     *
     * This method retrieves the total count of deals for a specific deal group.
     * If a user ID is provided, it will filter the deals by that user ID.
     *
     * @param int $userId The ID of the user to filter deals by. If 0, it will not filter by user.
     * @param string $dealGroup The deal group to filter by (e.g., 'active', 'won', 'lost').
     * @return int The total number of deals in the specified group.
     */
    public static function getTotalDealsByGroup(int $userId, string $dealGroup): int {
        try {
            $table = self::HOUZEZ_CRM_DEALS;
    
            // Prepare total count query
            $query = "SELECT COUNT(*) FROM `{$table}` WHERE deal_group = :deal_group";
            $params = [':deal_group' => $dealGroup];
    
            // Conditionally add user_id if provided
            if ($userId) {
                $query .= " AND user_id = :user_id";
                $params[':user_id'] = $userId;
            }
    
            $stmt = self::connect()->prepare($query);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            return (int) $stmt->fetchColumn();
    
        } catch (PDOException $e) {
            Logger::error("getTotalDealsByGroup: SQL Error: {$e->getMessage()} - Query: {$query}", compact('userId', 'dealGroup'));
            return 0;
        }
    }    
    
    /**
     * Retrieves all inquiries with pagination and filtering options.
     * 
     * This method fetches inquiries from the CRM system with various filtering capabilities
     * and returns paginated results along with total count information.
     *
     * @param int $userId The user ID to filter inquiries by. If 0, returns inquiries for all users.
     * @param string $keyword Search keyword to filter inquiries by enquiry_type.
     * @param int $leadId Optional lead ID to filter inquiries for a specific lead. Default 0.
     * @param int $itemsPerPage Number of items to return per page. Default 10.
     * @param int $page Current page number. Default 1.
     * 
     * @return array|null Returns an array containing:
     *                    - data: Array containing:
     *                      - results: Array of inquiry objects
     *                      - total_records: Total number of records matching filters
     *                      - items_per_page: Number of items per page
     *                      - page: Current page number
     *                    Returns null on error
     *
     * @throws PDOException on database errors (caught internally)
     * 
     * Example usage:
     * $inquiries = getAllInquiries(5, 'rental', 123, 20, 1);
     * // Returns inquiries for user 5, filtered by 'rental' keyword,
     * // for lead 123, 20 items per page, first page
     */
    public static function getAllInquiries(int $userId, string $keyword, int $leadId = 0, int $itemsPerPage = 10, int $page = 1) {
        try {
            $table = self::HOUZEZ_CRM_ENQUIRIES;
            $offset = ($page - 1) * $itemsPerPage;
            $params = [];
            
            // Start with a base query
            $query = "SELECT * FROM `{$table}` WHERE 1=1";
            $countQuery = "SELECT COUNT(*) FROM `{$table}` WHERE 1=1";
    
            // Add user filter only if $userId is set
            if ($userId) {
                $query .= " AND user_id = :user_id";
                $countQuery .= " AND user_id = :user_id";
                $params[':user_id'] = $userId;
            }
    
            // Add conditions for lead_id and keyword dynamically
            if ($leadId > 0) {
                $query .= " AND lead_id = :lead_id";
                $countQuery .= " AND lead_id = :lead_id";
                $params[':lead_id'] = $leadId;
            }
    
            if (!empty($keyword)) {
                $query .= " AND enquiry_type LIKE :keyword";
                $countQuery .= " AND enquiry_type LIKE :keyword";
                $params[':keyword'] = "%$keyword%";
            }
    
            // Add ordering and pagination
            $query .= " ORDER BY enquiry_id DESC LIMIT :offset, :items_per_page";
            $params[':offset'] = $offset;
            $params[':items_per_page'] = $itemsPerPage;
    
            // Execute main query
            $stmt = self::connect()->prepare($query);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
    
            // Execute count query for total records
            $countStmt = self::connect()->prepare($countQuery);

            foreach ($params as $param => $value) {
                if (!in_array($param, [':offset', ':items_per_page'])) {
                    $countStmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }

            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();
    
            // Logger::info("getAllInquiries: Total records: {$total}", compact('userId', 'keyword', 'leadId', 'itemsPerPage', 'page', 'query'));
            // Return structured response
            return [
                'data' => [
                    'results' => $results,
                    'total_records' => $total,
                    'items_per_page' => $itemsPerPage,
                    'page' => $page,
                ],
            ];
    
        } catch (PDOException $e) {
            Logger::error("getAllInquiries: SQL Error: {$e->getMessage()} - Query: {$query}", compact('userId', 'keyword', 'leadId', 'itemsPerPage', 'page'));
            return null;
        }
    }
    
    /**
     * Executes a raw SQL query with optional parameter binding
     * 
     * @param string $query The SQL query to execute
     * @param array $data Optional array of parameters to bind to the query. Keys should match parameter names in query.
     * @param bool $isReturnStmt Whether to return the PDOStatement object (true) or fetch results (false)
     * 
     * @return mixed Returns one of:
     *               - PDOStatement if $isReturnStmt is true
     *               - Array of objects containing query results if successful
     *               - false if query execution fails
     * 
     * @throws PDOException on database errors, which are caught and logged
     * 
     * Example usage:
     * $results = executeRawQuery("SELECT * FROM users WHERE id = :id", [':id' => 5]);
     * $stmt = executeRawQuery("SELECT * FROM users", [], true);
     */
    public static function executeRawQuery(string $query, array $data = [], bool $isReturnStmt = false) {
        try {
            $stmt = self::connect()->prepare($query);
            if(!empty($data)) {
                foreach ($data as $key => $value) {
                    $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            if($isReturnStmt) {
                return $stmt;
            }
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            Logger::error("executeRaqQuery: SQL Error: {$e->getMessage()} - Query: {$query}");
            return false;
        }
    }
}


trait OlderDB {
    /**
     * Retrieves all leads based on the provided parameters.
     *
     * @param int $userId The ID of the user. If 0, retrieves leads for all users.
     * @param string $keyword The search keyword to filter leads by mobile, email, first name, or last name.
     * @param int $itemsPerPage The number of leads to retrieve per page. Default is 10.
     * @param int $page The page number to retrieve. Default is 1.
     * @return array An array containing the results, total records, items per page, and current page.
     */
    public static function getAllLeads(int $userId, string $keyword, int $itemsPerPage = 10, int $page = 1) {
        try {
            $table = DB::HOUZEZ_CRM_LEADS;
            $offset = ($page * $itemsPerPage) - $itemsPerPage;

            // Start with a basic query
            $query = "SELECT * FROM `{$table}`";

            // Add user condition if userId is not zero
            if ($userId) {
                $query .= " WHERE user_id = :user_id";
            }

            // If keyword is present, modify the query to include search condition
            if (!empty($keyword)) {
                $keywordCondition = " (mobile LIKE :keyword OR email LIKE :keyword OR first_name LIKE :keyword OR last_name LIKE :keyword)";
                $query .= ($userId) ? " AND" : " WHERE"; // Check if WHERE needs to be added
                $query .= $keywordCondition;
            }

            // Prepare total count query
            $totalQuery = "SELECT COUNT(*) FROM ({$query}) AS combined_table";
            $stmt = DB::connect()->prepare($totalQuery);

            if ($userId) {
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            }

            if (!empty($keyword)) {
                $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
            }

            $stmt->execute();
            $total = $stmt->fetchColumn();

            // Prepare results query with limit and offset
            $query .= " ORDER BY lead_id DESC LIMIT :offset, :items_per_page";
            $stmt = DB::connect()->prepare($query);

            if ($userId) {
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            }

            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':items_per_page', $itemsPerPage, PDO::PARAM_INT);

            if (!empty($keyword)) {
                $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);

            $returnArray = [
                'data' => [
                    'results' => $results,
                    'total_records' => $total,
                    'items_per_page' => $itemsPerPage,
                    'page' => $page,
                ]
            ];

            return $returnArray;
        } catch (PDOException $e) {
            Logger::error("getAllLeads: SQL Error: {$e->getMessage()} - Query: {$query}", compact('userId', 'keyword', 'itemsPerPage', 'page'));
            return null;
        }
    }
 
    /**
     * Retrieve all deals for a specific user and deal group with pagination.
     *
     * @param int $userId The ID of the user whose deals are to be retrieved.
     * @param string $dealGroup The group of deals to retrieve (e.g., 'active', 'won', 'lost').
     * @param array $inIDs An array of deal IDs to filter.
     * @param int $itemsPerPage The number of deals to retrieve per page.
     * @param int $page The page number to retrieve.
     * @return array|null The results in an associative array or null on failure.
     */
    public static function getAllDeals(int $userId, string $dealGroup = 'active', $inIDs = [], int $itemsPerPage = 10, int $page = 1) {
        try {
            $table = DB::HOUZEZ_CRM_DEALS;
            $offset = ($page - 1) * $itemsPerPage;

            // Start with a basic query
            if($userId) {
                $baseQuery = "FROM `{$table}` WHERE (user_id = :user_id AND deal_group = :deal_group)";
            } else {
                $baseQuery = "FROM `{$table}` WHERE (user_id = :user_id OR deal_group = :deal_group)";
            }

            // Add OR condition for deal_id IN (...)
            if (is_array($inIDs) && !empty($inIDs)) {
                $inIDsString = implode(',', $inIDs); // Convert array to comma-separated string
                $baseQuery .= " OR deal_id IN ({$inIDsString})";
            }

            // Prepare total count query
            $totalQuery = "SELECT COUNT(*) {$baseQuery}";
            $stmt = DB::connect()->prepare($totalQuery);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':deal_group', $dealGroup, PDO::PARAM_STR);
            $stmt->execute();
            $total = $stmt->fetchColumn();

            // Prepare results query with ORDER BY and LIMIT
            $query = "SELECT * {$baseQuery} ORDER BY deal_id DESC LIMIT :offset, :items_per_page";
            $stmt = DB::connect()->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':deal_group', $dealGroup, PDO::PARAM_STR);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':items_per_page', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Logger::info("getAllDeals: ", compact('userId', 'dealGroup', 'itemsPerPage', 'page', 'inIDs', 'query'));
            return [
                'data' => [
                    'results' => $results,
                    'total_records' => $total,
                    'items_per_page' => $itemsPerPage,
                    'page' => $page,
                ]
            ];
            
        } catch (PDOException $e) {
            Logger::error("getAllDeals: SQL Error: {$e->getMessage()} - Query: {$query}", compact('userId', 'dealGroup', 'itemsPerPage', 'page'));
            return null;
        }
    }
}
?>