$(document).ready(function () {
	
	  // Fetch the game state and render the board
  function fetchGameState() {
    $.ajax({
      url: "/api/game/state",  // Endpoint to fetch the current game state
      type: "GET",
      success: function (response) {
        const board = $("#game-board");
        board.empty();  //clear the current board
	

		//loop through the board array to create the grid
        response.board.forEach((row, y) => {
          row.forEach((tile, x) => {
            const cell = $("<div></div>")		//create a new div for each tile
              .addClass("tile")   			   //add css tile class for styling
              .text(tile ? tile.symbol : "")  //display tile symbol
              .data("x", x)   				 //store x coord
              .data("y", y);  				//store y coord
            board.append(cell);				//append the cell to board 
          });
        });
      },
    });
  }

  fetchGameState();

  $("#game-board").on("click", ".tile", function () {
    const x = $(this).data("x");  //get x and y coords
    const y = $(this).data("y");

    $.ajax({
      url: "/api/game/move",  // Endpoint for moves
      type: "POST",
      data: { x, y, tile: "RED_SQUARE" }, // data sent to server (example.. to be changed)
      success: function () {
		if (response.success){
			fetchGameState(); //refresh game board
		}else{
			alert(response.message) //display error message
		}
      },
	  error: function () {
		alert("An error occurred while making the move.");
    },
  });
});
});
