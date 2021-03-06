<?php
/**
Utilitaires pour la recherche des coups :
**/

function tester_coups_vecteur($jeu, $i, $j, $di, $dj, $trait) {
	// Fonction permettant de gérer les coups des pièces pouvant parcourir des lignes entières (fou, tour ,dame)
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
			$cases .= Vec2StrCoup($i, $j, [$i+$di*$d, $j+$dj*$d], $trait); // Fonction définit dans utilitaires.php
		}
		if ($info[0] == false || $info[1] != 'vide') {$stop = true;} // Stop si la case bloque
		$d++;
	}
	return $cases;
}

function maj_roques($roques_trait, $trait, $nature, $coup) {
	// Fonction qui teste si une fois que le joueur $trait a effectué son coup $coup
	// avec la pièce $nature si le joeur peut encore roquer
	// XXX signifie que le grand roque est encore possible et XX que le petit l'est

	//Initialisation :
	$roques = $roques_trait;

	// Si le roi a été déplace, le joueur ne peut plus roquer
	if ($nature == 'R') {
		$roques = []; 
	} elseif (($trait == 1 & $coup[0] == 1 & $coup[1] == 1) | ($trait == 2 & $coup[0] == 1 & $coup[1] == 8)) {
		// Le grand roque n'est plus possible on teste si le petit l'est encore :
		if (array_search('XX', $roques_trait) === false) {
			// Le petit roque n'est déjà plus disponible, donc aucun roque n'est disponible :
			$roques = [];
		} else {
			$roques = ['XX'];
		}
	} elseif (($trait == 1 & $coup[0] == 8 & $coup[1] == 1) | ($trait == 2 & $coup[0] == 8 & $coup[1] == 8)) {
		// Le petit roque n'est plus possible on teste si le grand l'est encore :
		if (array_search('XXX', $roques_trait) === false) {
			// Le grand roque n'est déjà plus disponible, donc aucun roque n'est disponible :
			$roques = [];
		} else {
			$roques = ['XXX'];
		}
	}
	return $roques;
}

function maj_coup($jeu, $coup) {
	// Cette fonction met à jour le plateau de jeu $jeu
	// Une fois que le coup $coup a été effectué

	// Si le joueur effectue un roque ou un pp 
	// alors $coup[4] corresponde à l'option choisi 
	if (isset($coup[4])) {
		if ($coup[4] == 'XXX') {
			// Le joueur effectue un grand roque :
			$jeu[$coup[2]][$coup[3]] = $jeu[$coup[0]][$coup[1]];
			$jeu[$coup[0]][$coup[1]] = '';
			$jeu[$coup[2]+1][$coup[3]] = $jeu[$coup[2]-2][$coup[3]];
			$jeu[$coup[2]-2][$coup[3]] = '';
			return $jeu;

		} else if ($coup[4] == 'XX') {
			// Le joueur effectue un petit roque :
			$jeu[$coup[2]][$coup[3]] = $jeu[$coup[0]][$coup[1]];
			$jeu[$coup[0]][$coup[1]] = '';
			$jeu[$coup[2]-1][$coup[3]] = $jeu[$coup[2]+1][$coup[3]];
			$jeu[$coup[2]+1][$coup[3]] = '';
			return $jeu;
			
		} else if ($coup[4] == 'pp') {
			// Le joueur effectue un pp :
			$jeu[$coup[2]][$coup[1]] = '';
		}
	}

	// Si aucune option n'est effectué on effectue le coup $coup normalement :
	$jeu[$coup[2]][$coup[3]] = $jeu[$coup[0]][$coup[1]];
	$jeu[$coup[0]][$coup[1]] = '';

	return $jeu;
}

function enlever_echec_roi($jeu, $trait, $coups) {
	// Cette fonction enlever à $coups : les coups possibles par le joueur $trait
	// dans la partie $jeu, les coups qui mettent le roi de $trait en échec
	$coups_valides = [];
	foreach ($coups as $coup) {
		if (est_un_roque($coup)) {
			// On vérifie que le joueur peut roquer :
			$cases_menacees = menace_all($jeu, ($trait == 1 ? 2 : 1)); // fonction dans gestion_menaces.php
			$j = $coup[1];
			if ($coup[4] == 'XX') {
				// Petit roque : i=5,6,7 ne doivent pas être menacées
				print_r($cases_menacees[5][$j]);
				print_r($cases_menacees[6][$j]);
				print_r($cases_menacees[7][$j]);
				if (!($cases_menacees[5][$j] == true || $cases_menacees[6][$j] == true || $cases_menacees[7][$j] == true)) {
					$coups_valides[] = $coup;
				}
			} else {
				// Grand roque : i=3,4,5 ne doivent pas être menacées
				if (!($cases_menacees[3][$j] == true || $cases_menacees[4][$j] == true || $cases_menacees[5][$j] == true)) {
					$coups_valides[] = $coup;
				}
			}
		} else {
			// On effectue le coup :
			$jeu_tps = maj_coup($jeu, $coup); // fonction dans utilitaires_coups.php
			// Si le roi n'est pas en echec, on ajoute ce coup :
			if (!roi_en_echec($jeu_tps, $trait)) { //fonction dans utilitaires.php
				$coups_valides[] = $coup;
				
			}
		}
		
	}
	return $coups_valides;
}

function est_un_roque($coup) {
	// Fonction qui vérifie si $coup est un roque
	if (sizeof($coup) == 5) {
		if ($coup[4] == 'XX' | $coup[4] == 'XXX') {
			return true;
		}
	}
	return false;
}

function tester_pp($jeu, $coup, $piece) {
	$trait = $piece[1];
	$pp_possible = '';
	$col_pion = $coup[0];
	$case_pion = $coup[3]+($trait == 2 ? 1 : -1);

	if($piece[0] == 'p') {
		// on vérifie que le déplacement est bien de 2 :
		if (abs($coup[1] - $coup[3]) == 2) {
			// On vérifie la possible du pp à l'aide du coup précédent
			if ($trait == 1) {$x = 4;} else {$x = 5;}
			// On vérifie si il y a un pion à droite :
			if (est_au_joueur($jeu, $col_pion-1, $x, ($trait == 1 ? 2 : 1))) { // Fonction dans utilitaires.php
				// On vérfie si la pièce est un pion :
				if ($jeu[$col_pion-1][$x][0] == 'p') {
					$pp_possible .= '['.($col_pion-1).','.$x.','.$col_pion.','.$case_pion.',"pp"],';
				}
			}
			// On vérifie si il y a un pion à droite :
			if (est_au_joueur($jeu, $col_pion+1, $x, ($trait == 1 ? 2 : 1))) {
				// On vérfie si la pièce est un pion :
				if ($jeu[$col_pion+1][$x][0] == 'p') {
					$pp_possible .= '['.($col_pion+1).','.$x.','.$col_pion.','.$case_pion.',"pp"],';
				}
			}

		}
	}
	return $pp_possible;
}

?>