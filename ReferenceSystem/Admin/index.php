<?php

use ReferenceSystem\Modules\RSUser;
use ReferenceSystem\Modules\Database\Install as DBInstall;

include_once(__DIR__ . '/../vendor/autoload.php');

if(!DBInstall::isInstalled()) {
    include_once 'includes/install.php';
    return;
}

if(isset($_GET['action']) AND $_GET['action'] == 'logout')
{
    RSUser::Logout();
    header("Location: /ReferenceSystem/Admin/");
}

if (isset($_POST['username']) AND $_POST['username'] != '' AND isset($_POST['password']) AND $_POST['password'] != '')
    RSUser::Login($_POST['username'], $_POST['password']);

if (RSUser::isLoggedIn())
    include_once 'includes/mainPage.php';
else
    include_once 'includes/login.php';
