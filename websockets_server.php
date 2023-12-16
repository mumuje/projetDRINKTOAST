<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Lobby
{
    public $name;
    public $password;
    public $players;
    public $gameStarted = false;
    public $pseudos;
    public $nbstartparty = 0;
    public $nbjoueursLOBBY = 0;
    public $creator;
    public $game;

    public $currentPlayerIndex = 0;


    public function __construct($name, $password, $creator)
    {
        $this->name = $name;
        $this->game = null;  // Change this line
        $this->pseudos = [];
        $this->password = $password;
        $this->players = new \SplObjectStorage;
        $this->creator = $creator;  // Ajoutez cette ligne
        $this->players;
        $this->nbstartparty = 0;
        $this->nbjoueursLOBBY = 0;
        $this->currentPlayerIndex = 0;

    }

    public function setGame(Game $game)
    {  // Add this method
        $this->game = $game;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function addPlayer(ConnectionInterface $player, $pseudo)
    {
        $this->players->attach($player);
        $this->pseudos[spl_object_hash($player)] = $pseudo;

        $this->broadcast(json_encode(
            array(
                'type' => 'lobbyUpdated',
                'lobbyName' => $this->name,
                'playerCount' => count($this->players),
                'players' => $this->getPlayers()  // Ajoutez cette ligne
            )
        ));
    }

    public function removePlayer(ConnectionInterface $player)
    {
        $pseudo = $this->pseudos[spl_object_hash($player)];
        $this->players->detach($player);
        unset($this->pseudos[spl_object_hash($player)]);

        if ($this->creator === spl_object_hash($player)) {
            // Si il y a encore des joueurs dans le lobby
            if ($this->players->count() > 0) {
                // Attribuer le rôle de créateur au prochain joueur dans la liste
                $this->creator = spl_object_hash($this->players->current());
            } else {
                // Si il n'y a plus de joueurs dans le lobby, réinitialiser le créateur
                $this->creator = null;
            }
        }


        $this->broadcast(json_encode(
            array(
                'type' => 'playerLeft',
                'lobbyName' => $this->name,
                'playerCount' => $this->players->count(),
                'pseudo' => $pseudo,  // Ajoutez cette ligne
                'newCreator' => $this->creator  // Ajoutez cette ligne


            )
        ));
    }
    public function checkPassword($password)
    {
        return $this->password === $password;
    }
    public function broadcast($message)
    {
        foreach ($this->players as $player) {
            $player->send($message);
        }
    }
    public function getPlayers()
    {
        $players = [];
        foreach ($this->pseudos as $hash => $pseudo) {
            $players[] = [
                'pseudo' => $pseudo,
                'isCreator' => $this->creator === $hash
            ];
        }
        return $players;
    }
}



class TicTacToe
{
    private $board;

    public function __construct()
    {
        $this->board = array_fill(0, 3, array_fill(0, 3, null));
    }

    public function makeMove($player, $x, $y)
    {
        // Vérifiez si la case est vide et placez le symbole du joueur
        if ($this->board[$x][$y] === null) {
            $this->board[$x][$y] = $player;
            return true;
        }
        return false;
    }

    public function checkGameState()
    {
        // Vérifiez les lignes
        for ($i = 0; $i < 3; $i++) {
            if ($this->board[$i][0] !== null && $this->board[$i][0] === $this->board[$i][1] && $this->board[$i][0] === $this->board[$i][2]) {
                return $this->board[$i][0]; // Retourne le joueur gagnant
            }
        }

        // Vérifiez les colonnes
        for ($i = 0; $i < 3; $i++) {
            if ($this->board[0][$i] !== null && $this->board[0][$i] === $this->board[1][$i] && $this->board[0][$i] === $this->board[2][$i]) {
                return $this->board[0][$i]; // Retourne le joueur gagnant
            }
        }

        // Vérifiez les diagonales
        if ($this->board[0][0] !== null && $this->board[0][0] === $this->board[1][1] && $this->board[0][0] === $this->board[2][2]) {
            return $this->board[0][0]; // Retourne le joueur gagnant
        }
        if ($this->board[0][2] !== null && $this->board[0][2] === $this->board[1][1] && $this->board[0][2] === $this->board[2][0]) {
            return $this->board[0][2]; // Retourne le joueur gagnant
        }

        // Vérifiez si le tableau est plein (égalité)
        foreach ($this->board as $row) {
            foreach ($row as $cell) {
                if ($cell === null) {
                    return null; // Le jeu est toujours en cours
                }
            }
        }

        return 0; // Le jeu est terminé sans gagnant (égalité)
    }

    // Ajoutez ici des méthodes pour vérifier si un joueur a gagné, etc.
}

class ConnectFour
{
    private $board;

    public function __construct()
    {
        $this->board = array_fill(0, 6, array_fill(0, 7, null));
    }

    public function makeMove($player, $column)
    {
        // Vérifiez si la colonne est pleine et placez le symbole du joueur
        for ($row = 5; $row >= 0; $row--) {
            if ($this->board[$row][$column] === null) {
                $this->board[$row][$column] = $player;
                return true;
            }
        }
        return false;
    }

    // Ajoutez ici des méthodes pour vérifier si un joueur a gagné, etc.
}


class RockPaperScissors
{
    public function playRound($player1Move, $player2Move)
    {
        // Vérifiez qui a gagné le tour
        if ($player1Move === $player2Move) {
            return 0; // Match nul
        } elseif (
            ($player1Move === 'rock' && $player2Move === 'scissors') ||
            ($player1Move === 'scissors' && $player2Move === 'paper') ||
            ($player1Move === 'paper' && $player2Move === 'rock')
        ) {
            return 1; // Le joueur 1 gagne
        } else {
            return 2; // Le joueur 2 gagne
        }
    }
}


class Player
{
    public static $nextId = 1;  // Ajoutez cette ligne

    public $pseudo;
    public $isCreator;
    public $isPlayerTurn;
    public $id;
    public $active;
    public $cardsPlayedThisTurn;
    public $sipsTaken;
    public $currentMiniGame;


    public $currentMove;
    public $hasVoted;

    public function __construct($pseudo, $isCreator, $isPlayerTurn)
    {
        $this->id = self::$nextId++;
        $this->cardsPlayedThisTurn = 0;
        $this->pseudo = $pseudo;
        $this->isCreator = $isCreator;
        $this->isPlayerTurn = $isPlayerTurn;
        $this->sipsTaken = 0;
        $this->hasVoted = false;
    }
    public static function resetId()
    {
        self::$nextId = 1;
    }


    public function makeMove($move)
    {
        // Vérifier que le mouvement est valide
        if (!in_array($move, ['rock', 'paper', 'scissors'])) {
            throw new Exception("Invalid move: $move");
        }

        // Effectuer le mouvement
        $this->currentMove = $move;
    }
}


class Game
{

    public $currentPlayerPURPLE;
    private $lobbyName;
    private $players;
    public $gameStarted = false; // Add this line
    private $deck;
    public $playdisconnect;
    public $clients;
    private $selectedPlayer;
    public $activePlayer;
    public $gameetat = false;
    private $game;
    public $autoMove;
    private $minigames;
    public $countdownActive = false;
    private $blueCardPlayer;
    private $playedCards;
    private $currentAnswer;
    private $chosenNumbers;
    public $countdownStart;
    public $countdownDuration;
    public $numberOfPlayers;
    public $startparty = false;
    public $playerMoves = [];

    public $nbjoueursSETPSEUOS = 0;
    public $votesByPlayer = [];
    public $player1;
    public $player2;
    private $votes;
    public $isCardInPlay = 0;
    public $isCardInPlay2 = 0;
    public $TicTacToeAlready = false;
    public $miniGame;
    public $isCardInPlay3 = 0;

    public $isCardInPlay4 = 0;
    public $isCardInPlay5 = 0;
    public $selectedPlayers;
    private $lastPlayerMovePseudo;
    private $lastPlayerMove;
    public $countdownPausedAt;
    public $turnCount = 0;
    protected $player1Pseudo;
    protected $player2Pseudo;
    public $gameId;
    public $moves;
    protected $playerAnswer;
    private $currentQuestion = null;
    public $dropzone = [];

    public $playerDisconnected;


    public function __construct($lobbyName)
    {
        $this->chosenNumbers = array();
        $this->lobbyName = $lobbyName;
        $this->players = [];
        $this->clients = [];
        $this->playedCards = [];
        $this->numberOfPlayers = 0;
        $this->gameStarted = false;
        $this->playerDisconnected = false;
        $this->dropzone = [];
        $this->gameId = 0;
        $this->gameetat = false;
        $this->currentQuestion;
        $this->playerAnswer;
        $this->moves;
        $this->player2Pseudo = null;
        $this->player1Pseudo =  null;
        $this->turnCount = 0;
        $this->countdownPausedAt = 0;
        $this->lastPlayerMove = null;
        $this->selectedPlayers = [];
        $this->isCardInPlay = 0;
        $this->isCardInPlay2 = 0;
         $this->TicTacToeAlready = false;
        $this->miniGame;
        $this->isCardInPlay3 = 0;
        $this->isCardInPlay4 = 0;
        $this->isCardInPlay5 = 0;
        $this->nbjoueursSETPSEUOS = 0;
        $this->votesByPlayer = [];
        $this->player1;
        $this->player2;
        $this->votes;
        $this->currentPlayerPURPLE;
        $this->deck;
        $this->playdisconnect = false;
        $this->selectedPlayer;
        $this->activePlayer = null;
        $this->gameetat = false;
        $this->game = null;
        $this->autoMove = null;
        $this->minigames;
        $this->countdownActive = false;
        $this->blueCardPlayer;
        $this->currentAnswer = null;
        $this->countdownStart = null;
        $this->countdownDuration = null;
        $this->startparty = false;
        $this->playerMoves = [];

    }
    public function removePlayer($pseudo)
    {
        // Trouver l'index du joueur dans le tableau $this->players
        $index = array_search($pseudo, array_column($this->players, 'pseudo'));
        if ($index !== false) {
            // Supprimer le joueur du tableau $this->players
            array_splice($this->players, $index, 1);
        }
        // Supprimer le joueur du tableau $this->clients
        unset($this->clients[$pseudo]);
    }
    public function getOtherPlayers($currentPlayer)
    {
        $otherPlayers = [];

        foreach ($this->players as $player) {
            if ($player->pseudo != $currentPlayer->pseudo) {
                $otherPlayers[] = $player;
            }
        }

        return $otherPlayers;
    }
    public function updatePlayerConnection($player, $connection)
    {
        // Find the player in the players array
        foreach ($this->players as $key => $existingPlayer) {
            if ($existingPlayer === $player) {
                // Update the connection
                $this->clients[$existingPlayer] = $connection;
                return;
            }
        }
    }
    public function updatePlayerConnection2($player, $connection)
    {
        // Find the player in the players array
        foreach ($this->players as $key => $existingPlayer) {
            if ($existingPlayer->pseudo == $player->pseudo) {
                // Update the connection
                $this->clients[$existingPlayer->pseudo] = $connection;
                return;
            }
        }
    }
    public function isGameStarted(): bool
    {
        return $this->gameStarted;
    }
    public function getPlayers()
    {
        return $this->players;
    }
    public function startGame()
    {
        $this->gameStarted = true;
    }
    public function getCorrectAnswer()
    {
        return $this->currentAnswer;
    }
    public function findPlayerByPseudo($pseudo)
    {
        foreach ($this->players as $player) {
            if ($player->pseudo == $pseudo) {
                return $player;
            }
        }
        return null;
    }
    public function broadcast($message)
    {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
    public function broadcast3($message, $senderPseudo = null)
    {
        foreach ($this->clients as $client) {
            if ($client->pseudo !== $senderPseudo) {
                $client->send($message);
            }
        }
    }

    public function broadcast2($message, $targetPlayer)
    {
        foreach ($this->clients as $client) {
            // echo "Processing client with ID: " . spl_object_id($client) . "\n";
            $player = $this->getPlayerByConnection($client);
            if ($player && $player->pseudo == $targetPlayer->pseudo) {
                //   echo "Entered condition with player: " . $player->pseudo . "\n";
                $client->send($message);
            }
        }
    }
    public function broadcastToMultipleViolet($message, $targetPlayers)
    {
        foreach ($this->clients as $client) {
            $player = $this->getPlayerByConnection($client);
            if ($player && in_array($player->pseudo, array_map(function ($player) {
                return $player->pseudo;
            }, $targetPlayers))) {
                $client->send($message);
            }
        }
    }
    public function broadcastToOtherPlayer($pseudo, $message)
    {
        foreach ($this->clients as $client) {
            $player = $this->getPlayerByConnection($client);
            if ($player && $player->pseudo !== $pseudo) {
                $client->send($message);
            }
        }
    }
    private function getPlayerByConnection($connection)
    {
        foreach ($this->clients as $playerPseudo => $client) {
            if ($client == $connection) {
                // Find the player in the players array
                foreach ($this->players as $player) {
                    if ($player->pseudo == $playerPseudo) {
                        return $player;
                    }
                }
            }
        }
        return null;
    }
    public function addPlayer($playerData, $connection)
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tAdding player: " . $playerData['pseudo'] . "\n";
        if (!array_key_exists($playerData['pseudo'], $this->clients)) {
            $this->players[] = $playerData;
            $connection->pseudo = $playerData['pseudo'];
            $this->clients[$playerData['pseudo']] = $connection;
            $simplifiedClients = array_map(function ($connection) {
                return $connection->pseudo;
            }, $this->clients);
            // print_r($simplifiedClients);
        } else {
            echo "[" . date('Y-m-d H:i:s') . "]"  . "Player " . $playerData['pseudo'] . " is already in the list.\n";
        }
    }

    public function getLobbyName()
    {
        return $this->lobbyName;
    }

    public function loadGameState()
    {
        // Charger la chaîne JSON à partir du fichier
        $gameStateJson = file_get_contents('gameState.json');

        // Convertir la chaîne JSON en un tableau associatif
        $gameState = json_decode($gameStateJson, true);

        // Restaurer l'état du jeu à partir du tableau associatif
        $this->players = $gameState['players'];
        $this->activePlayer = $this->players[$gameState['activePlayerIndex']];
        $this->deck = $gameState['deck'];
        //  $this->discardPile = $gameState['discardPile'];
        $this->gameStarted = $gameState['gameStarted'];
        // $this->lobbyNames = $gameState['lobbyName'];     
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayers: " . print_r($this->players, true) . "\n";
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tActive Player: " . $this->activePlayer . "\n";
        // echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tDeck: " . print_r($this->deck, true) . "\n";
        //echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tDiscard Pile: " . print_r($this->discardPile, true) . "\n";
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tGame Started: " . ($this->gameStarted ? "Yes" : "No") . "\n";
        // echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tLobby Names: " . print_r($this->lobbyNames, true) . "\n";   
    }
    //public function getPlayers()
    //{
    //   return $this->players;
    //}
    public function getPlayerConnection($playerPseudo)
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tGetting connection for player: " . $playerPseudo . "\n";
        foreach ($this->players as $player) {
            if (is_object($player) && property_exists($player, 'pseudo')) {
                if ($player->pseudo === $playerPseudo) {

                    return $this->clients[$playerPseudo] ?? null;
                }
            }
        }
        return null;
    }



    public function startParty($players)
    {
        Player::resetId();
        $this->startparty = true;
        $this->gameStarted = true;
        $this->gameId = spl_object_id($this);
        // Créer un nouveau deck de cartes
        $this->deck = $this->createDeck();

        // Distribuer les cartes aux joueurs
        $players = $this->distributeCards($players);
        $this->players = $players;
        if (empty($players)) {
            echo "------------------------------------------------------------------------------------\n";
            echo "Erreur : la liste des joueurs est vide\n";
            echo "------------------------------------------------------------------------------------\n";
            return;
        }

        // Définir le premier joueur actif
        $activePlayerIndex = rand(0, count($players) - 1);
        if (!isset($players[$activePlayerIndex])) {
            echo "------------------------------------------------------------------------------------\n";
            echo "Error: activePlayerIndex does not exist in players array\n";
            echo "------------------------------------------------------------------------------------\n";
            return;
        }
        $this->activePlayer = $players[$activePlayerIndex];
        if (is_object($this->activePlayer)) {
            foreach ($players as $player) {
                if (is_object($player)) {
                    $player->isPlayerTurn = ($player === $this->activePlayer);
                }
            }
        } else {
            echo "------------------------------------------------------------------------------------\n";
            echo "Error: activePlayer is not an object after startParty\n";
            echo "------------------------------------------------------------------------------------\n";
        }
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tLe premier joueur est " . $players[$activePlayerIndex]->pseudo . "\n";
        $message = "Le premier joueur est " . $players[$activePlayerIndex]->pseudo . "\n";
        $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));
        // $this->saveGameState($players);
         echo "Instance de jeu dans startParty: " . spl_object_id($this) . "\n";
        return array('players' => $players, 'activePlayerIndex' => $activePlayerIndex);
        if (!is_object($this->activePlayer)) {
            echo "------------------------------------------------------------------------------------\n";
            echo "Error: activePlayer is not an object at the end of startParty\n";
            echo "------------------------------------------------------------------------------------\n";
        }
    }


    public function saveGameState($from)
    {
        $activePlayerIndex = array_search($this->activePlayer, $this->players);
        // Ajouter la propriété isPlayerTurn à chaque joueur
        // foreach ($this->players as $player) {
        //   $player->isPlayerTurn = ($player === $this->activePlayer);
        //}
        // Convertir l'état du jeu en une chaîne JSON
        $gameStateJson = json_encode(array(
            'players' => $this->players,
            'activePlayerIndex' => $activePlayerIndex,
            'deck' => $this->deck,
            //  'discardPile' => $this->discardPile,
            'gameStarted' => $this->gameStarted,
            // 'lobbyName' => $this->lobbyNames[spl_object_hash($from)]

        ));

        // Enregistrer la chaîne JSON dans un fichier
        file_put_contents('gameState.json', $gameStateJson);
    }
    public function updateActivePlayer()
    {

        // echo "Instance de jeu dans updateActivePlayer: " . spl_object_id($this) . "\n";
        if ($this->activePlayer === null) {
            echo "------------------------------------------------------------------------------------\n";
            echo "Erreur : activePlayer est null\n";
            echo "------------------------------------------------------------------------------------\n";
            return;
        }
        // Obtenir le hachage d'objet du joueur actif
        $currentIndex = array_search($this->activePlayer, $this->players);

        // Piocher une carte pour le joueur actif
        $this->drawCard($this->players[$currentIndex], $this->deck);

        // Définir le joueur actif comme inactif
        $this->players[$currentIndex]->isActive = false;
        $this->players[$currentIndex]->isPlayerTurn = false;

        // Obtenir les hachages d'objet des joueurs
        //$playerHashes = array_keys($this->players);

        // Trouver l'index du joueur actif
        //$currentIndex = array_search($currentIndex, $playerHashes);

        // Passer au joueur suivant
        $nextIndex = ($currentIndex + 1) % count($this->players);
        if ($this->turnCount >= 30) {
            return;
        }

        if ($nextIndex === 0) {
            $this->turnCount++;
            if ($this->turnCount >= 30) {
                $this->gameStarted = false;
                echo "[" . date('Y-m-d H:i:s') . "]"  . "La partie est terminée après 30 tours.\n";
                // Here you can add code to end the game
                usort($this->players, function ($a, $b) {
                    return $b->sipsTaken - $a->sipsTaken;
                });

                $rankingMessage = "FIN DE PARTIE\n\n";
                foreach ($this->players as $index => $player) {
                    $rankingMessage .= ($index + 1) . ". " . $player->pseudo . " : " . $player->sipsTaken . " gorgées\n";
                }

                // Envoyer le message de classement à tous les joueurs
                $this->broadcast(json_encode(array('type' => 'endGame', 'message' => $rankingMessage)));




                return;
            }
        }

        // Obtenir le hachage d'objet du joueur suivant
        // $nextPlayerHash = $playerHashes[$nextIndex];

        // Définir le joueur suivant comme actif
        $this->players[$nextIndex]->isActive = true;
        $this->players[$nextIndex]->isPlayerTurn = true;

        // Mettre à jour le joueur actif
        $this->activePlayer = $this->players[$nextIndex];

        // Diffuser le joueur actif mis à jour
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tC'est maintenant le tour de " . $this->players[$nextIndex]->pseudo . "\n";
        $message = "C'est maintenant le tour de " . $this->players[$nextIndex]->pseudo . "\n";
        $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));
    }
    public function distributeCards($players)
    {
        for ($i = 0; $i < count($players); $i++) {
            // Convertir le joueur en objet
            $players[$i] = (object) $players[$i];

            // Ajouter une propriété 'cards' au joueur
            $players[$i]->cards = array();

            for ($j = 0; $j < 4; $j++) {
                $players[$i]->cards[] = array_pop($this->deck);
            }
        }
        $message = "Cards distributed to players: " . print_r($players, true);
        $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));
        // print_r($players);
        return $players;
    }
    public function drawCard($player, &$deck)
    {
        if (count($player->cards) >= 6) {
            $message = $player->pseudo . " a déjà 6 cartes. Il ne peut pas en piocher une nouvelle.\n";
            $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));
            return $player;
        }
        // Vérifier si le deck est vide
        if (empty($this->deck)) {
            // Si le deck est vide, créer un nouveau deck
            $this->deck = $this->createDeck();
        }
        $player->cards[] = array_pop($this->deck);
        $message = $player->pseudo . " vient de piocher une carte.\n";
        $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));
        // print_r($player);
        return $player;
    }
    public function getCards()
    {
        return $this->deck;
    }
    public function getDropzone()
    {
        return $this->dropzone;
    }
    public function createDeck()
    {
        $cards = array();
        $cardId = 0;
        // Add blue cards
        for ($i = 0; $i < 25; $i++) {
            $cards[] = array('id' => 'card-' . $cardId++, 'color' => 'bleu', 'image' => 'img/bleu.png');
        }

        // Add yellow cards
        for ($i = 0; $i < 25; $i++) {
            $cards[] = array('id' => 'card-' . $cardId++, 'color' => 'jaune', 'image' => 'img/jaune.png');
        }

        // Add red cards
        for ($i = 0; $i < 15; $i++) {
            $cards[] = array('id' => 'card-' . $cardId++, 'color' => 'rouge', 'image' => 'img/rouge.png');
        }

        // Add green cards
        for ($i = 0; $i < 20; $i++) {
            $cards[] = array('id' => 'card-' . $cardId++, 'color' => 'verte', 'image' => 'img/verte.png');
        }

        // Add multicolor cards
        for ($i = 0; $i < 5; $i++) {
            $cards[] = array('id' => 'card-' . $cardId++, 'color' => 'multicolor', 'image' => 'img/multicolor.png');
        }

        // Add violet cards
        for ($i = 0; $i < 10; $i++) {
            $cards[] = array('id' => 'card-' . $cardId++, 'color' => 'violette', 'image' => 'img/violette.png');
        }

        // Mélanger le jeu de cartes
        shuffle($cards);

        return $cards;
    }

    public function playCard($player, $cardId)
    {
        // Trouver la carte dans le deck du joueur
        foreach ($player->cards as $key => $playerCard) {
            // Vérifier que la carte est dans le deck du joueur
            if ($playerCard['id'] === $cardId) {
                if ($this->isCardInPlay !== 0 && $this->isCardInPlay2 !== 0 && $this->isCardInPlay3 !== 0 && $this->isCardInPlay4 !== 0) {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Une carte est en jeu, vous ne pouvez pas jouer une autre carte avant la fin de l'action !.";
                    return;
                }
                // Vérifier si le joueur peut jouer la carte
                if ($playerCard['color'] == 'violette' || $playerCard['color'] == 'multicolor') {
                    if ($player->cardsPlayedThisTurn < 1) {
                        // Retirer la carte du deck du joueur
                        unset($player->cards[$key]);
                        $player->cards = array_values($player->cards);
                        // Ajouter la carte à la dropzone
                        $this->dropzone[] = $playerCard;
                        // Ajouter la carte à la pile de cartes jouées
                        $this->playedCards[] = $playerCard;
                        // Incrémenter le nombre de cartes jouées ce tour
                        $player->cardsPlayedThisTurn++;
                        $player->cardsPlayedThisTurn++;
                        if ($playerCard['color'] == 'multicolor') {
                            // Si la carte est bleue, exécuter la logique spécifique à cette couleur
                            $this->playmulticolorCard($player);
                        }
                        if ($playerCard['color'] == 'violette') {
                            // Si la carte est bleue, exécuter la logique spécifique à cette couleur
                            $this->playvioletCard($player);
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Vous ne pouvez jouer que deux cartes vertes, rouges, jaunes ou bleues ou une seule carte violette ou multicolore  par tour.";
                        return;
                    }
                } else {
                    if ($player->cardsPlayedThisTurn < 2) {
                        // Retirer la carte du deck du joueur
                        unset($player->cards[$key]);
                        $player->cards = array_values($player->cards);
                        // Ajouter la carte à la dropzone
                        $this->dropzone[] = $playerCard;
                        // Ajouter la carte à la pile de cartes jouées
                        $this->playedCards[] = $playerCard;
                        // Incrémenter le nombre de cartes jouées ce tour
                        $player->cardsPlayedThisTurn++;
                        if ($playerCard['color'] == 'bleu') {
                            // Si la carte est bleue, exécuter la logique spécifique à cette couleur
                            $this->playBleuCard($player);
                        }
                        if ($playerCard['color'] == 'jaune') {
                            // Si la carte est bleue, exécuter la logique spécifique à cette couleur
                            $this->playjauneCard($player);
                        }
                        if ($playerCard['color'] == 'rouge') {
                            // Si la carte est bleue, exécuter la logique spécifique à cette couleur
                            $this->playrougeCard($player);
                        }
                        if ($playerCard['color'] == 'verte') {
                            // Si la carte est bleue, exécuter la logique spécifique à cette couleur
                            $this->playvertCard($player);
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Vous ne pouvez jouer que deux cartes vertes, rouges, jaunes ou bleues par tour. ou une seule carte violette ou multicolore  par tour.";
                        return;
                    }
                }

                // Envoyer un message à tous les joueurs pour les informer de la carte jouée
                $message = $player->pseudo . " a joué la carte " . $playerCard['color'] . ".\n";
                $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));

                return;
            }
        }

        // Si la carte n'est pas trouvée dans le deck du joueur, envoyer un message d'erreur
        $message = "Erreur : " . $player->pseudo . " a tenté de jouer une carte qu'il ne possède pas.\n";
        $this->broadcast(json_encode(array('type' => 'message', 'content' => $message)));
    }
    public function playBleuCard($player)
    {
        $this->isCardInPlay = 1;
        // Envoyer un message au client pour lui demander de choisir des nombres
        $message = array(
            'type' => 'BLUECARDPLAYED',
            'content' => "Vous avez joué une carte bleue. Veuillez choisir des nombres entre 0 et 9."
        );
        $this->broadcast2(json_encode($message), $player);

        // Stocker le joueur qui a joué la carte bleue pour vérifier la réponse plus tard
        $this->blueCardPlayer = $player;


        $message = array(
            'type' => 'CHOOSEPLAYER',
            'content' => "Veuillez choisir un autre joueur.",
            'players' => $this->getOtherPlayers($player) // Vous devez implémenter cette fonction
        );

        $this->broadcast2(json_encode($message), $player);
    }
    public function GETBLUESTATE()
    {            
        echo "------------------------------------------------------------------------------------\n";
        if ($this->isCardInPlay == 1) {
            echo "GETBLUESTATE() CAS 1 (isCardInPlay == 1)\n";
            $this->playBleuCard($this->blueCardPlayer);
        } else if ($this->isCardInPlay == 2) {
            echo "GETBLUESTATE() CAS 2 (isCardInPlay == 2)\n";
            $data = new stdClass();
            $data->pseudo = $this->selectedPlayer->pseudo;
            $this->onPlayerSelected($data);
        } else if ($this->isCardInPlay == 3) {
            echo "GETBLUESTATE() CAS 3 (isCardInPlay == 3)\n";

            $this->playBleuCard($this->blueCardPlayer);
        } else {
            $this->isCardInPlay = 0;
        }
        echo "------------------------------------------------------------------------------------\n";
    }

    public function onPlayerSelected($data)
    {
        $this->isCardInPlay = 2;
        // Trouver le joueur sélectionné
        $this->selectedPlayer = $this->findPlayerByPseudo($data->pseudo);

        // Envoyer un message au joueur sélectionné pour lui demander de choisir un nombre
        $message = array(
            'type' => 'CHOOSENUMBER',
            'content' => "Veuillez choisir un nombre.",
            'pseudo' => $this->selectedPlayer->pseudo
        );

        $this->broadcast2(json_encode($message), $this->selectedPlayer);
    }
    public function onNumbersChosen($data)
    {
        $this->isCardInPlay = 3;
        // Trouver le joueur qui a choisi les nombres
        $player = $this->findPlayerByPseudo($data->pseudo);

        // Vérifier si c'est le joueur qui a joué la carte bleue
        if ($player == $this->blueCardPlayer) {
            // Stocker les nombres choisis par le joueur
            $this->blueCardPlayer->chosenNumbers = $data->numbers;
        } else {
            // Envoyer un message d'erreur au joueur
            $message = array(
                'type' => 'ERROR',
                'content' => "Vous n'êtes pas le joueur qui a joué la carte bleue."
            );
            $this->broadcast2(json_encode($message), $player);
        }
    }
    public function onNumberChosen($data)
    {
        $this->isCardInPlay = 0;
        error_log(print_r($data, true));
        if (!isset($data->pseudo)) {
            error_log('Pseudo non défini dans $data');
            return;
        }
        // Trouver le joueur qui a choisi le nombre
        $player = $this->findPlayerByPseudo($data->pseudo);
        $player->chosenNumber = $data->number;
        error_log(print_r($player, true));
        if (!is_object($player) || !isset($player->pseudo)) {
            error_log('Joueur non trouvé ou pseudo non défini dans $player');
            return;
        }

        // Vérifier si le nombre choisi est dans la liste des nombres choisis par le joueur qui a joué la carte
        if (isset($this->blueCardPlayer->chosenNumbers) && in_array($data->number, $this->blueCardPlayer->chosenNumbers)) {
            // Le nombre choisi est correct
            $message = array(
                'type' => 'NUMBERCORRECT',
                'content' => "$player->pseudo a trouvé la bonne réponse."
            );
        } else {
            $numberOfDrinks = count($this->blueCardPlayer->chosenNumbers);
            // Le nombre choisi est incorrect
            $player->sipsTaken += $numberOfDrinks;
            $message = array(
                'type' => 'NUMBERINCORRECT',
                'content' => "$player->pseudo doit boire $numberOfDrinks gorgées."
            );
        }
        $this->isCardInPlay = 0;
        // Envoyer le message à tous les joueurs
        $this->broadcast(json_encode($message));
    }
    public function playmulticolorCard($player)
    {
        $numbers = array_fill(0, 150, 1) // 1 apparaît 15% du temps
            + array_fill(150, 200, 2) // 2 apparaît 20% du temps
            + array_fill(350, 190, 3) // 3 apparaît 19% du temps
            + array_fill(540, 150, 4) // 4 apparaît 15% du temps
            + array_fill(690, 120, 5) // 5 apparaît 12% du temps
            + array_fill(810, 75, 6) // 6 apparaît 7.5% du temps
            + array_fill(885, 45, 7) // 7 apparaît 4.5% du temps
            + array_fill(930, 35, 8) // 8 apparaît 3.5% du temps
            + array_fill(965, 15, 9) // 9 apparaît 1.5% du temps
            + array_fill(980, 15, 10) // 10 apparaît 1.5% du temps
            + array_fill(995, 5, 15); // 15 apparaît 0.5% du temps

        $number = $numbers[array_rand($numbers)];
        $multicolorCardPlayer = $player;
        $otherPlayers = $this->getOtherPlayers($multicolorCardPlayer);
        foreach ($otherPlayers as $otherPlayer) {
            $otherPlayer->sipsTaken += $number;
        }
        // Définir la logique spécifique à la carte multicolore
        // Par exemple, vous pourriez envoyer un message au client pour lui indiquer qu'il a joué une carte multicolore
        $message = array(
            'type' => 'MULTICOLORCARDPLAYED',
            'content' => "TOUT LE MONDE DOIT BOIRE $number gorgées sauf $multicolorCardPlayer->pseudo."
        );
        $this->broadcast(json_encode($message));

        // Vous pouvez ajouter plus de logique ici en fonction de ce que doit faire la carte multicolore
    }
    public function playjauneCard($player)
    {
        $this->stopCountdown(); // Ajoutez cette ligne pour réinitialiser le compte à rebours
        $this->isCardInPlay2 = 1;
        echo "------------------------------------------------------------------------------------\n";
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tJAUNE ETAPE 1\n";
        // Étape 1 : Demander au joueur de choisir un autre joueur
        $message = array(
            'type' => 'CHOOSEPLAYERYELLOW',
            'content' => "Veuillez choisir un autre joueur pour la carte jaune.",
            'players' => $this->getOtherPlayers($player)
        );
        $this->blueCardPlayer = $player;
        $this->broadcast2(json_encode($message), $player);
    }

    public function onPlayerSelectedForYellowCard($data)
    {
        $this->isCardInPlay2 = 2;
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tJAUNE ETAPE 2\n";
        // Étape 2 : Poser une question générale au joueur sélectionné et commencer le compte à rebours
        $this->selectedPlayer = $this->findPlayerByPseudo($data->pseudo);
        if ($this->currentQuestion === null) {
            $this->currentQuestion = $this->getRandomQuestion();
        }
        $message = array(
            'type' => 'ASKQUESTION',
            'content' => $this->currentQuestion,
            'pseudo' => $this->selectedPlayer->pseudo,
        );
        $this->broadcast(json_encode($message));
        if ($this->countdownPausedAt) {
            // Si le compte à rebours est en pause, reprenez-le
            error_log('Countdown paused at resumeCOUNTDOWN ' . $this->countdownPausedAt);
            $this->resumeCountdown();
        } else {
            // Sinon, démarrez un nouveau compte à rebours
            $this->startCountdown(20);
        }
    }
    public function onAnswerReceived($data)
    {
        $this->playerAnswer = $data->answer;
        $this->isCardInPlay2 = 3;
        $this->isCardInPlay3 = 3;
        if ($this->isCardInPlay4 === 2) {
            $this->isCardInPlay4 = 3;
        }
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tETAPE END CARTE JAUNE/ROUGE/VERTE\n";
        echo "------------------------------------------------------------------------------------\n";
        // Étape 3 : Arrêter le compte à rebours et envoyer la réponse à tous les joueurs
        if ($this->countdownActive) { // Ajoutez cette ligne
            $this->stopCountdown(); // Vous devez implémenter cette fonction
        }
        if ($this->isCardInPlay2 === 3 && $this->isCardInPlay3 === 3 && $this->isCardInPlay4 === 0) {
            $message = array(
                'type' => 'SHOWANSWER',
                'playerAnswer' => $data->answer,
                'correctAnswer' => $this->getCorrectAnswer(), // Vous devez implémenter cette fonction
                'pseudo' => $this->selectedPlayer->pseudo,
            );
            $this->broadcast(json_encode($message));
        }
        if ($this->isCardInPlay2 === 3 && $this->isCardInPlay3 === 3) {
            // Étape 4 : Demander à tous les joueurs de voter
            $message = array(
                'type' => 'VOTE',
                'content' => "Veuillez voter si la réponse est correcte.",
            );
        }
        $this->broadcast(json_encode($message));
    }
    public function onAllVotesReceived()
    {

        $numbers = array_fill(0, 15, 1) // 1 apparaît 15% du temps
            + array_fill(15, 20, 2) // 2 apparaît 20% du temps
            + array_fill(35, 19, 3) // 3 apparaît 19% du temps
            + array_fill(54, 15, 4) // 4 apparaît 15% du temps
            + array_fill(69, 12, 5) // 5 apparaît 12% du temps
            + array_fill(81, 8, 6) // 6 apparaît 8% du temps
            + array_fill(89, 4, 7) // 7 apparaît 4% du temps
            + array_fill(93, 3, 8) // 8 apparaît 3% du temps
            + array_fill(96, 2, 9) // 9 apparaît 2% du temps
            + array_fill(98, 1, 10) // 10 apparaît 1% du temps
            + array_fill(99, 1, 11); // 11 apparaît 1% du temps

        $number = $numbers[array_rand($numbers)];
        echo "\t\tETAPE END TOUT LES VOTES RECUS\n";
        // Étape 5 : Déterminer si la majorité des joueurs pensent que la réponse est correcte
        $votesFor = $this->countVotesFor(); // Vous devez implémenter cette fonction
        $votesAgainst = $this->countVotesAgainst(); // Vous devez implémenter cette fonction

        if ($votesFor > $votesAgainst) {
            // Si la majorité des joueurs pensent que la réponse est correcte, le joueur gagne un point
            $message = array(
                'type' => 'SCOREUPDATE',
                'content' => $this->selectedPlayer->pseudo . " a eu la bonne réponse !",
            );
        } else {
            $this->selectedPlayer->sipsTaken += $number;
            // Si la majorité des joueurs pensent que la réponse est incorrecte, le joueur ne gagne rien
            $message = array(
                'type' => 'SCOREUPDATE',
                'content' => $this->selectedPlayer->pseudo . " doit boire $number.",
            );
        }
        $this->currentQuestion = null;
        $this->votes = [];
        $this->selectedPlayer = null;
        $this->isCardInPlay2 = 0;
        $this->isCardInPlay3 = 0;
        $this->isCardInPlay4 = 0;
        $this->votes = [];
        $this->votesByPlayer = [];
        $this->broadcast(json_encode($message));
    }
    public function getRandomQuestion()
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tJAUNE ETAPE RANDOMQUESTION\n";
        // Lire le fichier texte contenant les questions et les réponses
        $file = file_get_contents('txt/questions.txt');

        // Diviser le contenu du fichier en un tableau de lignes
        $lines = explode("\n", $file);

        // Obtenir une ligne aléatoire (question et réponse) du tableau
        $randomLine = $lines[array_rand($lines)];

        // Diviser la ligne en question et réponse
        list($question, $answer) = explode(';', $randomLine);

        // Stocker la réponse pour une utilisation ultérieure
        $this->currentAnswer = $answer;

        return $question;
    }
    public function startCountdown($seconds)
    {
        $gameId = spl_object_id($this);
        // Lire l'état du compte à rebours à partir d'un fichier
        $countdownStates = json_decode(file_get_contents('txt/countdownState.txt'), true);
        if ($countdownStates[$gameId]) {
            $this->countdownActive = $countdownStates[$gameId]['active'];
            $this->countdownStart = $countdownStates[$gameId]['start'];
            $this->countdownDuration = $countdownStates[$gameId]['duration'];
        }

        // Si un compte à rebours est déjà en cours, calculez le temps restant
        if ($this->countdownActive) {
            $elapsedTime = time() - $this->countdownStart;
            $remainingTime = $this->countdownDuration - $elapsedTime;
            if ($remainingTime > 0) {
                // Continuer le compte à rebours avec le temps restant
                $this->countdownStart = time();
                $this->countdownDuration = $remainingTime;
            } else {
                // Le compte à rebours est terminé, le réinitialiser
                $this->countdownActive = false;
                $this->countdownStart = null;
                $this->countdownDuration = null;
            }
        } else {
            // Commencer un nouveau compte à rebours
            $this->countdownStart = time();
            $this->countdownDuration = $seconds;
            $this->countdownActive = true;
        }

        // Enregistrer l'état du compte à rebours dans un fichier
        $countdownStates[$gameId] = array(
            'active' => $this->countdownActive,
            'start' => $this->countdownStart,
            'duration' => $this->countdownDuration,
        );
        file_put_contents('txt/countdownState.txt', json_encode($countdownStates));

        // Envoyer un message de compte à rebours aux clients
        $message = array(
            'type' => 'COUNTDOWN',
            'gameId' => $gameId,
            'start' => $this->countdownStart,
            'duration' => $this->countdownDuration,
        );
        $this->broadcast(json_encode($message));
    }
    public function resumeCountdown()
    {
        // Si le compte à rebours n'a pas été mis en pause, ne faites rien et retournez
        if (!$this->countdownPausedAt) {
            return;
        }

        // Calculez le temps écoulé depuis le début du compte à rebours
        $elapsedTime = $this->countdownPausedAt - $this->countdownStart;

        // Calculez le temps restant
        $remainingTime = $this->countdownDuration - $elapsedTime;

        // Réinitialisez le temps de pause
        $this->countdownPausedAt = null;

        // Redémarrez le compte à rebours avec le temps restant
        $this->startCountdown($remainingTime);
    }

    public function stopCountdown()
    {
        error_log(print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2), true));
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tETAPE COMPTEUR END\n";
        $this->countdownActive = false;

        error_log('stopCountdown called');
        // Réinitialiser le temps de début et la durée du compte à rebours
        $this->countdownStart = null;
        $this->countdownDuration = null;

        // Réinitialiser l'état du compte à rebours dans le fichier
        $countdownState = array(
            'active' => false,
            'start' => null,
            'duration' => null,
        );
        file_put_contents('txt/countdownState.txt', json_encode($countdownState));

        $message = array(
            'type' => 'STOP_COUNTDOWN',
        );
        $this->broadcast(json_encode($message));
    }

    public function countVotesFor()
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tETAPE VOTE YES\n";
        // Compter le nombre de votes pour la réponse du joueur
        $votesFor = 0;
        foreach ($this->votes as $vote) {
            if ($vote == 'correct') {
                $votesFor++;
            }
        }
        $this->isCardInPlay2 = 0;
        $this->isCardInPlay3 = 0;
        $this->isCardInPlay4 = 0;
        return $votesFor;
    }

    public function countVotesAgainst()
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tETAPE VOTE NO\n";
        // Compter le nombre de votes contre la réponse du joueur
        $votesAgainst = 0;
        foreach ($this->votes as $vote) {
            if ($vote == 'false') {
                $votesAgainst++;
            }
        }
        $this->isCardInPlay2 = 0;
        $this->isCardInPlay3 = 0;
        $this->isCardInPlay4 = 0;
        return $votesAgainst;
    }
    public function onVoteReceived($data)
    {        echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tNB JOUEURS " . $this->numberOfPlayers . "\n");

        error_log('onVoteReceived called with ' . print_r($data, true));
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tETAPE VOTE RECU\n";
        $vote = $data['vote'];
        $playerPseudo = $data['pseudo'];
        if (isset($this->votesByPlayer[$playerPseudo])) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . "Le joueur $playerPseudo a déjà voté";
            return;
        }
        $this->votes[] = $vote;
        $this->votesByPlayer[$playerPseudo] = $vote;
        echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tNB JOUEURS " . $this->numberOfPlayers . "\n");
        // Si tous les votes ont été reçus, appeler la méthode onAllVotesReceived
        if (count($this->votes) == $this->numberOfPlayers - 1) {
            $this->onAllVotesReceived();
        }
    }
    public function GETJAUNESTATE()
    {
        if ($this->isCardInPlay2 == 1) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 1 JAUNE RECONNEXION \n");
            $this->playjauneCard($this->blueCardPlayer);
        } else if ($this->isCardInPlay2 == 2) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 2 JAUNE RECONNEXION \n");
            $data = new stdClass();
            $data->pseudo = $this->selectedPlayer->pseudo;
            $this->onPlayerSelectedForYellowCard($data);
        } else if ($this->isCardInPlay2 == 3) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 3 JAUNE RECONNEXION \n");
            $data = new stdClass();
            $data->answer = $this->playerAnswer;
            $this->onAnswerReceived($data);
        } else {
            $this->isCardInPlay2 = 0;
        }
    }



    public function playvertCard($player)
    {
        $this->stopCountdown(); // Ajoutez cette ligne pour réinitialiser le compte à rebours
        $this->isCardInPlay3 = 1;
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tvert ETAPE 1\n";
        // Étape 1 : Demander au joueur de choisir un autre joueur
        $message = array(
            'type' => 'CHOOSEPLAYERVERT',
            'content' => "Veuillez choisir un autre joueur pour la carte verte.",
            'players' => $this->getOtherPlayers($player)
        );
        $this->blueCardPlayer = $player;
        $this->broadcast2(json_encode($message), $player);
    }
    public function onPlayerSelectedForvertCard($data)
    {
        $this->isCardInPlay3 = 2;
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tvert ETAPE 2\n";
        // Étape 2 : Poser une question générale au joueur sélectionné et commencer le compte à rebours
        $this->selectedPlayer = $this->findPlayerByPseudo($data->pseudo);
        if ($this->currentQuestion === null) {
            $this->currentQuestion = $this->getRandomEnigme();
        }
        $message = array(
            'type' => 'ASKQUESTION',
            'content' => $this->currentQuestion,
            'pseudo' => $this->selectedPlayer->pseudo,
        );
        $this->broadcast(json_encode($message));
        if ($this->countdownPausedAt) {
            // Si le compte à rebours est en pause, reprenez-le
            error_log('Countdown paused at resumeCOUNTDOWN ' . $this->countdownPausedAt);
            $this->resumeCountdown();
        } else {
            // Sinon, démarrez un nouveau compte à rebours
            $this->startCountdown(45);
        }
    }
    public function getRandomEnigme()
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tJAUNE ETAPE RANDOMEnigme\n";
        // Lire le fichier texte contenant les questions et les réponses
        $file = file_get_contents('txt/enigme.txt');

        // Diviser le contenu du fichier en un tableau de lignes
        $lines = explode("\n", $file);

        // Obtenir une ligne aléatoire (question et réponse) du tableau
        $randomLine = $lines[array_rand($lines)];

        // Diviser la ligne en question et réponse
        list($question, $answer) = explode(';', $randomLine);

        // Stocker la réponse pour une utilisation ultérieure
        $this->currentAnswer = $answer;

        return $question;
    }
    public function GETvertSTATE()
    {
        if ($this->isCardInPlay3 == 1) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 1 VERT RECO \n");
            $this->playvertCard($this->blueCardPlayer);
        } else if ($this->isCardInPlay3 == 2) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 2 VERT RECO \n");
            $data = new stdClass();
            $data->pseudo = $this->selectedPlayer->pseudo;
            $this->onPlayerSelectedForvertCard($data);
        } else if ($this->isCardInPlay3 == 3) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 3 VERT RECO \n");
            $data = new stdClass();
            $data->answer = $this->playerAnswer;
            $this->onAnswerReceived($data);
        } else {
            $this->isCardInPlay3 = 0;
        }
    }



    public function playrougeCard($player)
    {
        $this->stopCountdown(); // Ajoutez cette ligne pour réinitialiser le compte à rebours
        $this->isCardInPlay4 = 1;
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tred ETAPE 1\n";
        // Étape 1 : Demander au joueur de choisir un autre joueur
        $message = array(
            'type' => 'CHOOSEPLAYERROUGE',
            'content' => "Veuillez choisir un autre joueur pour la carte rouge.",
            'players' => $this->getOtherPlayers($player)
        );
        $this->blueCardPlayer = $player;
        $this->broadcast2(json_encode($message), $player);
    }


    public function onPlayerSelectedForROUGECard($data)
    {
        $this->isCardInPlay4 = 2;
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tred ETAPE 2\n";
        // Étape 2 : Poser une question générale au joueur sélectionné et commencer le compte à rebours
        $this->selectedPlayer = $this->findPlayerByPseudo($data->pseudo);
        if ($this->currentQuestion === null) {
            $this->currentQuestion = $this->getRandomAction();
        }
        $message = array(
            'type' => 'ASKAction',
            'content' => $this->currentQuestion,
            'pseudo' => $this->selectedPlayer->pseudo,
        );
        $this->broadcast(json_encode($message));
        if ($this->countdownPausedAt) {
            // Si le compte à rebours est en pause, reprenez-le
            error_log('Countdown paused at resumeCOUNTDOWN ' . $this->countdownPausedAt);
            $this->resumeCountdown();
        } else {
            // Sinon, démarrez un nouveau compte à rebours
            $this->startCountdown(300);
        }
    }

    public function getRandomAction()
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\trouge ETAPE RANDOMAction\n";
        // Lire le fichier texte contenant les questions et les réponses
        $file = file_get_contents('txt/action.txt');

        // Diviser le contenu du fichier en un tableau de lignes
        $lines = explode("\n", $file);

        // Obtenir une ligne aléatoire (question et réponse) du tableau
        $randomLine = $lines[array_rand($lines)];

        // Diviser la ligne en question et réponse
        list($action1, $action2) = explode(';', $randomLine);

        // Stocker la réponse pour une utilisation ultérieure
        return array('action1' => $action1, 'action2' => $action2);
    }
    public function GETrougeSTATE()
    {
        if ($this->isCardInPlay4 == 1) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 1 rouge reco\n");
            $this->playrougeCard($this->blueCardPlayer);
        } else if ($this->isCardInPlay4 == 2) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 2 rouge reco\n");
            $data = new stdClass();
            $data->pseudo = $this->selectedPlayer->pseudo;
            $this->onPlayerSelectedForROUGECard($data);
        } else if ($this->isCardInPlay4 == 3) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 3 rouge reco\n");
            $data = new stdClass();
            $data->answer = $this->playerAnswer;
            $this->onAnswerReceived($data);
        } else {
            $this->isCardInPlay4 = 0;
        }
    }


    public function playvioletCard($player)
    {
        // $this->stopCountdown(); // Réinitialiser le compte à rebours
        $this->isCardInPlay5 = 1; // Indiquer qu'une carte violette est en jeu
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tVIOLET ETAPE 1\n";
        $otherPlayers = $this->getOtherPlayers($player);
        $otherPlayers[] = $player;
        // Étape 1 : Demander au joueur de choisir deux autres joueurs
        $message = array(
            'type' => 'CHOOSEPLAYERSVIOLET',
            'content' => "Veuillez choisir deux autres joueurs pour la carte violette.",
            'players' => $otherPlayers
        );
        $this->blueCardPlayer = $player;
        $this->broadcast2(json_encode($message), $player);
    }

    public function onPlayersSelectedForVioletCard($selectedPlayers)
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tVIOLET ETAPE 2\n";

        // Réinitialiser l'indicateur de carte violette en jeu
        $this->isCardInPlay5 = 2;
        $this->selectedPlayers = array_values($selectedPlayers);
        $selectedPlayers = array_values($selectedPlayers);

        // Générer un mini-jeu aléatoire
        foreach ($selectedPlayers as $player) {
            if (is_object($player)) {
                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer is an object\n";
            } else {
                echo "[" . date('Y-m-d H:i:s') . "]"  . "Player is not an object\n";
            }
        }

        if ($this->miniGame === null) {
            $this->miniGame = $this->generateRandomMiniGame();
        }

        if (get_class($this->miniGame) === 'TicTacToe') {
            // Assurez-vous que selectedPlayers contient au moins deux joueurs
            if (count($selectedPlayers) >= 2) {
                $this->currentPlayerPURPLE = $selectedPlayers[0];
                $this->player1 = $selectedPlayers[0]; // Joueur 1
                $this->player2 = $selectedPlayers[1]; // Joueur 2
                error_log('player1: ' . $this->player1->pseudo);
                error_log('player2: ' . $this->player2->pseudo);
                error_log('VIOLETJOUEUR: ' . $this->currentPlayerPURPLE->pseudo);
                $data = [
                    'type' => 'miniGameSelected',
                    'game' => get_class($this->miniGame),
                    'moves' => $this->moves,
                    'player1' => $this->player1->pseudo,
                    'player2' => $this->player2->pseudo,
                    'VIOLETJOUEUR' => $this->currentPlayerPURPLE->pseudo,
                    'TicTacToeAlready' => $this->TicTacToeAlready,
                ];
            } else {
                // Gérer l'erreur
            }
        } else if (get_class($this->miniGame) === 'RockPaperScissors') {
            $data = [
                'type' => 'miniGameSelected',
                'game' => get_class($this->miniGame),
                'moves' => $this->moves
            ];
        }
        $this->broadcastToMultipleViolet(json_encode($data), $selectedPlayers);
        foreach ($selectedPlayers as $player) {
            $player->currentMiniGame = $this->miniGame;
        }
    }

    public function generateRandomMiniGame()
    {
        $randomNumber = rand(1, 3);
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tVIOLET GENRATE RANDOM MINIGAME WITH" . $randomNumber .  "\n";
        switch ($randomNumber) {
            case 1:
                // return new TicTacToe();
                /* $this->minigames = new RockPaperScissors();
           $this->moves = ['rock', 'paper', 'scissors'];
           return $this->minigames;*/
                $this->minigames = new TicTacToe();
                $this->moves = [
                    [0, 0], [0, 1], [0, 2],
                    [1, 0], [1, 1], [1, 2],
                    [2, 0], [2, 1], [2, 2]
                ];
                return $this->minigames;


            case 2:
                //return new ConnectFour();
                $this->minigames = new TicTacToe();
                $this->moves = [
                    [0, 0], [0, 1], [0, 2],
                    [1, 0], [1, 1], [1, 2],
                    [2, 0], [2, 1], [2, 2]
                ];
                return $this->minigames;


            case 3:
                $this->minigames = new RockPaperScissors();
                $this->moves = ['rock', 'paper', 'scissors'];
                return $this->minigames;
        }
    }

    public function updateMove($pseudo, $move)
    {
        $this->playerMoves[$pseudo] = $move;
    }

    public function playRound()
    {
        // Jouer le tour et obtenir le résultat
        $this->player1Pseudo = $this->players[0]->pseudo;
        $this->player2Pseudo = $this->players[1]->pseudo;
        if (get_class($this->miniGame) === 'TicTacToe') {
            // Pour le Morpion, jouer un tour pour chaque joueur
            $this->minigames->makeMove($this->player1Pseudo, $this->playerMoves[$this->player1Pseudo][0], $this->playerMoves[$this->player1Pseudo][1]);
            $this->minigames->makeMove($this->player2Pseudo, $this->playerMoves[$this->player2Pseudo][0], $this->playerMoves[$this->player2Pseudo][1]);
            // Vérifier si un des joueurs a gagné
            $result = $this->minigames->checkGameState();
        } else {
            $result = $this->minigames->playRound($this->playerMoves[$this->player1Pseudo], $this->playerMoves[$this->player2Pseudo]);
        }
        return $result;
    }

    public function handlePlayerMove($pseudo, $move)
    {

        /*  if (get_class($this->miniGame) === 'TicTacToe') {
        // Pour le Morpion, $move doit être une paire de coordonnées
        $move = explode(',', $move); // Convertir la chaîne de caractères en tableau
        $move = array_map('intval', $move); // Convertir les éléments du tableau en entiers
    }*/

        $this->updateMove($pseudo, $move);
        $this->lastPlayerMovePseudo = $pseudo;
        $this->lastPlayerMove = $move;
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tVIOLET ETAPE 3\n";
        $this->isCardInPlay5 = 3;


        $moveMessage = [
            'type' => 'playerMove',
            'pseudo' => $pseudo,
            'game' => get_class($this->miniGame),
            'move' => $move
        ];
        $this->broadcast(json_encode($moveMessage));

        // Vérifier si les deux joueurs ont fait un mouvement
        if (count($this->playerMoves) === 2) {
            // echo "[" . date('Y-m-d H:i:s') . "]"  . "VIOLET ETAPE 3";
            //  $this->isCardInPlay5 = 4;
            // Jouer le tour et obtenir le résultat
            $result = $this->playRound();
            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tRésultat du tour : $result\n";

            if (get_class($this->miniGame) === 'TicTacToe') {
                $this->isCardInPlay5 = 4;
                $this->checkGameResult($pseudo, $move, $result);
            }

            if ($result !== null) {

                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tRésultat du tour : $result\n";

                // Réinitialiser les mouvements des joueurs pour le prochain tour
                $this->playerMoves = [];
                $numbers = array_fill(0, 15, 1) // 1 apparaît 15% du temps
                    + array_fill(15, 20, 2) // 2 apparaît 20% du temps
                    + array_fill(35, 19, 3) // 3 apparaît 19% du temps
                    + array_fill(54, 15, 4) // 4 apparaît 15% du temps
                    + array_fill(69, 12, 5) // 5 apparaît 12% du temps
                    + array_fill(81, 8, 6) // 6 apparaît 8% du temps
                    + array_fill(89, 4, 7) // 7 apparaît 4% du temps
                    + array_fill(93, 3, 8) // 8 apparaît 3% du temps
                    + array_fill(96, 2, 9) // 9 apparaît 2% du temps
                    + array_fill(98, 1, 10) // 10 apparaît 1% du temps
                    + array_fill(99, 1, 11); // 11 apparaît 1% du temps

                $number = $numbers[array_rand($numbers)];
                // Préparer le message de résultat
                if (get_class($this->miniGame) === 'TicTacToe') {
                    $winnerPseudo = $result;
                    $winner = null;

                    foreach ($this->players as $player) {
                        if ($player->pseudo === $winnerPseudo) {
                            $winner = $player;
                            break;
                        }
                    }
                    if ($winner !== null && $winner !== 0) {
                        $loser = $this->players[0]->pseudo === $winner->pseudo ? $this->players[1] : $this->players[0];
                        $this->selectedPlayer = $loser;
                        $this->selectedPlayer->sipsTaken += $number;
                        $content = $winner !== null ? $winner->pseudo . " a gagné et " . $loser->pseudo  . " doit boire " . $number . " gorgée(s) !" : "Pas de gagnant tout le monde bois " . $number . " gorgée(s) !";
                    }
                    if ($winner === null) {
                        $content = "Pas de gagnant tout le monde bois " . $number . " gorgée(s) !";
                    }
                } else if (get_class($this->miniGame) === 'RockPaperScissors') {
                    $winner = $result === 1 ? $this->players[0] : ($result === 2 ? $this->players[1] : ($result === 0 ? 0 : null));
                    $loser = $result === 1 ? $this->players[1] : ($result === 2 ? $this->players[0] : ($result === 0 ? 0 : null));
                    $this->selectedPlayer = $loser;
                    if ($result !== 0) {
                    $this->selectedPlayer->sipsTaken += $number;
                    }
                    $content = $result === 1 ? $this->players[0]->pseudo . " a eu la bonne réponse et " . $this->players[1]->pseudo . " doit boire " . $number . " gorgée(s) !" : ($result === 2 ? $this->players[1]->pseudo . " a eu la bonne réponse et " . $this->players[0]->pseudo . " doit boire " . $number . " gorgée(s) !" : ($result === 0 ? "Il y a eu une égalité !" : null));

                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tLe gagnant est : " . ($winner !== null && $winner !== 0 ? $winner->pseudo . "\n": "\t\tPas de gagnant le pierre feille ciseaux recommence") . "\n";
                }
                $resultMessage = [
                    'type' => 'gameResult',
                    'game' => get_class($this->miniGame),
                    'moves' => $this->moves,
                    'result' => $result,
                    'winner' => $winner,
                    'content' => $content
                ];

                // Envoyer le résultat aux joueurs
                $this->broadcast(json_encode($resultMessage));
                if ($result !== 0) {
                    $this->selectedPlayer = null;
                    $this->moves = [];
                    $this->miniGame = null;
                    $this->blueCardPlayer = null;
                    $loser = null;
                    $winner = null;
                    $this->isCardInPlay5 = 0;
                    $this->TicTacToeAlready = false;
                    echo "------------------------------------------------------------------------------------\n";
                } else if ($result === 0) {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tEgalitée\n";
                    echo "------------------------------------------------------------------------------------\n";
                    if (get_class($this->miniGame) === 'TicTacToe') {
                        foreach ($this->selectedPlayers as $player) {
                            $player->sipsTaken += $number;
                        }
                        $this->selectedPlayer = null;
                        $this->moves = [];
                        $this->miniGame = null;
                        $this->blueCardPlayer = null;
                        $loser = null;
                        $winner = null;
                        $this->isCardInPlay5 = 0;
                        $this->TicTacToeAlready = false;
                    } else if (get_class($this->miniGame) === 'RockPaperScissors') {
                        $this->onPlayersSelectedForVioletCard($this->selectedPlayers);
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "erreur carte violette";
                }
            }
        }
    }
    public function checkGameResult($pseudo, $move, $result)
    {
        if (get_class($this->miniGame) === 'TicTacToe' && $result === null) {
            $moveMessage = [
                'type' => 'playerMove',
                'pseudo' => $pseudo,
                'game' => get_class($this->miniGame),
                'move' => $move
            ];
            $result = $this->playRound();
            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tNouveau tour joué, résultat : $result\n";
        }
    }
    public function GETvioletSTATE()
    {
        if ($this->isCardInPlay5 == 1) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 1 VIOLET RECO\n");
            $this->playvioletCard($this->blueCardPlayer);
        } else if ($this->isCardInPlay5 == 2) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 2 VIOLET RECO\n");
            $this->onPlayersSelectedForVioletCard($this->selectedPlayers);
        } else if ($this->isCardInPlay5 == 3) {
            echo "[" . date('Y-m-d H:i:s') . "]"  . ("\t\tCAS 3 VIOLET RECO\n");
            $this->handlePlayerMove($this->playdisconnect, $this->lastPlayerMove);
        } else if ($this->isCardInPlay5 == 4) {

            $data = [
                'type' => 'miniGameSelected',
                'game' => get_class($this->miniGame),
                'moves' => $this->moves,
                'player1' => $this->player1->pseudo,
                'player2' => $this->player2->pseudo,
                'VIOLETJOUEUR' => $this->currentPlayerPURPLE->pseudo,
                'TicTacToeAlready' => $this->TicTacToeAlready
            ];
            $this->broadcastToMultipleViolet(json_encode($data), $this->selectedPlayers);
        } else if ($this->isCardInPlay5 == 5) {
        } else {
            $this->isCardInPlay5 = 0;
        }
    }


    /*public function handlePlayerDisconnect($pseudo) {
    $this->isCardInPlay5 = 5;
    // Vérifier si isCardInPlay5 est vrai
    if ($this->isCardInPlay5) {
        // Faire un choix automatique pour le joueur
        $autoMove = $this->makeAutomaticMove();
        $this->handlePlayerMove($pseudo, $autoMove);
    }
}*/

    public function makeAutomaticMove()
    {
        // Générer un mouvement automatique
        // Vous pouvez remplacer cette logique par votre propre logique pour générer un mouvement
        $moves = ['rock', 'paper', 'scissors'];
        return $moves[array_rand($moves)];
    }
    // METHODE SUPPLEMENTAIRE A ADD AU JEU
}

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;



class MyWebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $lobbies;
    private $numPlayers;
    protected $resourceIds;
    protected $clientStates;
    protected $gameetat;
    protected $startparty = false;

    protected $entredeux = false;
    protected $pseudos;
    protected $active;
    public function __construct()
    {
        $this->clients = array();
        $this->lobbies = array();
        $this->pseudos = array();
        $this->clientStates = array();
        $this->resourceIds = array();

        echo "------------------------------------------------------------------------------------\n";
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tServeur WebSocket démarré\n";
        echo "------------------------------------------------------------------------------------\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $path = $conn->httpRequest->getUri()->getPath();
        if ($path == '/websocket') {
            // Traitez la connexion comme une connexion WebSocket
        } else {
            // Ignorez la connexion ou envoyez une réponse d'erreur
        }
        $this->clients[spl_object_hash($conn)] = $conn;
        $this->resourceIds[spl_object_hash($conn)] = spl_object_hash($conn);
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tNouvelle connexion\n";
        echo "------------------------------------------------------------------------------------\n";
        // $this->broadcastLobbyList();

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        switch ($data['type']) {
            case 'createLobby':
                $pseudo = $data['pseudo'];
                $lobbyName = $data['lobbyName'];
                $lobbyPassword = $data['password'];
                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tCreation d'un nouveau lobby: {$lobbyName} par {$pseudo}\n";

                if (isset($this->lobbies[$lobbyName])) {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tUn lobby avec ce nom existe déjà.\n";
                    $from->send(json_encode(
                        array(
                            'type' => 'error',
                            'message' => 'Un lobby avec ce nom existe déjà.'
                        )
                    ));
                    return;
                }
                $lobby = new Lobby($lobbyName, $lobbyPassword, spl_object_hash($from));
                $lobby->addPlayer($from, $pseudo);
                $this->lobbies[$lobbyName] = $lobby;
                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tLobby {$data['lobbyName']} ajouté à la salle des serveurs\n";
                        $from->send(json_encode(
                    array(
                        'type' => 'lobbyCreated',
                        'pseudo' => $pseudo,
                        'lobbyName' => $lobbyName,
                        'requiresPassword' => !empty($lobbyPassword), // Ajoutez cette ligne
                        'players' => $lobby->getPlayers()  // Ajoutez cette ligne
                    )
                ));
                $this->broadcastLobbyList();
                break;
            case 'joinLobby':
                $lobbyName = $data['lobbyName'];
                $lobbyPassword = $data['password'];
                $pseudo = $data['pseudo'];
                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\t{$pseudo} a rejoint le lobby {$lobbyName}\n";

                if (isset($this->lobbies[$lobbyName])) {
                    $lobby = $this->lobbies[$lobbyName];
                    if ($lobby->checkPassword($lobbyPassword)) {
                        if (count($lobby->getPlayers()) >= 8) {
                            $from->send(json_encode(
                                array(
                                    'type' => 'joinLobbyFailed',
                                    'message' => 'Le lobby est plein'
                                )
                            ));
                        } else {
                            $lobby->addPlayer($from, $pseudo);
                            $playerList = $lobby->getPlayers();
                            $from->send(json_encode(
                                array(
                                    'type' => 'lobbyJoined',
                                    'lobbyName' => $lobbyName,
                                    'players' => $playerList
                                )
                            ));
                        }
                        $lobby->broadcast(json_encode(
                            array(
                                'type' => 'playerJoined',
                                'pseudo' => $pseudo,
                                'playerCount' => count($lobby->players)
                            )
                        ));
                    } else {
                        $from->send(json_encode(
                            array(
                                'type' => 'joinLobbyFailed',
                                'message' => 'Mot de passe incorrect'
                            )
                        ));
                    }
                    $this->broadcastLobbyList();
                } else {
                    $from->send(json_encode(
                        array(
                            'type' => 'joinLobbyFailed',
                            'message' => 'Le lobby n\'existe pas'
                        )
                    ));
                }
                break;
            case 'startGame':
                $lobbyName = trim($data['lobbyName']);
                if (isset($this->lobbies[$lobbyName])) {

                    $this->entredeux = true;
                    $lobby = $this->lobbies[$lobbyName];
                    $players = $lobby->getPlayers(); // Récupérer la liste des joueurs dans le lobby
                    $this->numPlayers = count($players);
                    $lobby->nbjoueursLOBBY = count($players);

                    // Envoyer un message à tous les joueurs dans le lobby pour démarrer le jeu
                    $lobby->gameStarted = true;
                    $lobby->broadcast(json_encode(array('type' => 'startGame', 'gameStarted' => $lobby->gameStarted)));
                    // Check if a Game instance already exists
                    if ($lobby->game === null) {
                        // If not, create a new Game instance
                        $lobby->game = new Game($lobbyName);
                    }

                    $lobby->game->startGame();  // Change this line
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tDébut de la partie de {$lobbyName} avec {$lobby->nbjoueursLOBBY} joueurs\n";

                }
                break;

            case 'playerJoined':
                $lobbyName = trim($data['lobbyName']);
                break;
            case 'getLobbyList':
                $lobbyList = array();
                foreach ($this->lobbies as $lobbyName => $lobby) {
                    $lobbyList[] = array(
                        'name' => $lobbyName,
                        'playerCount' => count($lobby->players),
                        'lobbyName' => $lobbyName,
                        'requiresPassword' => !empty($lobby->password),  // Ajoutez cette ligne
                        'gameStarted' => $lobby->gameStarted, // Ajoutez cette ligne


                    );
                }
                $from->send(json_encode(
                    array(
                        'type' => 'lobbyList',
                        'lobbies' => $lobbyList,

                    )
                ));
                break;
            case 'setPseudo':

                $lobbyName = trim($data['lobbyName']);
                $pseudo = $data['pseudo'];
                $this->pseudos[spl_object_hash($from)] = $pseudo;
                $this->clients[spl_object_hash($from)] = $from;
                if (is_array($data) && isset($data['lobbyName']) && isset($data['pseudo'])) {
                    echo "[" . date('Y-m-d H:i:s') . "]" . "\t\tNouvelle connexion de $pseudo dans le lobby $lobbyName ({$this->resourceIds[spl_object_hash($from)]})\n";
                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $lobby->nbstartparty++;
                        $lobby->game->nbjoueursSETPSEUOS++;
                      

                        echo "------------------------------------------------------------------------------------\n";
                        echo ("COUCOU :" . $lobby->game->nbjoueursSETPSEUOS . "\n");
                        echo ("COUCOU2 :" . $lobby->nbjoueursLOBBY . "\n");
                        echo "------------------------------------------------------------------------------------\n";

                        
                        if ($lobby->game->startparty === true && $this->entredeux === false) {
                            $from->send(json_encode(array('type' => 'redirect', 'url' => 'index.php')));
                        }
                        if ($lobby->game->startparty) {
                            $from->send(json_encode(array('type' => 'redirect', 'url' => 'index.php')));
                        } else {
                            $playerData = ['pseudo' => $pseudo];
                            $lobby->game->addPlayer($playerData, $from);
                            if (count($lobby->getPlayers()) == $lobby->game->nbjoueursSETPSEUOS) {
                                $lobby->game->gameetat = true;
                            }

                            $playerList = $lobby->getPlayers();
                            $pseudos = array_map(function ($player) {
                                return ($player['pseudo']);
                            }, $playerList);
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tJoueurs dans le lobby $lobbyName : " . implode(', ', $pseudos) . "\n";


                            $playerList = $lobby->getPlayers();
                        }
                    }
                    $from->send(json_encode(array('type' => 'message', 'Hello, client!')));
                } else {
                    $from->send(json_encode(array('type' => 'redirect', 'url' => 'index.php')));
                    echo "TOUTE EST FAUX MDR C LA D";
                }

                break;
            case 'startparty':
                if (isset($data['lobbyName'])) {
                    $lobbyName = trim($data['lobbyName']);
                    if (isset($this->lobbies[$lobbyName])) {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tLa game à commencé pour le lobby : $lobbyName\n";
                        $lobby = $this->lobbies[$lobbyName];
                        $lobby->game->gameetat = false;
                        $players = $lobby->game->getPlayers(); // Récupérer la liste des joueurs dans la partie
                        if ($lobby->game->nbjoueursSETPSEUOS === $lobby->nbjoueursLOBBY) {


                            // Create a new Game instance
                            //     $game = new Game($lobbyName);
                            //   $lobby->setGame($game);  // Set the game instance in the lobby
                            // Add players to the game
                            foreach ($players as $key => $player) {
                                foreach ($lobby->game->clients as $pseudo => $client) {
                                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tClient pseudo: $pseudo\n";
                                }


                                // Create a new Player instance for each player
                                $playerObject = new Player($player['pseudo'], false, false);
                                $playerObject->active = 1; // Add this line
                                if ($playerObject === NULL) {
                                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: No player object for player: " . $player['pseudo'] . "\n";
                                    continue;
                                }
                                $playerObject->isPlayerTurn = false; // Add this line


                                // Replace the array entry with the Player object
                                $players[$key] = $playerObject;
                            }
                            // Appeler la méthode startParty pour initialiser l'état du jeu
                            foreach ($players as $player) {
                                //  if ($player->isPlayerTurn) {
                                //    echo "Player " . $player->pseudo . " has isPlayerTurn set to true\n";
                                //} else {
                                //  echo "Player " . $player->pseudo . " has isPlayerTurn set to false\n";
                                //}
                            }
                            // echo "AU DESSUS AVANT STARTPARTY EN DESSOUS APRES STARTPARTY";
                            $gameStartResult = $lobby->game->startParty($players);

                            $activePlayerIndex = $gameStartResult['activePlayerIndex'];
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tActive player index: $activePlayerIndex\n";

                            if (isset($players[$activePlayerIndex])) {

                                $players[$activePlayerIndex] = (object) $players[$activePlayerIndex];
                            } else {
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "No player at active player index\n";
                            }

                            if (isset($players[$activePlayerIndex]) && is_object($players[$activePlayerIndex])) {
                                $lobby->game->activePlayer = $players[$activePlayerIndex];
                            } else {
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Cannot set activePlayer\n";
                            }

                            if (!is_object($lobby->game->activePlayer)) {
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: activePlayer is not an object after startParty\n";
                            }

                            $gameState = $gameStartResult;
                            $gameState['gameStarted'] = true;
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tActive player " . $lobby->game->activePlayer->pseudo . " has isPlayerTurn set to " . ($lobby->game->activePlayer->isPlayerTurn ? "true" : "false") . "\n";
                            $gameState['isPlayerTurn'] = $lobby->game->activePlayer->isPlayerTurn;
                            $lobby->game->numberOfPlayers = 0;

                            // Envoyer l'objet gameState au client
                            foreach ($players as $player) {
                                $lobby->game->numberOfPlayers++;
                                $gameState['playerId'] = $player->id;
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer ID: " . $gameState['playerId'] . "\n";
                                $gameState['isPlayerTurn'] = $lobby->game->activePlayer->pseudo == $player->pseudo;
                                $message = json_encode(array('type' => 'gameStateUpdate', 'gameState' => $gameState));
                                $lobby->game->broadcast2($message, $player);
                            }

                            // $lobby->broadcast(json_encode(array('type' => 'gameStateUpdate', 'gameState' => $gameState)));
                            // $lobby->game->saveGameState($from);


                        } else {
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: TOUT LES JOUEURS NE SONT PAS LA\n";
                            $message = array(
                                'type' => 'NOSTARTPARTY',
                                'content' => "EN ATTENTE DE TOUT LES JOUEURS."
                            );
                        }
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: lobbyName not set in data\n";
                }

                break;

            case 'endTurn':
                $lobbyName = trim($data['lobbyName']);
                $lobby = $this->lobbies[$lobbyName];
                // Piocher une carte pour le joueur actif
                $players = $lobby->game->getPlayers(); // Récupérer la liste des joueurs dans la partie
                if ($lobby->game->isCardInPlay === 0 && $lobby->game->isCardInPlay2 === 0 && $lobby->game->isCardInPlay3 === 0 && $lobby->game->isCardInPlay4 === 0 && $lobby->game->isCardInPlay5 === 0) {
                    $lobby->game->updateActivePlayer();  // Change this line
                    // Continue to update the active player until an active player is found
                    while (!$lobby->game->activePlayer->active) {
                        $lobby->game->updateActivePlayer();
                    }
                    $lobby->game->activePlayer->cardsPlayedThisTurn = 0;
                    $lobby->game->isCardInPlay = 0;
                    $lobby->game->isCardInPlay2 = 0;
                    $lobby->game->isCardInPlay3 = 0;
                    $lobby->game->isCardInPlay4 = 0;
                    // Envoyer l'état du jeu mis à jour au client
                    $gameState = array(
                        'players' => $players,
                        'currentPlayerIndex' => $lobby->currentPlayerIndex,
                        'dropzone' => $lobby->game->getDropzone(),
                        'turnCount' => $lobby->game->turnCount // Ajouter le nombre de tours
                    );
                    $gameState['gameStarted'] = true; // Le jeu a déjà commencé si un tour se termine
                    $gameState['isPlayerTurn'] = $lobby->game->activePlayer->isPlayerTurn;
                    if ($lobby->game->gameStarted === true) {

                        foreach ($players as $player) {
                            $gameState['playerId'] = $player->id;
                            $gameState['isPlayerTurn'] = $lobby->game->activePlayer->pseudo == $player->pseudo;
                            $gameState['sipsTaken'] = $player->sipsTaken; // Ajouter le nombre de gorgées prises par le joueur
                            $message = json_encode(array('type' => 'gameStateUpdate', 'gameState' => $gameState));
                            $lobby->game->broadcast2($message, $player);
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "RIEN";
                    }
                } else {
                    $activePlayer = $lobby->game->activePlayer;
                    $message = array(
                        'type' => 'NOENDTURN',
                        'content' => "FINIS TON ACTION AVANT DE PASSER TON TOUR."
                    );
                    $jsonMessage = json_encode($message);
                    $lobby->game->broadcast2($jsonMessage, $activePlayer);
                    return;
                }
                break;
            case 'getGameState':
                if (isset($data['lobbyName']) && isset($data['pseudo'])) {
                    $lobbyName = trim($data['lobbyName']);
                    $pseudo = $data['pseudo'];
                    if (empty($pseudo)) {
                        $from->send(json_encode(array('type' => 'redirect', 'url' => 'index.php')));
                        return;
                    }
                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $lobby->game->startparty = true;
                        $players = $lobby->game->getPlayers(); // Récupérer la liste des joueurs dans la partie
                        echo "avant ajout d'un joueur dans le lobby " . $lobby->name . ", nombre de joueurs : " .  $lobby->game->numberOfPlayers . "\n";
                        $lobby->game->numberOfPlayers++;
                        echo "ajout d'un joueur dans le lobby " . $lobby->name . ", nombre de joueurs : " .  $lobby->game->numberOfPlayers . "\n";
                        // Vérifier si le joueur est déjà dans la partie
                        $playerExists = array_filter($players, function ($player) use ($pseudo) {
                            return $player->pseudo === $pseudo;
                        });
                        if (!$playerExists) {
                            $playerObject = new Player($pseudo, false, false);
                            $playerObject->active = 1; // Add this line
                            $lobby->game->addPlayer($playerObject, $from);
                            $players = $lobby->game->getPlayers(); // Mettre à jour la liste des joueurs
                        } else {
                            // Le joueur existe déjà, mettre à jour sa connexion WebSocket
                            $existingPlayer = reset($playerExists);
                            $existingPlayer->active = 1; // Add this line
                            $lobby->game->updatePlayerConnection2($existingPlayer, $from);
                        }
                        // $lobby->game->updateActivePlayer();  // Change this line
                        foreach ($players as $player) {
                            if ($player->isPlayerTurn) {
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer " . $player->pseudo . " has isPlayerTurn set to true\n";
                            } else {
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer " . $player->pseudo . " has isPlayerTurn set to false\n";
                            }
                        }
                        // Récupérer l'état actuel du jeu
                        $gameState = array(
                            'players' => $players,
                            'currentPlayerIndex' => $lobby->currentPlayerIndex,
                            'cards' => $lobby->game->getCards(), // Ajouter les cartes à gameState
                            'dropzone' => $lobby->game->dropzone,
                            'turnCount' => $lobby->game->turnCount,
                            'isCardInPlay' => $lobby->game->GETBLUESTATE(),
                            'isCardInPlay2' => $lobby->game->GETJAUNESTATE(),
                            'isCardInPlay3' => $lobby->game->GETvertSTATE(),
                            'isCardInPlay4' => $lobby->game->GETrougeSTATE(),
                            'isCadInPlay5' => $lobby->game->GETvioletSTATE(),


                            // Ajoutez ici d'autres informations sur le jeu que vous souhaitez inclure
                        );
                        $gameState['gameStarted'] = true; // Le jeu a déjà commencé si un tour se termine
                        $gameState['isPlayerTurn'] = $lobby->game->activePlayer->isPlayerTurn;

                        // Envoyer l'état du jeu au client
                        foreach ($players as $player) {
                            $gameState['playerId'] = $player->id;
                            $gameState['isPlayerTurn'] = $lobby->game->activePlayer->pseudo == $player->pseudo;
                            $message = json_encode(array('type' => 'gameStateUpdate', 'gameState' => $gameState));
                            $lobby->game->broadcast2($message, $player);
                        }
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: lobbyName not set in data\n";
                }
                break;
            case 'playCard':
                if (isset($data['lobbyName']) && isset($data['pseudo']) && isset($data['cardId'])) {
                    $lobbyName = trim($data['lobbyName']);
                    $pseudo = $data['pseudo'];
                    $cardId = $data['cardId'];
                    // echo "Received playCard request from $pseudo in lobby $lobbyName for card $cardId\n";


                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $players = $lobby->game->getPlayers();

                        // Trouver le joueur qui joue la carte
                        $playerExists = array_filter($players, function ($player) use ($pseudo) {
                            return $player->pseudo === $pseudo;
                        });

                        if ($playerExists) {
                            $player = reset($playerExists);
                            //  echo "$pseudo found in player list\n";

                            // Jouer la carte
                            $lobby->game->playCard($player, $cardId);
                            // echo "$pseudo played card $cardId\n";
                            $gameState['gameStarted'] = true; // Le jeu a déjà commencé si un tour se termine

                            // Récupérer l'état du jeu mis à jour
                            $gameState = array(
                                'players' => $players,
                                'currentPlayerIndex' => $lobby->currentPlayerIndex,
                                'cards' => $lobby->game->getCards(),
                                'dropzone' => $lobby->game->dropzone,
                                'turnCount' => $lobby->game->turnCount,
                                'gameStarted' => true,
                                'isPlayerTurn' => $lobby->game->activePlayer->isPlayerTurn,

                            );
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tGame state updated\n";

                            // Envoyer l'état du jeu à tous les joueurs
                            foreach ($players as $player) {
                                $gameState['playerId'] = $player->id;
                                $gameState['isPlayerTurn'] = $lobby->game->activePlayer->pseudo == $player->pseudo;
                                $message = json_encode(array('type' => 'gameStateUpdate', 'gameState' => $gameState));
                                $lobby->game->broadcast2($message, $player);
                            }
                        } else {
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: $pseudo not found in player list\n";
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in playCard request\n";
                }
                break;
            case 'playBlueCard':
                if (isset($data['lobbyName']) && isset($data['pseudo']) && isset($data['numbers'])) {
                    $lobbyName = trim($data['lobbyName']);
                    $pseudo = $data['pseudo'];
                    $numbers = $data['numbers'];

                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $players = $lobby->game->getPlayers();

                        // Trouver le joueur qui a choisi les nombres
                        $playerExists = array_filter($players, function ($player) use ($pseudo) {
                            return $player->pseudo === $pseudo;
                        });

                        if ($playerExists) {
                            $player = reset($playerExists);

                            // Appeler onNumbersChosen avec les données du joueur et les nombres choisis
                            $lobby->game->onNumbersChosen((object) [
                                'pseudo' => $pseudo,
                                'numbers' => $numbers
                            ]);
                        } else {
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: $pseudo not found in player list\n";
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in playBlueCard request\n";
                }
                break;
            case 'playerSelected':
                if (isset($data['pseudo']) && isset($data['lobbyName'])) {
                    $pseudo = $data['pseudo'];
                    $lobbyName = trim($data['lobbyName']);

                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];

                        // Appeler onPlayerSelected avec les données du joueur
                        $lobby->game->onPlayerSelected((object) [
                            'pseudo' => $pseudo
                        ]);
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in playerSelected request\n";
                }
                break;

            case 'numberChosen':
                if (isset($data['number']) && isset($data['lobbyName']) && isset($data['pseudo'])) {
                    $number = $data['number'];
                    $lobbyName = trim($data['lobbyName']);
                    $pseudo = $data['pseudo'];

                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];

                        // Appeler onNumberChosen avec le nombre choisi
                        $lobby->game->onNumberChosen((object) [
                            'number' => $number,
                            'pseudo' => $pseudo
                        ]);
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in numberChosen request\n";
                }
                break;
            case 'ANSWER':
                if (isset($data['answer']) && isset($data['lobbyName']) && isset($data['pseudo'])) {
                    $answer = $data['answer'];
                    $lobbyName = trim($data['lobbyName']);
                    $pseudo = $data['pseudo'];

                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $players = $lobby->game->getPlayers();

                        // Trouver le joueur qui a envoyé la réponse
                        $playerExists = array_filter($players, function ($player) use ($pseudo) {
                            return $player->pseudo === $pseudo;
                        });

                        if ($playerExists) {
                            $player = reset($playerExists);

                            // Appeler onAnswerSubmitted avec la réponse du joueur
                            $lobby->game->onAnswerReceived((object) [
                                'answer' => $answer,
                                'pseudo' => $pseudo
                            ]);
                        } else {
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: $pseudo not found in player list\n";
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in ANSWER request\n";
                }
                break;
            case 'VOTE':
                if (isset($data['lobbyName']) && isset($data['pseudo'])) {
                    $lobbyName = trim($data['lobbyName']);
                    $pseudo = $data['pseudo'];
                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $lobby->game->onVoteReceived($data);
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in VOTE request\n";
                }
                break;
            case 'playerSelected2':
                if (isset($data['pseudo']) && isset($data['lobbyName'])) {
                    $pseudo = $data['pseudo'];
                    $lobbyName = trim($data['lobbyName']);

                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $players = $lobby->game->getPlayers();

                        // Trouver le joueur qui a été sélectionné
                        $playerExists = array_filter($players, function ($player) use ($pseudo) {
                            return $player->pseudo === $pseudo;
                        });

                        if ($playerExists) {
                            $player = reset($playerExists);
                            if ($lobby->game->isCardInPlay2 === 1) {
                                // Appeler une méthode différente sur l'objet game
                                $lobby->game->onPlayerSelectedForYellowCard((object) [
                                    'pseudo' => $pseudo
                                ]);
                            } else if ($lobby->game->isCardInPlay3 === 1) {
                                $lobby->game->onPlayerSelectedForvertCard((object) [
                                    'pseudo' => $pseudo
                                ]);
                            } else if ($lobby->game->isCardInPlay4 === 1) {
                                $lobby->game->onPlayerSelectedForROUGECard((object) [
                                    'pseudo' => $pseudo
                                ]);
                            }
                            if ($lobby->game->isCardInPlay2 === 1 && $lobby->game->isCardInPlay3 === 1) {
                                $lobby->game->broadcast2(json_encode([
                                    'type' => 'playerSelectedResponse',
                                    'pseudo' => $pseudo
                                ]), $player);
                            } else if ($lobby->game->isCardInPlay4 === 1) {
                                $lobby->game->broadcast2(json_encode([
                                    'type' => 'playerSelectedResponseRED',
                                    'pseudo' => $pseudo
                                ]), $player);
                            }
                        } else {
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: $pseudo not found in player list\n";
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in playerSelected2 request\n";
                }
                break;
            case 'playersSelectedViolet':
                if (isset($data['pseudos']) && count($data['pseudos']) == 2 && isset($data['lobbyName'])) {
                    $pseudos = $data['pseudos'];
                    $lobbyName = trim($data['lobbyName']);

                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $players = $lobby->game->getPlayers();
                       // echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayers:\n";
                        //foreach ($players as $player) {
                         //   echo gettype($player) . "\n";
                        //}
                        // Trouver les joueurs qui ont été sélectionnés
                        $selectedPlayers = array_filter($players, function ($player) use ($pseudos) {
                            return is_object($player) && property_exists($player, 'pseudo') && in_array($player->pseudo, $pseudos);
                        });

                        if (count($selectedPlayers) == 2) {
                            // Appeler une méthode différente sur l'objet game
                            $lobby->game->onPlayersSelectedForVioletCard($selectedPlayers);

                            // Utiliser broadcastToMultiple au lieu de broadcast2
                            $lobby->game->broadcastToMultipleViolet(json_encode([
                                'type' => 'playersSelectedResponseViolet',
                                'pseudos' => $pseudos
                            ]), $selectedPlayers);
                        } else {
                            echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: One or both pseudos not found in player list\n";
                        }
                    } else {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Lobby $lobbyName not found\n";
                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "Error: Missing data in playersSelectedViolet request\n";
                }
                break;
            case 'playerMove':
                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tplayerMove received\n";
                if (isset($data['pseudo']) && isset($data['lobbyName'])) {
                    $pseudo = $data['pseudo'];
                    $lobbyName = trim($data['lobbyName']);
                    $game = $data['game'];
                    if (isset($this->lobbies[$lobbyName])) {
                        $lobby = $this->lobbies[$lobbyName];
                        $players = $lobby->game->getPlayers();
                        // Un joueur a fait un mouvement
                        $move = $data['move'];



                        // Vérifier le type de jeu et traiter le mouvement en conséquence
                        if (get_class($lobby->game->miniGame) === 'TicTacToe') {
                            //  if ($lobby->game->currentPlayerPURPLE === $lobby->game->selectedPlayers[0]) {
                            // Pour le Morpion, $move doit être une paire de coordonnées
                            if (is_string($move)) {
                                $move = explode(',', $move); // Convertir la chaîne de caractères en tableau
                                $move = array_map('intval', $move); // Convertir les éléments du tableau en entiers
                            }
                            $lobby->game->handlePlayerMove($pseudo, $move);
                            error_log("Handled player move for TicTacToe");

                            if ($lobby->game->currentPlayerPURPLE === $lobby->game->selectedPlayers[0]) {
                                $lobby->game->currentPlayerPURPLE = $lobby->game->player2;
                                error_log('player1dfgd: ' . $lobby->game->player1->pseudo);
                                error_log('player2dfgdf: ' . $lobby->game->player2->pseudo);
                                error_log('VIOLETJOUEURgfd: ' . $lobby->game->currentPlayerPURPLE->pseudo);
                            } else if ($lobby->game->currentPlayerPURPLE === $lobby->game->selectedPlayers[1]) {
                                $lobby->game->currentPlayerPURPLE = $lobby->game->player1;
                                error_log('player1dfgd2: ' . $lobby->game->player1->pseudo);
                                error_log('player2dfgdf2: ' . $lobby->game->player2->pseudo);
                                error_log('VIOLETJOUEURgfd2: ' . $lobby->game->currentPlayerPURPLE->pseudo);
                            }
                            if ($lobby->game->isCardInPlay5 !== 0) {
                                $lobby->game->TicTacToeAlready = true;
                                $data = [
                                    'type' => 'miniGameSelected',
                                    'game' => get_class($lobby->game->miniGame),
                                    'moves' => $lobby->game->moves,
                                    'player1' => $lobby->game->player1->pseudo,
                                    'player2' => $lobby->game->player2->pseudo,
                                    'VIOLETJOUEUR' => $lobby->game->currentPlayerPURPLE->pseudo,
                                    'TicTacToeAlready' => $lobby->game->TicTacToeAlready
                                ];
                                $lobby->game->broadcastToMultipleViolet(json_encode($data), $lobby->game->selectedPlayers);
                            } else if ($lobby->game->isCardInPlay5 === 0) {
                                $lobby->game->TicTacToeAlready = false;
                            }

                            //  $lobby->game->currentPlayerPURPLE = $lobby->game->currentPlayerPURPLE === $lobby->game->selectedPlayers[0] ? $lobby->game->selectedPlayers[1] : $lobby->game->selectedPlayers[0];

                            // } else {
                            //      echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tCE N'EST PAS TON TOUR";
                            //}
                        } else if (get_class($lobby->game->miniGame) === 'RockPaperScissors') {
                            // Pour PierreFeuilleCiseaux, $move est une seule valeur
                            // Assurez-vous que $move est un entier
                            //  $move = intval($move);
                            $lobby->game->handlePlayerMove($pseudo, $move);
                            error_log("Handled player move for RockPaperScissors");
                        }
                        // Mettre à jour le mouvement du joueur

                    }
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "erreur pseudo et lobbyname\n";
                }
                break;





                // case 'RESET' :
                //   $this->lobbies[$messageData['lobbyName']]->game->reset();
                // break;


        }
    }

    public function onClose(ConnectionInterface $conn)
    {

        foreach ($this->lobbies as $lobby) {
            if ($lobby->players->contains($conn)) {

                $pseudo = $lobby->pseudos[spl_object_hash($conn)];
                $gameStarted = $lobby->getGame() ? $lobby->getGame()->isGameStarted() : false;

                if (!$gameStarted) {
                    $lobby->removePlayer($conn);
                }
                $lobby->broadcast(json_encode(
                    array(
                        'type' => 'playerLeft',
                        'playerCount' => count($lobby->players)
                    )
                ));
                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tEtat de la party gamesstarted : " . ($lobby->getGame() ? $lobby->getGame()->isGameStarted() : 'false') . "\n";



                if (count($lobby->players) == 0 && ($lobby->getGame() === null || $lobby->getGame()->isGameStarted() == false)) {
                    unset($this->lobbies[$lobby->name]);
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPARTY SUPPRIME \n";
                }
                // $this->clientStates[spl_object_hash($conn)]['lastLobby'] = $lobby->name;
                $this->broadcastLobbyList();
                // Ajout de la vérification du jeu et de la décrémentation du nombre de joueurs


            }
        }
          //  $gameStarted = $lobby->getGame() ? $lobby->getGame()->isGameStarted() : false;
            if (is_object($lobby->game) && $lobby->game->startparty) {
                if ($lobby->game->startparty) {
                    echo " " . date('Y-m-d H:i:s') . " GAME STARTED " . ($lobby->game->startparty ? 'true' : 'false') . "\n";

           
                    
                    $lobby->game->playdisconnect = null;
                    foreach ($this->lobbies as $lobby) {
                        if (is_object($lobby->game) && $lobby->game->startparty) {
                            echo " " . date('Y-m-d H:i:s') . " GAME STARTED " . ($lobby->game->startparty ? 'true' : 'false') . "\n";
                        if (is_object($lobby->game)) {  // AJOUTE LE 10/12
                            $players = $lobby->game->getPlayers();
                            foreach ($players as $player) {
                                if (isset($lobby->game->clients[$player->pseudo])) {
                                    if (isset($lobby->game->clients[$player->pseudo]) && $lobby->game->clients[$player->pseudo] === $conn) {
                                        $lobby->game->playerDisconnected = $player;
                                        // Mettre la propriété active du joueur à 0
                                        if ($player !== null) {
                                            $isActive = $player->active;
                                        }
                                        if ($lobby->game->playerDisconnected !== null && $lobby->game->playerDisconnected->active != 0) {
                                            $lobby->game->playerDisconnected->active = 0;
                                            $lobby->game->playdisconnect = $lobby->game->playerDisconnected->pseudo;
                                            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer " . $lobby->game->playerDisconnected->pseudo . " is now inactive\n";
                                            echo "avant suppresion d'un joueur dans le lobby " . $lobby->name . ", nombre de joueurs : " .  $lobby->game->numberOfPlayers . "\n";
                                            $lobby->game->numberOfPlayers--;
                                            echo "apres suppresion d'un joueur dans le lobby " . $lobby->name . ", nombre de joueurs : " .  $lobby->game->numberOfPlayers . "\n";
                                            // ... Reste du code ...
                                        }
                                        if ($lobby->game->activePlayer->pseudo === $player->pseudo) {
                                            if ($lobby->game->isCardInPlay === 0 && $lobby->game->isCardInPlay2 === 0 && $lobby->game->isCardInPlay3 === 0 && $lobby->game->isCardInPlay4 === 0 && $lobby->game->isCardInPlay5 === 0) {

                                                $lobby->game->updateActivePlayer();
                                                $lobby->game->activePlayer->cardsPlayedThisTurn = 0;
                                                $lobby->game->isCardInPlay = 0;
                                                $lobby->game->isCardInPlay2 = 0;
                                                $lobby->game->isCardInPlay3 = 0;
                                                $lobby->game->isCardInPlay4 = 0;
                                                // Envoyer l'état du jeu mis à jour au client
                                                $gameState = array('players' => $players, 'currentPlayerIndex' => $lobby->currentPlayerIndex, 'dropzone' => $lobby->game->getDropzone());
                                                $gameState['gameStarted'] = true; // Le jeu a déjà commencé si un tour se termine
                                                $gameState['isPlayerTurn'] = $lobby->game->activePlayer->isPlayerTurn;

                                                foreach ($players as $player) {
                                                    $gameState['playerId'] = $player->id;
                                                    $gameState['isPlayerTurn'] = $lobby->game->activePlayer->pseudo == $player->pseudo;
                                                    $message = json_encode(array('type' => 'gameStateUpdate', 'gameState' => $gameState));
                                                    $lobby->game->broadcast2($message, $player);
                                                }
                                            }
                                        }

                                        // Continue to update the active player until an active player is found

                                        break 2;
                                    }
                                } else {
                                    $conn->send(json_encode(array('type' => 'redirect', 'url' => 'index.php')));
                                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer " . $player->pseudo . " does not exist in the clients array.\n";
                                }
                            }
                        }
                    }
                }



                    foreach ($this->lobbies as $lobby) {
                        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tGame: " . $lobby->name . "\n";
                        if (is_object($lobby->game)) {  // AJOUTE LE 10/12
                            $players = $lobby->game->getPlayers();
                            foreach ($players as $player) {
                                echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPlayer: " . $player->pseudo . ", Active: " . ($player->active ? "Yes" : "No") . "\n";
                            }

                            echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tFERMETURE OUI OUI\n";
                            error_log('Countdown active3: ' . ($lobby->game->countdownActive ? 'true' : 'false'));
                            if ($lobby->game->countdownActive) {
                                $elapsedTime = time() - $lobby->game->countdownStart;
                                $lobby->game->countdownRemaining = $lobby->game->countdownDuration - $elapsedTime;
                                $lobby->game->countdownPausedAt = time();
                                $lobby->game->countdownActive = false;
                            }
                            if ($lobby->game->startparty === true) {
                                $allPlayersInactive = true;
                                if ($lobby->game->isCardInPlay5 === 2 || $lobby->game->isCardInPlay5 === 3) {
                                    //  $lobby->game->handlePlayerDisconnect($pseudo);
                                    if (get_class($lobby->game->miniGame) === 'RockPaperScissors') {
                                        $lobby->game->autoMove = $lobby->game->makeAutomaticMove();
                                        // $lobby->game->handlePlayerMove($lobby->game->playdisconnect, $lobby->game->autoMove);
                                        $lobby->game->isCardInPlay5 = 3;
                                    } else if (get_class($lobby->game->miniGame) === 'TicTacToe') {
                                        $lobby->game->isCardInPlay5 = 4;
                                    }
                                }
                                foreach ($lobby->game->getPlayers() as $player) {
                                    if ($player->active != 0) {
                                        $allPlayersInactive = false;
                                        break;
                                    }
                                }
                                if ($allPlayersInactive) {

                                    unset($this->lobbies[$lobby->name]);
                                    $lobby->game->startparty = false;
                                    $lobby->game = null;
                                    // Supprimez la partie ici
                                    foreach ($this->clients as $client) {
                                        $client->send(json_encode([
                                            'type' => 'lobbyList',
                                            'lobbies' => array_values($this->lobbies)
                                        ]));
                                    }
                                } else {
                                }
                                if (isset($lobby->game) && isset($lobby->game->activePlayer) && isset($lobby->game->activePlayer->pseudo)) {
                                    if ($lobby->game->activePlayer->pseudo === $player->pseudo) {
                                        if ($lobby->game->isCardInPlay === 0 && $lobby->game->isCardInPlay2 === 0 && $lobby->game->isCardInPlay3 === 0 && $lobby->game->isCardInPlay4 === 0 && $lobby->game->isCardInPlay5 == 0) {
                                            while (!$lobby->game->activePlayer->active) {
                                                $lobby->game->updateActivePlayer();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            echo "gameetat: " . ($lobby->game->gameetat ? 'true' : 'false') . ", startparty: " . ($lobby->game->startparty ? 'true' : 'false') . "\n";
        if (isset($lobby->game) && $lobby->game->gameetat === true && $lobby->game->startparty === false) {
            echo "gameetat: " . ($lobby->game->gameetat ? 'true' : 'false') . ", startparty: " . ($lobby->game->startparty ? 'true' : 'false') . "\n";
            $lobby->nbstartparty--;
            $lobby->game->nbjoueursSETPSEUOS--;
            if ($lobby->nbstartparty === 0) {
                echo "[" . date('Y-m-d H:i:s') . "]" . "\t\tSUPPRESION DE LA PARTIE2 \n";
                unset($this->lobbies[$lobby->name]);
                $lobby->game = null;
            }

            if (isset($this->clients[spl_object_hash($conn)]) && isset($this->pseudos[spl_object_hash($conn)])) {
                $pseudo = $this->pseudos[spl_object_hash($conn)];
            } else {
                echo "[" . date('Y-m-d H:i:s') . "]" . "\t\tPseudo non défini pour la connexion " . spl_object_hash($conn) . "\n";
            }
            // Trouver le joueur par son pseudo
            $player = array_filter($lobby->getPlayers(), function ($player) use ($pseudo) {
                return $player['pseudo'] === $pseudo;
            });
            // Si le joueur est trouvé, supprimer le joueur par son pseudo
            if (!empty($player) && $lobby->game !== null) {
                $lobby->game->removePlayer($pseudo);
            }
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'type' => 'lobbyList',
                    'lobbies' => array_values($this->lobbies)
                ]));
            }
        }
        
     
    /*    if (isset($lobby->game) && $lobby->game->gameetat === true && $lobby->game->startparty === false) {
            if ($lobby->game->gameetat === true && $lobby->game->startparty === false) {
                $lobby->nbstartparty--;
                $lobby->game->nbjoueursSETPSEUOS--;
                if ($lobby->nbstartparty === 0) {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tSUPPRESION DE LA PARTIE3 \n";
                    unset($this->lobbies[$lobby->name]);
                    $lobby->game->startparty = false;
                    $lobby->game = null;
                }

                if (isset($this->clients[spl_object_hash($conn)]) && isset($this->pseudos[spl_object_hash($conn)])) {
                    $pseudo = $this->pseudos[spl_object_hash($conn)];
                } else {
                    echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tPseudo non défini pour la connexion " . spl_object_hash($conn) . "\n";
                }
                // Trouver le joueur par son pseudo
                $player = array_filter($lobby->getPlayers(), function ($player) use ($pseudo) {
                    return $player['pseudo'] === $pseudo;
                });
                // Si le joueur est trouvé, supprimer le joueur par son pseudo
                if (!empty($player) && $lobby->game !== null) {
                    $lobby->game->removePlayer($pseudo);
                }
                foreach ($this->clients as $client) {
                    $client->send(json_encode([
                        'type' => 'lobbyList',
                        'lobbies' => array_values($this->lobbies)
                    ]));
                }
            }
        }*/

        unset($this->clients[spl_object_hash($conn)]);
        unset($this->pseudos[spl_object_hash($conn)]);  // Supprimez le pseudo de $this->pseudos
        echo "[" . date('Y-m-d H:i:s') . "]"  . "\t\tDeconnexion : ({$this->resourceIds[spl_object_hash($conn)]})\n";




        unset($this->resourceIds[spl_object_hash($conn)]);
        $this->broadcastLobbyList();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "[" . date('Y-m-d H:i:s') . "]"  . "Erreur: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function broadcastLobbyList()
    {
        $lobbyList = array();
        foreach ($this->lobbies as $lobby) {
            $lobbyList[] = array(
                'name' => $lobby->name,
                'playerCount' => $lobby->players->count(),
                'requiresPassword' => !empty($lobby->password),
                'gameStarted' => $lobby->gameStarted,

            );
        }
        foreach ($this->clients as $client) {
            $client->send(json_encode(
                array(
                    'type' => 'lobbyList',
                    'lobbies' => $lobbyList
                )
            ));
        }
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new MyWebSocketServer()
        )
    ),
    8080,
    '0.0.0.0'
);
//$server = new \Ratchet\App('localhost', 8080);

$server->run();
