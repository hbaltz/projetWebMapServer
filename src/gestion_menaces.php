<?php
/**
Fichier comprennant la gestion de la recherche de l'ensemble des cases que menacent un joueur $trait
En entrée la fonction prend $jeu les informations de la partie
En sortie de la fonction principale menace_all : un tableau des cases menacées par $trait
**/

/*
Fonction principale :
*/

function menace_all($jeu, $trait){
	// Initialisation :
	$cases_men = '';

	// On parcours l'ensemble du plateau pour déterminer l'ensemble des cases menacées par un joueur :
	for ($i = 1; $i <= 8; $i++) {
		for ($j = 1; $j <= 8; $j++) {
			if(est_au_joueur($jeu, $i, $j, $trait){ //Fonction dans utilitaires.php
				// On récupère la nature de la pièce à la position $i, $j si la case est au joueur $trait
				$nature = $jeu[$i][$j][0];
				// On stocke quelle pièce est la menace en premier
				$cases_men .= '['.$i.','.$j.'],'; 
				// Puis en fonction de sa nature on détermine ce qu'elle menace :
				if ($nature == 'p') {$cases_men .= menace_pion($jeu, $trait, $i, $j);}
				else if ($nature == 'C') {$cases_men .= menace_cavalier($jeu, $trait, $i, $j);}
				else if ($nature == 'T') {$cases_men .= menace_tour($jeu, $trait, $i, $j);}
				else if ($nature == 'F') {$cases_men .= menace_fou($jeu, $trait, $i, $j);}
				else if ($nature == 'D') {$cases_men .= menace_dame($jeu, $trait, $i, $j);}
				else if ($nature == 'R') {$cases_men .= menace_roi($jeu, $trait, $i, $j);}
			}
		}
	}
	
	$cases_men = '['.substr($cases_men,0,-1).']';
	$cases_men = json_decode($cases_men);

	// A l'aide de se vecteur on associe à chacune des cases du plateau :
	// - true si elle est menacee
	// - false si ne l'est pas
	$jeu_menaces = array_fill(1, 8, array_fill(1, 8, false));
	foreach ($cases_menacees as $menace) {$jeu_menaces[$menace[0]][$menace[1]] = true;}

	return $jeu_menaces;
}

/*
Sous-fonctions :
*/

function menace_pion($jeu, $trait, $i, $j){
	$cases = ''; 

	// On gére le déplacement du pion 
	$p = ($trait == 1 ? 1 : -1); 

	// Un pion ne peut prendre que les pièces placer en diagonale :
	$cases .= Vec2Str(prendrePce($jeu, $i-1, $j+$p, $trait)); // Fonctions dans utilitaires.php
	$cases .= Vec2Str(prendrePce($jeu, $i+1, $j+$p, $trait));	
	
	return $cases;
}

function menace_cavalier($jeu, $trait, $i, $j){
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
				$cases .= Vec2Str(prendrePce($jeu, $i+$coef_lin*$lin, $j+$coef_hor*$hor, $trait));
			}
		}
	}	
	
	return $cases;
}

function menace_tour($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On sert de la fonction tester_menaces_vecteur défini dans utilitaire_menaces.php :
	$cases .= tester_menaces_vecteur($jeu, $i, $j, 1, 0, $trait); 
	$cases .= tester_menaces_vecteur($jeu, $i, $j, -1, 0, $trait); 
	$cases .= tester_menaces_vecteur($jeu, $i, $j, 0, 1, $trait); 
	$cases .= tester_menaces_vecteur($jeu, $i, $j, 0, -1, $trait);

	return $cases; 
}

function menace_fou($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// On sert de la fonction tester_menaces_vecteur défini dans utilitaire_menaces.php :
	$cases .= tester_menaces_vecteur($jeu, $i, $j, 1, 1, $trait); 
	$cases .= tester_menaces_vecteur($jeu, $i, $j, 1, -1, $trait); 
	$cases .= tester_menaces_vecteur($jeu, $i, $j, -1, 1, $trait); 
	$cases .= tester_menaces_vecteur($jeu, $i, $j, -1, -1, $trait); 

	return $cases;
}

function menace_dame($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// La dame possède les menaces de la tour et du fou :
	$cases .= menace_tour($jeu, $trait, $i, $j); 
	$cases .= menace_fou($jeu, $trait, $i, $j); 

	return $cases;
}

function menace_roi($jeu, $trait, $i, $j){
	// Initialisation :
	$cases = '';

	// Le roi peux se déplacer d'une case dans l'ensemble des directions :
	for ($di = -1; $di <= 1; $di++) {
		for ($dj = -1; $dj <= 1; $dj++) {
			$cases .= Vec2Str(prendrePce($jeu, $i+$di, $j+$dj, $trait));
		}
	}

	return $cases;
}

?>