<?php

session_start();

try
{
	$bdd = new PDO('mysql:host=eu-cdbr-west-03.cleardb.net;dbname=heroku_b23248b0c3aa0d5;charset=utf8', 'b7c134289c57fb', '2ffb6aae');
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}

if(isset($_POST['message']) and $_POST['message'] != '') {
$req = $bdd->prepare('INSERT INTO messages(id_partie, pseudo, message) VALUES(:id, :pseudo, :message)');
$req->execute(array(
'id' => $_SESSION['id'],
'pseudo' => $_SESSION['pseudo'],
'message' => $_POST['message']
));
}

header('Location: partie.php');
?>
