<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, user-scalable=no">
  <title>DrinkToast| Game</title>
  <link rel="icon" href="img/logo.png" type="image/png">
  <link rel="stylesheet" href="game.css">
</head>

<body>

  <div id="game-board">
    <div id="game-area">
      <div id="dropzone"></div>
      <div id="persistentElementsJAUNE">
                <div id="JAUNECardDialog" style="display: none;"></div>
                <input type="text" id="answerInput" style="display: none;">
                <button id="submitAnswer" style="display: none;">Valider!</button>
                <div id="playerAnswerDisplay" style="display: none;"></div>
                <div id="correctAnswerDisplay" style="display: none;"></div>
                <button id="voteCorrectButtonDisplay" style="display:none;">VRAIE</button>
                <button id="voteFalseButtonDisplay" style="display:none;">FAUX</button>
                <div id="countdownDisplay" style="display: none;"></div>
                </div>
                <div id="action1Element" class="JAUNECardDialog" style="display: none;"></div>
                <div id="action2Element" class="JAUNECardDialog" style="display: none;"></div>


                <div id="moveSelection"></div>
                      <div id="game-scene">
        <!-- Game objects go here -->
        <!--  <div id="player0-cards" class="player-cards"></div>
                <div id="player1-cards" class="player-cards"></div>
                <div id="player2-cards" class="player-cards"></div>
                <div id="player3-cards" class="player-cards"></div>-->
              

        <div id="blueCardDialog" style="display: none;">
          <!-- Ajoutez ici les éléments pour permettre au joueur de choisir des nombres -->
          <input type="checkbox" class="numberCheckbox" value="0">0
          <input type="checkbox" class="numberCheckbox" value="1">1
          <input type="checkbox" class="numberCheckbox" value="2">2
          <input type="checkbox" class="numberCheckbox" value="3">3
          <input type="checkbox" class="numberCheckbox" value="4">4
          <input type="checkbox" class="numberCheckbox" value="5">5
          <input type="checkbox" class="numberCheckbox" value="6">6
          <input type="checkbox" class="numberCheckbox" value="7">7
          <input type="checkbox" class="numberCheckbox" value="8">8
          <input type="checkbox" class="numberCheckbox" value="9">9
          <button id="submitNumbers">Entrer</button>

        </div>


        <!-- Add more divs for more players -->
      </div>
     <!-- <div id="current-player">C'est le tour de : </div>-->
      <input type="button" id="startparty" name="startparty" value="Lancer la partie">



    </div>

  </div>
  </div>

  <button id="end-turn" style="display: none;">FIN DU TOUR</button>
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
  <script src="js/game.js"></script>
</body>

</html>