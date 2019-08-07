<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ReferenceSystem.php';

if(isset($_POST['mode']))
{
    //Обработка запроса на добавление статьи
    if($_POST['mode'] == 'addReference')
    {
        if (isset($_POST['path_p1']) AND isset($_POST['path_p2']) AND isset($_POST['content'])
            AND $_POST['path_p1'] != '' AND $_POST['path_p2'] != '') {
            //Определяем следует добить главу или нечто иное
            $path = ($_POST['path_p1'] != '...') ? $_POST['path_p1'] . '/' . $_POST['path_p2'] : $_POST['path_p2'];
            $content = $_POST['content'];
            //Добавляем
            echo ReferenceSystem\ReferenceSystem::AddReference($path, $content);
        }
    }
    //Обработка запроса на редактирование статьи
    elseif ($_POST['mode'] == 'editReference')
    {
        if ($_POST['action'] == 'Редактировать')
        {
            if (isset($_POST['path']) AND isset($_POST['caption']) AND isset($_POST['content'])
                AND $_POST['path'] != '' AND $_POST['caption'] != '')
            {
                $path = $_POST['path'];
                $caption = $_POST['caption'];
                $content = $_POST['content'];
                ReferenceSystem\ReferenceSystem::EditReference($path,$caption, $content);
            }
        }
        elseif ($_POST['action'] == 'Удалить')
        {
            if (isset($_POST['path']) AND $_POST['path'] != '')
            {
                $path = $_POST['path'];
                ReferenceSystem\ReferenceSystem::RemoveReference($path);
            }
        }
    }
    elseif ($_POST['mode'] == 'getArticle' and isset($_POST['item_id']) and  $_POST['item_id'] != ''
        and isset($_POST['uniqueClass']) and isset($_POST['pathname']) and $_POST['pathname'] != '')
    {
        $arr = ReferenceSystem\ReferenceSystem::GetReferenceOfHTMLItem($_POST['item_id'],$_POST['uniqueClass'],$_POST['pathname']);
        echo json_encode($arr);
    }
    //Обработка запроса на редактирование списка HTML детей
    elseif ($_POST['mode'] == 'HTMLChildren')
    {
        if($_POST['action'] == 'Добавить')
        {
            if (isset($_POST['parent']) AND isset($_POST['HTMLelId'])
                AND $_POST['parent'] != '' AND $_POST['HTMLelId'] != ''
                AND isset($_POST['uniqueClass']) AND isset($_POST['pathname'])
                AND $_POST['pathname'] != '')
            {
                $path = $_POST['parent'];
                $HTMLel = $_POST['HTMLelId'];
                $uniqueClass = $_POST['uniqueClass'];
                $pathName = $_POST['pathname'];
                ReferenceSystem\ReferenceSystem::AddReferenceToHTMLItem($HTMLel,$uniqueClass,$pathName,$path);
            }
        }
        elseif ($_POST['action'] == 'Удалить')
        {
            if (isset($_POST['pathOrId']) AND isset($_POST['HTMLelId'])
                AND $_POST['pathOrId'] != '' AND $_POST['HTMLelId'] != ''
                AND isset($_POST['uniqueClass']) AND isset($_POST['pathname'])
                AND $_POST['pathname'] != '')
            {
                $path = $_POST['pathOrId'];
                $HTMLel = $_POST['HTMLelId'];
                $uniqueClass = $_POST['uniqueClass'];
                $pathName = $_POST['pathname'];
                ReferenceSystem\ReferenceSystem::RemoveReferenceOfHTMLItem($HTMLel,$uniqueClass,$pathName, $path);
            }
        }
    }


}