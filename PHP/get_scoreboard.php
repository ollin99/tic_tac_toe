<?php
require '../PHP/db_connection.php';

//Hämtar vissa kolumner från databasen som ska visas i en scoreboard

$sql = "
SELECT 
  g.game_id,
  g.winner,
  px.player_name AS player_x_name,
  po.player_name AS player_o_name
FROM games g
JOIN players px ON g.player_x = px.player_id
JOIN players po ON g.player_o = po.player_id
WHERE g.winner IS NOT NULL
ORDER BY g.game_id DESC
";

$stmt = $db->prepare($sql);
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'games' => $games
]);
