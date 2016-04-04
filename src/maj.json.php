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

                    // On vérifie si je joueur peut encore roquer :
                    $roques_trait = maj_roques($roques_trait, $trait, $nature_pce, $coup); // Fonction dans utilitaires_coups.php

                    // On effectue le coup $coup :
                    $bdd_jeu = maj_coup($bdd_jeu, $coup); // fonction dans utilitaires_coups.php

                    // On calcule les cases que le joueur $trait peut voir après le coup :
                    $cases_vis = vue_all($bdd_jeu, $coup[2], $coup[3]); // fonction dans gestion_vues.php

                    // On regarde les cases que menacent le joueur adverse après le coup :
                    $menaces_aps = menace_all($bdd_jeu, $trait_aut); //fonction dans gestion_menaces.php

                    // On regroupe les deux tableaux de menaces
                    $menace_glb = union_menaces($menaces_avt, $menaces_aps); //fonction dans utilitaires_menaces.php

                    // On vérifie si l'adversaire peut voir le coup :
                    $voit = voir_coup($coup, $menace_glb); //fonction dans utilitaires_vues.php
                    $il_joue = $voit[0];
                    $voir_nat = $voit[1];

                    // On vérifie si l'adversaire est en échec : 
                    $echec_autre = roi_en_echec($bdd_jeu, $trait_aut); // fonction dans utilitaires.php

                    // On calcule les coups possibles que peut effectuer l'adversaire :
                    $coup_pos = coup_all($bdd_jeu, $trait_aut);

                    // On recoupe à $coup_pos les coups qui mettent le roi en échec :
                    $coups_pos = enlever_echec_roi($bdd_jeu, $trait_autre, $coups_pos); // fonction dans utilitaires_coups.php

                    // On vérifie que la partie n'est pas fini :
                    if (count($coups_pos) == 0) {
                        // On vérifie si l'adversaire est en échec et mat ou pat :
                        if ($echec_autre == true) {$fin = 'mat';} 
                        else {$fin = 'pat';}
                    } else {
                        // Sinon il s'agit d'un abandon :
                        $coups_pos[] = 'abandon';
                    }

                    // On met à jour les historiques de chacun des joueurs :
                    $histo_trait = ['je_joue' => $coup, 'vues' => $cases_vis];
                    $histo_autre = ['il_joue' => $il_joue, 'coups' => $coups_pos];

                    // Si la partie est fini ou qu'il y a un échehc on prévient les joueurs :
                    if (isset($fin)) {   
                        $histo_trait[$fin] = 1;
                        $histo_aut[$fin] = 1;
                    } elseif ($echec_autre) {
                        $histo_trait['echec'] = 1;
                        $histo_aut['echec'] = 1;
                    }

                    // Si l'autre joueur peut voir la pièce on met à jour son historique :
                    if ($voir_nat === true) {$histo_autre['nature'] = $nature_pce;}

                    // On ajoute les variables à l'historique récupéré dans la bdd :
                    $bdd_histo_trait[] = $histo_trait;
                    $bdd_histo_aut[] = $histo_aut;

                    // On vérifie si on doit changer de tour
                    if ($trait_autre == 1) { // C'est de nouveau au joueur 1 de jouer : on change de tour
                        $tour++;
                    }

                }
                /**
                Mise à jour de la BDD :
                **/

                // Si la partie est finie, on met l'etat de la partie à l'état final :
                if (isset($fin)) {$fin_sql = ', etat_partie = "'.$fin.'"';} 
                else {$fin_sql = '';}

                $sql = 'UPDATE parties SET tour = ' . $tour .
                    ', trait = ' . $trait_aut .
                    ', roques_j'.$trait. " = '" . json_encode($roques_trait) . "'" .
                    ", etat_du_jeu = '" . json_encode($bdd_jeu) . "'" .
                    ', histo_j'.$trait." = '" . json_encode($bdd_histo_trait) . "'" .
                    ', histo_j'.$trait_aut." = '" . json_encode($bdd_histo_aut) . "'" .
                    $fin_sql .
                    '  WHERE id=' . $partie;
                //echo "<br /><br />".$sql."<br />";
                $nb_modifs = $bdd->exec($sql);
                //echo $nb_modifs . ' entrees ont modifiees !';

                // On renvoie la dernière ligne de $bdd_histo_trait :
                echo json_encode($histo_trait);

            }else{
                // Si c'est bien à ce joeur de jouer on lui renvoie son histo pour ce tour et ce trait :
                $bdd_histo_trait = json_decode($bdd_tps['histo_j'.$trait], true);
                $info = $bdd_histo_trait[($bdd_tour-1)*2];
                echo json_encode($info);
            }

        }else{
            echo '{"erreur":"Le tour ou le trait ne correspondent pas à ceux de la bdd !"}';
        }
    }else{
        echo '{"erreur":"Le joueur ne correspond pas à celui de la bdd !"}';
    }

// Fermeture de la connexion :
$bdd = null;	
}

?>    