<div id="messages">
<?php
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=codenames;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}

$reponse = $bdd->prepare('SELECT pseudo, message FROM messages WHERE id_partie = ? ORDER BY id');
$reponse->execute(array($_SESSION['id']));

while($donnees = $reponse->fetch()) {
	?>

		<p>
			<strong><?php echo htmlspecialchars($donnees['pseudo']); ?></strong>&nbsp:&nbsp<?php echo htmlspecialchars($donnees['message']); ?><br/>
		</p>

	<?php
}

$reponse->closeCursor();

?>
</div>
<form action="chat_post.php" method="post">
	<p>
		<label for="message">Message: </label><input type="text" name="message" id="message" autocomplete="off" />
	</p>
</form>

<script type="text/javascript">
	var zoneMsg = document.getElementById('message');
	zoneMsg.focus();

	var messages = document.querySelector('#messages');
	messages.scrollTop = messages.scrollHeight - messages.clientHeight;

</script>