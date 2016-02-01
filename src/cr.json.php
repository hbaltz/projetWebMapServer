<?php 
/**
    Fichier qui vérifie que l’identifiant de login (tel qu’il figure dans la session) est valide et correspond à l’un des joueurs inscrits sur la partie (paramètres "partie"). 
    Il renvoie ensuite soit le message d’erreur, soit le contenu du CR c'est-à-dire les informations (tour, trait, cote, histo) concernnt la partie
**/

// Si une session existe on récupere les informations de session:
if (file_exists('session.php')) {include('session.php');}

if (!isset($_SESSION['user'])) {
    // Pas d'utilisateur connecté, on passe en mode debug
    // Le testeur rentre le cote a la main

    if (isset($_GET["cote"])) {

        $cote = $_GET["cote"];
        $session_nom_joueur = 'joueur_'.$cote;

    } else {die('{"erreur":"Parametres incorrectes"}');}
    /*** Fin ***/  

} else {
    $session_nom_joueur = $_SESSION['user'];
}

try {$bdd = new PDO('mysql:host=localhost;dbname=echec', 'root', '');}
catch (Exception $e) {die('{"erreur":"Erreur BDD : ' . $e->getMessage().'"}');}

// On inclut les utilitaires :
include('utilitaires.php');

// On recupere le parametre : partie en GET
// Seul partie doit au moins etre renseigne
if (isset($_GET["partie"])) {
    // Les parametres minimaux sont definis on peut continuer :

    $partie = $_GET["partie"];

    if (isset($session_nom_joueur)) {
        // Recuperation des autres info (tour, trait, histo) dans la BDD :
        $req = 'SELECT * FROM parties WHERE id='.$partie;
        $bdd_tps = repBdd($bdd, $req);

        // Definition de cote :
        if ($bdd_tps['j1'] == $session_nom_joueur) {$cote = 1;}
        elseif ($bdd_tps['j2'] == $session_nom_joueur) {$cote = 2;}

        // Si le jour n'apparteint pas a la partie, on renvoie une erreur :
        else {echo '{"erreur":"Vous n\'avez pas acces a cette partie"}'; $bdd = null; die();}

    } else {echo '{"erreur":"Erreur cote serveur."}'; $bdd = null; die('');} // Probleme de connexion
    
    // On stocke les differents parametres recapitulant la partie :
    $recap = [
        'tour' => $bdd_tps['tour'],
        'trait' => $bdd_tps['trait'],
        'cote' => $cote,
        'histo' => $bdd_tps['histo_j'.$cote]
    ];

    echo json_encode($recap);

    // Fermeture de la connexion :
    $bdd = null;
}

?> 