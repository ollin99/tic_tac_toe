(function() { 
  let scoreboardModal, scoreboardContainer, closeButton;

  //Funktion som initierar scoreboard-elementen
  function initScoreboard() {
    scoreboardModal = document.getElementById("scoreboard-modal");
    scoreboardContainer = document.getElementById("scoreboard");
    closeButton = document.getElementById("close-modal");

    closeButton.addEventListener("click", () => {
      scoreboardModal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
      if (event.target === scoreboardModal) {
        scoreboardModal.style.display = "none";
      }
    });
  }

  // Öppnar scoreboarden och hämtar data + visar modalen
  function openScoreboard() {
    scoreboardModal.style.display = "block";
    fetch('../PHP/get_scoreboard.php')
      .then(res => res.json())
      .then(data => {
        renderScoreboard(data.games);
      })
      .catch(err => console.error("Error loading scoreboard:", err));
  }

  // Ritar upp scoreboard tabellen
  function renderScoreboard(games) {
    scoreboardContainer.innerHTML = ""; 
    let html = `<h2>Scoreboard</h2>`;
    html += `<table border="1" style="margin: auto;">`;
    html += `<tr><th>Game ID</th><th>Player X</th><th>Player O</th><th>Winner</th></tr>`;

    games.forEach(game => {
      let winnerText = "";
      if (game.winner === "X") {
        winnerText = `${game.player_x_name} (X)`;
      } else if (game.winner === "O") {
        winnerText = `${game.player_o_name} (O)`;
      } else {
        winnerText = "Draw"; 
      }
      html += `
        <tr>
          <td>${game.game_id}</td>
          <td>${game.player_x_name}</td>
          <td>${game.player_o_name}</td>
          <td>${winnerText}</td>
        </tr>
      `;
    });

    html += `</table>`;
    scoreboardContainer.innerHTML = html;
  }

  document.addEventListener("DOMContentLoaded", () => {
    initScoreboard();
  });

  // Exponerar "openScoreboard" funktionen i ett global scope
  window.openScoreboard = openScoreboard;
})();
