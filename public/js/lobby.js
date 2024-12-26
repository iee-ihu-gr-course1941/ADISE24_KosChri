$(document).ready(function () {
  //Fetch the list of available games
  function fetchGameList() {
    $.ajax({
      url: "/api/game/list", //Endpoint for available games
      type: "GET",
      success: function (response) {
        const gameList = $("#game-list");
        gameList.empty(); //Clear the existing list

        //Populate the game list
        response.games.forEach((game) => {
          const gameItem = $("<div></div>")
            .addClass("game-item")
            .text(`Game ID: ${game.id}, Players: ${game.players}/${game.maxPlayers}`)
            .data("gameId", game.id);

          //Add a join button for each game
          const joinButton = $("<button></button>")
            .addClass("join-button")
            .text("Join")
            .data("gameId", game.id)
            .on("click", function () {
              joinGame($(this).data("gameId"));
            });

          gameItem.append(joinButton);
          gameList.append(gameItem);
        });
      },
      error: function () {
        alert("Failed to fetch game list.");
      },
    });
  }
    
  // Create a new game
  $("#create-game-button").on("click", function () {
    $.ajax({
      url: "/api/game/create", //Endpoint to create a game
      type: "POST",
      success: function (response) {
        if (response.success) {
          alert("Game created! Game ID: " + response.gameId);
          fetchGameList(); //Refresh the game list
        } else {
          alert("Failed to create a game.");
        }
      },
      error: function () {
        alert("An error occurred while creating the game.");
      },
    });
  });

	
  //Join a game
  function joinGame(gameId) {
    $.ajax({
      url: "/api/game/join",  //Endpoint to join a game
      type: "POST",
      data: { gameId },
      success: function (response) {
		  if(response.success){  
			window.location.href = "game.html"; // Redirect to game board
		  }else{
			  alert("Failed to join the game:" + response.message);
		  }
      },
	  error: function(){
		alert("An error occured while joining the game");  
	  },
    });
  }

  // Fetch game list on page load
  fetchGameList();
});
