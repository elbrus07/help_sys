<?php

use ReferenceSystem\Modules\RSArticle;
use ReferenceSystem\Modules\RSArticleModes;
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
        if(!isNotEmpty($_POST['path']) AND !isNotEmpty($_POST['id']))
            die('Не задан путь или id');
        $pathOrId = isNotEmpty($_POST['path']) ? $_POST['path'] : $_POST['id'];
        $type = isNotEmpty($_POST['path']) ? RSArticleModes::PATH : RSArticleModes::ID;

        echo (new RSArticle($pathOrId, $type))->toJSON();
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
        echo RSArticle::create($path, $content);
    }
    elseif($action == 'edit')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if (!((isNotEmpty($_POST['path']) or isNotEmpty($_POST['id'])) AND isNotEmpty($_POST['caption'])
            AND isset($_POST['content'])))
            die('Не задан один из параметров');
        $pathOrId = isNotEmpty($_POST['path']) ? $_POST['path'] : $_POST['id'];
        $type = isNotEmpty($_POST['path']) ? RSArticleModes::PATH : RSArticleModes::ID;
        $caption = $_POST['caption'];
        $content = $_POST['content'];
        echo (new RSArticle($pathOrId, $type))->edit($caption, $content);
    }
    elseif($action == 'remove')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if(!isNotEmpty($_POST['path']) AND !isNotEmpty($_POST['id']))
            die('Не задан путь или id');
        $pathOrId = isNotEmpty($_POST['path']) ? $_POST['path'] : $_POST['id'];
        $type = isNotEmpty($_POST['path']) ? RSArticleModes::PATH : RSArticleModes::ID;
        echo (new RSArticle($pathOrId, $type))->remove();
    }
}
elseif ($mode == 'html_children')
{
    if($action == 'getArticle')
    {
        if (!(isNotEmpty($_POST['item_id'])))
            die('Не задан один из параметров');

        $item_id = $_POST['item_id'];

        echo (new RSArticle($item_id, RSArticleModes::HTML_CHILD_DATA))->toJSON();
    }
    elseif ($action == 'get')
    {
        if(!isNotEmpty($_POST['path']) AND !isNotEmpty($_POST['id']))
            die('Не задан путь или id');
        $pathOrId = isNotEmpty($_POST['path']) ? $_POST['path'] : $_POST['id'];
        $type = isNotEmpty($_POST['path']) ? RSArticleModes::PATH : RSArticleModes::ID;
        $arr = (new RSArticle($pathOrId, $type))->getHTMLChildren();
        echo json_encode($arr);
    }
    elseif ($action == 'add')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if(!isNotEmpty($_POST['path']) AND !isNotEmpty($_POST['id']))
            die('Не задан путь или id');
        if (!(isNotEmpty($_POST['HTMLelId'])))
            die('Не задан id элемента');

        $HTMLel = $_POST['HTMLelId'];
        $pathOrId = isNotEmpty($_POST['path']) ? $_POST['path'] : $_POST['id'];
        $type = isNotEmpty($_POST['path']) ? RSArticleModes::PATH : RSArticleModes::ID;
        echo (new RSArticle($pathOrId, $type))->addHTMLChild($HTMLel);
    }
    elseif ($action == 'remove')
    {
        if(!RSUser::isLoggedIn())
            die('Этот метод доступен только администратору справочной системы');
        if(!isNotEmpty($_POST['path']) AND !isNotEmpty($_POST['id']))
            die('Не задан путь или id');
        if (!(isNotEmpty($_POST['HTMLelId'])))
            die('Не задан один из параметров');

        $HTMLel = $_POST['HTMLelId'];
        $pathOrId = isNotEmpty($_POST['path']) ? $_POST['path'] : $_POST['id'];
        $type = isNotEmpty($_POST['path']) ? RSArticleModes::PATH : RSArticleModes::ID;
        echo (new RSArticle($pathOrId, $type))->removeHTMLChild($HTMLel);
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