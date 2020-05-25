<?php 
if(!isset($_POST['nom_partie']) and !isset($_POST['pass']))
{
	echo ("Veuillez choisir un nom et un mot de pass pour la partie");
}
elseif(!isset($_POST['nom_partie']) or $_POST['nom_partie'] == '') {
	echo "Vous devez rentrer un nom pour la partie";
}
elseif (!isset($_POST['pass']) or $_POST['pass'] == '') {
	echo "Vous devez entrer un mot de passe";
} 
else
{
	try
	{
		$bdd = new PDO('mysql:host=eu-cdbr-west-03.cleardb.net;dbname=heroku_b23248b0c3aa0d5;charset=utf8', 'b7c134289c57fb', '2ffb6aae');
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
	$req = $bdd->prepare('SELECT COUNT(*) AS c FROM parties WHERE nom_partie=?');
	$req->execute(array($_POST['nom_partie']));
	$nb_parties = $req->fetch();
	$req->closeCursor();

	if ($nb_parties['c']>=1) {
		echo "Une partie porte deja ce nom";
	}


	else {

		//Creation des données de la table "parties"

		$pass_hache = password_hash($_POST['pass'], PASSWORD_DEFAULT);

		$req=$bdd->prepare('INSERT INTO parties(nom_partie, pass, nb_joueurs,nb_tours,fini,date_creation) VALUES(:nom_partie, :pass, 0,0,0,NOW())');
		$req->execute(array(
			'nom_partie' => $_POST['nom_partie'],
			'pass' => $pass_hache
		));
		$req->closeCursor();

		$req = $bdd->prepare('SELECT id FROM parties WHERE nom_partie = ?');
		$req->execute(array($_POST['nom_partie']));

		$reponse = $req->fetch();
		$id_partie = $reponse['id'];

		$req->closeCursor();


		// Création des données de la table "cartes"

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
		}

		header('location: connexion.php?nom_partie='.$_POST['nom_partie']);
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Inscription</title>
	<link rel="stylesheet" type="text/css" href="inscription.css">
</head>
<body>
	<h1>Créer une nouvelle partie</h1>
	<section id="formulaire">
		<form method="post">

			<label class="form_col" for="nom_partie">Nom de la partie :</label>
			<input name="nom_partie" id="nom_partie" type="text" autocomplete="off" 
			value="<?php echo (isset($_POST['nom_partie'])) ? $_POST['nom_partie'] : '' ?>" />
			<br /><br />

			<label class="form_col" for="pass">Mot de passe :</label>
			<input name="pass" id="pass" type="password" />
			<br /><br />


			<span class="form_col"></span>
			<input type="submit" value="Créer" />
		</form>
		<p>

		</p>
	</section>
</body>
</html>
