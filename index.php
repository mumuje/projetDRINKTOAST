<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DrinkToast | Accueil</title>
  <link rel="icon" href="img/logo.png" type="image/png">

  <!-- Votre style CSS -->
  <link rel="stylesheet" href="style.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  
  <style>
    .color-gold {
      color: gold;
    }

    .color-red {
      color: red;
    }

    .color-blue {
      color: blue;
    }

    .bg-gold {
      background-color: gold;
    }

    .bg-red {
      background-color: red;
    }

    .bg-blue {
      background-color: blue;
    }

    /* Ajoutez ici vos styles personnalisés */
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-gold">

    <a class="navbar-brand" href="index.php">
      <img src="img/logo.png" alt="Logo" class="logo" width="100px">
    </a>

    <a class="navbar-brand" href="index.php">
      <h1 class="color-red">DrinkToast</h1>
    </a>
  </nav>

  <div class="container mt-4">
    <header class="text-center mb-4">
      <h1 class="color-red">Drink Duel</h1>
    </header>
    <main>
      <div class="container mt-4">
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="left-image">Image gauche</div>
          </div>
          <div class="col-md-6">
            <div class="right-image">Image droite</div>
          </div>
        </div>

        <!-- Formulaire pour enregistrer le pseudo -->
        <form id="pseudo-form" method="post" class="mb-4">
          <div class="form-group">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" class="form-control" required maxlength="15">
          </div>
          <button type="submit" class="btn btn-primary">Enregistrer le pseudo!</button>
        </form>


        <!-- Création et gestion de lobby -->
        <div id="lobby-creation" class="mb-4">
          <h2>Créer un nouveau lobby</h2>

          <form id="create-lobby-form" method="post" class="mb-4">
            <div class="form-group">
              <label for="lobby-name">Nom du lobby :</label>
              <input type="text" id="lobby-name" name="lobby-name" class="form-control" required autocomplete="off" maxlength="20">
            </div>
            <div class="form-group">
              <label for="lobby-password">Mot de passe du lobby (laisser vide pour un lobby public) :</label>
              <input type="password" id="lobby-password" name="lobby-password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Créer le lobby</button>
          </form>

          <h2>Rejoindre un lobby existant :</h2>
          <ul id="lobby-list" class="list-group"></ul>
        </div>

        <!-- Informations sur le lobby -->
        <div id="lobby-info" style="display: none;">
          <h1>Lobby: <span id="lobby-name-display"></span></h1>
          <h3 id="player-count">Joueurs:</h3>
          <ul id="player-list" class="list-group">
            <!-- La liste des joueurs sera ajoutée ici par le script JavaScript -->
          </ul>
          <button type="button" id="start-game" class="btn btn-success" onclick="startGame()">Lancer la partie</button>
          <p id="error-message" class="text-danger"></p>
          <div id="game-rules" class="mt-4">
            <img src="img/regle.png" alt="voici les petites régles d'amour" id="rules-image" class="img-fluid">
            <h3>Règles du jeu :</h3>
            <!-- Règles du jeu ici -->
          </div>
        </div>
        <p class="warning-text">L'abus d'alcool est dangereux pour la santé, à consommer avec modération. <br> Vous pouvez jouer sans boire!!</p>
      </div>
    </main>


    <footer class="text-center mt-4">
      <p class="color-blue">&copy; 2023 DrinkToast</p>
      <p class="text-muted" style="font-size: small;">Version 1.0.0</p>
    </footer>
  </div>

  <!-- Bootstrap JS et jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <!-- Votre script JS -->
  <script src="/app.js"></script>
</body>

</html>