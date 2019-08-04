<?php
include_once 'classes/ReferenceSystem.php';
if(isset($_POST['mode']) and $_POST['mode'] != '')
{
    if ($_POST['mode'] == 'getArticle' and isset($_POST['path']) and $_POST['path'] != '')
    {
        echo ReferenceSystem\ReferenceSystem::GetArticleByPath($_POST['path'])->toJSON();
    }
    elseif ($_POST['mode'] == 'getHTMLChildren' and isset($_POST['path']) and $_POST['path'] != '')
    {
        $arr = ReferenceSystem\ReferenceSystem::GetHTMLItemsOfReference($_POST['path']);
        echo json_encode($arr);
    }
}