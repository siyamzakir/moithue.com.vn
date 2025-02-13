<?php
require_once __DIR__ . '/../main-config.php';

// $pdo = new PDO(
//     DATABASE_CONFIGURATION['driver'] 
//         . ':host=' . DATABASE_CONFIGURATION['host'] 
//         . ';dbname=' . DATABASE_CONFIGURATION['database'],
//     DATABASE_CONFIGURATION['username'],
//     DATABASE_CONFIGURATION['password'],
//     [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
// );

// $users_query = $pdo->query('SELECT `ID`, `user_email`, `display_name` FROM `wp_users`');

// $users = $users_query->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/DB.php';
$tables = DB::showAllTables();
$tables = array_map(function($table) {
    return $table['Tables_in_' . DATABASE_CONFIGURATION['database']];
}, $tables);


$migrated = null;
$drop = null;

$has_assigned_editor = DB::hasAssignedEditor(1, 361);
$get_editors = DB::get(DB::LISTING_EDITORS);
$get_post_ids = DB::getPostIdsByEditorId(4);

// migrate listing editors table
// $drop = DB::dropTable('wp_listing_editors');
// $migrated = DB::migrateListingEditorsTable();

echo PHP_EOL
    . json_encode(compact(/* 'tables', 'migrated', 'drop', 'get_editors',  */'get_post_ids', 'has_assigned_editor')) . 
PHP_EOL;
?>