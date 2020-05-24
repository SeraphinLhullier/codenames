<table id="cartes_mots">
	<?php  

	$req = $bdd->prepare('SELECT etat, position FROM cartes WHERE id_partie = ?');
	$req->execute(array($_SESSION['id']));
	while($entree = $req->fetch()) {
		if ($entree['etat'] == 3) {
			if ($_SESSION['joueur']==0){
				$etat[$entree['position']] = 3;
			} else {
				$etat[$entree['position']] = 4;
			}
		}
		elseif ($entree['etat'] == 4) {
			if ($_SESSION['joueur']==0){
				$etat[$entree['position']] = 4;
			} else {
				$etat[$entree['position']] = 3;
			}
		 
		} 
		else {
			$etat[$entree['position']] = $entree['etat'];
		}
	}


			// Les cartes changent de sens en fonction du joueur
	if ($_SESSION['joueur'] ==0) {
		for ($i = 0 ; $i<5 ; $i++) {
			echo '<tr>';
			for ($j = 0 ; $j < 5 ; $j++) {
				echo '<td class="carte_'.$etat[5*$i+$j].'"><span class="mot">';
				if($fini == 0 and $nb_tours%2 == 0 and ($etat[5*$i+$j]==0 or $etat[5*$i+$j]==3)) {
					echo '<a href = "traitement.php?carte='.(5*$i+$j).'">'.$_SESSION['mots'][5*$i+$j].'</a>';
				} else {
					echo $_SESSION['mots'][5*$i+$j];
				}
				echo '</span></td>';
			}
			echo '</tr>';
		}

	}

	if ($_SESSION['joueur'] ==1) {
		for ($i = 0 ; $i<5 ; $i++) {
			echo '<tr>';
			for ($j = 0 ; $j < 5 ; $j++) {
				echo '<td class="carte_'.$etat[24-(5*$i+$j)].'"><span class="mot">';
				if($fini==0 and $nb_tours%2 == 1 and ($etat[24-(5*$i+$j)]==0 or $etat[24-(5*$i+$j)]==3)) {
					echo '<a href = "traitement.php?carte='.(24-(5*$i+$j)).'">'.$_SESSION['mots'][24-(5*$i+$j)].'</a>';
				} else {
					echo $_SESSION['mots'][24-(5*$i+$j)];
				}
				echo '</span></td>';
			}
			echo '</tr>';
		}

	}



	?>
</table>