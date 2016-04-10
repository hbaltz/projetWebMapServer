<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Test unitaire cr.json.php</title>
                                                             
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="test_uni.js"></script>

    <script type="text/javascript">

    $(document).ready(function() {
    	para = {
    		titre : 'Test unitaire cr.json.php',
    		commentaires : "Ce test reprend l'exemple donnée dans le document de spécifications afin de vérifier son bon déroulement.",
	    	// Requetes à executer pour le test
	    	requetes : [ // url, commentaire, retour attendu
				['cr.json.php?partie=[id]&cote=2', "les blancs ont le trait – CR des noirs"],
			],
			retours : '["{\\"tour\\":\\"1\\",\\"trait\\":\\"1\\",\\"cote\\":1,\\"histo\\":\\"[{\\\\\\"coups\\\\\\":[[2,1,1,3],[2,1,3,3],[7,1,6,3],[7,1,8,3],[1,2,1,3],[2,2,2,3],[3,2,3,3],[4,2,4,3],[5,2,5,3],[6,2,6,3],[7,2,7,3],[8,2,8,3],[1,2,1,4],[2,2,2,4],[3,2,3,4],[4,2,4,4],[5,2,5,4],[6,2,6,4],[7,2,7,4],[8,2,8,4]]}]\\"} "]',
	    	// lien de création d'une partie pour le teste
	    	creation : '../debug/gestion_partie.php?action=creer&nom=test_unitaire_cr',
	    	// On supprime la partie :
	    	fin : '../debug/gestion_partie.php?action=suppr&nom=test_unitaire_cr'
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