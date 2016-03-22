<?php
/**
Fichier comprennant la gestion de la recherche de l'ensemble des coups possiple pour un joueur $trait
En entrée la fonction prend $jeu les informations de la partie
En sortie de la fonction principale coup_all : un tableau des coups possibles sous la forme [i0,j0,i1,j1,"option"]
**/

/*
Fonction principale :
*/

function coup_all($jeu, $trait){
	// Initialisation :
	$cases_coup = '';

	// On parcours l'ensemble du plateau pour déterminer l'ensemble des coups possibles par un joueur :
	for ($i = 1; $i <= 8; $i++) {
		for ($j = 1; $j <= 8; $j++) {
			if(est_au_joueur($jeu, $i, $j, $trait){ //Fonction dans utilitaires.php
				// On récupère la nature de la pièce à la position $i, $j si la case est au joueur $trait
				$nature = $jeu[$i][$j][0];
				// En fonction de sa nature on détermine ce qu'elles sont les coups disponibles :
				if ($nature == 'p') {$cases_coup .= coup_pion($jeu, $trait, $i, $j);}
				else if ($nature == 'C') {$cases_coup .= coup_cavalier($jeu, $trait, $i, $j);}
				else if ($nature == 'T') {$cases_coup .= coup_tour($jeu, $trait, $i, $j);}
				else if ($nature == 'F') {$cases_coup .= coup_fou($jeu, $trait, $i, $j);}
				else if ($nature == 'D') {$cases_coup .= coup_dame($jeu, $trait, $i, $j);}
				else if ($nature == 'R') {$cases_coup .= coup_roi($jeu, $trait, $i, $j);}
			}
		}
	}
	
	$cases_coup = '['.substr($cases_coup,0,-1).']';
	$cases_coup = json_decode($cases_coup);

	return $cases_coup;
}

/*
Sous-fonctions :
*/

function coup_pion($jeu, $trait, $i, $j){
	$cases = ''; 

	// On gére le déplacecoupt du pion 
	$p = ($trait == 1 ? 1 : -1); 

	// Si pas de cases devant le pion peut avancer d'une case :
	$cases .= Vec2StrCoup($i, $j, prendrePceVide($jeu, $i, $j+$p, $trait), $trait); // Fonctions dans utilitaires.php	

	// On gére les disponibilités liées au déplacement +2 cases devant
	if (($trait-1)*5+2 == $j) {
		// Si la case devant est vide, donc si $case n'est pas vide :
		if ($cases != '') {
			$cases .= Vec2StrCoup($i, $j, prendrePceVide($jeu, $i, $j+2*$p, $trait), $trait);
		}
	}
	// On gére la possibilité de manger les cases en diagonale :
	$cases .= Vec2StrCoup($i, $j, prendrePceEn($jeu, $i-1, $j+$p, $trait), $trait);
	$cases .= Vec2StrCoup($i, $j, prendrePceEn($jeu, $i+1, $j+$p, $trait), $trait);
	
	return $cases;
}

function coup_cavalier($jeu, $trait, $i, $j){
	$cases = '';
	// Le cavalier possède plusieurs mouvecoupts possibles :
	// - il effectue un mouvecoupt vers la gauche ou la droite de deux cases puis effectue un mouvecoupt vers le haut ou la bas d'une case
	// - il effectue un mouvecoupt vers le haut ou la bas de deux cases puis effectue un mouvecoupt vers la gauche ou la droite d'une case

	// On utilise donc les varialbe suivantes :
	// $lin : -1 vers la gauche ou +1 vers la droite
	// $hor : -1 vers le bas ou +1 vers le haut
	
	// $coef_lin pour le coefficient des déplaccoupts linéaires : 1 ou 2
	// $coef_hor pour le coefficient des déplaccoupts horizontaux : 1 ou 2 

	for ($coef_hor = 1; $coef_hor <= 2; $coef_hor++) {
		$coef_lin = 3 - $coef_hor; // $coef_lin = 1 quand $coef_hor = 2 et inversecoupt
		for ($hor = -1; $hor <= 1; $hor += 2) {
			for ($lin = -1; $lin <= 1; $lin += 2) {
				$cases .= Vec2StrCoup($i, $j, prendrePce($jeu, $i+$coef_lin*$lin, $j+$coef_hor*$hor, $trait), $trait);
			}
		}
	}	
	
	return $cases;
}

function coup_tour($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On sert de la fonction tester_coups_vecteur défini dans utilitaire_coups.php :
	$cases .= tester_coups_vecteur($jeu, $i, $j, 1, 0, $trait); 
	$cases .= tester_coups_vecteur($jeu, $i, $j, -1, 0, $trait); 
	$cases .= tester_coups_vecteur($jeu, $i, $j, 0, 1, $trait); 
	$cases .= tester_coups_vecteur($jeu, $i, $j, 0, -1, $trait);

	return $cases; 
}

function coup_fou($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On sert de la fonction tester_coups_vecteur défini dans utilitaire_coups.php :
	$cases .= tester_coups_vecteur($jeu, $i, $j, 1, 1, $trait); 
	$cases .= tester_coups_vecteur($jeu, $i, $j, 1, -1, $trait); 
	$cases .= tester_coups_vecteur($jeu, $i, $j, -1, 1, $trait); 
	$cases .= tester_coups_vecteur($jeu, $i, $j, -1, -1, $trait); 

	return $cases;
}

function coup_dame($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// La dame possède les coups de la tour et du fou :
	$cases .= coup_tour($jeu, $trait, $i, $j); 
	$cases .= coup_fou($jeu, $trait, $i, $j); 

	return $cases;
}

function coup_roi($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// Le roi peux se déplacer d'une case dans l'ensemble des directions 
	// et prendre une case dans l'ensemble des direction :
	for ($di = -1; $di <= 1; $di++) {
		for ($dj = -1; $dj <= 1; $dj++) {
			$cases .= Vec2StrCoup($i, $j, prendrePce($jeu, $i+$di, $j+$dj, $trait), $trait);
		}
	}

	return $cases;
}

?>