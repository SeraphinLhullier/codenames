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

$valid_get = isset($_GET['carte']) and is_int($_GET['carte']) and 0<=$_GET['carte'] and $_GET['carte'] <= 24;


// Le joueur veut finir son tour

if ($_SESSION['joueur'] == $nb_tours%2 and $fini == 0 and $valid_get) {
	if($_GET['carte']==-1) {
		// echo "Fin du tour volontaire";
		$req = $bdd->prepare('UPDATE parties SET nb_tours=? WHERE id= ?');
		$req->execute(array(
			$nb_tours+1,
			$_SESSION['id']
		));
	}

	
	if($_SESSION['joueur']==0){
		$req = $bdd->prepare('SELECT joueur_2 FROM fiche_reponse WHERE id_partie = ? and numero_carte = ?');
		$req->execute(array(
			$_SESSION['id'],
			$_GET['carte']
		));
		$rep = $req->fetch();
		$reponse = intval($rep['joueur_2']);
		$req->closeCursor();

		// echo $reponse;
		// Si on depasse le tour 10 alors on doit finir la partie (par défaut perdre)
		if ($nb_tours>9) {
			// echo 10;
			$req = $bdd->prepare('UPDATE parties SET fini=-1 WHERE id = ?');
			$req->execute(array(
				$_SESSION['id']
			));
		}


			if ($reponse == 1) { //La carte est bonne
				// echo 3;
				$req = $bdd->prepare('UPDATE cartes SET etat=1 WHERE id_partie = ? and position = ?');
				$req->execute(array(
					$_SESSION['id'],
					$_GET['carte']
				));

				$req = $bdd->prepare('SELECT COUNT(*) AS c FROM cartes WHERE id_partie = ? and etat = 1');
				$req->execute(array(
					$_SESSION['id']
				));
				$rep = $req->fetch();
				$nb_bons = $rep['c'];
				$req->closeCursor();
				if ($nb_bons == 15) {
					$req = $bdd->prepare('UPDATE parties SET fini=1 WHERE id = ?');
					$req->execute(array(
						$_SESSION['id']
					));

				}
			} 
			elseif($reponse ==2) { //La carte est un assassin
				// echo 4;
				$req = $bdd->prepare('UPDATE cartes SET etat=2 WHERE id_partie = ? and position = ?');
				$req->execute(array(
					$_SESSION['id'],
					$_GET['carte']
				));

				$req = $bdd->prepare('UPDATE parties SET fini=-1 WHERE id = ?');
				$req->execute(array(
					$_SESSION['id']
				));

			}
			else { //La carte est neutre
				// echo 5;
				// On increment le nombre de tours
				$req = $bdd->prepare('UPDATE parties SET nb_tours=? WHERE id = ?');
				$req->execute(array(
					$nb_tours+1,
					$_SESSION['id']
				));

				$req = $bdd->prepare('SELECT etat FROM cartes WHERE id_partie = ? and position = ?');
				$req->execute(array(
					$_SESSION['id'],
					$_GET['carte']
				));
				$rep = $req->fetch();
				$ancien_etat = $rep['etat'];
				$req->closeCursor();

				if ($ancien_etat==0 and $_SESSION['joueur']==0){
					// echo 6;
					$req = $bdd->prepare('UPDATE cartes SET etat=4 WHERE id_partie = ? and position = ?');
					$req->execute(array(
						$_SESSION['id'],
						$_GET['carte']
					));
				}
				elseif ($ancien_etat==3 and $_SESSION['joueur']==0){
					// echo 8;
					$req = $bdd->prepare('UPDATE cartes SET etat=5 WHERE id_partie = ? and position = ?');
					$req->execute(array(
						$_SESSION['id'],
						$_GET['carte']
					));
				}
			}


		}



		if($_SESSION['joueur']==1){
			$req = $bdd->prepare('SELECT joueur_1 FROM fiche_reponse WHERE id_partie = ? and numero_carte = ?');
			$req->execute(array(
				$_SESSION['id'],
				$_GET['carte']
			));
			$rep = $req->fetch();
			$reponse = $rep['joueur_1'];
			$req->closeCursor();


		// Si on depasse le tour 10 alors on doit finir la partie (par défaut perdre)
			if ($nb_tours>=10) {
				$req = $bdd->prepare('UPDATE parties SET fini=-1 WHERE id = ?');
				$req->execute(array(
					$_SESSION['id']
				));
			}


			if ($reponse == 1) { //La carte est bonne
				$req = $bdd->prepare('UPDATE cartes SET etat=1 WHERE id_partie = ? and position = ?');
				$req->execute(array(
					$_SESSION['id'],
					$_GET['carte']
				));

				$req = $bdd->prepare('SELECT COUNT(*) AS c FROM cartes WHERE id_partie = ? and etat = 1');
				$req->execute(array(
					$_SESSION['id']
				));
				$rep = $req->fetch();
				$nb_bons = $rep['c'];
				$req->closeCursor();
				if ($nb_bons == 15) {
					$req = $bdd->prepare('UPDATE parties SET fini=1 WHERE id = ?');
					$req->execute(array(
						$_SESSION['id']
					));

				}
			} 
			elseif($reponse ==2) { //La carte est un assassin
				$req = $bdd->prepare('UPDATE cartes SET etat=2 WHERE id_partie = ? and position = ?');
				$req->execute(array(
					$_SESSION['id'],
					$_GET['carte']
				));

				$req = $bdd->prepare('UPDATE parties SET fini=-1 WHERE id = ?');
				$req->execute(array(
					$_SESSION['id']
				));

			}
			else { //La carte est neutre

				// On increment le nombre de tours
				$req = $bdd->prepare('UPDATE parties SET nb_tours=? WHERE id = ?');
				$req->execute(array(
					$nb_tours+1,
					$_SESSION['id']
				));

				$req = $bdd->prepare('SELECT etat FROM cartes WHERE id_partie = ? and position = ?');
				$req->execute(array(
					$_SESSION['id'],
					$_GET['carte']
				));
				$rep = $req->fetch();
				$ancien_etat = $rep['etat'];
				$req->closeCursor();

				if ($ancien_etat==0 and $_SESSION['joueur']==1){
					$req = $bdd->prepare('UPDATE cartes SET etat=3 WHERE id_partie = ? and position = ?');
					$req->execute(array(
						$_SESSION['id'],
						$_GET['carte']
					));
				}
				elseif ($ancien_etat==4 and $_SESSION['joueur']==1){
					$req = $bdd->prepare('UPDATE cartes SET etat=5 WHERE id_partie = ? and position = ?');
					$req->execute(array(
						$_SESSION['id'],
						$_GET['carte']
					));
				}
			}



		}
	}

	// echo '<a href="partie.php">retour</a>';
	header("location: partie.php");
	?>
