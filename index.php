<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DrinkToast | Accueil</title>
  <link rel="icon" href="img/logo.png" type="image/png">


  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
  <!-- Votre style CSS -->
  <?php include 'include_css.php'; ?>


</head>

<body>
    <nav class="navbar navbar-expand-lg" style="background-color: var(--dark-color)">

      <a class="navbar-brand" href="index.php">
        <img src="img/logo.png" alt="Logo" class="logo" width="100px">
      </a>

      <a class="navbar" href="index.php">
        <h1 class="title">DrinkToast</h1>
      </a>

      <button class="navbar-toggler light" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list" style="color:white; font-size: 48px;"></i>
      </button>

      <div class="collapse navbar-collapse hidden" id="navbarContent">
  <div class="row">

    <!-- Lien pour les règles -->
   <!-- <div class="col-lg-2">
      <a class="navbar" href="regles.php">
        <h2 class="color-white">
          <i class="bi bi-list-nested"></i>  Icône de règles 
          Règles
        </h2>
      </a>
    </div> -->

    <!-- Lien pour la carte -->
    <div class="col-lg-2"> 
      <a class="navbar" href="cartes.php">
        <h2 class="color-white">
          <i class="bi bi-joystick"></i> <!-- Icône de carte à jouer -->
          Carte
        </h2>
      </a>
    </div> 

  </div> 
</div>

    </nav>



    <div class="container mt-4">
      <header class="text-center mb-4">
        <h1 class="title-1">Drink Duel</h1>
      </header>
      <main>

        <!-- Image et description du jeu -->
        <div class="container mt-4 mb-4">
          <div class="card rounded">
            <div class="row no-gutters">
              <div class="col-md-6">
                <img src="img/drinkduel.png" alt="Image de duel" class="img-fluid">
              </div>
              <div class="col-md-6">
                <div class="card-body">
                  <p class="card-text text-justify">
                    Bienvenue sur DrinkToast, le jeu qui vous permet de vous amuser avec vos amis tout en buvant un coup !<br>
                    Pour jouer, il vous suffit de créer un lobby et d'inviter vos amis à vous rejoindre. Une fois que tout le monde est prêt,
                    vous pouvez lancer la partie et commencer à jouer ! Pour voir le pouvoir de chaque carte cliquer
                    <a href="cartes.php">ici</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>


        <!-- Formulaire pour enregistrer le pseudo -->
        <div class="d-flex justify-content-center mb-4 position-relative w-100"> <!-- ADD PAR ADRIEN LE 18/12 -->
          <div class="left-image position-absolute d-none d-md-block"></div>
          <div class="col-12 col-md-8 col-lg-5"> <!-- """" ICI C'est POUR LE RESPOONSIVE -->
            <div class="card-body"> <!-- ADD PAR ADRIEN LE 18/12  SA C DES TRUC BOOSTRAP -->
              <form id="pseudo-form" method="post" class="mb-4">
                <div class="form-group">
                  <label for="pseudo">Pseudo :</label>
                  <input type="text" id="pseudo" name="pseudo" class="form-control" required maxlength="15">
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer le pseudo!</button>
              </form>
            </div>
          </div>
          <div class="right-image position-absolute d-none d-md-block"></div>
        </div>


        <!-- Création et gestion de lobby -->
        <div id="lobby-creation" class="mb-4">
          <div class="d-flex justify-content-center"> <!-- Ajout de la classe d-flex et justify-content-center ADRIEN -->
            <div class="col-12 col-md-8 col-lg-8"> <!-- """" ICI C'est POUR LE RESPOONSIVE  ADRIEN-->
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
            </div>
          </div>


          <div class="d-flex justify-content-center">
            <div class="col-12 col-md-8 col-lg-8">
              <h2 class="h4 h2-md">Rejoindre un lobby existant :</h2>
              <ul id="lobby-list" class="list-group"></ul>
            </div>
          </div>
        </div>

        <!-- Informations sur le lobby -->
        <div id="lobby-info" class="container" style="display: none;">
          <div class="row justify-content-center position-relative">
            <div class="left-image2 position-absolute d-none d-md-block"></div>

            <div class="col-12 col-md-8 col-lg-6">
              <h1 class="text-center p-3 border rounded bg-light shadow">Lobby: <span id="lobby-name-display"></span></h1>
              <h3 id="player-count" class="text-center">Joueurs:</h3>
              <ul id="player-list" class="list-group">
                <!-- La liste des joueurs sera ajoutée ici par le script JavaScript -->
              </ul>
              <button type="button" id="start-game" class="btn btn-success mt-3 start-game-button" onclick="startGame()">Lancer la partie</button>
              <p id="error-message" class="text-danger"></p>

              <div id="game-rules" class="card">
                <div class="card-body position-relative">
                  <div class="col-12 col-sm-6">
                    <h3 class="card-title">Règles du jeu :</h3>
                    <p class="card-text"><strong>CONCEPT DU JEU :</strong></p>
                  </div>
                  <img src="img/regle.png" alt="voici les petites régles d'amour" id="rules-image" class="card-img-top position-absolute" style="right: 0; top: 0;">
                  <div>
                    <p class="card-text">6 cartes différentes</p>
                    <ul class="card-text">
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
              </div>

            </div>
            <div class="right-image2 position-absolute d-none d-md-block"></div>

          </div>
        </div>
        <div class="mb-4 text-center">
          <p class="alert alert-danger">L'abus d'alcool est dangereux pour la santé, à consommer avec modération. <br> Vous pouvez jouer sans boire!!</p>
        </div>
      </main>



    </div>

    <footer class="bg-dark text-white pt-4 pb-4 mt-5">
      <div class="container">
        <div class="row">
          <!-- Section À propos -->
          <div class="col-md-4 mb-3">
            <h5>À propos de DrinkToast</h5>
          </div>

          <!-- Section Liens utiles -->
          <div class="col-md-4 mb-3">
            <h5>Liens utiles</h5>
            <ul class="list-unstyled">
              <li><a href="#" class="text-white">Politique de confidentialité</a></li>
              <li><a href="#" class="text-white">Termes et conditions</a></li>
              <li><a href="#" class="text-white">FAQ</a></li>
            </ul>
          </div>

          <!-- Section Contact et réseaux sociaux -->
          <div class="col-md-4 mb-3">
            <h5>Contactez-nous</h5>
            <p>Email : <a class="link" href="mailto:drinktoastgame@gmail.com">drinktoastgame@gmail.com</a></p>
          </div>
        </div>

        <div class="border-top pt-3 mt-3">
          <p class="mb-0">&copy; 2023 DrinkToast</p>
          <p style="font-size: small;">Version 1.0.0</p>
        </div>
    </footer>


    <!-- Bootstrap JS et jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Votre script JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/app.js"></script>
  </div>
</body>

</html>