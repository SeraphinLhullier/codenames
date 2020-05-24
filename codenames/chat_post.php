<?php

session_start();

try
{
	$bdd = new PDO('mysql:host=localhost;dbname=codenames;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
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