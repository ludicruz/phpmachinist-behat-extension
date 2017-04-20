<?php
// Setup Mysql Server For Test
$pdo = new PDO('mysql:host=localhost;port=3307', 'root', null, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$pdo->exec("DROP DATABASE IF EXISTS testdb");
$pdo->exec("CREATE DATABASE testdb");
$pdo->exec("USE testdb");
$pdo->exec("CREATE TABLE user (id INTEGER PRIMARY KEY AUTO_INCREMENT, username VARCHAR(64), companyId INTEGER, active BIT)");
$pdo->exec("CREATE TABLE company (id INTEGER PRIMARY KEY AUTO_INCREMENT, name VARCHAR(64))");


// Setup PostgreSQL Server For Test
$pdo = new PDO('pgsql:host=localhost;dbname=machinist_test', null, null, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$pdo->exec("DROP TABLE IF EXISTS \"user\"");
$pdo->exec("DROP TABLE IF EXISTS \"company\"");
$pdo->exec("CREATE TABLE \"user\" (id SERIAL PRIMARY KEY, username VARCHAR(64), companyid INTEGER, active BOOLEAN)");
$pdo->exec("CREATE TABLE company (id SERIAL PRIMARY KEY, name VARCHAR(64))");



// Setup SQLite file for test
$file = '/tmp/phpMachinistExtensionBehatTest.sq3';
if (file_exists($file)) {
    unlink($file);
}

$pdo = new PDO('sqlite:' . $file, null, null, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$pdo->exec('CREATE TABLE user (id INTEGER PRIMARY KEY ASC, username, companyId INTEGER, active)');
$pdo->exec('CREATE TABLE company (id INTEGER PRIMARY KEY ASC, name)');
unset($pdo);
