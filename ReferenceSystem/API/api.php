<?php

use ReferenceSystem\Modules\ReferenceSystem;
use ReferenceSystem\Modules\RSUser;

include_once(__DIR__ . '/../vendor/autoload.php');


if (!(isNotEmpty($_POST['mode']) and $_POST['action']))
    die('Не заданы необходимые переменные');

$mode = $_POST['mode'];
$action = $_POST['action'];
if($mode == 'article')
{
    if($action == 'get')
    {
        if(!isNotEmpty($_POST['pathOrId']))
            die('Не задан путь или id');
        $pathOrId = $_POST['pathOrId'];
        if(is_numeric($pathOrId))
            echo ReferenceSystem::GetArticleByDBId($pathOrId)->toJSON();
        else
            echo ReferenceSystem::GetArticleByPath($pathOrId)->toJSON();
    }
    elseif ($action == 'add')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if (!(isNotEmpty($_POST['path_p1']) AND isNotEmpty($_POST['path_p2']) AND isset($_POST['content'])))
            die('Не задан один из параметров');
        //Определяем следует добить главу или нечто иное
        $path = ($_POST['path_p1'] != '...') ? $_POST['path_p1'] . '/' . $_POST['path_p2'] : $_POST['path_p2'];
        $content = $_POST['content'];
        //Добавляем
        echo ReferenceSystem::AddReference($path, $content);
    }
    elseif($action == 'edit')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if (!(isNotEmpty($_POST['pathOrId']) AND isNotEmpty($_POST['caption'])
            AND isset($_POST['content'])))
            die('Не задан один из параметров');
        $pathOrId = $_POST['pathOrId'];
        $caption = $_POST['caption'];
        $content = $_POST['content'];
        echo ReferenceSystem::EditReference($pathOrId,$caption, $content);
    }
    elseif($action == 'remove')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if(!isNotEmpty($_POST['pathOrId']))
            die('Не задан путь или id');
        $pathOrId = $_POST['pathOrId'];
        echo ReferenceSystem::RemoveReference($pathOrId);
    }
}
elseif ($mode == 'html_children')
{
    if($action == 'getArticle')
    {
        if (!(isNotEmpty($_POST['item_id'])  and isset($_POST['uniqueClass']) and isNotEmpty($_POST['pathname'])))
            die('Не задан один из параметров');

        $item_id = $_POST['item_id'];
        $uniqueClass = $_POST['uniqueClass'];
        $pathname = $_POST['pathname'];

        $arr = ReferenceSystem::GetReferenceOfHTMLItem($item_id,$uniqueClass,$pathname);
        echo json_encode($arr);
    }
    elseif ($action == 'get')
    {
        if(!isNotEmpty($_POST['pathOrId']))
            die('Не задан путь или id');
        $pathOrId = $_POST['pathOrId'];
        $arr = ReferenceSystem::GetHTMLItemsOfReference($pathOrId);
        echo json_encode($arr);
    }
    elseif ($action == 'add')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if (!(isNotEmpty($_POST['pathOrId']) AND isNotEmpty($_POST['HTMLelId']) AND isset($_POST['uniqueClass'])
            AND isNotEmpty($_POST['pathname'])))
            die('Не задан один из параметров');
        $HTMLel = $_POST['HTMLelId'];
        $uniqueClass = $_POST['uniqueClass'];
        $pathName = $_POST['pathname'];
        $pathOrId = $_POST['pathOrId'];
        echo ReferenceSystem::AddReferenceToHTMLItem($HTMLel,$uniqueClass,$pathName,$pathOrId);
    }
    elseif ($action == 'remove')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if (!(isNotEmpty($_POST['pathOrId']) AND isNotEmpty($_POST['HTMLelId']) AND isset($_POST['uniqueClass'])
            AND isNotEmpty($_POST['pathname'])))
            die('Не задан один из параметров');
        $HTMLel = $_POST['HTMLelId'];
        $uniqueClass = $_POST['uniqueClass'];
        $pathName = $_POST['pathname'];
        $pathOrId = $_POST['pathOrId'];
        ReferenceSystem::RemoveReferenceOfHTMLItem($HTMLel,$uniqueClass,$pathName,$pathOrId);
    }
}
elseif ($mode == 'another')
{
    if ($action == 'isAdmin')
        echo RSUser::isLoggedIn();
}


function isNotEmpty($var)
{
    if(isset($var) and $var != '')
        return true;
    else
        return false;
}