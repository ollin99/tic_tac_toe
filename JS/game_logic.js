document.addEventListener("DOMContentLoaded", function () {
    const board = document.getElementById("board");
    const statusElem = document.getElementById("status");
    const btnBack = document.getElementById("btn-back");
    const gameOverModal = document.getElementById("game-over-modal");
    const closeGameOverModal = document.getElementById("close-gameover-modal");
    const gameOverTitle = document.getElementById("game-over-title");
    const gameOverText = document.getElementById("game-over-text");
    const btnRestart = document.getElementById("btn-restart");
    const btnMenu = document.getElementById("btn-menu");
    const btnScoreboard = document.getElementById("btn-scoreboard");

    const urlParams = new URLSearchParams(window.location.search);
    const gameId = urlParams.get("game_id");

  //Ladda spelbrädet från servern
  fetch('../PHP/load_game.php?game_id=${gameId}') 
  .then((res) => res.json())
  .then((data) => {
    updateBoard(data);
    statusElem.textContent = data.status;
    checkGameOver(data.status);
  })
  .catch((err) => console.error("Error loading game:", err));

  //Lyssnar på användartryck på spelbrädan
  board.addEventListener("click", (event) => {
    if (event.target.classList.contains("cell") && !event.target.classList.contains("taken")) {
      const cellIndex = event.target.dataset.index;

      // Skicka drag till servern
      fetch('../PHP/play_move.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
          cell: cellIndex, 
          gameId: gameId
        })
      })
        .then((res) => res.json())
        .then((data) => {
          updateBoard(data);
          statusElem.textContent = data.status;
          checkGameOver(data.status);
        })
        .catch((err) => console.error("Error playing move:", err));
    }
  });

  btnBack.addEventListener("click", () => {
    window.location.href = "../HTML/menu.html";
  });

    //Funktion för att uppdatera brädet, skapar nytt div element och sätter in det som ett värde X, O eller ""
  function updateBoard(data) {
    board.innerHTML = ""; 
    data.board.forEach((cellValue, index) => {
      const cellDiv = document.createElement("div");
      cellDiv.classList.add("cell");
      cellDiv.dataset.index = index;
      cellDiv.textContent = cellValue;

      if (cellValue !== "") {
        cellDiv.classList.add("taken");
      }  

      board.appendChild(cellDiv);
    });
  }

  //Om spelet är över skicka winnaren eller om det blev lika
  function checkGameOver(status) {
    if (status.includes("Winner:") || status.includes("draw!")) {
      openGameOverModal(status);
    }
  }

  function openGameOverModal(statusMessage){
    gameOverTitle.textContent = "Game Over!";
    gameOverText.textContent = statusMessage;
    gameOverModal.style.display = "block";
  }

  closeGameOverModal.addEventListener("click", () => {
    gameOverModal.style.display = "none";
  });

  btnRestart.addEventListener("click", () => {
    window.location.href = "../HTML/select_players.html"; 
  });

  btnMenu.addEventListener("click", () => {
    window.location.href = "../HTML/menu.html";
  });

  //Anropar den globala metoden som skapades i scoreboard.js
  btnScoreboard.addEventListener("click", () => {
    openScoreboard();
  });
});