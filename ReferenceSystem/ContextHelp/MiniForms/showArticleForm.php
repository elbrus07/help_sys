<?php

use ReferenceSystem\Modules\ReferenceSchemeItem;
use ReferenceSystem\Modules\ReferenceSystem;

include_once(__DIR__ . '/../../vendor/autoload.php');
?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>Справка</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/ReferenceSystem/ContextHelp/MiniForms/css/showArticleForm.css">
    <script type="text/javascript">
        $(document).ready(function () {
            //Отрубаем обработчики событий (чтобы они не накладывались друг на друга)
            $(document).off('click', '#RS_TableOfContentsButton');
            $(document).off('click', '#RS_TableOfContents li a');
            $(document).off('click', '#RS_searchButton');

            $(document).on('click', '#RS_TableOfContentsButton', function (event) {
                event.preventDefault();
                let btn = $(event.currentTarget);
                let toc = $('#RS_TableOfContentsContainer');
                let content = $('#RS_Content');
                if(toc.css('display') === 'none') {
                    content.css('display','none');
                    toc.css('display','block');
                    btn.html('Текст статьи');
                } else {
                    toc.css('display','none');
                    content.css('display','block');
                    btn.html('Оглавление');
                }
            });

            $(document).on('click', '#RS_TableOfContents li a', function (event) {
                event.preventDefault();
                let aid = $(event.currentTarget).attr('href');
                $('#RS_DataContainer').load("/ReferenceSystem/ContextHelp/MiniForms/showArticleForm.php #RS_Search,#RS_RefContainer", {
                    'page_id': aid
                });
            });

            $(document).on('click', '#RS_searchButton', function (event) {
                event.preventDefault();
                let aid = $('#RS_TableOfContents li a[style="text-decoration: underline"]').attr('href');
                $('#RS_DataContainer').load("/ReferenceSystem/ContextHelp/MiniForms/showArticleForm.php #RS_Search,#RS_RefContainer", {
                    'page_id': aid,
                    'searchText': $('#RS_searchText').val()
                }, function () {
                    $('#RS_TableOfContentsButton').trigger('click');
                });
            });

        });
    </script>
</head>
<body>
<div id="RS_DataContainer">
    <div id="RS_Search" class="RS_Help">
            <a href="/" id="RS_TableOfContentsButton">Оглавление</a>
        <form>
            <label for="RS_searchText"><span style="color: white">Поиск: </span></label>
            <input type="text" name="searchText" id="RS_searchText">
            <input type="submit" value="Найти" id="RS_searchButton">
        </form>
    </div>
    <div id="RS_RefContainer" class="RS_Help class1 class2 RS_D1">
        <div id="RS_TableOfContentsContainer">
            <ul id="RS_TableOfContents">
                <?php
                $sch = ReferenceSystem::GetReferenceScheme(array());
                for($i = 0; $i<count($sch); $i++)
                    PrintVertex($sch[$i]);
                ?>
            </ul>
        </div>
        <div id="RS_Content">
            <?php
            if(isset($_POST['page_id']) and $_POST['page_id'] != '')
            {
                $article = ReferenceSystem::GetArticleByDBId(intval($_POST['page_id']));
                echo "<h2>$article->Caption</h2>\r\n";
                echo $article->Content;
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>


<?php
/**
 * Крайне извращенная реализация обхода в грубину для вывода оглавления
 *
 * @param ReferenceSchemeItem $Vertex
 */
function PrintVertex($Vertex)
{
    //Сохраняем текущий запрос
    $query = '';
    if(isset($_POST['searchText']) != '')
        $query = '&' . http_build_query(array_merge(array('searchText' => $_POST['searchText'])));

    //Если задан поисковый запрос, то проверяем каждую статью на содержание вхождения
    if(isset($_POST['searchText']) AND trim($_POST['searchText']) != '')
    {
        $search = trim($_POST['searchText']);
        $articles = ReferenceSystem::SearchReference($search);
        $flag = false;
        if(count($articles) > 0)
        {
            for($i = 0; $i<count($articles) and !$flag; $i++)
                $flag = $articles[$i]->Id == $Vertex->Article->Id;
        }
        //В случае если статья не содержит вхождения, то окрашиваем заголовок в серый
        if($flag)
            echo '<li><a href="' . $Vertex->Article->Id . $query . '">' . $Vertex->Article->Caption . '</a>' . "</li>\r\n";
        else
            echo '<li><a style="color: gray;" href="' . $Vertex->Article->Id . $query . '">' .
                $Vertex->Article->Caption . '</a>' . "</li>\r\n";
    }
    else
        //Если не задан поисковый запрос
        if(isset($_POST['page_id']) and $_POST['page_id'] == $Vertex->Article->Id)
            echo '<li><a style="text-decoration: underline" href="' . $Vertex->Article->Id . $query . '">' . $Vertex->Article->Caption . '</a>' . "</li>\r\n";
        else
            echo '<li><a href="' . $Vertex->Article->Id . $query . '">' . $Vertex->Article->Caption . '</a>' . "</li>\r\n";
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        echo "<ul>\r\n";
        PrintVertex($Vertex->Children[$i]);
        echo "</ul>\r\n";
    }
}
?>