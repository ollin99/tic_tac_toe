<?php
//Den här filen är till för att göra databasen tillgängligt för all andra php-filer, med hjälp av "require 'db_connection.php'"

$host = 'localhost';
$dbName = 'tic_tac_toe_db'; 
$user = 'root';             
$pass = '';                 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

try {
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}