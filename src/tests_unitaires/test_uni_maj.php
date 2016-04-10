<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Test unitaire maj.json.php</title>
                                                             
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="test_uni.js"></script>

    <script type="text/javascript">

    $(document).ready(function() {
    	para = {
    		titre : 'Test unitaire maj.json.php',
    		commentaires : "Ce test test le bon déroulement de maj.json.php.",
	    	// Requetes à executer pour le test
	    	requetes : [ // url, commentaire, retour attendu
				['maj.json.php?partie=[id]&tour=1&trait=1&cote=1&coup=15', "les blancs ont le trait – MAJ des blancs"],
			],
			retours : '["{\\"je_joue\\":[4,2,4,4],\\"vues\\":[[4,5],[3,5],[5,5]]}    "]',
	    	// lien de création d'une partie pour le teste
	    	creation : '../debug/gestion_partie.php?action=creer&nom=test_unitaire_maj',
	    	// On supprime la partie :
	    	fin : '../debug/gestion_partie.php?action=suppr&nom=test_unitaire_maj'
	    };

    	// On lance le test :
    	if (para.retours != '') {effectuer_test_unitaire(para);} else {maj_test_unitaire(para);}
    });
    </script>
</head>  

<body>
	<h2 id="titre"></h2>
	<div class="result">
		<p>Résultat du test : <span id="actu" class="en_cours">test en cours...</span></p>
		<p>Commentaires sur le test :</p>
		<div id="commentaires"></div>
	</div>

	<div id="avancement">
		<p id="sur"></p>
		<p>Requete en cours : <span id="en_cours">Initialisation...</span></p>
	</div>

	<div id="error">
		<h3>Erreur :</h3>
	</div>

</body>
</html>