<?php
error_reporting(E_ALL);
require_once ('includes/smarty.class.php');
include "config.php";

function error($reason) {
	return ["status" => "error", "message"=> $reason];
}

$tpl = new Template();

function main() {
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
	} else {
        if(strlen($_POST['user_pass']) > 5) {
            return error ("Bitte mehr als 5 Zeichen für das Passwort verwenden");
        }

        $user_id = $_POST['user_id'];
        $user_pass = $_POST['user_pass'];
        $user_mail = $_POST['user_mail'];

        //defining Usernames who are not Allowed
        $forbidden_names = array('admin', 'webmaster', 'root', 'administrator');


        //only establishe database connection if no forbidden name was used
        if(in_array(strtolower($user_id), $forbidden_names)) {
            return error('Benutzername nicht erlaubt, bitte wähle einen anderen!');
        } else {

            //establishing Database Connection
            $dbh = new PDO("mysql:host=$MYSQL_HOST;dbname=$MYSQL_DATA", $MYSQL_USER, $MYSQL_PASS);


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



    }


}
$result = main();

$tpl->assign('self', $_SERVER['PHP_SELF']);
$tpl->assign('status', $result["status"]);
$tpl->assign('message', $result["message"]);
$tpl->assign('page', 'index');
$tpl->render('layout');