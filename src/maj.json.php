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

// On inclut ce qui est utile à la gestion des vues :
include('gestion_vues.php');
include('utilitaires_vues.php');

// On inclut ce qui est utile à la gestion des menaces :
include('gestion_menaces.php');
include('utilitaires_menaces.php');

// On inclut ce qui est utile à la gestion des coups :
include('gestion_coups.php');
include('utilitaires_coups.php');

// Recuperation des parametres : partie, cote, tour, trait et coup en GET
if (isset($_GET["partie"], $_GET["cote"], $_GET["tour"], $_GET["trait"])) {
	// Les parametres sont defini ont peut continuer :
	$partie = $_GET["partie"];
	$cote = $_GET["cote"];
	$tour = $_GET["tour"];
	$trait = $_GET["trait"];

    // Recuperation des informations stockées dans la BDD :
    $req = 'SELECT * FROM parties WHERE id='.$partie;
    $bdd_tps = repBdd($bdd, $req);
    $bdd_nom_joueur = $bdd_tps['j'.$cote];

    // On teste que cote correspond à bien à bdd_nom_joueur :
    if($cote == $bdd_nom_joueur){

        // On vérifie que la partie en est au stade indiqué par les paramètres "tour" et "trait" :
        $bdd_trait = $bdd_tps['trait'];
        $bdd_tour = $bdd_tps['tour'];

        if($bdd_trait == $trait && $bdd_tour == $tour){
            //Si le paramètre optionnel "coup" est fourni et on récupère les inforamtions de la partie :
            if (isset($_GET["coup"])){
                $coup = $_GET["coup"];
                // Recuperation de l'etat de partie :
                $bdd_jeu = json_decode($bdd_tps['etat_du_jeu'], true); // Fonction dans utilitaires.php
                // Recuperation de l'historiques des coups du joueur étant en tain de jouer :
                $bdd_histo_trait = json_decode($bdd_tps['histo_j'.$trait], true); 
                // Coup choisi :
                $coup = $bdd_histo_trait[$bdd_tour*2-2]['coups'][$coup];
                // Recuperation des roques possibles pour le joueur au trait :
                $roques_trait = json_decode($bdd_tps['roques_j'.$trait], true);

                // Si le jouer choisi d'abandonner, c'est la fin de la partie :
                if($coup == 'abandon'){
                    $fin = 'abandon_'.$trait;
                    $bdd_histo_trait[] = ["abandon"=>1];
                    $bdd_histo_aut[] = ["abandon"=>1];

                // Sinon l’arbitrage est lancé pour jouer le coup et rédiger une nouvelle situation :
                }else{
                    // On recupere les informations de l'autre joueur :
                    if($trait==1){
                        $trait_aut = 2;
                    }else{
                       $trait_aut = 1;
                    }

                    // Recuperation de l'historiques des coups de l'adversaire :
                    $bdd_histo_aut = json_decode($bdd_tps['histo_j'.$trait_aut], true);
                    // Recuperation des roques possibles pour l'adversaire :
                    $roques_aut = json_decode($bdd_tps['roques_j'.$trait_aut], true);

                    // On récupére la nature de la pièce que le joueur souhaite bouger :
                    $nature_pce = $bdd_jeu[$coup[0]][$coup[1]][0];

                    /**
                    Arbitrage :
                    **/

                    // On regarde les cases que menacent le joueur adverse avant le coup :
                    $menaces_avt = menace_all($bdd_jeu, $trait_aut); //fonction dans gestion_menaces.php

                    // On effectue le coup $coup :
                    $bdd_jeu = maj_coup($bdd_jeu, $coup); // fonction dans utilitaires_coups.php

                    // On calcule les cases que le joueur $trait peut voir après le coup :
                    $cases_vis = vue_all($bdd_jeu, $coup[2], $coup[3]); // fonction dans gestion_vues.php

                    // On regarde les cases que menacent le joueur adverse après le coup :
                    $menaces_aps = menace_all($bdd_jeu, $trait_aut); //fonction dans gestion_menaces.php

                    // On regroupe les deux tableaux de menaces
                    $menace_glb = union_menaces($menaces_avt, $menaces_aps); //fonction dans utilitaires_menaces.php


                }


            }

        }else{
            echo '{"erreur":"Le tour ou le trait ne correspondent pas à ceux de la bdd !"}';
        }
    }else{
        echo '{"erreur":"Le joueur ne correspond pas à celui de la bdd !"}';
    }
}

// Fermeture de la connexion :
$bdd = null;	

?>    