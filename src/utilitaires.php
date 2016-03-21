<?php
/**
	Divers utilitaires utiles pour le controle et la mise a jour des parties :
**/

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

function Vec2Str($vec) {
	// Fonction qui convertit un vecteur en string
	if ($vec != false) {return '['.$dest[0].','.$dest[1].'],';}
	else {return '';}
}

?>