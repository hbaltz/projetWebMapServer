<?php
/**
Fichier comprennant la gestion de la recherche de l'ensemble des cases visibles après qu'un joueur est joué
En entrée la fonction prend $jeu les informations de vue, $i et $j la position du pion
En sortie de la fonction principale vue_all : un tableau des cases vis grâce à ce nouveau coup, chaque case étant codée sous la forme : [i,j,"piece"]
**/

/*
Fonctions principale :
*/

function vue_all($jeu, $i, $j){
	$cases_vis = '';
	$trait = $jeu[$i][$j][1];

	// On récupère les cases visibles en se basant sur la nature de la pièce :
	if ($jeu[$i][$j][0] == 'p') {$cases_vis .= vue_pion($jeu, $trait, $i, $j);}
	else if ($jeu[$i][$j][0] == 'C') {$cases_vis .= vue_cavalier($jeu, $trait, $i, $j);}
	else if ($jeu[$i][$j][0] == 'T') {$cases_vis .= vue_tour($jeu, $trait, $i, $j);}
	else if ($jeu[$i][$j][0] == 'F') {$cases_vis .= vue_fou($jeu, $trait, $i, $j);}
	else if ($jeu[$i][$j][0] == 'R') {$cases_vis .= vue_roi($jeu, $trait, $i, $j);}
	else if ($jeu[$i][$j][0] == 'D') {$cases_vis .= vue_dame($jeu, $trait, $i, $j);}
	
	$cases_vis = '['.substr($cases_vis,0,-1).']';
	$cases_vis = json_decode($cases_vis);

	return $cases_vis;
}

/*
Sous-fonctions :
*/

function vue_pion($jeu, $trait, $i, $j){
	$cases = ''; 
	// On fait avancer le pion 
	$p = ($trait == 1 ? 1 : -1); 
	$case .= vues($jeu, $i, $j+$p, $trait);
	// On gére la vision lièe au déplacement +2 cases devant
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

}


?>