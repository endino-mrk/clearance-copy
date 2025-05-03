<?php

// config/database_bootstrap.php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

// Load database configuration
$dbConfig = require __DIR__ . '/database.php';

$capsule->addConnection([
    'driver'    => 'mysql', // or pgsql, sqlite, sqlsrv
    'host'      => $dbConfig['host'],
    'port'      => $dbConfig['port'],
    'database'  => $dbConfig['dbname'],
    'username'  => $dbConfig['username'],
    'password'  => $dbConfig['password'],
    'charset'   => $dbConfig['charset'],
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// Set the event dispatcher used by Eloquent models... (optional)
// $capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional)
$capsule->bootEloquent();

return $capsule; // Optionally return the capsule instance 