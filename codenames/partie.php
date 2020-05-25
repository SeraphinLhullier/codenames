<?php 
session_start();
try
{
	$bdd = new PDO('mysql:host=eu-cdbr-west-03.cleardb.net;dbname=heroku_b23248b0c3aa0d5;charset=utf8', 'b7c134289c57fb', '2ffb6aae');
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e)
{
	die('Erreur : '.$e->getMessage());
}

$req = $bdd->prepare('SELECT nb_tours, fini FROM parties WHERE id=?');
$req->execute(array($_SESSION['id']));

$rep = $req->fetch();

$nb_tours = $rep['nb_tours'];
$fini = $rep['fini'];

$req->closeCursor();

if($nb_tours<2) {
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
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Codenames</title>
	<link rel="stylesheet" type="text/css" href="partie.css">
	<meta http-equiv="refresh" content="2" > 
</head>
<body>
	<div id="plateau">
		<?php include("cartes_mots.php"); ?>
		<div id="right">
			<div id="reponse">
				<?php include("carte_reponse.php"); ?>
				<?php
				if ($fini != 0) {
					include("carte_reponse2.php");
				}
				?>
			</div>
			<div id="chat">
				<?php include("chat.php"); ?>
			</div>
		</div>

	</div>

	<?php 
	// Si la partie n'est pas finie
	if ($fini == 0) {
		if ($nb_tours%2 == $_SESSION['joueur']) {
			?>
			<a href="traitement.php?carte=-1">Fin du tour</a>
			<?php
		}
		else {
			echo "C'est au tour de l'autre joueur";
		}
		echo '<br/>Il vous reste : '.(9 - $nb_tours).' tour(s).';
		echo '<br /><a href="accueil.php">Retour à l\'accueil</a>';
	}

	// Si la partie est finie
	else {
		if ($fini == 1){
			echo "Vous avez <strong>gagné !!!</strong> ";
		} else{
			echo "Vous avez <strong>perdu :(</strong> ";

		}



		echo '<br /><a href="accueil.php">Retour à l\'accueil</a>';
		echo '<br /><a href="play_again.php">Nouvelle partie</a>';
	}


	?>

</body>
</html>
