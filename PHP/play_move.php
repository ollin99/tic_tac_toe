<?php
require '../PHP/db_connection.php';

//Hämtar inputs som kommer via POST från game_logic.js
$data = json_decode(file_get_contents("php://input"), true);
$cellIndex = $data['cell'] ?? null;
$gameId = $data['gameId'] ?? null;

if ($cellIndex === null || !$gameId) {
    echo json_encode([
        'board' => ["", "", "", "", "", "", "", "", ""],
        'status' => "Missing data"
    ]);
    exit;
}

//Detta är till för att kunna visa de spelande användarna
$sql = "
    SELECT 
      g.*, 
      px.player_name AS player_x_name, 
      po.player_name AS player_o_name
    FROM games g
    JOIN players px ON g.player_x = px.player_id
    JOIN players po ON g.player_o = po.player_id
    WHERE g.game_id = :gid
";
$stmt = $db->prepare($sql);
$stmt->execute(['gid' => $gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    // Om spelet inte finns
    echo json_encode([
        'board' => ["", "", "", "", "", "", "", "", ""],
        'status' => "No game found!"
    ]);
    exit;
}

$board = json_decode($game['board_state'], true);
$currentPlayer = $game['current_player'];
$winner = $game['winner'];

// Sätter vinnare eller lika
if ($winner) {
    if ($winner === 'X') {
        $displayStatus = "Winner: " . $game['player_x_name'] . " (X)";
    } elseif ($winner === 'O') {
        $displayStatus = "Winner: " . $game['player_o_name'] . " (O)";
    } else {
        $displayStatus = "It's a draw!";
    }
    echo json_encode([
        'board' => $board,
        'status' => $displayStatus
    ]);
    exit;
}

// Kolla om rutan är ledig
if ($board[$cellIndex] === "") {
    $board[$cellIndex] = $currentPlayer;
    $winner = checkWinner($board);

    // Om ingen vinnare, växla spelare. Om vi har en vinnare behåll current_player och winner
    if (!$winner) {
        $nextPlayer = ($currentPlayer === 'X') ? 'O' : 'X';
    } else {
        $nextPlayer = $currentPlayer;
    }

    // Uppdatera databasen: bräde, current_player och winner (om någon vann)
    $updateQuery = $db->prepare("
        UPDATE games
        SET board_state = :board_state,
            current_player = :next_player,
            winner = :winner
        WHERE game_id = :game_id
    ");
    $updateQuery->execute([
        ':board_state' => json_encode($board),
        ':next_player' => $winner ? $currentPlayer : $nextPlayer, 
        ':winner' => $winner, 
        ':game_id' => $gameId
    ]);

    // Bygg $displayStatus beroende på vinnare och vems tur det är
    if ($winner) {
        // 'X', 'O' eller 'D'
        if ($winner === 'X') {
            $displayStatus = "Winner: " . $game['player_x_name'] . " (X)";
        } elseif ($winner === 'O') {
            $displayStatus = "Winner: " . $game['player_o_name'] . " (O)";
        } else {
            $displayStatus = "It's a draw!";
        }
    } else {
        // Ingen vinnare, nextPlayer har turen
        if ($nextPlayer === 'X') {
            $displayStatus = "Player " . $game['player_x_name'] . " (X)'s turn";
        } else {
            $displayStatus = "Player " . $game['player_o_name'] . " (O)'s turn";
        }
    }

} 

echo json_encode([
    'board' => $board,
    'status' => $displayStatus
]);

//Denna funktion läser av hur spelbrädet ser ut och försöker hitta om det finns någon som har vunnit 
function checkWinner($board) {
    //Första raden är möjliga "rows" kombinationer, rad 2 'r "columns" kobinationer och den sista raden är diagonalt
    $winningCombos = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], 
        [0, 3, 6], [1, 4, 7], [2, 5, 8], 
        [0, 4, 8], [2, 4, 6]          
    ];

    //Kontrollera vinnande kombinationer
    foreach ($winningCombos as $combo) {
        if ($board[$combo[0]] !== ""
            && $board[$combo[0]] === $board[$combo[1]]
            && $board[$combo[0]] === $board[$combo[2]]) {
            return $board[$combo[0]]; 
        }
    }

    return in_array("", $board) ? null : 'D'; 
}
?>