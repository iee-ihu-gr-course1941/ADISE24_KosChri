$(document).ready(function () {
  function fetchGameState() {
    $.ajax({
      url: "/api/game/state",
      type: "GET",
      success: function (response) {
        const board = $("#game-board");
        board.empty();

        response.board.forEach((row, y) => {
          row.forEach((tile, x) => {
            const cell = $("<div></div>")
              .addClass("tile")
              .text(tile ? tile.symbol : "")
              .data("x", x)
              .data("y", y);
            board.append(cell);
          });
        });
      },
    });
  }

  fetchGameState();

  $("#game-board").on("click", ".tile", function () {
    const x = $(this).data("x");
    const y = $(this).data("y");

    $.ajax({
      url: "/api/game/move",
      type: "POST",
      data: { x, y, tile: "RED_SQUARE" }, // Example data
      success: function () {
        fetchGameState();
      },
    });
  });
});
