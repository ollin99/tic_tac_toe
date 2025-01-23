document.addEventListener("DOMContentLoaded", () => {
    const btnStart = document.getElementById("btn-start-game");
    const btnCreate = document.getElementById("btn-create-players");
    const btnScoreboard = document.getElementById("btn-scoreboard");

    btnScoreboard.addEventListener("click", () => {
      openScoreboard();
    });
  
    btnStart.addEventListener("click", () => {
      window.location.href = "../HTML/select_players.html"; 
    });
  
    btnCreate.addEventListener("click", () => {
      window.location.href = "../HTML/create_players.html";
    });
  });
  