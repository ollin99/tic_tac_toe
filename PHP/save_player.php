<?php
require '../PHP/db_connection.php';

//Skapar en användaren i select_player.js och stoppar in den i databaser på detta sätt

$data = json_decode(file_get_contents("php://input"), true);
$playerName = $data['name'] ?? null;

if ($playerName) {
    $stmt = $db->prepare("INSERT INTO players (player_name) VALUES (?)");
    $success = $stmt->execute([$playerName]);

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}