export function updateGameState(gameState) {
    const gameScene = document.getElementById("game-scene");
    document.body.appendChild(blueCardDialog);
    document.body.appendChild(persistentElementsJAUNE);
    gameScene.innerHTML = ""; // Clear the game scene
    gameScene.appendChild(blueCardDialog);
    gameScene.appendChild(persistentElementsJAUNE);
    const gameArea = document.getElementById("game-area");
  
    // Create a visual representation of the game state
    // console.log(gameState.players); // Log the players array
  
    // Create an element to display the turn count
    let turnCountElement = document.getElementById("turn-count");
    if (!turnCountElement) {
      // Si turnCountElement n'existe pas, le créer
      turnCountElement = document.createElement("div");
      turnCountElement.id = "turn-count";
      turnCountElement.classList.add("turn-count-class");
      gameArea.appendChild(turnCountElement);
    }
    const turnCount = gameState.turnCount ? gameState.turnCount : 0;
    if (window.innerWidth < 1900) {
      turnCountElement.textContent = turnCount + "/30";
    } else {
      turnCountElement.textContent = "Nombre de tours: " + turnCount + "/30";
    }
    let pseudoIndex = gameState.players.findIndex(
      (player) => player.pseudo === pseudo
    );
  
    for (let i = 0; i < gameState.players.length; i++) {
      const player = gameState.players[i];
      var playerId = gameState.playerId;
      var playerCardsElement = document.createElement("div");
      const sipsTakenElement = document.createElement("div");
      sipsTakenElement.classList.add("sips-taken");
      sipsTakenElement.style.top = `${i * 55}px`; // Adjust as needed
      if (window.innerWidth <= 1900) {
        sipsTakenElement.innerHTML =
          player.pseudo +
          ": " +
          player.sipsTaken +
          '<img src="img/createur.png" width="30px"/>';
      } else {
        sipsTakenElement.textContent =
          player.pseudo + ": Gorgées prises: " + player.sipsTaken;
      }
      playerCardsElement.classList.add("player-cards"); // Ajoutez cette ligne
  
      if (player.pseudo === pseudo) {
        playerCardsElement.id = "player1-cards";
        playerCardsElement.classList.add("player" + 1 + "-cards");
        if (playerId !== 1) {
          tempId = playerId;
        }
      } else if (playerId === 1 && player.pseudo !== pseudo) {
        playerCardsElement.id = "player" + tempId + "-cards"; // Attribuer l'ID stocké au joueur avec l'ID 1
        playerCardsElement.classList.add("player" + tempId + "-cards");
      } else {
        playerCardsElement.id = "player" + playerId + "-cards";
        playerCardsElement.classList.add("player" + playerId + "-cards");
      }
  
      if (gameStarted === false) {
        endTurnButton = document.getElementById("end-turn");
        endTurnButton.style.display = "none";
      }
  
      ////////////METTRE EN HAUT A GAUCHE PAR EXEMPLE ET AJOUTER LE PSEUDO A COTE DES CARTE UNIQUEMENT POUR LE JOUEUR ACTRIF /////////////////
      const playerElement = document.createElement("div");
      //playerElement.classList.add('player-cards'); // Add this line
      //playerElement.textContent = player.pseudo;
      ////////////METTRE EN HAUT A GAUCHE PAR EXEMPLE ET AJOUTER LE PSEUDO A COTE DES CARTE UNIQUEMENT POUR LE JOUEUR ACTRIF /////////////////
  
      // If the player is the active player, create a visual representation of their cards
      if (player.isPlayerTurn) {
        playerElement.textContent = player.pseudo;
        playerElement.classList.add("player-pseudo");
        var currentPlayerElement = document.getElementById("current-player");
        //  currentPlayerElement.textContent = "C'est le tour de : " + player.pseudo;
        const gameArea = document.getElementById("game-area");
        sipsTakenElement.classList.add("sips-taken");
        sipsTakenElement.style.top = `${i * 55}px`; // Adjust as needed
        if (window.innerWidth <= 1900) {
          sipsTakenElement.innerHTML =
            `<strong>${player.pseudo} : ${player.sipsTaken}</strong>` +
            '<img src="img/createur.png" width="30px"/>';
        } else {
          sipsTakenElement.innerHTML = `<strong>${player.pseudo} : Gorgées prises: ${player.sipsTaken}</strong>`;
        }
  
        if (player.pseudo === pseudo) {
          playerCardsElement.id = "player1-cards";
          playerCardsElement.classList.add("player" + 1 + "-cards");
          if (playerId !== 1) {
            tempId = playerId;
          }
        } else if (playerId === 1 && player.pseudo !== pseudo) {
          playerCardsElement.id = "player" + tempId + "-cards"; // Attribuer l'ID stocké au joueur avec l'ID 1
          playerCardsElement.classList.add("player" + tempId + "-cards");
        } else {
          playerCardsElement.id = "player" + playerId + "-cards";
          playerCardsElement.classList.add("player" + playerId + "-cards");
        }
      }
  
      if (player.isPlayerTurn && player.pseudo === pseudo) {
        for (let i = 0; i < player.cards.length; i++) {
          const card = player.cards[i];
          const cardElement = document.createElement("div");
          //cardElement.textContent = card.color;
          cardElement.id = card.id;
          cardElement.classList.add("card"); // Add this line
          cardElement.classList.add("card-" + i); // Add this line
          cardElement.draggable = true; // Add this line
  
          cardElement.addEventListener("dragstart", function (event) {
            //   console.log('Drag started');
            //  console.log('Card ID:', cardElement.id); // Log the card ID
            event.dataTransfer.setData("text/plain", cardElement.id);
          });
          cardElement.addEventListener("touchstart", function (event) {
            // Obtenir toutes les cartes
            var cards = document.querySelectorAll(".card");
  
            // Utiliser event.currentTarget à la place de cardElement
            var currentCard = event.currentTarget;
  
            // Si la carte cliquée était déjà sélectionnée, la désélectionner
            if (currentCard.classList.contains("card-selected")) {
              currentCard.classList.remove("card-selected");
              activeCardId = null;
            } else {
              // Parcourir toutes les cartes et les désélectionner
              for (var i = 0; i < cards.length; i++) {
                cards[i].classList.remove("card-selected");
              }
  
              // Si la carte cliquée n'était pas sélectionnée, la sélectionner
              currentCard.classList.add("card-selected");
              activeCardId = currentCard.id;
            }
          });
          cardElement.addEventListener("touchmove", function (event) {
            event.preventDefault(); // Prevent scrolling on touch devices
          });
  
          const cardImage = document.createElement("img");
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
      } else if (!player.isPlayerTurn && player.pseudo === pseudo) {
        for (let i = 0; i < player.cards.length; i++) {
          const card = player.cards[i];
          const cardElement = document.createElement("div");
          const cardImage = document.createElement("img");
          cardImage.src = card.image;
          cardElement.appendChild(cardImage);
          playerElement.appendChild(cardElement);
          playerCardsElement.appendChild(cardElement);
        }
        gameScene.appendChild(playerCardsElement); // Ajoutez playerCardsElement à gameScene
      }
      const dropzone = document.getElementById("dropzone");
      dropzone.innerHTML = ""; // Clear the dropzone
      let j = 0;
      if (gameState.dropzone && Array.isArray(gameState.dropzone)) {
        for (let i = 0; i < gameState.dropzone.length; i++) {
          j++;
          const card = gameState.dropzone[i];
          const cardElement = document.createElement("div");
          const cardImage = document.createElement("img");
          cardImage.src = "img/" + card.color + ".png"; // Replace this with your card image URL
          cardElement.appendChild(cardImage);
          cardElement.id = card.id;
          cardElement.classList.add("card-in-dropzone");
          cardElement.classList.add("card-" + j); // Add this line
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
          const playerCardsElement = document.createElement("div");
          displayIndex = index < pseudoIndex ? index + 2 : index + 1;
          playerCardsElement.id = "player" + displayIndex + "-cards";
          playerCardsElement.classList.add("player-cards");
          playerCardsElement.classList.add("player" + displayIndex + "-cards");
          // console.log(playerCardsElement.classList);
          for (let i = 0; i < player.cards.length; i++) {
            const cardElement = document.createElement("div");
            const cardImage = document.createElement("img");
            cardImage.src = "img/dos.png"; // Change this to the path of your back card image
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
      endTurnButton = document.getElementById("end-turn");
      endTurnButton.style.display = "none";
    }
  }
  
  export function updateButtonDisplay() {
    // console.log('Updating button display, gameStarted:', gameStarted, 'isPlayerTurn:', isPlayerTurn);
  
    //  console.log("UPDATEBUTTONDISPLAY");
    if (gameStarted) {
      //  console.log("GAME START UPDATEBUTTONDISPLAY");
      startPartyButton.style.display = "none";
      if (isPlayerTurn) {
        //   console.log("TOUR DU JOUEUR UPDATEBUTTONDISPLAY");
        endTurnButton.style.display = "block";
        //console.log(endTurnButton)
      } else {
        endTurnButton.style.display = "none";
        // console.log(" PAS TOUR DU JOUEUR UPDATEBUTTONDISPLAY");
        //console.log(endTurnButton)
      }
    } else {
      // console.log("PAS DE GAME");
      startPartyButton.style.display = "none";
      endTurnButton.style.display = "none";
    }
  }
  
  export function adjustGameSceneSize() {
    var gameScene = document.getElementById("game-scene");
    var aspectRatio = 1; // Replace with the aspect ratio of your image
  
    if (window.innerWidth <= 1900) {
      // If the screen width is 900px or less
      gameScene.style.width = window.innerWidth + "px";
      gameScene.style.height = window.innerHeight + "px";
    } else {
      var windowAspectRatio = window.innerWidth / window.innerHeight;
      if (windowAspectRatio > aspectRatio) {
        gameScene.style.height = window.innerHeight + "px";
        gameScene.style.width = window.innerHeight * aspectRatio + "px";
      } else {
        gameScene.style.width = window.innerWidth + "px";
        gameScene.style.height = window.innerWidth / aspectRatio + "px";
      }
    }
  }