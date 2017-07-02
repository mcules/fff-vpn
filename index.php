<form name="register" action="<?php echo $_SERVER[PHP_SELF];?>" method="POST" accept-charset="UTF-8">
	Benutzername: <input type="text" name="user_id"  maxlength="32"><br/>
	Passwort: <input type="password" name="user_pass"  maxlength="32"><br/>
	Email: <input type="text" name="user_mail"  maxlength="64"><br/>
	<input type="submit" name="submit" value="Registrieren">
</form>

<?php
error_reporting(E_ALL);
include "config.php";

$dbh = new PDO("mysql:host=$MYSQL_HOST;dbname=$MYSQL_DATA", $MYSQL_USER, $MYSQL_PASS);

if(isset($_POST['submit']) && $_POST['submit']=='Registrieren') {
	if($_POST['user_id'] != '' && $_POST['user_pass'] != '' && $_POST['user_mail'] != '') {
		$user_id = $_POST['user_id'];
		$user_pass = $_POST['user_pass'];
		$user_mail = $_POST['user_mail'];

		$stmt = $dbh->prepare("SELECT user_id FROM user where user_id = :user_id");
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			if(is_array($row)) {
				die('Benutzername nicht verfügbar');
			}
		}
		$stmt = $dbh->prepare("INSERT INTO user (user_id, user_pass, user_mail) VALUES (:user_id, :user_pass, :user_mail);");
		$stmt->bindParam(':user_id', $user_id);
		$stmt->bindParam(':user_pass', password_hash($user_pass, PASSWORD_DEFAULT));
		$stmt->bindParam(':user_mail', $user_mail);
		if($stmt->execute()) {
			echo "Benutzer erfolgreich erstellt";
		}
		$dbh = null;
	}
	else {
		die('Es ist ein Fehler aufgetreten. Bitte fülle alle Felder aus und versuche es noch einmal.');
	}
}
?>
<br /><br />
<hr>
<b>Konfigurationen:</b><br />
<a href="vpn.freifunk-franken.de.ovpn">ovpn config</a><br />
<a href="vpn.freifunk-franken.de.ovpn.zip">ovpn config (zip)</a><br />
<a href="vpn.freifunk-franken.de.visz">Viscocity config</a>
