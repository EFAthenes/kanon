<?php
//echo print_r($data,true);
//exit;
?>
Madame, Monsieur,
<br /><br />
Votre identifiant et votre mot de passe sur la Plateforme <?php echo $data["server_name"] ?> ont &eacute;t&eacute; r&eacute;initialis&eacute;s.
<br />
<br />
Votre nom d'utilisateur sur le site est : <br />
<?php echo $data["email"]; ?>
<br />
<br />
Votre mot de passe est :<br />
<?php echo $data["password"]; ?>
<br />
<br />
<br />
Vous pouvez vous connecter en <a href="<?php echo $data["url"]; ?>">cliquant ici</a> ou en vous rendant &agrave; l'adresse suivante: <br />
<?php echo $data["url"]; ?>
<br />
<?php echo $data["footer"]; ?>