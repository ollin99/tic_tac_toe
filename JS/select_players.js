document.addEventListener("DOMContentLoaded", () => {
    const selectX = document.getElementById("select-x");
    const selectO = document.getElementById("select-o");
    const btnStart = document.getElementById("btn-start-game");
    const btnBack = document.getElementById("btn-back");

    //Hämtar spelare från databasen med hjälp av get_player.php
    fetch('../PHP/get_players.php')
      .then(res => res.json())
      .then(data => {
        data.players.forEach(player => {
          const optionX = document.createElement("option");
          optionX.value = player.player_id;
          optionX.textContent = player.player_name;
          selectX.appendChild(optionX);

          const optionO = document.createElement("option");
          optionO.value = player.player_id;
          optionO.textContent = player.player_name;
          selectO.appendChild(optionO);
        });
      })
      .catch(err => console.error("Error fetching players:", err));

    //Skapar ett nytt spel i databasen med hjälp av create_game.php och skickar sedan det gameId vidare till själva spelet (game.html)
    btnStart.addEventListener("click", () => {
      const xId = selectX.value;
      const oId = selectO.value;
      if (xId === oId) {
        alert("You cannot choose the same player for both X and O!");
        return;
      }
      fetch('../PHP/create_game.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          playerX: xId,
          playerO: oId
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.href = `../HTML/game.html?game_id=${data.game_id}`;
          
        } else {
          alert("Failed to create game.");
        }
      });
    });

    btnBack.addEventListener("click", () => {
      window.location.href = "../HTML/menu.html";
    });
  });