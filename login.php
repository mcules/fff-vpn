<?php
/**
 * Copyright by IT Stall (www.itstall.de) 2017
 * User: Dennis Eisold
 * Date: 16.07.2017
 */

session_start();
require_once ('includes/smarty.class.php');
include "config.php";

if($_GET['action'] == 'logout') {
    echo "test";
    session_destroy();
}

$tpl = new Template();

$form_sent = isset($_POST['submit']) && $_POST['submit']=='Login';
if(!$form_sent) {
    $tpl->assign('status', 'form');
}
else {
    $user_id = $_POST['user_id'];
    $user_pass = $_POST['user_pass'];

    $dbh = new PDO("mysql:host=$MYSQL_HOST;dbname=$MYSQL_DATA", $MYSQL_USER, $MYSQL_PASS);
    $stmt = $dbh->prepare("SELECT * FROM user WHERE user_id = :user_id;");
    $stmt->bindParam(':user_id', $user_id);
    if($stmt->execute()) {
        $user = $stmt->fetch();
        if($user !== false) {
            if($user_id == $user['user_id'] && password_verify($user_pass, $user['user_pass'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_mail'] = $user['user_mail'];
                $tpl->assign('status', "success");
                $tpl->assign('message', "Login erfolgreich<br /><br /><a href=\"user.php\">Weiter...</a>");
            }
        }
        else {
            $tpl->assign('status', "error");
            $tpl->assign('message', "Login fehlgeschlagen");
        }
    }
    $dbh = null;
}

$tpl->assign('self', $_SERVER['PHP_SELF']);
$tpl->assign('page', 'login');
$tpl->render('layout');