<?php
/**
	Divers utilitaires utiles pour le controle et la mise a jour des parties :
**/

/*
BDD :
*/

function repBdd($bdd, $req) {
	// Recupere la reponse d'une bdd $bdd a la requete $sql
	$reponse = $bdd->query($req);
	while ($donnees = $reponse->fetch()) {
		$reponse->closeCursor();
		return $donnees;
	}
	$reponse->closeCursor();
	return false;	
}

/*
Convertiseur :
*/

function Vec2Str($vec) {
	// Fonction qui convertit un vecteur en string
	if ($vec != false) {return '['.$dest[0].','.$dest[1].'],';}
	else {return '';}
}

/*
Info sur les pions
*/

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

function est_au_joueur($jeu, $i, $j, $trait) {
	// Détermine si le pion à la posisiton ($i,$j) sur le plateau de la partie $jeu est bien au joueur $trait
	$info = info_case($jeu, $i, $j, $trait);
	if ($info[0] & $info[1] == 'allie') {return true;}
	else {return false;}
}

function prendrePce($jeu, $i, $j, $trait) {
	// Cette fonction teste si le joueur $trait peut prendre ou non la pièce en $i, $j
	$info = info_case($jeu, $i, $j, $trait, $menaces);
	// Si la case existe et qu(elle est vide ou à l'ennemi alors il peut la prendre)
	if ($info[0] & ($info[1] == 'ennemi' || $info[1] == 'vide')) {return [$i, $j];}
	else {return false;}
}

?>