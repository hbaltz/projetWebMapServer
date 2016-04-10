function effectuer_test_unitaire(para) {
	$('#titre').html(para.titre);
	$('#commentaires').html(para.commentaires);
	$('#actu').html('Initialisation...');
	$('#avancement').show();

	// On effectue l'initialisation :
	var html = $.ajax({
		url: para.creation,
		async: false
	}).responseText;

	// On recupere l'idp :
	info = JSON.parse(html);
	if (typeof info.idp == undefined) {
		$('#actu').html('Erreur lors de l\'initialisation').addClass('rate');
	} else {
		$('#actu').html('Initialisation réussie : on lance les requetes...');
		var idp = info.idp;
		var nb_total = para.requetes.length;
		var valide = true;
		var retours = JSON.parse(para.retours);

		$.each(para.requetes, function(i, elm) {
			$('#sur').html('Requetes '+(i+1)+'/'+nb_total+'...');
			$('#en_cours').html(elm[1]+'...');

			var html = $.ajax({
				url: '../'+elm[0].replace('[id]', idp),
				async: false
			}).responseText;

			if (!equals(JSON.parse(html), JSON.parse(retours[i]))) {
				$('#error').show();
				$('#error').append('<p>'+elm[1]+'</p>');
				valide = false;
			}
		});

		if (valide) {$('#actu').html('Test unitaire réussi !').addClass('reussi');}
		else {$('#actu').html('Test unitaire raté !').addClass('rate');}

		// Suppression de la partie dans la table :
		$('#sur').html('Test terminé.');
		$('#en_cours').html('Suppression de la partie dans la BDD');

		var html = $.ajax({
			url: para.fin,
			async: false
		}).responseText;

		if (/succes/.test(html)) {
			$('#sur').html('Test terminé. Suppression de la partie dans la BDD réussie.');
			$('#en_cours').html('Tout s\'est bien déroulé.');
			$('#avancement').hidpe().delay(500);
		} else {
			$('#sur').html('Test terminé. Suppression de la partie dans la BDD ECHOUEE !');
			$('#en_cours').html('Le test unitaire est validpé. Mais la partie n\'a pas été supprimée correctement !');
		}

	}
}

function maj_test_unitaire(para) {
	$('#titre').html(para.titre);
	$('#actu').html('Initialisation...');
	$('#avancement').show();

	// On effectue l'initialisation :
	var html = $.ajax({
		url: para.creation,
		async: false
	}).responseText;

	// On recupere l'idp :
	info = JSON.parse(html);
	if (typeof info.idp == undefined) {
		$('#actu').html('Erreur lors de l\'initialisation').addClass('rate');
	} else {
		$('#actu').html('Initialisation réussie : on lance les requetes...');
		var idp = info.idp;
		var nb_total = para.requetes.length;
		var valide = true;
		var req = [];

		$.each(para.requetes, function(i, elm) {
			$('#sur').html('Requetes '+(i+1)+'/'+nb_total+'...');
			$('#en_cours').html(elm[1]+'...');

			var html = $.ajax({
				url: '../'+elm[0].replace('[id]', idp),
				async: false
			}).responseText;

			req.push(html);
		});


		$('#avancement').html('<textarea idp="req" style="height:300px;widpth:800px;"></textarea>');
		$('#req').val(JSON.stringify(req).replace(/\\/g, '\\\\'));

		console.log((JSON.stringify(req).replace(/\\/g, '\\\\')));
	}
}

function equals(obj1, obj2) {
    function _equals(obj1, obj2) {
        return JSON.stringify(obj1)
            === JSON.stringify($.extend(true, {}, obj1, obj2));
    }
    return _equals(obj1, obj2) && _equals(obj2, obj1);
}