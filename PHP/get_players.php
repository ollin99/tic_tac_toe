<?php
require '../PHP/db_connection.php';

//Hämtar alla spelare från databastabeller players
$stmt = $db->prepare("SELECT player_id, player_name FROM players");
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['players' => $players]);
