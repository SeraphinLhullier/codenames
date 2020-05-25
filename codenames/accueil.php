<?php 
try
{
	$bdd = new PDO('mysql:host=eu-cdbr-west-03.cleardb.net;dbname=heroku_b23248b0c3aa0d5;charset=utf8', 'b7c134289c57fb', '2ffb6aae');
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e)
{
	die('Erreur : '.$e->getMessage());
}

$req = $bdd->query('SELECT id FROM parties WHERE date_creation <= DATE_SUB(NOW(), INTERVAL 2 DAY)');
$i = 0;
while ($id = $req->fetch()) {
	$listeId[$i] = $id['id'];
	$i++;
}

while ($i > 0) {
	$i--;
	// On supprime les données de cette partie
	$req = $bdd->prepare('DELETE FROM parties WHERE id = ?');
	$req->execute(array($listeId[$i]));

	$req = $bdd->prepare('DELETE FROM fiche_reponse WHERE id_partie = ?');
	$req->execute(array($listeId[$i]));

	$req = $bdd->prepare('DELETE FROM cartes WHERE id_partie = ?');
	$req->execute(array($listeId[$i]));

	$req = $bdd->prepare('DELETE FROM messages WHERE id_partie = ?');
	$req->execute(array($listeId[$i]));
}

?>



<!DOCTYPE html>
<html>
<head>
	<title>Codenames</title>
	<meta charset="utf-8">
	<meta http-equiv="refresh" content="5" > 
</head>
<body>
	<header><h1>Codenames</h1></header>

	<section>
		<table>
			<tr>
				<th>Id</th>
				<th>Nom</th>
				<th>nb_joueurs</th>
			</tr>
			<?php
			$req = $bdd->query('SELECT id, nom_partie, nb_joueurs, fini FROM parties ORDER BY nom_partie');
			while($partie = $req->fetch()) {
				if ($partie['fini']==0) {
					?>
					<tr>
						<th><?php echo $partie['id'];?></th>
						<th><a href="connexion.php?nom_partie=<?php echo $partie['nom_partie'];?>"><?php echo $partie['nom_partie'];?></a></th>
						<th><?php echo $partie['nb_joueurs'];?></th>
					</tr>

					<?php 
				}
			}
			?>
		</table>
		<a href="new_game.php">Créer une nouvelle partie</a>
	</section>


	<script type="text/javascript" src="refresh.js"></script>
</body>
</html>
