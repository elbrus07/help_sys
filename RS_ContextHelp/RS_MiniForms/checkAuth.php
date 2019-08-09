<?php

use ReferenceSystem\RSUser;

include_once ($_SERVER['DOCUMENT_ROOT'].'/classes/RSUser.php');

if (RSUser::isLoggedIn())
    echo true;
else
    echo false;