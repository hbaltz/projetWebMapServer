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
	$cases .= Vec2Str(prendrePce($jeu, $i-1, $j+$p, $trait), $trait); // Fonctions dans utilitaires.php
	$cases .= Vec2Str(prendrePce($jeu, $i+1, $j+$p, $trait), $trait);	
	
	return $cases;
}

?>