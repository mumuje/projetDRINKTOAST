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

            <!-- Lien pour les règles -->
            <a class="navbar" href="regles.php">
                <h2 class="color-white">
                    <i class="bi bi-list-nested"></i> <!-- Icône de règles -->
                    Règles
                </h2>
            </a>

            <!-- Lien pour la carte -->
            <a class="navbar" href="cartes.php">
                <h2 class="color-white">
                    <i class="bi bi-joystick"></i> <!-- Icône de carte à jouer -->
                    Carte
                </h2>
            </a>

        </div>

    </nav>

    <div class="container container-wide mt-4">
        <header class="text-center mb-4">
            <h1 class="title-1">Les cartes :</h1>
        </header>
        <main>
            <div class="cards-container">
                <!----------------------------------CARTE BLEU---------------------------------->

                <div class="wrap animate pop">
                    <div class="overlay">
                        <div class="overlay-content animate slide-left delay-2">
                            <h1 class="animate slide-left pop delay-4">Bleu</h1>
                            <p class="animate slide-left pop delay-5 special-text" style="color: white; margin-bottom: 2.5rem;">
                                Commune: <em>devine le nombre</em></p>
                        </div>
                        <div class="image-content animate slide delay-5"></div>
                        <div class="dots animate">
                            <div class="dot animate slide-up delay-6"></div>
                            <div class="dot animate slide-up delay-7"></div>
                            <div class="dot animate slide-up delay-8"></div>
                        </div>
                    </div>
                    <div class="text">
                        <p class="special-text2"><img class="inset" src="img/bleu.png" alt="" />Bienvenue dans le
                            délicieux défi de la Carte
                            Bleue, un jeu palpitant de "devine le nombre" qui va pimenter votre soirée !</p>
                        <p class="special-text2">Le joueur qui
                            brandit fièrement la carte bleue, a la mission de choisir un ou plusieurs chiffres entre 0
                            et 9.</p>
                        <p class="special-text2">Une fois que le choix est fait, la tension monte, et c'est le moment de
                            la révélation ! Le
                            joueur confirme ses chiffres soigneusement choisis et jette le défi à un joueur de son choix
                            parmi tous les intrépides participants. Le joueur choisi devra se mesurer au défi en tentant
                            de dénicher au moins un des chiffres mystères. Et là, la fête commence vraiment!</p>
                        <p class="special-text2">Imaginez ceci : Notre valeureux joueur A sélectionne les chiffres 1, 5,
                            8, et 9 avec un
                            sourire malicieux. Il désigne ensuite le courageux joueur B pour relever le défi. Alors que
                            le joueur B concentre toute son attention. D'un geste assuré, il choisit le chiffre 2. Oh,
                            la déception ! Le chiffre ne correspond pas, et voilà notre joueur B condamné à s'abreuver
                            de 4 gorgées exaltantes pour célébrer son courage et sa témérité!</p>
                        <p class="special-text2">Alors, que le jeu commence, que les rires résonnent, et que la Carte
                            Bleue ajoute une touche
                            d'intrigue et de gaieté à votre soirée. À vos chiffres, prêts, buvez !</p>

                    </div>
                </div>

                <!----------------------------------CARTE JAUNE---------------------------------->


                <div class="wrapJaune animate pop">
                    <div class="overlayJaune">
                        <div class="overlay-contentJaune animateJaune slide-left delay-2">
                            <h1 class="animateJaune slide-left pop delay-4">Jaune</h1>
                            <p class="animateJaune slide-left pop delay-5 special-text" style="color: white; margin-bottom: 2.5rem;">
                                Commune: <em>Question General</em></p>
                        </div>
                        <div class="image-contentJaune animate slide delay-5"></div>
                        <div class="dotsJaune animateJaune">
                            <div class="dotJaune animate slide-upJaune delay-6"></div>
                            <div class="dotJaune animate slide-upJaune delay-7"></div>
                            <div class="dotJaune animate slide-upJaune delay-8"></div>
                        </div>
                    </div>
                    <div class="textJaune">
                        <p class="special-text2"><img class="insetJaune" src="img/jaune.png" alt="" />Bienvenue dans
                            l'électrisant défi de la
                            Carte Jaune, un jeu enivrant de questions aléatoires qui va ajouter une pincée de mystère à
                            votre soirée ! </p>
                        <p class="special-text2">Le joueur chanceux qui brandit fièrement la carte jaune a la mission de
                            choisir un joueur et
                            de lui lancer une question surprise, une question pleine d'incertitude qui piquera sa
                            curiosité.</p>
                        <p class="special-text2">Une fois que la question est posée, la tension monte, et voilà le
                            moment crucial ! Le joueur
                            choisi doit relever le défi en répondant à la question dans un délai horrible de 20
                            secondes. La fête débute réellement à ce moment-là !</p>
                        <p class="special-text2">Imaginez ceci : Notre joueur A, espiègle, pointe du doigt le courageux
                            joueur B pour
                            affronter l'inconnu. Le compte à rebours commence alors que le joueur B se lance dans une
                            course contre la montre pour offrir une réponse. Les secondes s'égrènent, et voilà le
                            dénouement ! Mais la Carte Jaune réserve une surprise supplémentaire : tous les participants
                            votent sur la validité de la réponse du joueur B.</p>
                        <p class="special-text2">Alors que les votes fusent, que les rires résonnent, la Carte injecte
                            un zeste d'intrigue et
                            de camaraderie à votre soirée. À vos réponses, prêts, votez !</p>
                    </div>
                </div>



                <!----------------------------------CARTE ROUGE---------------------------------->


                <div class="wrapRouge animate pop">
                    <div class="overlayRouge">
                        <div class="overlay-contentRouge animateRouge slide-left delay-2">
                            <h1 class="animateRouge slide-left pop delay-4">Rouge</h1>
                            <p class="animateRouge slide-left pop delay-5 special-text" style="color: white; margin-bottom: 2.5rem;">
                                Commune: <em>Action</em></p>
                        </div>
                        <div class="image-contentRouge animate slide delay-5"></div>
                        <div class="dotsRouge animateRouge">
                            <div class="dotRouge animate slide-upRouge delay-6"></div>
                            <div class="dotRouge animate slide-upRouge delay-7"></div>
                            <div class="dotRouge animate slide-upRouge delay-8"></div>
                        </div>
                    </div>
                    <div class="textRouge">
                        <p class="special-text2"><img class="insetRouge" src="img/rouge.png" alt="" />Bienvenue dans
                            l'énergique défi de la
                            Carte Rouge, une explosion d'actions qui va propulser votre soirée vers des sommets
                            d'amusement !</p>
                        <p class="special-text2">Le joueur audacieux qui brandit fièrement la carte rouge a une mission
                            excitante : choisir un
                            complice parmi les participants. Ce joueur intrépide se verra assigner une action à
                            réaliser, en présentiel pour le groupe physique ou en distancel pour les esprits
                            éparpillés à travers les ondes virtuelles.</p>
                        <p class="special-text2">Une fois l'action dévoilée, la tension monte, et voilà le moment
                            crucial ! Le joueur élu
                            se lance dans une cascade d'actions, ayant 5 minutes pour exécuter l'action en personne ou
                            pour réaliser l'action à distance. La véritable fête commence à ce moment précis !</p>
                        <p class="special-text2">Imaginez ceci : Notre joueur A, espiègle, désigne le courageux joueur B
                            pour affronter le
                            défi. Le compte à rebours s'enclenche, et le joueur B se précipite pour accomplir l'action
                            en face à face avec le groupe présent ou à travers les ondes pour le groupe en distancel.
                        </p>
                        <p class="special-text2">Les 5 minutes s'écoulent, et voilà le dénouement ! La Carte Rouge
                            réserve une surprise
                            supplémentaire : tous les participants vote sur la réussite ou non de
                            l'action du joueur B, chacune adaptée à son public respectif.</p>
                        <p class="special-text2"> Les rires fusent, les
                            votes résonnent, la Carte Rouge insuffle une dose d'intrigue et de camaraderie à votre
                            soirée. À vos actions, prêts, votez !</p>
                    </div>
                </div>

                <!----------------------------------CARTE VERTE---------------------------------->


                <div class="wrapVerte animate pop">
                    <div class="overlayVerte">
                        <div class="overlay-contentVerte animateVerte slide-left delay-2">
                            <h1 class="animateVerte slide-left pop delay-4">Verte</h1>
                            <p class="animateVerte slide-left pop delay-5 special-text" style="color: white; margin-bottom: 2.5rem;">
                                Commune: <em>Enigme</em></p>
                        </div>
                        <div class="image-contentVerte animate slide delay-5"></div>
                        <div class="dotsVerte animateVerte">
                            <div class="dotVerte animate slide-upVerte delay-6"></div>
                            <div class="dotVerte animate slide-upVerte delay-7"></div>
                            <div class="dotVerte animate slide-upVerte delay-8"></div>
                        </div>
                    </div>
                    <div class="textVerte">
                        <p class="special-text2"><img class="insetVerte" src="img/verte.png" alt="" />Bienvenue dans le
                            fascinant défi de la
                            Carte Verte, une aventure énigmatique qui va ajouter une touche de mystère à votre soirée !
                        </p>
                        <p class="special-text2">Le joueur rusé qui brandit fièrement la carte Verte a pour mission de
                            choisir un complice
                            parmi les participants. Ce joueur intrépide se voit confier une énigme à résoudre, un défi
                            intellectuel captivant.</p>
                        <p class="special-text2">Une fois l'énigme dévoilée, l'excitation monte, et voilà le moment
                            crucial ! Le joueur élu
                            plonge dans les méandres de l'énigme, disposant de 45 secondes pour démêler le mystère. La
                            véritable fête débute à ce moment précis !</p>
                        <p class="special-text2">Imaginez ceci : Notre joueur A, malicieux, désigne le courageux joueur
                            B pour relever le
                            défi. Le compte à rebours s'amorce, et le joueur B s'immerge dans la réflexion, tentant
                            habilement de trouver la solution à l'énigme.</p>
                        <p class="special-text2">Les 45 secondes s'écoulent, et voilà le dénouement ! La Carte Verte
                            réserve une surprise
                            supplémentaire : tous les participants votent sur la justesse ou non de
                            la réponse du joueur B.</p>
                        <p class="special-text2"> Les rires éclatent, les votes retentissent, la Carte Verte insuffle
                            une dose d'intrigue et de camaraderie à votre soirée. À vos énigmes, prêts, votez !</p>
                    </div>
                </div>

                <!----------------------------------CARTE MULTICOLORE---------------------------------->


                <div class="wrapMulticolore animate pop">
                    <div class="overlayMulticolore">
                        <div class="overlay-contentMulticolore animateMulticolore slide-left delay-2">
                            <h1 class="animateMulticolore slide-left pop delay-4 custom-h1">Multicolore</h1>
                            <p class="animateMulticolore slide-left pop delay-5 special-text" style="color: white; margin-bottom: 2.5rem;">
                                Rare: <em>Boisson en folie</em></p>
                        </div>
                        <div class="image-contentMulticolore animate slide delay-5"></div>
                        <div class="dotsMulticolore animateMulticolore">
                            <div class="dotMulticolore animate slide-upMulticolore delay-6"></div>
                            <div class="dotMulticolore animate slide-upMulticolore delay-7"></div>
                            <div class="dotMulticolore animate slide-upMulticolore delay-8"></div>
                        </div>
                    </div>
                    <div class="textMulticolore">
                        <p class="special-text2"><img class="insetMulticolore" src="img/multicolor.png" alt="" />Bienvenue dans l'intriguant
                            défi de la Carte Multicolore, un tourbillon mystérieux qui va teinter votre soirée d'une
                            aura magique !</p>
                        <p class="special-text2">Le joueur astucieux qui arbore fièrement la carte Multicolore détient
                            un pouvoir exceptionnel
                            : faire boire tous les participants, à l'exception de lui-même.</p>
                        <p class="special-text2">Le suspense atteint son apogée, et voilà le dénouement ! La Carte
                            Multicolore réserve une
                            surprise rare et précieuse : tous les participants, à l'exception de celui qui a posé la
                            carte, sont conviés à savourer un breuvage en buvant un nombre mystérieux de gorgées,
                            pouvant varier entre 1 et 15 gorgées (avec une chance exceptionnellement rare de 0.1% pour
                            les 15 gorgées).</p>
                        <p class="special-text2">Les
                            rires éclatent, la convivialité s'installe, la Carte Multicolore insuffle une dose
                            de plaisir instantané et de camaraderie à votre soirée. À vos verres, prêts, buvez !</p>
                    </div>
                </div>

                <!----------------------------------CARTE VIOLETTE---------------------------------->


                <div class="wrapViolette animate pop">
                    <div class="overlayViolette">
                        <div class="overlay-contentViolette animateViolette slide-left delay-2">
                            <h1 class="animateViolette slide-left pop delay-4">Violette</h1>
                            <p class="animateViolette slide-left pop delay-5 special-text" style="color: white; margin-bottom: 2.5rem;">
                                Rare: <em>Grand duel de Gorgées</em></p>
                        </div>
                        <div class="image-contentViolette animate slide delay-5"></div>
                        <div class="dotsViolette animateViolette">
                            <div class="dotViolette animate slide-upViolette delay-6"></div>
                            <div class="dotViolette animate slide-upViolette delay-7"></div>
                            <div class="dotViolette animate slide-upViolette delay-8"></div>
                        </div>
                    </div>
                    <div class="textViolette">
                        <p class="special-text2"><img class="insetViolette" src="img/violette.png" alt="" />Bienvenue
                            dans le captivant défi
                            de la Carte Violette, un éclat mystérieux qui va teinter votre soirée d'une aura envoûtante
                            !</p>
                        <p class="special-text2">Le joueur rusé qui arbore fièrement la carte Violette détient un
                            pouvoir extraordinaire :
                            choisir deux joueurs (peut même se choisir lui-même) pour s'affronter dans un mini-jeu 1v1
                            palpitant.</p>
                        <p class="special-text2">Le suspense atteint son apogée, et voilà le dénouement ! La Carte
                            Violette propose trois
                            mini-jeux différents : Pierre-Feuille-Ciseaux, Morpion, ou encore le mystérieux "Pour
                            Combien". Dans ce dernier, le joueur A commence avec 10 possibilités numériques. Il choisit
                            un chiffre parmi ces 10, et le joueur B doit deviner le chiffre choisi.</p>
                        <p class="special-text2">Si le joueur B ne trouve pas le bon chiffre, le nombre de possibilités
                            diminue
                            progressivement jusqu'à atteindre 2. Le joueur A choisit alors entre ces 2 chiffres, et la
                            bataille continue. Le jeu persiste avec les 2 possibilités restantes jusqu'à ce que l'un des
                            joueurs trouve le chiffre de l'autre.</p>
                        <p class="special-text2">Le suspense monte, et c'est là que ça devient intéressant ! À chaque
                            tour, le nombre de
                            gorgées augmente, faisant planer une atmosphère de défi et de prise de risque. Le perdant
                            peut rapidement s'engloutir dans un océan de gorgées alcoolisées, ajoutant une touche
                            d'ivresse ludique à la fête.</p>
                        <p class="special-text2">Alors que les rires fusent et la compétition s'installe. La
                            Carte Violette insuffle une dose de suspense et de camaraderie à votre soirée. À vos jeux,
                            prêts, combattez !</p>
                    </div>
                </div>

                <div class="alert alert-info" style="text-align: center;">
                    <p><strong>Écoutez bien !</strong> Vous avez le pouvoir de déployer deux cartes communes à chaque
                        tour ou une carte rare par tour ! Le plaisir avant tout !!!</p>
                </div>


                <div class="mb-4 text-center">
                    <p class="alert alert-danger">L'abus d'alcool est dangereux pour la santé, à consommer avec
                        modération. <br> Vous pouvez jouer sans boire!!</p>
                </div>
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
                    <p>Email : <a class="link" href="mailto:drinktoastgame@gmail.com">drinktoastgame@gmail.com</a>
                    </p>
                </div>
            </div>

            <div class="border-top pt-3 mt-3">
                <p class="mb-0">&copy; 2023 DrinkToast</p>
                <p style="font-size: small;">Version 1.0.0</p>
            </div>
        </div>
    </footer>


    <!-- Bootstrap JS et jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Votre script JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/cartes.js"></script>
</body>

</html>