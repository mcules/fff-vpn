<?php
error_reporting(E_ALL);

function error($reason) {
	return ["status" => "error", "message"=> $reason];
}

function main() {
	include "config.php";

	$all_fields_defined = (
		(isset($_POST['user_id']) && $_POST['user_id'] != '') +
		(isset($_POST['user_pass']) && $_POST['user_pass'] != '')+
		(isset($_POST['user_mail']) && $_POST['user_mail'] != '')
	) == 3;

	$form_sent = isset($_POST['submit']) && $_POST['submit']=='Registrieren';

	if(!$form_sent) {
		return ["status" => "form"];
	}

	if($form_sent && !$all_fields_defined) {
		return error("Bitte alle Felder ausfüllen");
	}

	$user_id = $_POST['user_id'];
	$user_pass = $_POST['user_pass'];
	$user_mail = $_POST['user_mail'];
	$forbidden_names = array('admin', 'webmaster');

	$dbh = new PDO("mysql:host=$MYSQL_HOST;dbname=$MYSQL_DATA", $MYSQL_USER, $MYSQL_PASS);

	if($form_sent) {
		if($user_id != '' && $user_pass != '' && $user_mail != '') {
			if(in_array($user_id, $forbidden_names)) {
				return error('Benutzername nicht erlaubt, bitte wähle einen anderen!');
			}
			$stmt = $dbh->prepare("SELECT user_id FROM user where user_id = :user_id");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			while ($row = $stmt->fetch()) {
				if(is_array($row)) {
					return error('Benutzername nicht verfügbar');
				}
			}
			$user_pass_hash = password_hash($user_pass, PASSWORD_DEFAULT);
			$stmt = $dbh->prepare("INSERT INTO user (user_id, user_pass, user_mail) VALUES (:user_id, :user_pass, :user_mail);");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->bindParam(':user_pass', $user_pass_hash);
			$stmt->bindParam(':user_mail', $user_mail);
			if($stmt->execute()) {
				return ["status" => "success", "message" => "Benutzer erfolgreich erstellt"];
			}
			$dbh = null;
		}
		else {
			return error('Es ist ein Fehler aufgetreten. Bitte fülle alle Felder aus und versuche es noch einmal.');
		}
	}
}
$result = main();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<title>vpn.freifunk-franken.de</title>
	<link href="assets/style.css" rel="stylesheet" type="text/css" media="screen"/>
</head>
<body>
	<div id="header">
		<div class="inner">
			<h1>vpn.freifunk-franken.de</h1>
			<h2>Freifunk Franken VPN</h2>
			<img src="assets/freifunk.svg" alt="freifunk"/>
		</div>
	</div>
	<div id="stripe" style="">
		<a style="" href="http://www.freifunk-franken.de/" target="_blank">freifunk-franken.de</a>
	</div>
	<div id="container">
		<div class="form">
			<h3>Registieren</h3>
			<?php if ($result["status"] == "form") { ?>
			<form id="register" name="register" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" accept-charset="UTF-8">
				<label for="user_id">Benutzername:</label>
				<input type="text" name="user_id" id="user_id" maxlength="32"/>

				<label for="user_pass">Passwort:</label>
				<input type="password" name="user_pass" id="user_pass" maxlength="32"/>

				<label for="user_mail">Email:</label>
				<input type="text" name="user_mail" id="user_mail" maxlength="64"/>

				<input type="submit" name="submit" value="Registrieren" class="btn"/>
			</form>
			<?php } else if ($result["status"] == "error" || $result["status"] == "success") { ?>
			<div class="<?php echo $result["status"]?>">
				<?php echo $result["message"] ?>
			</div>
			<?php  } ?>
		</div>
		<div class="config">
			<h3>Konfiguration</h3>
			<ul>
				<li><a href="vpn.freifunk-franken.de.ovpn">ovpn config</a></li>
				<li><a href="vpn.freifunk-franken.de.ovpn.zip">ovpn config (zip)</a></li>
				<li><a href="vpn.freifunk-franken.de.visz">Viscocity config</a></li>
			</ul>
	</div>
	</body>
</html>
