
var gameStarted3 = false;
let socket;
if(socket && socket.readyState === WebSocket.OPEN) {
  socket.close();
}
socket = new WebSocket('ws://192.168.1.101:8080/websocket');
//const socket = new WebSocket('ws://localhost:8080/websocket');

socket.addEventListener('open', (event) => {
  console.log('Connexion WebSocket ouverte');
  socket.send(JSON.stringify({ type: 'getLobbyList' }));
  
});

socket.addEventListener('message', (event) => {
  console.log('Message reçu du serveur WebSocket:', event.data);
  const data = JSON.parse(event.data);

  if (data.type === 'lobbyList') {
    updateLobbyList(data.lobbies);
  }
  // Handle other message types...
});

socket.addEventListener('close', (event) => {
  console.log('Connexion WebSocket fermée');
});

socket.addEventListener('error', (event) => {
  console.error('Erreur de connexion WebSocket:', event);
});
let currentLobby = null;
let playersInCurrentLobby = new Set();
let playerList = document.getElementById('player-list');
let Creator = false;

// Créer un nouveau lobby
document.getElementById('create-lobby-form').addEventListener('submit', (event) => {
  event.preventDefault();
  const lobbyName = document.getElementById('lobby-name').value;
  const lobbyPassword = document.getElementById('lobby-password').value;
  const pseudo = localStorage.getItem('pseudo');
  if (!pseudo) {
    alert('Vous devez entrer un pseudo avant de créer un lobby.');
    return;
  }
  socket.send(JSON.stringify({ type: 'createLobby', lobbyName, pseudo, password: lobbyPassword }));
  console.log("Lobby créé : " + lobbyName + ", Pseudo : " + pseudo);
    console.log("coucou" + lobbyName);
    document.getElementById('pseudo-form').style.display = 'none';
 

});

const handleClick = (event) => {
  event.preventDefault();
};
//pseudo du joueur 
window.onload = function() {
    var gameStarted = false;
  var gameState = {};
  localStorage.setItem('gameStarted', gameStarted);
  localStorage.setItem('gameState', JSON.stringify(gameState));
  const pseudoInput = document.getElementById('pseudo');
  const pseudoButton = document.querySelector('#pseudo-form button');
  const storedPseudo = localStorage.getItem('pseudo');

  if (storedPseudo) {
    // Si un pseudo a déjà été enregistré
    pseudoInput.value = storedPseudo;
    pseudoInput.disabled = true;
    pseudoButton.textContent = 'Changer le pseudo';
  }

  document.getElementById('pseudo-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const pseudo = pseudoInput.value;
    if (pseudoButton.textContent === 'Changer le pseudo') {
      // Réinitialiser le champ de saisie
      pseudoInput.type = 'text';
      pseudoInput.value = '';
      pseudoInput.disabled = false;
      // Changer le texte du bouton
      pseudoButton.textContent = 'Enregistrer le pseudo!';
    } else {
      // Si aucun pseudo n'a été enregistré
      // Enregistrer le pseudo
      localStorage.setItem('pseudo', pseudo);
      console.log("Pseudo enregistré : " + localStorage.getItem('pseudo')); // Affiche le pseudo dans la console
      // Remplacer le champ de saisie par le pseudo
      pseudoInput.type = 'text';
      pseudoInput.value = pseudo;
      pseudoInput.disabled = true;
      // Changer le texte du bouton
      pseudoButton.textContent = 'Changer le pseudo';
    }
  });
}

// Rejoindre un lobby existant
const lobbyList = document.getElementById('lobby-list');
if (lobbyList) {
  socket.addEventListener('message', (event) => {
    console.log('Message reçu du serveur WebSocket:', event.data);
    const data = JSON.parse(event.data);
    if (data.type === 'lobbyList') {


      const lobbies = data.lobbies;
      lobbyList.innerHTML = '';
      lobbies.forEach((lobby) => {
        const gameStarted2 = lobby.gameStarted; // Ajoutez cette ligne

        const listItem = document.createElement('li');
        const link = document.createElement('a');
        link.href = `#`;
        link.dataset.lobbyName = lobby.name; // Ajoute un attribut de données personnalisé avec le nom du lobby
        link.textContent = `${lobby.name} (${lobby.playerCount}/8 joueurs)`; // Si lobby.players est un nombre     
        if (gameStarted2) {
         link.classList.add('disabled');
          link.href = '#';
          link.removeEventListener('click', handleClick);
        }     
        else {
          link.addEventListener('click', (event) => handleClick(event, lobby));
      }
        //let password = '';
        if (lobby.requiresPassword) {
          const img = document.createElement('img');
          img.src = 'img/cadena.png'; // Mettez le chemin correct si le fichier n'est pas dans le même répertoire
          img.alt = 'Cadenas';
          img.style.width = '20px'; // Définir la largeur de l'image
          img.style.height = '20px'; // Définir la hauteur de l'image
      
              link.appendChild(img);
          }
      
        link.addEventListener('click', (event) => {
          const pseudo = localStorage.getItem('pseudo');
          event.preventDefault();
          let password = '';
          if (!pseudo) {
            alert('Vous devez entrer un pseudo avant de rejoindre un lobby.');
            return;
        }
          if (lobby.requiresPassword) {
            password = prompt('Veuillez entrer le mot de passe pour le lobby ' + lobby.name + ' :');
          }     
               // Cachez le formulaire de création de lobby
          document.getElementById('lobby-creation').style.display = 'none';

          document.getElementById('pseudo-form').style.display = 'none';
          
          // Affichez les informations sur le lobby
          document.getElementById('lobby-info').style.display = 'block';

          // Mettez à jour le nom du lobby affiché
          if (lobby.requiresPassword) {
            console.log('Le lobby nécessite un mot de passe');
            document.getElementById('lobby-name-display').innerHTML = `${lobby.name} <img src="img/cadena.png" alt="Cadenas" style="width: 30px; height: 30px;">`;
          } else {
            document.getElementById('lobby-name-display').textContent = lobby.name;
          }


          // Envoyez un message au serveur pour rejoindre le lobby
     
          console.log({type: 'joinLobby', lobbyName: lobby.name, pseudo, password, });
          socket.send(JSON.stringify({ type: 'joinLobby', lobbyName: lobby.name, pseudo, password }));
        });

        
      listItem.appendChild(link);
      lobbyList.appendChild(listItem);
      console.log(lobby);
      });
      
    
    } else if (data.type === 'lobbyCreated') {
      const lobbyName = data.lobbyName;
      const listItem = document.createElement('li');
      const link = document.createElement('a');
      const requiresPassword = data.requiresPassword; // Assume that the server sends this information
      const pseudo = data.pseudo; // Assume that the server sends the pseudo of the lobby creator
      
      link.href = `#`;
      link.textContent = `${lobbyName} (1 joueur)`;
      link.addEventListener('click', (event) => {
        event.preventDefault();
        // Cachez le formulaire de création de lobby$pseudo$ps$pseudo
        document.getElementById('lobby-creation').style.display = 'none';

  
        // Affichez les informations sur le lobby
        document.getElementById('lobby-info').style.display = 'block'; 
    
        // Mettez à jour le nom du lobby affiché
        if (requiresPassword) {
          document.getElementById('lobby-name-display').innerHTML = `${lobbyName} <img src="img/cadena.png" alt="Cadenas" style="width: 30px; height: 30px;">`;
        } else {
          document.getElementById('lobby-name-display').textContent = lobbyName;
        }


      });
      listItem.appendChild(link);
      lobbyList.appendChild(listItem);


    
      // Cachez le formulaire de création de lobby
      document.getElementById('lobby-creation').style.display = 'none';
    
      // Affichez les informations sur le lobby
      document.getElementById('lobby-info').style.display = 'block';
    
      // Mettez à jour le nom du lobby affiché
      if (requiresPassword) {
        document.getElementById('lobby-name-display').innerHTML = `${lobbyName} <img src="img/cadena.png" alt="Cadenas" style="width: 30px; height: 30px;">`;
      } else {
        document.getElementById('lobby-name-display').textContent = lobbyName;
      }

    } else if (data.type === 'lobbyJoined') {
      console.log ("Joined lobby")
      currentLobby = data.lobbyName;
      const playerList = document.getElementById('player-list');
      playerList.innerHTML = '';
      
      if (data.players) {
        data.players.forEach(player => {
          const listItem = document.createElement('li');
          listItem.textContent = player.pseudo;
          if (player.isCreator) {
            Creator = true;
            listItem.classList.add('creator');
            const crownImage = document.createElement('img');
            crownImage.src = 'img/createur.png'; // Remplacez par le chemin de votre image de couronne
            crownImage.alt = 'Crown';
            crownImage.classList.add('crown-icon');
            listItem.appendChild(crownImage);
          } else {
            Creator = false;
          }
          playerList.appendChild(listItem);
        });
      }
    }
    
     else if (data.type === 'lobbyUpdated') {

      console.log('lobb updated');
      const lobbyName = data.lobbyName;
      const playerCount = document.getElementById('player-list').children.length;
      // Mettez à jour l'affichage du lobby
      const lobbyElement = Array.from(document.querySelectorAll('#lobby-list a')).find(a => a.textContent.includes(lobbyName));

      const playerList = document.getElementById('player-list');
      playerList.innerHTML = '';
      for (let player of data.players) {
  // Parcourir la liste des joueurs
    // Créer un nouvel élément de liste pour chaque joueur
    const listItem = document.createElement('li');

    listItem.textContent = player.pseudo;
      if (player.isCreator) {
        Creator = true;
        listItem.classList.add('creator');
        const crownImage = document.createElement('img');
        crownImage.src = 'img/createur.png'; // Remplacez par le chemin de votre image de couronne
        crownImage.alt = 'Crown';
        crownImage.classList.add('crown-icon');
        listItem.appendChild(crownImage);
      } 
   
      const playerList = document.getElementById('player-list');
      playerList.appendChild(listItem);
      playersInCurrentLobby.add(player.pseudo);
    }
    updatePlayerCount();
  
    }
    
    else if (data.type === 'playerJoined' && !playersInCurrentLobby.has(data.pseudo)) {
      console.log ("player joined")
      const pseudo = data.pseudo;
      const playerList = document.getElementById('player-list');
      const listItem = document.createElement('li');
      listItem.textContent = pseudo;
      playerList.appendChild(listItem);
      const lobbyLink = document.querySelector(`a[data-lobby-name="${data.lobbyName}"]`);

      
      
    } else if (data.type === 'playerLeft') {
      const pseudo = data.pseudo;
      const playerList = document.getElementById('player-list');
      const playerListItem = Array.from(playerList.children).find(li => li.textContent === pseudo);
      if (playerListItem) {
        if (playerListItem.classList.contains('creator')) {
          playerListItem.classList.remove('creator');
          const nextPlayer = playerListItem.nextElementSibling;
          if (nextPlayer) {
            // Attribuer le rôle de créateur au prochain joueur
            nextPlayer.classList.add('creator');
            const crownImage = document.createElement('img');
            crownImage.src = 'img/createur.png'; // Remplacez par le chemin de votre image de couronne
            crownImage.alt = 'Crown';
            crownImage.classList.add('crown-icon');
            nextPlayer.appendChild(crownImage);
            Creator = true;
          }
        }
          playerList.removeChild(playerListItem);
         
          updatePlayerCount(); // Ajoutez cette ligne
      }
      const lobbyLink = document.querySelector(`a[data-lobby-name="${data.lobbyName}"]`);
     // if (lobbyLink) {
        // Mettez à jour le texte du lien avec le nouveau nombre de joueurs
       // lobbyLink.textContent = `${data.lobbyName} (${data.playerCount}/82 joueurs)`;
     // }
    } 
    
    else if (data.type === 'joinLobbyFailed') {

      document.getElementById('pseudo-form').style.display = 'block';

      document.getElementById('lobby-creation').style.display = 'block';

      document.getElementById('lobby-info').style.display = 'none';

      document.getElementById('lobby-name-display').textContent = '';
      alert(data.message);



    } else if (data.type === 'startGame')  {
      /*const currentLobby = localStorage.getItem('currentLobby');
      if (lobbyName !== data.lobbyName) {
          console.log('Received startGame message for a different lobby');
          return;
      }*/
  
      gameStarted3 = data.gameStarted;
      console.log(gameStarted3);
      const lobbyName = document.getElementById('lobby-name-display').textContent;
      window.location.href = `game.php?lobby=${encodeURIComponent(lobbyName)}`;   
  }
     
     
     else if (data.type === 'error') {
      document.getElementById('pseudo-form').style.display = 'block';

      document.getElementById('lobby-creation').style.display = 'block';

      document.getElementById('lobby-info').style.display = 'none';

      document.getElementById('lobby-name-display').textContent = '';
      alert(data.message);
     } // else if
      });
}

function startGame() {
  const playerCount = document.getElementById('player-list').children.length;
  const errorMessageElement = document.getElementById('error-message');
  if (Creator == true) {
    if (playerCount < 2) {
      errorMessageElement.textContent = "Erreur : au moins deux joueurs dans le lobby pour commencer le jeu.";
      return;
    }
    errorMessageElement.textContent = ''; // Clear any previous error message
    console.log("création de la partie!");
    const lobbyName = document.getElementById('lobby-name-display').textContent;
    socket.send(JSON.stringify({ type: 'startGame', lobbyName }));
  } else 
  {
    console.log("no createur");
    errorMessageElement.textContent = "Erreur : Seul le créateur de la partie peut lancer la partie.";
  }

}

function updatePlayerCount() {
  const lobbyName = document.getElementById('lobby-name-display').textContent;
  const lobbyElement = Array.from(document.querySelectorAll('#lobby-list a')).find(a => a.textContent.includes(lobbyName));  const playerCountElement = document.getElementById('player-count');
  const playerCount = document.getElementById('player-list').children.length;
  playerCountElement.textContent = `Joueurs: ${playerCount}/8`;
 // if (lobbyElement) {
   // lobbyElement.textContent = `${lobbyName} (${playerCount}/8 joueurs)`;
  //}
}

function updateLobbyList(lobbies) {
  const lobbyList = document.getElementById('lobby-list');
  lobbyList.innerHTML = ''; // Clear the lobby list

  for (let lobby of lobbies) {
    const listItem = document.createElement('li');
    const link = document.createElement('a');
    link.textContent = `${lobby.name} (${lobby.players}/8 joueurs)`;
    link.href = `#lobby/${lobby.name}`;

  

    listItem.appendChild(link);
    lobbyList.appendChild(listItem);
  }
}
