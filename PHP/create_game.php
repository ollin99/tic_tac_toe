<?php
require '../PHP/db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$playerX = $data['playerX'] ?? null;
$playerO = $data['playerO'] ?? null;

//Om användaren har vald två olika spelare används detta i select_players.js, här skapar vi ett nytt spel i databasen
if ($playerX && $playerO && $playerX !== $playerO) {
    $initialBoard = json_encode(["", "", "", "", "", "", "", "", ""]); 
    $currentPlayer = 'X';
    $winner = null;

    $stmt = $db->prepare("
        INSERT INTO games (player_x, player_o, board_state, current_player, winner)
        VALUES (?, ?, ?, ?, ?)
    ");
    $success = $stmt->execute([$playerX, $playerO, $initialBoard, $currentPlayer, $winner]);

    $gameId = $db->lastInsertId();

    echo json_encode(['success' => $success, 'game_id' => $gameId]);
} else {
    echo json_encode(['success' => false]);
}
