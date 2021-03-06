<?php
/**
Fichier comprenant la gestion de la recherche de l'ensemble des cases visibles après qu'un joueur est joué
En entrée la fonction prend $jeu les informations de la partie, $i et $j la position du pion
En sortie de la fonction principale vue_all : un tableau des cases vues grâce à ce nouveau coup, chaque case étant codée sous la forme : [i,j,"piece"]
**/

/*
Fonction principale :
*/

function vue_all($jeu, $i, $j){
	// Initialisation :
	$cases_vis = '';
	$trait = $jeu[$i][$j][1];

	// On récupère la nature de la pièce à la position $i, $j
	$nature = $jeu[$i][$j][0];

	// On récupère les cases visibles en se basant sur la nature de la pièce :
	if ($nature == 'p') {$cases_vis .= vue_pion($jeu, $trait, $i, $j);}
	else if ($nature == 'C') {$cases_vis .= vue_cavalier($jeu, $trait, $i, $j);}
	else if ($nature == 'T') {$cases_vis .= vue_tour($jeu, $trait, $i, $j);}
	else if ($nature == 'F') {$cases_vis .= vue_fou($jeu, $trait, $i, $j);}
	else if ($nature == 'D') {$cases_vis .= vue_dame($jeu, $trait, $i, $j);}
	else if ($nature == 'R') {$cases_vis .= vue_roi($jeu, $trait, $i, $j);}
	
	$cases_vis = '['.substr($cases_vis,0,-1).']';
	$cases_vis = json_decode($cases_vis);

	return $cases_vis;
}

/*
Sous-fonctions :
*/

function vue_pion($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On gére le déplacement du pion 
	$p = ($trait == 1 ? 1 : -1); 
	$cases .= vues($jeu, $i, $j+$p, $trait); //fonction défini dans utilitaire_vues.php
	// On gére la vision liée au déplacement +2 cases devant
	if (($trait-1)*5+2 == $j) {
		// Si la case devant est vide, donc si $case n'est pas vide :
		if ($cases != '') {
			$cases .= vues($jeu, $i, $j+2*$p, $trait);
		}
	}
	// On gére la vision des cases que la pièce peut manger
	$cases .= vues($jeu, $i-1, $j+$p, $trait);
	$cases .= vues($jeu, $i+1, $j+$p, $trait);
	
	return $cases;
}

function vue_cavalier($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = ''; 

	// Le cavalier possède plusieurs mouvements possibles :
	// - il effectue un mouvement vers la gauche ou la droite de deux cases puis effectue un mouvement vers le haut ou la bas d'une case
	// - il effectue un mouvement vers le haut ou la bas de deux cases puis effectue un mouvement vers la gauche ou la droite d'une case

	// On utilise donc les varialbe suivantes :
	// $lin : -1 vers la gauche ou +1 vers la droite
	// $hor : -1 vers le bas ou +1 vers le haut
	
	// $coef_lin pour le coefficient des déplacments linéaires : 1 ou 2
	// $coef_hor pour le coefficient des déplacments horizontaux : 1 ou 2

	for ($coef_hor = 1; $coef_hor <= 2; $coef_hor++) {
		$coef_lin = 3 - $coef_hor; // $coef_lin = 1 quand $coef_hor = 2 et inversement
		for ($hor = -1; $hor <= 1; $hor += 2) {
			for ($lin = -1; $lin <= 1; $lin += 2) {
				$cases .= vues($jeu, $i+$lin*$coef_lin, $j+$hor*$coef_hor, $trait);
			}
		}
	}

	return $cases;
}

function vue_tour($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On sert de la fonction tester_vues_vecteur défini dans utilitaire_vues.php :
	$cases .= tester_vues_vecteur($jeu, $i, $j, 1, 0, $trait); 
	$cases .= tester_vues_vecteur($jeu, $i, $j, -1, 0, $trait); 
	$cases .= tester_vues_vecteur($jeu, $i, $j, 0, 1, $trait); 
	$cases .= tester_vues_vecteur($jeu, $i, $j, 0, -1, $trait);

	return $cases; 
}

function vue_fou($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On sert de la fonction tester_vues_vecteur défini dans utilitaire_vues.php :
	$cases .= tester_vues_vecteur($jeu, $i, $j, 1, 1, $trait); 
	$cases .= tester_vues_vecteur($jeu, $i, $j, 1, -1, $trait); 
	$cases .= tester_vues_vecteur($jeu, $i, $j, -1, 1, $trait); 
	$cases .= tester_vues_vecteur($jeu, $i, $j, -1, -1, $trait); 

	return $cases;
}

function vue_dame($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// La dame possède les vues de la tour et du fou :
	$cases .= vue_tour($jeu, $trait, $i, $j); 
	$cases .= vue_fou($jeu, $trait, $i, $j); 

	return $cases;
}

function vue_roi($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// Le roi peux se déplacer d'une case dans l'ensemble des directions :
	for ($di = -1; $di <= 1; $di++) {
		for ($dj = -1; $dj <= 1; $dj++) {
			$cases .= vues($jeu, $i+$di, $j+$dj, $trait);
		}
	}

	return $cases;
}

?>