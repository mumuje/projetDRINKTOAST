
/*************************************************************************/
// Initialisation des variables
let currentLobby = null;
let playersInCurrentLobby = new Set();
let playerList = document.getElementById("player-list");
let Creator = false;
var gameStarted3 = false;
let socket;

/*************************************************************************/
// =Connexion websocket
if (socket && socket.readyState === WebSocket.OPEN) {
  socket.close();
}
socket = new WebSocket("ws://109.122.198.14:8080/websocket");

socket.addEventListener("open", (event) => {
  console.log("Connexion WebSocket ouverte");
  socket.send(JSON.stringify({ type: "getLobbyList" }));
});

socket.addEventListener("message", (event) => {
  console.log("Message reçu du serveur WebSocket:", event.data);
  const data = JSON.parse(event.data);

  if (data.type === "lobbyList") {
    updateLobbyList(data.lobbies);
  }
});

socket.addEventListener("close", (event) => {
  console.log("Connexion WebSocket fermée");
});

socket.addEventListener("error", (event) => {
  console.error("Erreur de connexion WebSocket:", event);
});


/*************************************************************************/
// Créer un nouveau lobby
document.getElementById("create-lobby-form")
  .addEventListener("submit", (event) => {
    event.preventDefault(); // Empêche le rechargement de la page lors de la soumission du formulaire
    const lobbyName = document.getElementById("lobby-name").value; // Récupère le nom du lobby
    const lobbyPassword = document.getElementById("lobby-password").value; // Récupère le mot de passe du lobby
    const pseudo = localStorage.getItem("pseudo"); // Récupère le pseudo de l'utilisateur
    if (!pseudo) { // Vérifie si le pseudo existe
      alert("Vous devez entrer un pseudo avant de créer un lobby."); // Affiche une alerte si le pseudo n'existe pas
      return; // Termine la fonction
    }
    // Envoie un message au serveur pour créer un nouveau lobby
    socket.send(
      JSON.stringify({
        type: "createLobby",
        lobbyName,
        pseudo,
        password: lobbyPassword,
      })
    );
    console.log("Lobby créé : " + lobbyName + ", Pseudo : " + pseudo); // Affiche un message dans la console
    console.log("coucou" + lobbyName); // Affiche un autre message dans la console
    document.getElementById("pseudo-form").style.display = "none"; // Cache le formulaire de pseudo
  });

// Une fonction qui empêche l'action par défaut lors d'un clic
const handleClick = (event) => {
  event.preventDefault();
};

/*************************************************************************/
// création du pseudo du joueur
window.onload = function () {
  var gameStarted = false;
  var gameState = {};
  localStorage.setItem("gameStarted", gameStarted);
  localStorage.setItem("gameState", JSON.stringify(gameState));
  const pseudoInput = document.getElementById("pseudo");
  const pseudoButton = document.querySelector("#pseudo-form button");
  const storedPseudo = localStorage.getItem("pseudo");

  if (storedPseudo) {
    // Si un pseudo a déjà été enregistré
    pseudoInput.value = storedPseudo;
    pseudoInput.disabled = true;
    pseudoButton.textContent = "Changer le pseudo";
  }

  document.getElementById("pseudo-form").addEventListener("submit", (event) => {
    event.preventDefault();
    const pseudo = pseudoInput.value;
    if (pseudoButton.textContent === "Changer le pseudo") {
      // Réinitialiser le champ de saisie
      pseudoInput.type = "text";
      pseudoInput.value = "";
      pseudoInput.disabled = false;
      // Changer le texte du bouton
      pseudoButton.textContent = "Enregistrer le pseudo!";
    } else {
      // Si aucun pseudo n'a été enregistré
      // Enregistrer le pseudo
      localStorage.setItem("pseudo", pseudo);
      console.log("Pseudo enregistré : " + localStorage.getItem("pseudo")); // Affiche le pseudo dans la console
      // Remplacer le champ de saisie par le pseudo
      pseudoInput.type = "text";
      pseudoInput.value = pseudo;
      pseudoInput.disabled = true;
      // Changer le texte du bouton
      pseudoButton.textContent = "Changer le pseudo";
    }
  });
};



/*************************************************************************/
//TOUTES LES REPONSES DU SERVEUR 
const lobbyList = document.getElementById("lobby-list");
if (lobbyList) {
  socket.addEventListener("message", (event) => {
    console.log("Message reçu du serveur WebSocket:", event.data);
    const data = JSON.parse(event.data);

    /***********************************************/
    // MESSAGE LOBBYLIST DU SERVEUR
    if (data.type === "lobbyList") {
      const lobbies = data.lobbies;
      lobbyList.innerHTML = "";
      lobbies.forEach((lobby) => {
        const gameStarted2 = lobby.gameStarted;

        const listItem = document.createElement("li");
        const link = document.createElement("a");
        link.href = `#`;
        link.dataset.lobbyName = lobby.name; // Ajoute un attribut de données personnalisé avec le nom du lobby
        link.textContent = `${lobby.name} (${lobby.playerCount}/8 joueurs)`; 
        if (gameStarted2) {
          link.classList.add("disabled");
          link.href = "#";
          link.removeEventListener("click", handleClick);
        } else {
          link.addEventListener("click", (event) => handleClick(event, lobby));
        }
        if (lobby.requiresPassword) {
          const img = document.createElement("img");
          img.src = "img/cadena.png"; 
          img.alt = "Cadenas";
          img.style.width = "20px"; 
          img.style.height = "20px"; 

          link.appendChild(img);
        }

        link.addEventListener("click", (event) => {
          const pseudo = localStorage.getItem("pseudo");
          event.preventDefault();
          let password = "";
          if (!pseudo) {
            alert("Vous devez entrer un pseudo avant de rejoindre un lobby.");
            return;
          }
          if (lobby.requiresPassword) {
            password = prompt(
              "Veuillez entrer le mot de passe pour le lobby " +
                lobby.name +
                " :"
            );
          }
          // Cachez le formulaire de création de lobby
          document.getElementById("lobby-creation").style.display = "none";

          document.getElementById("pseudo-form").style.display = "none";

          // Affichez les informations sur le lobby
          document.getElementById("lobby-info").style.display = "block";

          // Mettez à jour le nom du lobby affiché
          if (lobby.requiresPassword) {
            console.log("Le lobby nécessite un mot de passe");
            document.getElementById(
              "lobby-name-display"
            ).innerHTML = `${lobby.name} <img src="img/cadena.png" alt="Cadenas" style="width: 30px; height: 30px;">`;
          } else {
            document.getElementById("lobby-name-display").textContent =
              lobby.name;
          }

          // Envoyez un message au serveur pour rejoindre le lobby

          console.log({
            type: "joinLobby",
            lobbyName: lobby.name,
            pseudo,
            password,
          });
          socket.send(
            JSON.stringify({
              type: "joinLobby",
              lobbyName: lobby.name,
              pseudo,
              password,
            })
          );
        });

        listItem.appendChild(link);
        lobbyList.appendChild(listItem);
        console.log(lobby);
      });
    } 
        /***********************************************/
        // MESSAGE LOBBYCREATED DU SERVEUR
    else if (data.type === "lobbyCreated") {
      const lobbyName = data.lobbyName;
      const listItem = document.createElement("li");
      const link = document.createElement("a");
      const requiresPassword = data.requiresPassword; 
      const pseudo = data.pseudo;

      link.href = `#`;
      link.textContent = `${lobbyName} (1 joueur)`;
      link.addEventListener("click", (event) => {
        event.preventDefault();
        // Cachez le formulaire de création de lobby$pseudo$ps$pseudo
        document.getElementById("lobby-creation").style.display = "none";

        // Affichez les informations sur le lobby
        document.getElementById("lobby-info").style.display = "block";

        // Mettez à jour le nom du lobby affiché
        if (requiresPassword) {
          document.getElementById(
            "lobby-name-display"
          ).innerHTML = `${lobbyName} <img src="img/cadena.png" alt="Cadenas" style="width: 30px; height: 30px;">`;
        } else {
          document.getElementById("lobby-name-display").textContent = lobbyName;
        }
      });
      listItem.appendChild(link);
      lobbyList.appendChild(listItem);

      // Cachez le formulaire de création de lobby
      document.getElementById("lobby-creation").style.display = "none";

      // Affichez les informations sur le lobby
      document.getElementById("lobby-info").style.display = "block";

      // Mettez à jour le nom du lobby affiché
      if (requiresPassword) {
        document.getElementById(
          "lobby-name-display"
        ).innerHTML = `${lobbyName} <img src="img/cadena.png" alt="Cadenas" style="width: 30px; height: 30px;">`;
      } else {
        document.getElementById("lobby-name-display").textContent = lobbyName;
      }
    } 
      /***********************************************/
      // MESSAGE LOBBYJOINED DU SERVEUR
    else if (data.type === "lobbyJoined") {
      console.log("Joined lobby");
      currentLobby = data.lobbyName;
      const playerList = document.getElementById("player-list");
      playerList.innerHTML = "";

      if (data.players) {
        data.players.forEach((player) => {
          const listItem = document.createElement("li");
          listItem.textContent = player.pseudo;
          if (player.isCreator) {
            Creator = true;
            listItem.classList.add("creator");
            const crownImage = document.createElement("img");
            crownImage.src = "img/createur.png"; 
            crownImage.alt = "Crown";
            crownImage.classList.add("crown-icon");
            listItem.appendChild(crownImage);
          } else {
            Creator = false;
          }
          playerList.appendChild(listItem);
        });
      }
    } 
          /***********************************************/
          // MESSAGE LOBBYUPDATED DU SERVEUR
    else if (data.type === "lobbyUpdated") {
      console.log("lobb updated");
      const lobbyName = data.lobbyName;
      const playerCount =
        document.getElementById("player-list").children.length;
      // Mettez à jour l'affichage du lobby
      const lobbyElement = Array.from(
        document.querySelectorAll("#lobby-list a")
      ).find((a) => a.textContent.includes(lobbyName));

      const playerList = document.getElementById("player-list");
      playerList.innerHTML = "";
      for (let player of data.players) {
        // Parcourir la liste des joueurs
        // Créer un nouvel élément de liste pour chaque joueur
        const listItem = document.createElement("li");

        listItem.textContent = player.pseudo;
        if (player.isCreator) {
          Creator = true;
          listItem.classList.add("creator");
          const crownImage = document.createElement("img");
          crownImage.src = "img/createur.png"; 
          crownImage.alt = "Crown";
          crownImage.classList.add("crown-icon");
          listItem.appendChild(crownImage);
        }

        const playerList = document.getElementById("player-list");
        playerList.appendChild(listItem);
        playersInCurrentLobby.add(player.pseudo);
      }
      updatePlayerCount();
    } else if (data.type === "playerJoined" && !playersInCurrentLobby.has(data.pseudo))
     {
      console.log("player joined");
      const pseudo = data.pseudo;
      const playerList = document.getElementById("player-list");
      const listItem = document.createElement("li");
      listItem.textContent = pseudo;
      playerList.appendChild(listItem);
      const lobbyLink = document.querySelector(
        `a[data-lobby-name="${data.lobbyName}"]`
      );
    } 
        /***********************************************/
        // MESSAGE PLAYERLEFT DU SERVEUR
    else if (data.type === "playerLeft") {
      const pseudo = data.pseudo;
      const playerList = document.getElementById("player-list");
      const playerListItem = Array.from(playerList.children).find(
        (li) => li.textContent === pseudo
      );
      if (playerListItem) {
        if (playerListItem.classList.contains("creator")) {
          playerListItem.classList.remove("creator");
          const nextPlayer = playerListItem.nextElementSibling;
          if (nextPlayer) {
            // Attribuer le rôle de créateur au prochain joueur
            nextPlayer.classList.add("creator");
            const crownImage = document.createElement("img");
            crownImage.src = "img/createur.png"; 
            crownImage.alt = "Crown";
            crownImage.classList.add("crown-icon");
            nextPlayer.appendChild(crownImage);
            Creator = true;
          }
        }
        playerList.removeChild(playerListItem);

        updatePlayerCount();
      }
      const lobbyLink = document.querySelector(
        `a[data-lobby-name="${data.lobbyName}"]`
      );
    } 
        /***********************************************/
        // MESSAGE joinLobbyFailed DU SERVEUR
    else if (data.type === "joinLobbyFailed") {
      document.getElementById("pseudo-form").style.display = "block";

      document.getElementById("lobby-creation").style.display = "block";

      document.getElementById("lobby-info").style.display = "none";

      document.getElementById("lobby-name-display").textContent = "";

      alert(data.message);
    } 
        /***********************************************/
        // MESSAGE joinLobbyFailed DU SERVEUR
    else if (data.type === "startGame") {
      gameStarted3 = data.gameStarted;
      console.log(gameStarted3);
      const lobbyName =
        document.getElementById("lobby-name-display").textContent;
      window.location.href = `game.php?lobby=${encodeURIComponent(lobbyName)}`;
    } else if (data.type === "error") {
      document.getElementById("pseudo-form").style.display = "block";

      document.getElementById("lobby-creation").style.display = "block";

      document.getElementById("lobby-info").style.display = "none";

      document.getElementById("lobby-name-display").textContent = "";


      alert(data.message);
    } // else if
  });
}






/*************************************************************************/
// Permet de lancer la game
function startGame() {
  const playerCount = document.getElementById("player-list").children.length;
  const errorMessageElement = document.getElementById("error-message");
  if (Creator == true) {
    if (playerCount < 2) {
      errorMessageElement.textContent =
        "Erreur : au moins deux joueurs dans le lobby pour commencer le jeu.";
      return;
    }
    errorMessageElement.textContent = ""; 
    console.log("création de la partie!");
    const lobbyName = document.getElementById("lobby-name-display").textContent;
    socket.send(JSON.stringify({ type: "startGame", lobbyName }));
  } else {
    console.log("no createur");
    errorMessageElement.textContent =
      "Erreur : Seul le créateur de la partie peut lancer la partie.";
  }
}
/*************************************************************************/
// Permet de mettre a jour le nombre de joueurs dans le lobby
function updatePlayerCount() {
  const lobbyName = document.getElementById("lobby-name-display").textContent;
  const lobbyElement = Array.from(
    document.querySelectorAll("#lobby-list a")
  ).find((a) => a.textContent.includes(lobbyName));
  const playerCountElement = document.getElementById("player-count");
  const playerCount = document.getElementById("player-list").children.length;
  playerCountElement.textContent = `Joueurs: ${playerCount}/8`;
}





/*************************************************************************/
// Permet de mettre à jour la liste des lobbies
function updateLobbyList(lobbies) {
  const lobbyList = document.getElementById("lobby-list");
  lobbyList.innerHTML = ""; // Clear the lobby list

  for (let lobby of lobbies) {
    const listItem = document.createElement("li");
    const link = document.createElement("a");
    link.textContent = `${lobby.name} (${lobby.players}/8 joueurs)`;
    link.href = `#lobby/${lobby.name}`;

    listItem.appendChild(link);
    lobbyList.appendChild(listItem);
  }
}

/*************************************************************************/
// Permet de mettre à jour le compteur de caractères restants
// lorsque l'utilisateur saisit du texte dans le champ de saisie
//

function updateCounter() {
  var remaining = 1000 - document.getElementById("message").value.length;
  document.getElementById("counter").textContent =
    remaining + " caractères restants";
}
updateCounter();
