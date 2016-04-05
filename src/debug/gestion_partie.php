<?php
/**
	La fonction permet de :
		- Créer une nouvelle partie (j1 = 'a', j2 = 'b') : prend en get action=creer&nom=nom_de_partie
		- Remmetre une partie à zéro une partie : prend en get action=reset&idp=id_bdd_de_la_partie
		- Supprimer une partie : prend en get action=suppr&nom=nom_de_partie ou action=suppr&idp=id_bdd_de_la_partie
**/

// Recuperation des parametres : partie
if (isset($_GET["action"])) {
    // Le apramétre action est défini on peut continuer :
	$action = $_GET["action"];
	
	// Normalement defini par include('session.php');
	try {$bdd = new PDO('mysql:host=localhost;dbname=echec', 'root', '');}
	catch (Exception $e) {die('{"erreur":"Erreur : ' . $e->getMessage().'"}');}

	// Variables :
	$bdd_jeu = '[[],["",["T",1],["p",1],"","","","",["p",2],["T",2]],["",["C",1],["p",1],"","","","",["p",2],["C",2]],["",["F",1],["p",1],"","","","",["p",2],["F",2]],["",["D",1],["p",1],"","","","",["p",2],["D",2]],["",["R",1],["p",1],"","","","",["p",2],["R",2]],["",["F",1],["p",1],"","","","",["p",2],["F",2]],["",["C",1],["p",1],"","","","",["p",2],["C",2]],["",["T",1],["p",1],"","","","",["p",2],["T",2]]]';
	$bdd_histo_j1 = '[{"coups":[[2,1,1,3],[2,1,3,3],[7,1,6,3],[7,1,8,3],[1,2,1,3],[2,2,2,3],[3,2,3,3],[4,2,4,3],[5,2,5,3],[6,2,6,3],[7,2,7,3],[8,2,8,3],[1,2,1,4],[2,2,2,4],[3,2,3,4],[4,2,4,4],[5,2,5,4],[6,2,6,4],[7,2,7,4],[8,2,8,4]]}]';

	// En fonction de l'action choisie le code va créer, reset ou supprimer une partie :
	if ($action == 'reset') {
		// Reset fonctionne à l'aide de l'idp de la partie stockée dans la bdd :
		if(!isset($_GET["idp"])) {die;}
		$idp = $_GET["idp"];
		
		// On écrit la requête sql :
		$sql = 'UPDATE parties SET tour = 1' .
			', trait = 1' .
			", roques_j1 = '" . '["XX", "XXX"]' . "'" .
			", roques_j2 = '" . '["XX", "XXX"]' . "'" .
			", etat_du_jeu = '" . $bdd_jeu . "'" .
			", histo_j1 = '" . $bdd_histo_j1 . "'" .
			", histo_j2 = '[]'" .
			'  WHERE idp=' . $idp;

		// On éxecute la requête SQL :
		$nb_modifs = $bdd->exec($sql);

		if ($nb_modifs) {
			echo '{"succes":"Partie '.$idp.' modifiee avec succes"}';
		} else {
			echo '{"erreur":"Probleme lors de la requete ! Ou partie deja reset..."}';
		}
	} elseif ($action == 'creer') {
		// Créer fonctionne à l'aide du nom que l'on veut donner à la partie :
		if(!isset($_GET["nom"])) {die;}
		$nom = $_GET["nom"];
		
		// On écrit la requête sql :
		$sql = 'INSERT INTO parties SET tour = 1' .
			', trait = 1' .
			", roques_j1 = '" . '["XX", "XXX"]' . "'" .
			", roques_j2 = '" . '["XX", "XXX"]' . "'" .
			", etat_du_jeu = '" . $bdd_jeu . "'" .
			", histo_j1 = '" . $bdd_histo_j1 . "'" .
			", histo_j2 = '[]'" .
			', j1 = "a"' .
			', j2 = "b"' .
			', nom = "' . $nom . '"' .
			', etat_partie = "en_cours"';

		// On éxecute la requête SQL :
		$nb_modifs = $bdd->exec($sql);

		if ($nb_modifs) {
			echo '{"succes":"Partie creee avec succes", "nom":"'.$nom.'", "idp":'.$bdd->lastInsertId().'}';
		} else {
			echo '{"erreur":"Probleme lors de la requete ! Ou partie deja reset..."}';
		}
	} elseif ($action == 'suppr') {		
		// Suppr fonctionne à l'aide de l'idp ou du nom de la partie stockée dans la bdd :

		// On écrit la requête sql :
		if(isset($_GET["idp"])) {$sql = 'DELETE FROM parties WHERE idp = ' . $_GET["idp"]; $info = $_GET["idp"];}
		elseif(isset($_GET["nom"])) {$sql = 'DELETE FROM parties WHERE nom = "' . $_GET["nom"] . '"'; $info = $_GET["nom"];}
		else {die();}

		// On éxecute la requête SQL :
		$nb_modifs = $bdd->exec($sql);

		if ($nb_modifs) {
			echo "Partie ".$info." supprimee avec succes";
		} else {
			echo '{"erreur":"Probleme lors de la requete ! Ou partie deja reset..."}';
		}

	}

	// Fermeture de la connexion :
	$bdd = null;
} else {echo '{"erreur":"Action invalide !"}';}


?>