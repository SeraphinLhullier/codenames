<?php
session_start();

try
{
	$bdd = new PDO('mysql:host=localhost;dbname=codenames;charset=utf8', 'root', '');
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e)
{
	die('Erreur : '.$e->getMessage());
}

$id_partie=$_SESSION['id'];

$req = $bdd->prepare('DELETE FROM fiche_reponse WHERE id_partie = ?');
$req->execute(array($id_partie));

$req = $bdd->prepare('DELETE FROM cartes WHERE id_partie = ?');
$req->execute(array($id_partie));

$req = $bdd->prepare('UPDATE parties SET nb_tours = 0, fini = 0, date_creation = NOW() WHERE id = ?');
$req->execute(array($id_partie));


$i = 0;
$req = $bdd->query('SELECT mot FROM mots ORDER BY RAND() LIMIT 25');

while($mot = $req->fetch()) {
	$mots[$i] = $mot['mot'];
	$i++;
}


for ($i = 0; $i<25; $i++) {
	$req->closeCursor();
	$req = $bdd->prepare('INSERT INTO cartes(id_partie, position, mot, etat) VALUES(:id, :position, :mot, :etat)');
	$req->execute(array(
		'id' => $id_partie,
		'position' => $i,
		'mot' => $mots[$i],
		'etat' => 0
	));
	$_SESSION['mots'][$i] = $mots[$i];
}
$req->closeCursor();


		// Creation des données de la table "fiche_reponse" 

$j1 = array(2, 1, 1, 1, 2, 1, 3, 2, 3, 3, 3, 3, 3, 1 ,1, 1, 1, 1, 3, 3, 3, 3, 3, 3, 3);
$j2 = array(2, 1, 1, 1, 1, 2, 2, 3, 1, 1, 1, 1, 1, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3);

$order = range(0,24);
shuffle($order);




for ($i = 0; $i<25; $i++) {
	$req = $bdd->prepare('INSERT INTO fiche_reponse(id_partie, numero_carte, joueur_1, joueur_2) VALUES(:id, :position, :j1, :j2)');
	$req->execute(array(
		'id' => $id_partie,
		'position' => $i,
		'j1' => $j1[$order[$i]],
		'j2' => $j2[$order[$i]]
	));
	if ($_SESSION['joueur'] == 0){
		$_SESSION['reponse'][$i] = $j1[$order[$i]];
		$_SESSION['reponse2'][$i] = $j2[$order[$i]];
	} else {
		$_SESSION['reponse'][$i] = $j2[$order[$i]];
		$_SESSION['reponse2'][$i] = $j1[$order[$i]];
	}

}

header('location: partie.php');
?>