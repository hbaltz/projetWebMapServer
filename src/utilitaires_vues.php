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

function info_case($jeu, $i, $j, $trait, $menaces = false) {
	// Fonction qui retroune les informations suivantes sur une case
	// existence : la case existe-t-elle ?,
	// contenu : est-ce une pièce alliée, ennemie ou vide ?
	// menacee : est-elle menacee ?
	$menacee = 0;
	if (0 < $i & $i < 9 & 0 < $j & $j < 9) {
		$existe = true;
		// Si la case existe alors on regarde si elle est alliée ou ennemi, et on renvoie
		// alors allie si elle est ennemi
		// les ennemi si elle n'est pa ennemi et qu'elle existe
		// vide sinon
		if (($case = $jeu[$i][$j]) != '') {
			if ($case[1] == $trait) {$contenu = 'allie';}
			else {$contenu = 'ennemi';}
		} else {$contenu = 'vide';}
		if ($menaces != false) {
			$menacee = ($menaces[$i][$j] == 1 ? 1 : 0);
		}
	} else {
		$existe = false;
		$contenu = '';
	}
	return [$existe, $contenu, $menacee];
}


?>