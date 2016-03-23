<?php
/**
Utilitaires pour la recherche des menaces :
**/

function tester_menaces_vecteur($jeu, $i, $j, $di, $dj, $trait) {
	// Fonction permettant de gérer les menaces des pièces pouvant parcourir des lignes entières (fou, tour ,dame)
	// Les variables $di et $dj permettent de choisir le sens de déplacement ainsi :
	// $di = 1 et $dj = 0 correspond à un déplacement vertical vers la droite
	// $di = 0 et $dj = 1 correspond à un déplacement horizontale vers le haut
	// ...etc
	$cases = '';
	// Tant qu'il n'y a pas de pièce et que ce n'est pas le bord on continue
	$d = 1;
	$stop = false;
	while (!$stop) {
		// On récupère les informations sur la case
		$info = info_case($jeu, $i + $di*$d, $j + $dj*$d, $trait); //Fonction dans utilitaires.php
		if ($info[0] & ($info[1] == 'ennemi' || $info[1] == 'vide')) {
			$cases .= Vec2Str([$i+$di*$d, $j+$dj*$d]); // Fonction définit dans utilitaires
		}
		if ($info[0] == false || $info[1] != 'vide') {$stop = true;} // Stop si la case bloque
		$d++;
	}
	return $cases;
}

function union_menaces($m1, $m2) {
	// Cette fonction regroupe deux tableaux de menaces (true = menace)
	for ($i=1; $i < 9; $i++) { 
		for ($j=1; $j < 9; $j++) { 
			if ($m2[$i][$j] == true) {$m1[$i][$j] = true;}
		}
	}
	return $m1;
}

?>