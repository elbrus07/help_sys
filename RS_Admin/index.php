<?php

use ReferenceSystem\RSUser;

include_once ($_SERVER['DOCUMENT_ROOT'].'/classes/RSUser.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'/classes/ReferenceSystem.php');

if(isset($_GET['action']) AND $_GET['action'] == 'logout')
{
    RSUser::Logout();
    header("Location: /RS_Admin/");
}

if (isset($_POST['username']) AND $_POST['username'] != '' AND isset($_POST['password']) AND $_POST['password'] != '')
    RSUser::Login($_POST['username'], $_POST['password']);

if (RSUser::isLoggedIn())
    include_once 'includes/mainPage.php';
else
    include_once 'includes/login.php';
