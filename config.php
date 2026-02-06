<?php
// Absoluter Pfad zum Projektroot auf dem Server
define('ROOT_PATH', rtrim(__DIR__, '/\\'));

// Basis URL relativ zur Webroot
// Passe '/PROJEKT_KUNDENAUFTRAG' an, falls dein Projektordner anders heißt
define('BASE_URL', '/PROJEKT_KUNDENAUFTRAG');

// Asset Version für Cache Busting
define('ASSET_VERSION', '1');


$host = "127.0.0.1;port=3306"; 
$db   = "db_hofladen";
$user = "root";
$pass = "";

try {
$pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
