<?php

use ReferenceSystem\Modules\RSUser;

require_once __DIR__.'/../vendor/autoload.php';

if(!RSUser::isLoggedIn())
    die('Вы не имеете права загружать изображения');

/*********************************************
 * Change this line to set the upload folder *
 *********************************************/
$imageFolder = "";

reset ($_FILES);
$temp = current($_FILES);
if (is_uploaded_file($temp['tmp_name'])){
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    }

    /*
      If your script needs to receive cookies, set images_upload_credentials : true in
      the configuration and enable the following two headers.
    */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }

    // Verify extension
    $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array("gif", "jpg", "png"))) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    $name = getRandomFileName('', $ext);
    $filetowrite = $imageFolder . $name  . '.' . $ext;
    move_uploaded_file($temp['tmp_name'], $filetowrite);

    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    echo json_encode(array('location' => $filetowrite));
} else {
    // Notify editor that the upload failed
    header("HTTP/1.1 500 Server Error");
}

function getRandomFileName($path, $extension='')
{
    $extension = $extension ? '.' . $extension : '';
    $path = $path ? $path . '/' : '';

    do {
        $name = md5(microtime() . rand(0, 9999));
        $file = $path . $name . $extension;
    } while (file_exists($file));

    return $name;
}