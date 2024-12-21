$(document).ready(function () {
  // Fetch available games
  function fetchGames() {
    $.ajax({
      url: "/api/game/list",
      type: "GET",
      success: function (response) {
        const gameList = $("#game-list");
        gameList.empty();

        response.games.forEach((game) => {
          gameList.append(
            `<li>${game.id} - Players: ${game.players}<button class="join-game" data-id="${game.id}">Join</button></li>`
          );
        });

        $(".join-game").click(function () {
          const gameId = $(this).data("id");
          joinGame(gameId);
        });
      },
    });
  }

  // Join a game
  function joinGame(gameId) {
    $.ajax({
      url: "/api/game/join",
      type: "POST",
      data: { gameId },
      success: function () {
        window.location.href = "game.html"; // Redirect to game board
      },
    });
  }

  // Fetch games on load
  fetchGames();

  // Create new game
  $("#create-game").click(function () {
    $.ajax({
      url: "/api/game/create",
      type: "POST",
      success: function () {
        fetchGames(); // Refresh the game list
      },
    });
  });
});
