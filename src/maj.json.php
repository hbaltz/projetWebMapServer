<?php 
/**
 Fichier qui vérifie que l’identifiant de login (tel qu’il figure dans la session) est valide et correspond à l’un des joueurs inscrits sur la partie (paramètres "partie" et "cote"). 
 Le service vérifie que la partie en est au stade indiqué par les paramètres "tour" et "trait". 
 Si le paramètre optionnel "coup" est fourni et est correct alors l’arbitrage est lancé pour jouer le coup et rédiger une nouvelle situation.
 Si l’une des conditions précédentes n’est pas vérifiée, une erreur sera lancée.
**/

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

//On tente de se connecter a la bdd
try {$bdd = new PDO('mysql:host=localhost;dbname=echec', 'root', '');}
catch (Exception $e) {die('{"erreur":"Erreur BDD : ' . $e->getMessage().'"}');}

// On inclut les utilitaires :
include('utilitaires.php');

// Recuperation des parametres : partie, cote, tour, trait et coup en GET
if (isset($_GET["partie"], $_GET["cote"], $_GET["tour"], $_GET["trait"])) {
	// Les parametres sont defini ont peut continuer :
	$partie = $_GET["partie"];
	$cote = $_GET["cote"];
	$tour = $_GET["tour"];
	$trait = $_GET["trait"];
}

// Fermeture de la connexion :
$bdd = null;	

?>    