<?php
/**
 * Copyright by IT Stall (www.itstall.de) 2017
 * User: Dennis Eisold
 * Date: 16.07.2017
 */

session_start();
require_once ('includes/smarty.class.php');
include "config.php";

$tpl = new Template();

if(!isset($_SESSION['user_id'])) {
    die('Bitte zuerst <a href="login.php">anmelden</a>');
}
else {
    $tpl->assign('loggedin', true);
}

$tpl->assign('page', 'user');
$tpl->render('layout');