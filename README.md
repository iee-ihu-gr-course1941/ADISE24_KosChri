# ADISE24_KosChri
Qwirkle game! 

Game.php has the basic functions that manipulate and use the database's tables.
GameController.php has combinations of the basic functions that exist in the game.php
Qwirkle.php is the api.
The user is authenticated through the initial token they receive when they set their user. They then copy that and paste it on the environment variable $token on postman, so they can be identified easily for all their moves. The token is inputed  in the headers of the request and retrived automatically through the saved API requests of postman.

API :

setUser (POST) :
Sets the user based on a name given through the body of the request. There is a trigger on the table where if 2 players exist no more can be added.(players table)

getUser (GET) :
Returns all the info including their score,token,name, tiles placed and discarded this turn.

start (POST):
Initializes the game, meaning that it initializes the board, gives players drawing hands of 6 tiles, gives the turn to a random player between the 2.

board (GET) :
Returns the state of the board.

status (GET) :
Returns the status of the game along with the current player turn.

hand (GET) :
Returns the hand of the player with indexes for ease of use. (1,2,3..)

place (POST) :
User attempts to place a tile with the chosen index to the board in x,y (row, col) and does so if the move is valid. Place is not permitted if the user has exchanged any tiles to the bag this turn since the player must only do one of the two per turn.

exchange(POST) :
User sends the selected index of tile to the bag. This is not permitted if they have placed a tile since they can only do one of two per turn. They draw at their end of the turn equal to the ammount returned.

turnEnd (POST) :
Player ends their turn and draws based on how many tiles they placed or on how many they discarded(until they hit 6 tiles, if tile is empty they are notified that they drew less). This function also checks for game end conditions (board full || tile empty && empty hand). It updates the turn to the other player.


