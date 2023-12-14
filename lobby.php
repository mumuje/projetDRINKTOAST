<?php
$lobbyName = $_GET['lobby'];
// Récupérez les informations sur le lobby à partir du serveur WebSocket
// ...
?>
<!DOCTYPE html>
<html>
<head>
  <title>Lobby: <?php echo htmlspecialchars($lobbyName); ?></title>
  <script src="app.js"></script>
</head>
<body>
  <h1>Lobby: <?php echo htmlspecialchars($lobbyName); ?></h1>
  <ul id="player-list">
    <!-- La liste des joueurs sera ajoutée ici par le script JavaScript -->
  </ul>
  <button type="button" id="start-game" onclick="startGame()">Lancer la partie</button>
</body>
</html>