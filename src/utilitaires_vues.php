<?php
/**
Utilitaires pour la recherche de vues :
**/

function vues($jeu, $i, $j, $trait) {
	// Fonction utile pour retrouver ce que voit un joueur $trait dans la partie $jeu 
	$info = info_case($jeu, $i, $j, $trait);
	// Si la case existe alors on regarde si elle est alliée ou ennemi, et on renvoie
	// alors les coord et la pièce si elle est ennemi
	// les coordonnées si la case existe
	// false sinon
	if ($info[0]) {
	 		if ($info[1] == 'ennemi') { 
			return '['.$i.','.$j.',"'.$jeu[$i][$j][0].'"],'; 
		} else {
			return '['.$i.','.$j.'],';
		} 
	} else {return false;} 
}

function tester_vues_vecteur($jeu, $i, $j, $di, $dj, $trait) {
	// Fonction permettant de gérer les déplacement des pièces pouvant parcourir des lignes entières (fou, tour ,dame)
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

function voir_coup($coup, $menace){
	// Cette fonction teste si le joueur peut voir le coup joué par l'adeversaire en fonction du tableau de menaces
	// et remplit le vecteur il_joue qui comprend le détail du coup
	$il_joue = [0, 0, 0, 0];

	$voit_nature = false;
	if ($menace[$coup[0]][$coup[1]] == true) {
		$il_joue[0] = $coup[0];
		$il_joue[1] = $coup[1];
		$voit = true;
	}
	if ($menace[$coup[2]][$coup[3]] == true) {
		$il_joue[2] = $coup[2];
		$il_joue[3] = $coup[3];
		$voit = true;
	}

	return [$il_joue, $voit];

}

?>