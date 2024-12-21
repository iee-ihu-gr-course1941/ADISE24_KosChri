$(document).ready(function () {
  $("#login-form").submit(function (e) {
    e.preventDefault();

    const username = $("#username").val();
    const password = $("#password").val();

    $.ajax({
      url: "/api/login",
      type: "POST",
      data: { username, password },
      success: function (response) {
        if (response.success) {
          window.location.href = "lobby.html"; // Redirect to game lobby
        } else {
          alert("Invalid username or password.");
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  });
});
