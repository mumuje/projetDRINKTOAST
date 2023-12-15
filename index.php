
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST['pseudo'];
    $message = $_POST['message'];

    // Formatage du message
    $formattedMessage = "Pseudo: " . $pseudo . "\nMessage: " . $message . "\n\n";

    // Écriture du message dans le fichier
    file_put_contents('messages.txt', $formattedMessage, FILE_APPEND);
}
?>

<!DOCTYPE html>
<html>
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> DrinkToast | Accueil</title>
    <link rel="icon" href="img/logo.png" type="image/png"> 
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
  <div class="content">
    <header>
    <div class="left-section"> 
    <a href="index.php">
    <img src="img/logo.png" alt="Logo" class="logo"></a> 
    <nav>
        <ul>
<li><button id="contactButton">Contact</button></li>
        </ul>
      </nav>
</div>
      <h1>DrinkToast : <br> Drink Duel</h1>
      <div class="right-section"></div>
    </header>
    <main>
    <div class="left-image"></div>
<div class="right-image"></div>
    <form id="pseudo-form" method="post">
    <label for="pseudo">Pseudo :</label>
    <input type="text" id="pseudo" name="pseudo" required>
    <button type="submit">Enregistrer le pseudo!</button>
    
</form>

<div id ="contact">
<form method="post">
    <label for="pseudo">Pseudo :</label>
    <input type="text" id="pseudo" name="pseudo" required>
    <label for="message">Message :</label>
    <textarea id="message" name="message" rows="4" cols="50" maxlength="1000" required oninput="updateCounter()"></textarea>
    <div id="counter" style="text-align: right;"></div>
    <button type="submit">Envoyer</button>
</form>
</div>

      <div id="lobby-creation">
        <h2>Créer un nouveau lobby</h2>
        <form id="create-lobby-form" method="post">
    <label for="lobby-name">Nom du lobby :</label>
    <input type="text" id="lobby-name" name="lobby-name" required autocomplete="off">
        <label for="lobby-password">Mot de passe du lobby (laisser vide pour un lobby public) :</label>
    <input type="password" id="lobby-password" name="lobby-password">
    <button type="submit">Créer le lobby</button>
</form>
        <h2>Rejoindre un lobby existant :</h2>
        <ul id="lobby-list"></ul>
      </div>

      <div id="lobby-info" style="display: none;">
        <h1>Lobby: <span id="lobby-name-display"></span></h1>
        <h3 id="player-count">Joueurs:</h3>
                <ul id="player-list">
          <!-- La liste des joueurs sera ajoutée ici par le script JavaScript -->
          
        </ul>
        <button type="button" id="start-game" class="start-game-button" onclick="startGame()">Lancer la partie</button>
        <p id="error-message" style="color: red;"></p>        
        <div id="game-rules">
        <img src="img/regle.png" alt="voici les petites régles d'amour" id="rules-image">
    <h3>Règles du jeu :</h3>
    <p><strong>CONCEPT DU JEU :</strong></p>
    <p>6 cartes différentes</p>
    <ul>
    <li>Carte Bleue -> Devine le nombre</li>
      <li>Carte Jaune -> Question général sinon X gorgée(s)</li>
      <li>Carte Rouge -> Action physique sinon X gorgée(s)</li>
      <li>Carte Verte -> Énigme sinon X gorgée(s)</li>
      <li>Carte RARE Multicolore -> Tout le monde prend X gorgée(s)</li>
      <li>Carte RARE Violette -> 2 joueurs dans un mini jeu (1v1)</li>
  </ul>
    <!-- Ajoutez plus de règles ici -->
  </div>
        </div>
        <p class="warning-text">L'abus d'alcool est dangereux pour la santé, à consommer avec modération. <br> Vous pouvez jouer sans boire!!</p><br>
    </main>

    <footer>
      <p>Copyright(pas du tout) © 2023 DrinkToast</p>
    </footer>
    <script src="app.js"></script>
    </div>

  </body>

</html>