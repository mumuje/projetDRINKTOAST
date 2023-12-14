let socket;
var gameStarted = localStorage.getItem('gameStarted') === 'true' ? true : false;
var gameState = JSON.parse(localStorage.getItem('gameState')) || {};
let turnTimeout; // Variable to hold the timeout
let currentPlayer2;
let tempId;
let displayIndex;
var selectedPseudos = []; 
var currentGame;
var player1;
var player2;
var currentPlayerPURPLE

const questionElement = document.getElementById('JAUNECardDialog');
const playerAnswerElement = document.getElementById('playerAnswerDisplay');
const correctAnswerElement = document.getElementById('correctAnswerDisplay');
//let i = 0;

function openSocketConnection() {
  if (socket && socket.readyState === WebSocket.OPEN) {
    socket.close();
  }
  pseudo = localStorage.getItem('pseudo');
  let urlParams = new URLSearchParams(window.location.search);
  lobbyName = urlParams.get('lobby');
  localStorage.setItem('lobbyName', lobbyName);
  startPartyButton = document.getElementById('startparty');
  endTurnButton = document.getElementById('end-turn');
  endTurnButton.style.display = 'none';
 
  socket = new WebSocket('ws://localhost:8080/websocket');

  socket.onopen = function (event) {
    if (localStorage.getItem('gameStarted') === 'true') {
      //socket.send(JSON.stringify({ type: 'setPseudo', pseudo: pseudo, lobbyName: lobbyName }));
      socket.send(JSON.stringify({ type: 'getGameState', pseudo: pseudo, lobbyName: lobbyName }));
      startPartyButton.style.display = 'none';

    } else {
    startPartyButton.style.display = 'block';
   // console.log('Connection opened');
    socket.send(JSON.stringify({ type: 'setPseudo', pseudo: pseudo, lobbyName: lobbyName }));
    //console.log(pseudo);   
    }
  };

  socket.addEventListener('message', (event) => {
    const data = JSON.parse(event.data);
   // console.log(data); // Ajoutez cette ligne
       if (data.type === 'gameStateUpdate') {
      gameState = data.gameState;
      localStorage.setItem('gameState', JSON.stringify(gameState));
      gameStarted = gameState.gameStarted;
      localStorage.setItem('gameStarted', gameState.gameStarted);

      isPlayerTurn = gameState.isPlayerTurn;
      updateGameState(gameState);
       } else if (data.type === 'NOENDTURN') {
       showMessage(data.content, 3000);
       dropzone.style.visibility = 'hidden';

    }  else if (data.type === 'BLUECARDPLAYED') {
      playBlueCard();
    } else if (data.type === 'CHOOSEPLAYER') {
      players = data.players;
    } else if (data.type === 'CHOOSENUMBER') {
      selectedPseudo = data.pseudo;
      chooseNumber();
    } else if (data.type === 'NUMBERCORRECT' || data.type === 'NUMBERINCORRECT') {
      showMessage(data.content, 3000);
    } else if (data.type === 'MULTICOLORCARDPLAYED'){
      showMessage(data.content, 5000);
    } else if (data.type === 'CHOOSEPLAYERYELLOW') {
      dropzone.style.visibility = 'hidden';
      choosePlayer2(data.players);
    } else if (data.type === 'CHOOSEPLAYERVERT') {
      dropzone.style.visibility = 'hidden';
      choosePlayer2(data.players);
    } 
    
    else if (data.type === 'CHOOSEPLAYERROUGE') {
      dropzone.style.visibility = 'hidden';
      choosePlayer2(data.players);
    } 
    
    
    else if (data.type === 'ASKQUESTION') {
      dropzone.style.visibility = 'hidden';
      // Afficher la question
      showQuestion(data.content, data.pseudo);
   } else if (data.type === 'ASKAction') {
    dropzone.style.visibility = 'hidden';

    showAction(data.content, data.pseudo);


   } else if (data.type === 'SHOWANSWER') {
    dropzone.style.visibility = 'hidden';
    correctAnswerElement.style.display = 'none';
    playerAnswerDisplay.style.display = 'none';
    questionElement.style.display = 'none';
    action1Element.style.display = 'none';
    action2Element.style.display = 'none';
      // Afficher la réponse du joueur et la réponse correcte
      showAnswer(data.playerAnswer, data.correctAnswer, data.pseudo);
  } else if (data.type === 'VOTE') {
    dropzone.style.visibility = 'hidden';

      // Demander aux joueurs de voter
      startVoting();
  } else if (data.type === 'SCOREUPDATE') {

      // Afficher le message de mise à jour du score
      showMessage(data.content, 3000);
      currentPlayer2 = null;
  } else if (data.type === 'COUNTDOWN') {
      // Démarrer le compte à rebours
      startCountdown(data.start, data.duration);
  } else if (data.type === 'playerSelectedResponse') {
    dropzone.style.visibility = 'hidden';
    currentPlayer2 = data.pseudo;
    if (data.pseudo === pseudo) {
      document.getElementById('answerInput').style.display = 'block';
      document.getElementById('submitAnswer').style.display = 'block';
    }
  }  else if (data.type === 'playerSelectedResponseRED') {
    dropzone.style.visibility = 'hidden';
    currentPlayer2 = data.pseudo;
    if (data.pseudo === pseudo) {
      document.getElementById('answerInput').style.display = 'none';
      document.getElementById('submitAnswer').style.display = 'block';
    }
  } else if (data.type === 'STOP_COUNTDOWN') {
    stopCountdown();
} else if (data.type === 'redirect') {
  window.location.href = data.url;
} else if (data.type === 'endGame') {
  var endTurnElement = document.getElementById('end-turn');
  endTurnElement.classList.add('hidden');
 // console.log('data.message:', data.message);
  endTurnButton = document.getElementById('end-turn');
  endTurnButton.style.display = 'none';

  gameStarted = false;
  //console.log('Game ended, gameStarted:', gameStarted);
  updateButtonDisplay();

  endTurnButton.style.display = 'none';

  dropzone.style.visibility = 'hidden';
  startPartyButton.style.display = 'none';


  // Créez un conteneur pour le message et le bouton
var container = document.createElement('div');
container.className = 'end-container';

// Créez l'élément de message
var parts = data.message.split('\n\n');
var endMessage = parts[0];
var rankingMessage = parts[1];

var messageElement = document.createElement('p');
messageElement.className = 'end-message';
messageElement.innerHTML = rankingMessage.replace(/\n/g, '<br>');
container.appendChild(messageElement);

// Créez le bouton
var buttonElement = document.createElement('button');
buttonElement.className = 'home-button';
buttonElement.textContent = 'Retour à l\'accueil';
buttonElement.addEventListener('click', function() {
    window.location.href = 'index.php';
});
container.appendChild(buttonElement);

// Ajoutez le conteneur à votre page
document.body.appendChild(container);
}  else if (data.type === 'NOSTARTPARTY') {
  showMessage(data.content, 3000);
  dropzone.style.visibility = 'hidden';

}  else if (data.type === 'CHOOSEPLAYERSVIOLET') {
  dropzone.style.visibility = 'hidden';
  choosePlayersViolet(data.players);




}  else if (data.type === 'miniGameSelected') {
  dropzone.style.visibility = 'hidden';
 // console.log("Un mini-jeu a été sélectionné : " + data.game);
  currentGame = data.game;
  //console.log("Les mouvements possibles sont : " + data.moves.join(', '));

  // Supposons que vous ayez un élément avec l'ID 'moveSelection' où vous voulez ajouter les boutons
  var moveSelection = document.getElementById('moveSelection');

  // Supprimez tous les enfants existants


  if (currentGame === 'TicTacToe') {
  //  console.log('Received data:', data);
   // console.log ("player1 " + data.player1 + " player2 " + data.player2 + " CURRENTJOUEUR " + data.VIOLETJOUEUR);
    player1 = data.player1; 
    player2 = data.player2; 
    currentPlayerPURPLE = data.VIOLETJOUEUR; // Le joueur 1 est le premier à jouer
    //console.log(player1 + " " + player2 + " " + currentPlayerPURPLE);
    //console.log("été sélectionné : " + currentGame);
    var table;
    // Générer une grille 3x3 pour le jeu de Morpion
    if (data.TicTacToeAlready === false) {
      while (moveSelection.firstChild) {
        moveSelection.removeChild(moveSelection.firstChild);
      }
      localStorage.removeItem('tableState');
      table = document.createElement('table');
      table.style.borderCollapse = 'collapse';
      for (var i = 0; i < 3; i++) {
        var row = document.createElement('tr');
        for (var j = 0; j < 3; j++) {
          var cell = document.createElement('td');
          cell.className = 'game-cell'; // Ajoutez ceci pour ajouter une classe CSS à chaque cellule
          cell.addEventListener('click', handleCellClick);
          //cell.style.border = '1px solid black'; // Ajoutez ceci pour ajouter une bordure à chaque cellule
          //cell.style.width = '50px'; // Ajoutez ceci pour définir la largeur de chaque cellule
          //cell.style.height = '50px'; // Ajoutez ceci pour définir la hauteur de chaque cellule
          cell.setAttribute('data-played', 'false');
          row.appendChild(cell);
        }
        table.appendChild(row);
      }
      moveSelection.appendChild(table);

      var tableState = [];
      var rows = table.getElementsByTagName('tr'); // Remplacez 'table' par la référence à votre table de Morpion
      for (var i = 0; i < rows.length; i++) {
          var rowState = [];
          var cells = rows[i].getElementsByTagName('td');
          for (var j = 0; j < cells.length; j++) {
              var cell = cells[j];
              rowState.push({
                played: cell.getAttribute('data-played'),
                content: cell.textContent
            });
          }
          tableState.push(rowState);
      }
      localStorage.setItem('tableState', JSON.stringify(tableState));
    } else {
      if (pseudo === currentPlayerPURPLE) {
     //   console.log ("C'est ton tour");
      }
      // Si le jeu a déjà commencé, mettez simplement à jour la table existante
      var existingTable = moveSelection.querySelector('table');
      if (existingTable) {
        var rows = existingTable.getElementsByTagName('tr');
        // Reste du code...
      } else {
     //   console.error("La table existante n'a pas été trouvée");
        var savedTableState = JSON.parse(localStorage.getItem('tableState'));
        if (savedTableState) {
          table = document.createElement('table');
          table.style.borderCollapse = 'collapse';
          for (var i = 0; i < savedTableState.length; i++) {
              var row = document.createElement('tr');
              for (var j = 0; j < savedTableState[i].length; j++) {
                  var cell = document.createElement('td');
                  cell.className = 'game-cell';
                  cell.addEventListener('click', handleCellClick);
                 // cell.style.border = '1px solid black';
                  //cell.style.width = '50px';
                  //cell.style.height = '50px';
                  cell.setAttribute('data-played', savedTableState[i][j].played);
                  cell.textContent = savedTableState[i][j].content;
                  row.appendChild(cell);
              }
              table.appendChild(row);
          }
          moveSelection.appendChild(table);
      } else {
            console.error("La table existante n'a pas été trouvée");
        }

      }
      table = moveSelection.getElementsByTagName('table')[0];
      var rows = table.getElementsByTagName('tr');
      for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        for (var j = 0; j < cells.length; j++) {
          var cell = cells[j];
          // Parcourez tous les mouvements possibles
        }
      }
    }
  //  console.log(moveSelection);
  }
if (currentGame === 'RockPaperScissors') {
  while (moveSelection.firstChild) {
    moveSelection.removeChild(moveSelection.firstChild);
}
//  console.log("Un mini-jeu : " + currentGame);
  // Ajoutez un bouton pour chaque mouvement possible
  data.moves.forEach(function(move) {
      var button = document.createElement('button');
      button.className = 'game-button-RockPaperScissors';
      button.textContent = move;
      button.addEventListener('click', function() {
  //      console.log('Button clicked');
          socket.send(JSON.stringify({ type: 'playerMove', move: move, pseudo: pseudo, lobbyName: lobbyName, game: currentGame  }));
          
          var buttons = moveSelection.getElementsByTagName('button');
          while(buttons.length > 0){
            buttons[0].parentNode.removeChild(buttons[0]);
          }
      });
      moveSelection.appendChild(button);
  });
}
} 




else if (data.type === 'playerMove') {
  dropzone.style.visibility = 'hidden';
  currentGame = data.game;
  if (currentGame === 'TicTacToe') {
    var table = document.querySelector('table');

    if (!table) {
        // Récupérez l'état de la table
        var savedTableState = JSON.parse(localStorage.getItem('tableState'));
        if (savedTableState) {
            table = document.createElement('table');
            table.style.borderCollapse = 'collapse';
            for (var i = 0; i < savedTableState.length; i++) {
                var row = document.createElement('tr');
                for (var j = 0; j < savedTableState[i].length; j++) {
                    var cell = document.createElement('td');
                    cell.className = 'game-cell';
                    cell.addEventListener('click', handleCellClick);
                   // cell.style.border = '1px solid black';
                    //cell.style.width = '50px';
                    //cell.style.height = '50px';
                    cell.setAttribute('data-played', savedTableState[i][j].played === 'true' ? 'true' : 'false');
                    cell.textContent = savedTableState[i][j].content;
                   row.appendChild(cell);
                }
                table.appendChild(row);
            }
            moveSelection.appendChild(table);
        } else {
            console.error("La table existante n'a pas été trouvée");
            return;
        }
    }

    var cell = table.rows[data.move[0]].cells[data.move[1]];
    cell.setAttribute('data-played', 'true');
    cell.textContent = data.pseudo === player1 ? ' X ' : ' O ';
    // Mettez à jour l'état de la grille de jeu
    var tableState = [];
    for (var i = 0; i < table.rows.length; i++) {
      tableState[i] = [];
      for (var j = 0; j < table.rows[i].cells.length; j++) {
          tableState[i][j] = {
              played: table.rows[i].cells[j].getAttribute('data-played'),
              content: table.rows[i].cells[j].textContent
          };
      }
  }

// Sauvegardez l'état de la grille de jeu dans le localStorage
localStorage.setItem('tableState', JSON.stringify(tableState));

} else if (currentGame === 'RockPaperScissors'){
// Mettre à jour l'état du jeu en fonction du mouvement du joueur
//game.updateMove(data.pseudo, data.move);

  var playerMoveElement = document.getElementById(data.pseudo + 'Move');
  if (playerMoveElement) {
      playerMoveElement.textContent = data.move;
  }
}
//console.log(data.pseudo + " a choisi : " + data.move);

}
 else if (data.type ==='gameResult') {
  if (currentGame === 'RockPaperScissors') {

  //console.log("Le résultat du jeu est : " + data.result);
  //console.log("Le gagnant est : " + data.winner.pseudo); 
  if (data.result === 0 ) {

    // Supposons que vous ayez un élément avec l'ID 'moveSelection' où vous voulez ajouter les boutons
    var moveSelection = document.getElementById('moveSelection');
  
    // Supprimez tous les enfants existants
    while (moveSelection.firstChild) {
        moveSelection.removeChild(moveSelection.firstChild);
    }
  
    // Ajoutez un bouton pour chaque mouvement possible
    data.moves.forEach(function(move) {
        var button = document.createElement('button');
        button.textContent = move;
        button.addEventListener('click', function() {
        //  console.log('Button clicked');
            socket.send(JSON.stringify({ type: 'playerMove', move: move, pseudo: pseudo, lobbyName: lobbyName  }));
            
            var buttons = moveSelection.getElementsByTagName('button');
            while(buttons.length > 0){
              buttons[0].parentNode.removeChild(buttons[0]);
            }
        });
        moveSelection.appendChild(button);
    });
  }
  showMessage(data.content, 3000);  
  // Mettre à jour l'interface utilisateur pour montrer le résultat du jeu

} else if  (currentGame === 'TicTacToe') {
  var cells = document.querySelectorAll('#gameBoard .cell');
  for (var i = 0; i < cells.length; i++) {
    cells[i].textContent = '';
  }
  var moveSelection = document.getElementById('moveSelection');
  while (moveSelection.firstChild) {
    moveSelection.removeChild(moveSelection.firstChild);
}
 // moveSelection.removeChild(moveSelection.firstChild);
  showMessage(data.content, 3000);  
  localStorage.removeItem('tableState');

}
dropzone.style.visibility = 'visible';

} // else if
 
  });




  socket.onclose = function (event) {
    console.error('Erreur WebSocket : ', event);
  //  console.log('Connection closed');
   // console.log('Connection closed, trying to reconnect...');
    // conn = new WebSocket('ws://localhost:8080/websocket?playerId=' + playerId);
  };

  socket.onerror = function (error) {
    console.error('Erreur WebSocket : ', error);
 //   console.log('WebSocket error: ', error);
  };

  document.getElementById('startparty').addEventListener('click', function () {
   // console.log('Button clicked');
    socket.send(JSON.stringify({ type: 'startparty', lobbyName: lobbyName }));
  });

  document.getElementById('end-turn').addEventListener('click', function () {
    if (gameStarted) {
    socket.send(JSON.stringify({ type: 'endTurn', lobbyName: lobbyName, pseudo: pseudo }));
    } else {
      endTurnButton.style.display = 'none';
    }
  });
}

window.onload = function() {
  adjustGameSceneSize();
}

window.onresize = function() {
  adjustGameSceneSize();
};
const dropzone = document.getElementById('dropzone');

dropzone.addEventListener('dragover', function(event) {
  event.preventDefault(); 
});

dropzone.addEventListener('drop', function(event) {
  event.preventDefault();
  const cardId = event.dataTransfer.getData('text/plain');
 // console.log('Dropped card ID:', cardId); 
  const cardElement = document.getElementById(cardId);
  cardElement.classList.add('card-in-dropzone'); 
  dropzone.appendChild(cardElement);
  socket.send(JSON.stringify({
    type: 'playCard',
    cardId: cardId,
    lobbyName: lobbyName,
    pseudo: pseudo
  }));
});
let activeCardId = null;

dropzone.addEventListener('touchend', function(event) {
  if (activeCardId) {
  //  console.log('Dropped card ID:', activeCardId);
    const cardElement = document.getElementById(activeCardId);
    cardElement.classList.add('card-selected');
    cardElement.classList.add('card-in-dropzone');
    dropzone.appendChild(cardElement);
    socket.send(JSON.stringify({
      type: 'playCard',
      cardId: activeCardId,
      lobbyName: lobbyName,
      pseudo: pseudo
    }));
    activeCardId = null;
  }
});


function updateGameState(gameState) {
  const gameScene = document.getElementById('game-scene');
  document.body.appendChild(blueCardDialog);
  document.body.appendChild(persistentElementsJAUNE);
  gameScene.innerHTML = ''; // Clear the game scene
  gameScene.appendChild(blueCardDialog);
  gameScene.appendChild(persistentElementsJAUNE);
  const gameArea = document.getElementById('game-area');

  // Create a visual representation of the game state
 // console.log(gameState.players); // Log the players array
  
    // Create an element to display the turn count
    let turnCountElement = document.getElementById('turn-count');
    if (!turnCountElement) {
      // Si turnCountElement n'existe pas, le créer
      turnCountElement = document.createElement('div');
      turnCountElement.id = 'turn-count';
      turnCountElement.classList.add('turn-count-class');
      gameArea.appendChild(turnCountElement);
    }
    const turnCount = gameState.turnCount ? gameState.turnCount : 0;
    if (window.innerWidth < 1900) {
      turnCountElement.textContent = turnCount+'/30';
    } else {
      turnCountElement.textContent = 'Nombre de tours: ' + turnCount+'/30';
    }    let pseudoIndex = gameState.players.findIndex(player => player.pseudo === pseudo);

  for (let i = 0; i < gameState.players.length; i++) {
    const player = gameState.players[i];
    var playerId = gameState.playerId;
  var playerCardsElement = document.createElement('div');
  const sipsTakenElement = document.createElement('div');
  sipsTakenElement.classList.add('sips-taken');
  sipsTakenElement.style.top = `${i * 55}px`; // Adjust as needed
  if (window.innerWidth <= 1900) {
    sipsTakenElement.innerHTML = player.pseudo + ': ' + player.sipsTaken  + '<img src="img/createur.png" width="30px"/>';
    } else {
    sipsTakenElement.textContent = player.pseudo + ': Gorgées prises: ' + player.sipsTaken;
  }  
  playerCardsElement.classList.add('player-cards'); // Ajoutez cette ligne

  if (player.pseudo === pseudo){
    playerCardsElement.id = 'player1-cards';
    playerCardsElement.classList.add('player' + 1 + '-cards');
    if (playerId !== 1) {
    tempId = playerId;
    }
  }  else if (playerId === 1 && player.pseudo !== pseudo) {
    playerCardsElement.id = 'player' + tempId + '-cards'; // Attribuer l'ID stocké au joueur avec l'ID 1
    playerCardsElement.classList.add('player' + tempId + '-cards');

} else {
  playerCardsElement.id = 'player' + playerId + '-cards';
  playerCardsElement.classList.add('player' + playerId + '-cards');
  }


    if (gameStarted === false) {
      endTurnButton = document.getElementById('end-turn');
      endTurnButton.style.display = 'none';
    }

    ////////////METTRE EN HAUT A GAUCHE PAR EXEMPLE ET AJOUTER LE PSEUDO A COTE DES CARTE UNIQUEMENT POUR LE JOUEUR ACTRIF /////////////////
    const playerElement = document.createElement('div');
    //playerElement.classList.add('player-cards'); // Add this line
    //playerElement.textContent = player.pseudo;
        ////////////METTRE EN HAUT A GAUCHE PAR EXEMPLE ET AJOUTER LE PSEUDO A COTE DES CARTE UNIQUEMENT POUR LE JOUEUR ACTRIF /////////////////

    // If the player is the active player, create a visual representation of their cards
if (player.isPlayerTurn) {
  playerElement.textContent = player.pseudo;
  playerElement.classList.add("player-pseudo");
  var currentPlayerElement = document.getElementById('current-player');
//  currentPlayerElement.textContent = "C'est le tour de : " + player.pseudo;
  const gameArea = document.getElementById('game-area');
  sipsTakenElement.classList.add('sips-taken');
  sipsTakenElement.style.top = `${i * 55}px`; // Adjust as needed
  if (window.innerWidth <= 1900) {
    sipsTakenElement.innerHTML = `<strong>${player.pseudo} : ${player.sipsTaken}</strong>` + '<img src="img/createur.png" width="30px"/>';
  } else {
    sipsTakenElement.innerHTML = `<strong>${player.pseudo} : Gorgées prises: ${player.sipsTaken}</strong>`;
  }  

  if (player.pseudo === pseudo){
    playerCardsElement.id = 'player1-cards';
    playerCardsElement.classList.add('player' + 1 + '-cards');
    if (playerId !== 1) {
    tempId = playerId;
    }
  }  else if (playerId === 1 && player.pseudo !== pseudo) {
    playerCardsElement.id = 'player' + tempId + '-cards'; // Attribuer l'ID stocké au joueur avec l'ID 1
    playerCardsElement.classList.add('player' + tempId + '-cards');

} else {
  playerCardsElement.id = 'player' + playerId + '-cards';
  playerCardsElement.classList.add('player' + playerId + '-cards');
  }
}

    if (player.isPlayerTurn && player.pseudo === pseudo) {
      for (let i = 0; i < player.cards.length; i++) {
        
        const card = player.cards[i];
        const cardElement = document.createElement('div');
        //cardElement.textContent = card.color;
        cardElement.id = card.id;
        cardElement.classList.add('card'); // Add this line
        cardElement.classList.add('card-' + i); // Add this line
        cardElement.draggable = true; // Add this line

        cardElement.addEventListener('dragstart', function(event) {
       //   console.log('Drag started');
        //  console.log('Card ID:', cardElement.id); // Log the card ID
          event.dataTransfer.setData('text/plain', cardElement.id);
        });
        cardElement.addEventListener('touchstart', function(event) {
          // Obtenir toutes les cartes
          var cards = document.querySelectorAll('.card');
        
          // Utiliser event.currentTarget à la place de cardElement
          var currentCard = event.currentTarget;
        
          // Si la carte cliquée était déjà sélectionnée, la désélectionner
          if (currentCard.classList.contains('card-selected')) {
            currentCard.classList.remove('card-selected');
            activeCardId = null;
          } else {
            // Parcourir toutes les cartes et les désélectionner
            for (var i = 0; i < cards.length; i++) {
              cards[i].classList.remove('card-selected');
            }
        
            // Si la carte cliquée n'était pas sélectionnée, la sélectionner
            currentCard.classList.add('card-selected');
            activeCardId = currentCard.id;
          }
        });
        cardElement.addEventListener('touchmove', function(event) {
          event.preventDefault(); // Prevent scrolling on touch devices
        });


        const cardImage = document.createElement('img');
        cardImage.src = card.image;
        cardElement.appendChild(cardImage);
        playerElement.appendChild(cardElement);
        playerCardsElement.appendChild(cardElement); // Ajoutez la carte à playerCardsElement

      } 
      gameScene.appendChild(playerCardsElement); // Ajoutez playerCardsElement à gameScene

      ///////FONCTIONNEL A AJOUTE A LA FIN ////////////////
          //    turnTimeout = setTimeout(function() {
          //socket.send(JSON.stringify({ type: 'endTurn', lobbyName: lobbyName, pseudo: pseudo }));
        //}, 100000); // 600000 milliseconds = 10 minutes
              ///////FONCTIONNEL A AJOUTE A LA FIN ////////////////
    } 
    else if (!player.isPlayerTurn && player.pseudo === pseudo) {
      for (let i = 0; i < player.cards.length; i++) {
        const card = player.cards[i];
        const cardElement = document.createElement('div');
        const cardImage = document.createElement('img');
        cardImage.src = card.image;
        cardElement.appendChild(cardImage);
        playerElement.appendChild(cardElement);
        playerCardsElement.appendChild(cardElement);
    }
    gameScene.appendChild(playerCardsElement); // Ajoutez playerCardsElement à gameScene

    }
    const dropzone = document.getElementById('dropzone');
    dropzone.innerHTML = ''; // Clear the dropzone
let j = 0;
    if (gameState.dropzone && Array.isArray(gameState.dropzone)) {
      for (let i = 0; i < gameState.dropzone.length; i++) {
        j++;
        const card = gameState.dropzone[i];
        const cardElement = document.createElement('div');
      const cardImage = document.createElement('img');
      cardImage.src = 'img/' + card.color + '.png'; // Replace this with your card image URL
      cardElement.appendChild(cardImage);
      cardElement.id = card.id;
      cardElement.classList.add('card-in-dropzone');
      cardElement.classList.add('card-' + j); // Add this line
      dropzone.appendChild(cardElement);
      if (j == 6) {
        j = 0;
      }
    }
  } else {
  //  console.log ("tableau vide ou début de partie");
  }
  for (let index = 0; index < gameState.players.length; index++) {
    const player = gameState.players[index];
  if (player.pseudo !== pseudo) {
    const playerCardsElement = document.createElement('div');
    displayIndex = (index < pseudoIndex ? index + 2 : index + 1);
    playerCardsElement.id = 'player' + displayIndex + '-cards';
    playerCardsElement.classList.add('player-cards');
    playerCardsElement.classList.add('player' + displayIndex + '-cards');
   // console.log(playerCardsElement.classList);
  for (let i = 0; i < player.cards.length; i++) {

    
    const cardElement = document.createElement('div');
    const cardImage = document.createElement('img');
    cardImage.src = 'img/dos.png'; // Change this to the path of your back card image
    cardElement.appendChild(cardImage);
    playerCardsElement.appendChild(cardElement); // Ajoutez la carte à playerCardsElement
  }
  gameScene.appendChild(playerCardsElement); // Ajoutez playerCardsElement à gameScene
  }
  }
  gameArea.appendChild(sipsTakenElement);
      gameScene.appendChild(playerElement);
  }
  gameArea.appendChild(turnCountElement);

  updateButtonDisplay();
  if (gameStarted === false) {
    endTurnButton = document.getElementById('end-turn');
    endTurnButton.style.display = 'none';
  }
}

function updateButtonDisplay() {
 // console.log('Updating button display, gameStarted:', gameStarted, 'isPlayerTurn:', isPlayerTurn);

//  console.log("UPDATEBUTTONDISPLAY");
  if (gameStarted) {
  //  console.log("GAME START UPDATEBUTTONDISPLAY");
    startPartyButton.style.display = 'none';
    if (isPlayerTurn) {
   //   console.log("TOUR DU JOUEUR UPDATEBUTTONDISPLAY");
      endTurnButton.style.display = 'block';
      //console.log(endTurnButton)
    } else {
      endTurnButton.style.display = 'none';
     // console.log(" PAS TOUR DU JOUEUR UPDATEBUTTONDISPLAY");
      //console.log(endTurnButton)
    }
  } else {
   // console.log("PAS DE GAME");
    startPartyButton.style.display = 'none';
    endTurnButton.style.display = 'none';
  }
}

function adjustGameSceneSize() {
  var gameScene = document.getElementById('game-scene');
  var aspectRatio = 1; // Replace with the aspect ratio of your image

  if (window.innerWidth <= 1900) { // If the screen width is 900px or less
    gameScene.style.width = window.innerWidth + 'px';
    gameScene.style.height = window.innerHeight + 'px';
  } else {
    var windowAspectRatio = window.innerWidth / window.innerHeight;
    if (windowAspectRatio > aspectRatio) {
      gameScene.style.height = window.innerHeight + 'px';
      gameScene.style.width = (window.innerHeight * aspectRatio) + 'px';
    } else {
      gameScene.style.width = window.innerWidth + 'px';
      gameScene.style.height = (window.innerWidth / aspectRatio) + 'px';
    }
  }
}

function submitNumbers() {
 // console.log('submitNumbers button clicked');

  // Récupérer les nombres sélectionnés
  var checkboxes = document.getElementsByClassName('numberCheckbox');
  var selectedNumbers = [];
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      selectedNumbers.push(checkboxes[i].value);
    }
  }
  //console.log('Selected numbers: ', selectedNumbers);

  // Envoyer les nombres sélectionnés au serveur
  socket.send(JSON.stringify({
    type: 'playBlueCard',
    numbers: selectedNumbers,
    lobbyName: lobbyName,
    pseudo: pseudo
  }));
//  console.log('Numbers sent to server');

  // Cacher le dialogue
  document.getElementById('blueCardDialog').style.display = 'none';
  choosePlayer(players);
}
function playBlueCard() {
  dropzone.style.visibility = 'hidden';
 // console.log('playBlueCard function called');

  // Afficher le dialogue pour choisir des nombres
  document.getElementById('blueCardDialog').style.display = 'block';
//  console.log('blueCardDialog should be visible now');

  var submitNumbersButton = document.getElementById('submitNumbers');

  // Supprimer l'écouteur d'événements précédent
  submitNumbersButton.removeEventListener('click', submitNumbers);

  // Ajouter un nouvel écouteur d'événements
  submitNumbersButton.addEventListener('click', submitNumbers);
}
var selectedPseudo;
function choosePlayer(players) {
  selectedPseudo = '';

  // Supprimer l'ancien div de sélection des joueurs, s'il existe
  var oldDiv = document.getElementById('playerSelection');
  if (oldDiv) document.body.removeChild(oldDiv);

  // Créer un nouvel élément div pour contenir les boutons
  var div = document.createElement('div');
  div.id = 'playerSelection';
  div.className = 'playerSelection'; 

  // Créer un bouton pour chaque joueur
  for (var i = 0; i < players.length; i++) {
    var button = document.createElement('button');
    button.textContent = players[i].pseudo;
    button.addEventListener('click', function() {
      // Stocker le pseudo du joueur sélectionné dans la variable globale
      selectedPseudo = this.textContent;
    //  console.log(selectedPseudo);

      // Envoyer le pseudo du joueur sélectionné au serveur
      socket.send(JSON.stringify({
        type: 'playerSelected',
        pseudo: selectedPseudo,
        lobbyName: lobbyName,
      }));

      // Supprimer la div de sélection des joueurs
      document.body.removeChild(div);
    });
    div.appendChild(button);
  }

  // Ajouter la div de sélection des joueurs au corps du document
  document.body.appendChild(div);
}
function choosePlayer2(players) {
  // Supprimer l'ancien div de sélection des joueurs, s'il existe
  var oldDiv = document.getElementById('playerSelection');
  if (oldDiv) document.body.removeChild(oldDiv);

  // Créer un nouvel élément div pour contenir les boutons
  var div = document.createElement('div');
  div.id = 'playerSelection';
  div.className = 'playerSelection'; 

  // Créer un bouton pour chaque joueur
  for (var i = 0; i < players.length; i++) {
    var button = document.createElement('button');
    button.textContent = players[i].pseudo;
    button.addEventListener('click', function() {
      // Stocker le pseudo du joueur sélectionné dans la variable globale
      selectedPseudo = this.textContent;

      // Envoyer le pseudo du joueur sélectionné au serveur
     // console.log({
      //  type: 'playerSelected2',
       // pseudo: selectedPseudo,
        //lobbyName: lobbyName,
     // });
      socket.send(JSON.stringify({
        type: 'playerSelected2',
        pseudo: selectedPseudo,
        lobbyName: lobbyName,
      }));

      // Supprimer la div de sélection des joueurs
      document.body.removeChild(div);
    });
    div.appendChild(button);
  }

  // Ajouter la div de sélection des joueurs au corps du document
  document.body.appendChild(div);
}
function chooseNumber() {
  dropzone.style.visibility = 'hidden';
  // Supprimer l'ancien div de sélection des nombres, s'il existe
  var oldDiv = document.getElementById('numberSelection');
  if (oldDiv) document.body.removeChild(oldDiv);

  // Créer un nouvel élément div pour contenir les boutons
  var div = document.createElement('div');
  div.id = 'numberSelection';
  div.className = 'numberSelection';

  // Créer un bouton pour chaque nombre de 0 à 9
  for (var i = 0; i < 10; i++) {
    var button = document.createElement('button');
    button.textContent = i;
    button.addEventListener('click', function() {
     // console.log(selectedPseudo);

      // Envoyer le nombre choisi au serveur
      var message = {
        type: 'numberChosen',
        number: this.textContent,
        lobbyName: lobbyName,
        pseudo: selectedPseudo,
      };
      
      //console.log('Message sent to server: ', message);
      
      socket.send(JSON.stringify(message));

      // Supprimer la div de sélection des nombres
      document.body.removeChild(div);
    });
    div.appendChild(button);
  }
  document.body.appendChild(div);
}
function showMessage(content, displayTime) {
  var buttons = moveSelection.getElementsByTagName('button');
  while(buttons.length > 0){
    buttons[0].parentNode.removeChild(buttons[0]);
  }
  dropzone.style.visibility = 'visible';
  correctAnswerElement.style.display = 'none';
  playerAnswerDisplay.style.display = 'none';
  questionElement.style.display = 'none';
  action1Element.style.display = 'none';
  action2Element.style.display = 'none';
  voteCorrectButtonElement.style.display = 'none';
  voteFalseButtonElement.style.display = 'none';

  // Supprimer l'ancien div de message, s'il existe
  var oldDiv = document.getElementById('message');
  if (oldDiv) document.body.removeChild(oldDiv);

  // Créer un nouvel élément div pour afficher le message
  var div = document.createElement('div');
  div.id = 'message';
  div.textContent = content;
  div.className = 'message'; // Appliquer la classe CSS

  // Ajouter le div au corps du document
  document.body.appendChild(div);

  // Supprimer le div après le temps spécifié ou lorsque l'utilisateur clique dessus
  div.addEventListener('click', function() {
    document.body.removeChild(div);
  });

  // Supprimer le div après 5 secondes
  setTimeout(function() {
    document.body.removeChild(div);
  }, displayTime);
}
let answerSent = false;
let intervalId;
let playerAnswer = '';
let answerInputElement;
let submitAnswerButton;
let countdownElement;
function showQuestion(question, currentPlayer) {
  currentPlayer2 = currentPlayer;
  answerSent = false;

  answerInputElement = document.getElementById('answerInput');
  submitAnswerButton = document.getElementById('submitAnswer');
  countdownElement = document.getElementById('countdownDisplay');
  questionElement.textContent = question;
  questionElement.style.display = 'block';

  answerInputElement.addEventListener('input', (event) => {
    playerAnswer = event.target.value;
  });

  if (currentPlayer2 === pseudo) {
    document.getElementById('answerInput').style.display = 'block';
    document.getElementById('submitAnswer').style.display = 'block';
  }




  // Définir la fonction de rappel de l'écouteur d'événements

  // Supprimer l'écouteur d'événements précédent
  let newButton = submitAnswerButton.cloneNode(true);
submitAnswerButton.parentNode.replaceChild(newButton, submitAnswerButton);
submitAnswerButton = newButton;
  // Ajouter le nouvel écouteur d'événements
  submitAnswerButton.addEventListener('click', submitAnswer);
}
function submitAnswer() {
  answerSent = true;
 // console.log('submitAnswer called, answerSent is now', answerSent); // playerAnswer = '';

  //console.log(playerAnswer);
  clearInterval(intervalId);
  countdownElement.style.display = 'none';
  answerInputElement.style.display = 'none';
  submitAnswerButton.style.display = 'none';
  socket.send(JSON.stringify({
    lobbyName: lobbyName,
    type: 'ANSWER',
    answer: playerAnswer,
    pseudo: pseudo,
  }));
}
function showAnswer(playerAnswer, correctAnswer, currentPlayer) {
  currentPlayer2 = currentPlayer;
 // console.log('showAnswer called with', playerAnswer, correctAnswer);

  playerAnswerElement.textContent = playerAnswer;
  correctAnswerElement.textContent = correctAnswer;
  playerAnswerElement.style.display = 'block';
  correctAnswerElement.style.display = 'block';
}
// Définir les éléments de bouton en dehors de la fonction
let voteCorrectButtonElement = document.getElementById('voteCorrectButtonDisplay');
let voteFalseButtonElement = document.getElementById('voteFalseButtonDisplay');
function startVoting() {
  // Supprimer les écouteurs d'événements précédents
  let newVoteCorrectButton = voteCorrectButtonElement.cloneNode(true);
  voteCorrectButtonElement.parentNode.replaceChild(newVoteCorrectButton, voteCorrectButtonElement);
  voteCorrectButtonElement = newVoteCorrectButton;

  let newVoteFalseButton = voteFalseButtonElement.cloneNode(true);
  voteFalseButtonElement.parentNode.replaceChild(newVoteFalseButton, voteFalseButtonElement);
  voteFalseButtonElement = newVoteFalseButton;


  if (currentPlayer2 !== pseudo) {
    voteCorrectButtonElement.style.display = 'block';
    voteFalseButtonElement.style.display = 'block';
  } else {
    voteCorrectButtonElement.style.display = 'none';
    voteFalseButtonElement.style.display = 'none';
    document.getElementById('answerInput').style.display = 'none';
    document.getElementById('submitAnswer').style.display = 'none';
  }

  // Ajouter de nouveaux écouteurs d'événements
  voteCorrectButtonElement.addEventListener('click', voteCorrect);
  voteFalseButtonElement.addEventListener('click', voteFalse);
}
function hideElements() {
  voteCorrectButtonElement.style.display = 'none';
  voteFalseButtonElement.style.display = 'none';
  correctAnswerElement.style.display = 'none';
  playerAnswerDisplay.style.display = 'none';
  questionElement.style.display = 'none';
  action1Element.style.display = 'none';
  action2Element.style.display = 'none';
}
function voteFalse() {
  // Envoyer le vote "faux" au serveur
  socket.send(JSON.stringify({
    lobbyName: lobbyName,
    type: 'VOTE',
    vote: 'false',
    pseudo: pseudo,

  }));
  hideElements();
}
function voteCorrect() {
  // Envoyer le vote "correct" au serveur
  socket.send(JSON.stringify({
    lobbyName: lobbyName,
    type: 'VOTE',
    vote: 'correct',
    pseudo: pseudo,

  }));
  hideElements();
}
function startCountdown(start, duration) {
  //console.log('startCountdown called with', start, duration);
  //console.log('answerSent at the start of startCountdown is', answerSent);
  stopCountdown();
  const answerInputElement = document.getElementById('answerInput');
  const submitAnswerButton = document.getElementById('submitAnswer');
  const end = start * 1000 + duration * 1000; // Convertir en millisecondes
  const playerAnswerElement = document.getElementById('playerAnswerDisplay');
  const countdownElement = document.getElementById('countdownDisplay');
  countdownElement.style.display = 'block';
  const updateCountdown = () => {
    const remaining = (end - Date.now()) / 1000;
    countdownElement.textContent = Math.round(remaining);
    if (remaining <= 0) {
      clearInterval(intervalId);
      countdownElement.style.display = 'none';
      answerInputElement.style.display = 'none';
      submitAnswerButton.style.display = 'none';
      if (!answerSent) {
        submitAnswer();
    }
    }
  };
  updateCountdown();
  intervalId = setInterval(updateCountdown, 1000);
}
function stopCountdown() {
//  console.log('stopCountdown called');
  clearInterval(intervalId);
  countdownElement.style.display = 'none';
}



function showAction(content, currentPlayer) {
  currentPlayer2 = currentPlayer;
  answerSent = false;

  answerInputElement = document.getElementById('answerInput');
  submitAnswerButton = document.getElementById('submitAnswer');
  countdownElement = document.getElementById('countdownDisplay');
  action1Element = document.getElementById('action1Element');
  action2Element = document.getElementById('action2Element');  

  action1Element.textContent = content.action1;
  action2Element.textContent = content.action2;

  action1Element.style.display = 'block';
  action2Element.style.display = 'block';

  if (currentPlayer2 === pseudo) {
    document.getElementById('answerInput').style.display = 'none';
    document.getElementById('submitAnswer').style.display = 'block';
  }

  // Définir la fonction de rappel de l'écouteur d'événements

  // Supprimer l'écouteur d'événements précédent
  let newButton = submitAnswerButton.cloneNode(true);
submitAnswerButton.parentNode.replaceChild(newButton, submitAnswerButton);
submitAnswerButton = newButton;
  // Ajouter le nouvel écouteur d'événements
  submitAnswerButton.addEventListener('click', submitAnswer);
}



function handleCellClick() {
 // console.log('Cell clicked:', this.parentNode.rowIndex, this.cellIndex);
  //console.log('Current player:', currentPlayerPURPLE);
  //console.log('Player attempting to make a move:', pseudo);
  if (pseudo !== currentPlayerPURPLE) {
    console.log("CE N'EST PAS TON TOUR");
    return; // Ajoutez cette ligne pour retourner de la fonction si ce n'est pas le tour du joueur
  }
  if (this.getAttribute('data-played') === 'false') {
  //  console.log('Attempting to make a move:', this.parentNode.rowIndex, this.cellIndex);
    var move = [this.parentNode.rowIndex, this.cellIndex];
    socket.send(JSON.stringify({ type: 'playerMove', move: move, pseudo: pseudo, lobbyName: lobbyName, game: currentGame }));
   // console.log('Move sent to server:', move);
    this.setAttribute('data-played', 'true'); // Mettez à jour l'attribut pour indiquer que la cellule a été jouée
    this.textContent = currentPlayerPURPLE === player1 ? ' X ' : ' O '; // Mettez à jour le contenu de la cellule avec le symbole du joueur actuel
  } else {
 //   console.log('Move not allowed:', this.parentNode.rowIndex, this.cellIndex);
  }
}



function choosePlayersViolet(players) {
  // Supprimer l'ancien div de sélection des joueurs, s'il existe
  var oldDiv = document.getElementById('playerSelection');
  if (oldDiv) document.body.removeChild(oldDiv);

  // Créer un nouvel élément div pour contenir les boutons
  var div = document.createElement('div');
  div.id = 'playerSelection';
  div.className = 'playerSelection'; 

  // Créer un bouton pour chaque joueur
  for (var i = 0; i < players.length; i++) {
    var button = document.createElement('button');
    button.textContent = players[i].pseudo;
    button.addEventListener('click', function() {
      // Ajouter le pseudo du joueur sélectionné à la liste des pseudos sélectionnés
      selectedPseudos.push(this.textContent);
     // console.log("Selected players: " + selectedPseudos);
      this.parentNode.removeChild(this);
      // Si deux joueurs ont été sélectionnés, envoyer les pseudos au serveur
      if (selectedPseudos.length == 2) {
       // console.log({
        //  type: 'playersSelectedViolet',
         // pseudos: selectedPseudos,
          //lobbyName: lobbyName,
        //});
        socket.send(JSON.stringify({
          type: 'playersSelectedViolet',
          pseudos: selectedPseudos,
          lobbyName: lobbyName,
        }));

        // Réinitialiser la liste des pseudos sélectionnés pour la prochaine fois
        selectedPseudos = [];

        // Supprimer la div de sélection des joueurs
        document.body.removeChild(div);
      }
    });
    div.appendChild(button);
  }

  // Ajouter la div de sélection des joueurs au corps du document
  document.body.appendChild(div);
}




// Créez une structure de données pour stocker les mouvements des joueurs
var game = {
  playerMoves: {}
};

// Créez une fonction pour mettre à jour le mouvement d'un joueur
game.updateMove = function(pseudo, move) {
  // Envoyer le mouvement au serveur via le WebSocket
  socket.send(JSON.stringify({
    type: 'playerMove',
    pseudo: pseudo,
    lobbyName: lobbyName,
    move: move
  }));
};


// Appeler la fonction pour ouvrir la connexion WebSocket
openSocketConnection();