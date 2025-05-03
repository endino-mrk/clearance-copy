<?php

// Load our separate database config to avoid duplication
$dbConfig = require __DIR__ . '/config/database.php';

return
[
    'paths' => [
        // Point to our existing database/migrations directory
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds' // Keep seeds separate
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog', // Table Phinx uses to track migrations
        'default_environment' => 'development', // Environment to use by default
        'development' => [
            'adapter' => 'mysql', // Keep adapter as mysql
            'host' => $dbConfig['host'],
            'name' => $dbConfig['dbname'],
            'user' => $dbConfig['username'],
            'pass' => $dbConfig['password'],
            'port' => $dbConfig['port'],
            'charset' => $dbConfig['charset'],
            'collation' => 'utf8mb4_unicode_ci' // Good practice to specify collation
        ],
        // You can configure production/testing environments similarly if needed
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ]
    ],
    'version_order' => 'creation'
];
