<table id="carte_reponse">
	<?php  
			// Les cartes changent de sens en fonction du joueur
	if ($_SESSION['joueur'] ==0) {
		for ($i = 0 ; $i<5 ; $i++) {
			echo '<tr>';
			for ($j = 0 ; $j < 5 ; $j++) {
				echo '<td class="reponse_'.$_SESSION['reponse'][5*$i+$j].'"></td>';
			}
			echo '</tr>';
		}
	}

	if ($_SESSION['joueur'] ==1) {
		for ($i = 0 ; $i<5 ; $i++) {
			echo '<tr>';
			for ($j = 0 ; $j < 5 ; $j++) {
				echo '<td class="reponse_'.$_SESSION['reponse'][24-(5*$i+$j)].'"></td>';
			}
			echo '</tr>';
		}
	}
	?>

</table>