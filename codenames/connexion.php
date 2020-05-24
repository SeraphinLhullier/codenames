<?php 

// Si on a déjà validé le formulaire une fois
if (isset($_POST['submit'])) {
	try
	{
		$bdd = new PDO('mysql:host=localhost;dbname=codenames;charset=utf8', 'root', '');
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
//  Récupération de l'utilisateur et de son pass hashé
	$req = $bdd->prepare('SELECT id, pass, nb_joueurs FROM parties WHERE nom_partie = :nom_partie');
	$req->execute(array(
		'nom_partie' => $_GET['nom_partie']));
	$resultat = $req->fetch();
	$req->closeCursor();

// Comparaison du pass envoyé via le formulaire avec la base
	$isPasswordCorrect = password_verify($_POST['pass'], $resultat['pass']);

	if (!$resultat)
	{
		header("location: new_game.php");
	}
	elseif ($resultat['nb_joueurs'] >=2) {
		echo "Cette partie est deja remplie";
	}
	elseif(!isset($_POST['pseudo']) or $_POST['pseudo'] == '') {
		echo 'Vous devez rentrer un pseudo';
	}
	else
	{
		if ($isPasswordCorrect) {
			session_start();
			$_SESSION['joueur'] = $resultat['nb_joueurs'];
			$_SESSION['id'] = $resultat['id'];
			$_SESSION['pseudo'] = $_POST['pseudo'];

			$req = $bdd->prepare('SELECT mot, position FROM cartes WHERE id_partie = ?');
			$req->execute(array($_SESSION['id']));
			while($carte = $req->fetch()) {
				$_SESSION['mots'][$carte['position']] = $carte['mot'];
			}
			$req->closeCursor();

			$req = $bdd->prepare('SELECT numero_carte, joueur_1, joueur_2 FROM fiche_reponse WHERE id_partie = ?');
			$req->execute(array($_SESSION['id']));
			while($carte = $req->fetch()) {
				$_SESSION['reponse'][$carte['numero_carte']] = $carte['joueur_'.($_SESSION['joueur']+1)];
				$_SESSION['reponse2'][$carte['numero_carte']] = $carte['joueur_'.((1-$_SESSION['joueur'])+1)];
			}
			$req->closeCursor();





			echo 'Vous êtes connecté !';

			$req = $bdd->prepare('UPDATE parties SET nb_joueurs = :nb_joueurs WHERE id = :id');
			$req->execute(array(
				'nb_joueurs' => $_SESSION['joueur']+1,
				'id' => $_SESSION['id']
			));


			header("location: partie.php");

		}
		else {
			echo 'Mauvais mot de passe !';
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Connexion</title>
	<link rel="stylesheet" type="text/css" href="inscription.css">
</head>
<body>
	<h1>Connexion à la partie <strong>"<?php echo htmlspecialchars($_GET['nom_partie']) ?>"</strong></h1>
	<section id="formulaire">
		<form method="post">

			<label class="form_col" for="pseudo">Pseudo</label>
			<input name="pseudo" id="pseudo" type="text" />
			<br /><br />

			<label class="form_col" for="pass">Mot de passe :</label>
			<input name="pass" id="pass" type="password" />
			<br /><br />

			<span class="form_col"></span>
			<input type="submit" value="Se connecter" name="submit"/>
		</form>
		<p>

		</p>
	</section>
</body>
</html>