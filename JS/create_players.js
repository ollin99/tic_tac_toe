document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("player-form");
    const backBtn = document.getElementById("btn-back");

    //Skickar användarinmatad data till databasen med hjälp av save_player.php
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const playerName = document.getElementById("player-name").value;

      fetch('../PHP/save_player.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name: playerName })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Player created: " + playerName);
          document.getElementById("player-name").value = "";
        } else {
          alert("Error creating player");
        }
      })
      .catch(err => console.error("Error:", err));
    });

    backBtn.addEventListener("click", () => {
      window.location.href = "../HTML/menu.html"; 
    });
  });