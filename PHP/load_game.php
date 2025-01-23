<?php
require '../PHP/db_connection.php';

//Hämta game_id
$gameId = $_GET['game_id'];
if (!$gameId) {
    echo json_encode([ 'board' => array_fill(0, 9, ""), 'status' => "No game_id provided" ]);
    exit;
}

//Detta är till för att kunna visa de spelande användarna
$sql = "SELECT 
          g.*, 
          px.player_name AS player_x_name, 
          po.player_name AS player_o_name
        FROM games g
        JOIN players px ON g.player_x = px.player_id
        JOIN players po ON g.player_o = po.player_id
        WHERE g.game_id = :gid";

$stmt = $db->prepare($sql);
$stmt->execute(['gid' => $gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

//När spelet laddas in kommer denna sträng upp 
if (!$game) {
    echo json_encode([
        'board' => array_fill(0, 9, ""),
        'status' => "Player X starts"
    ]);
    exit;
}
$board = json_decode($game['board_state'], true);
$currentPlayer = $game['current_player'];
$winner = $game['winner'];

//När spelet laddas in anropas denna en gång egentligen bara för att säkerställa att winner inte har fått något felaktigt värde, nedanstående kod finns i play_move.db
if ($game['winner']) {
    if ($game['winner'] === 'X') {
        $status = "Winner: " . $game['player_x_name'] . " (X)";
    } elseif ($game['winner'] === 'O') {
        $status = "Winner: " . $game['player_o_name'] . " (O)";
    } else {
        $status = "It's a draw!";
    }
} else {
    if ($game['current_player'] === 'X') {
        $status = "Player " . $game['player_x_name'] . " (X)'s turn";
    } else {
        $status = "Player " . $game['player_o_name'] . " (O)'s turn";
    }
}

echo json_encode([
    'board' => json_decode($game['board_state'], true),
    'status' => $status
]);
