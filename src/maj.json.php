<?php 
/**
 Fichier qui vérifie que l’identifiant de login (tel qu’il figure dans la session) est valide et correspond à l’un des joueurs inscrits sur la partie (paramètres "partie" et "cote"). 
 Le service vérifie que la partie en est au stade indiqué par les paramètres "tour" et "trait". 
 Si le paramètre optionnel "coup" est fourni et est correct alors l’arbitrage est lancé pour jouer le coup et rédiger une nouvelle situation.
 Si l’une des conditions précédentes n’est pas vérifiée, une erreur sera lancée.
**/


?>    