<?php
    include_once "classes/ReferenceSystem.php";
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Тест форм</title>
    <meta charset="UTF-8">
    <style type="text/css">
        html,
        body {
            height: 97%;
            margin: 0;
            padding: 5px;
        }
        #RefContainer {
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            border: 10px solid black;
            height: 50%;
            margin-left: 10px;
            margin-right: 10px;
            border-radius: 0px 0px 10px 10px;

        }
        #TableOfContentsContainer {
            overflow: auto;
            border: 3px solid black;
            width: 20%;
            height: 100%;
            padding-left: 5px;
            padding-right: 5px;
        }
        #Content {
            overflow: auto;
            border: 3px solid black;
            width:80%;
            height: 100%;
            padding: 0px 8px 0px 8px;
        }
        #Search {
            border-radius: 10px 10px 0px 0px;
            display: flex;
            justify-content: space-between;
            border: 3px solid black;
            margin-left: 10px;
            margin-right: 10px;
            padding: 8px 12px 2px 12px;
            background-color: black;
            text-align: right;
            vertical-align: center;
        }
        #TableOfContents {
            list-style-type: none;
            margin: 5px 0px 5px 0px;
            padding: 0px;

        }
        #TableOfContents li {
            border-bottom: 1px solid gray;
        }
        #notFoundLink {
            color: gray;
        }
        #TableOfContents ul {
            list-style-type: none;
        }
        #searchText {
            border-radius: 10px;
        }
        #searchButton {
            border-radius: 10px;
        }
        #searchButton:active {
            outline: none;
            border: none;
        }
        #searchButton:focus {
            outline:0;
        }
        a {
            color: black;
            text-decoration: none;
        }
        a:hover {
            color: black;
        }
        #helpHeader {
            color: white;
            font-size: 20px;
        }
    </style>
    <!-- Подключение модуля контекстных подсказок -->
    <link rel="stylesheet" href="/RS_ContextHelp/css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="/RS_ContextHelp/js/context_reference.js"></script>
</head>
<body>
    <!-- Когда-нибудь здесь будет выходная форма для тестов -->
    <div id="Search" class="RS_Help">
        <div id="helpHeader">
            Справка
        </div>
        <form>
            <span style="color: white">Поиск: </span>
            <input type="text" name="searchText" id="searchText">
            <input type="submit" value="Найти" id="searchButton">
        </form>
    </div>
    <div id="RefContainer" class="RS_Help class1 class2 RS_D1">
        <div id="TableOfContentsContainer">
            <ul id="TableOfContents">
                <?php
                $sch = ReferenceSystem\ReferenceSystem::GetReferenceScheme(array());
                for($i = 0; $i<count($sch); $i++)
                    PrintVertex($sch[$i]);
                ?>
            </ul>
        </div>
        <div id="Content">
            <?php
                if(isset($_GET['page_id']) and $_GET['page_id'] != '')
                {
                    $article = ReferenceSystem\ReferenceSystem::GetArticleByDBId(intval($_GET['page_id']));
                    echo "<h2>$article->Caption</h2>\r\n";
                    echo $article->Content;
                }
            ?>
        </div>
    </div>
</body>
</html>


<?php
/**
 * Крайне извращенная реализация обхода в грубину для вывода оглавления
 *
 * @param ReferenceSystem\ReferenceSchemeItem $Vertex
 */
function PrintVertex($Vertex)
{
    //Сохраняем текущий запрос
    $query = '';
    if(isset($_GET['searchText']) != '')
        $query = '&' . http_build_query(array_merge(array('searchText' => $_GET['searchText'])));

    //Если задан поисковый запрос, то проверяем каждую статью на содержание вхождения
    if(isset($_GET['searchText']) AND trim($_GET['searchText']) != '')
    {
        $search = trim($_GET['searchText']);
        $articles = ReferenceSystem\ReferenceSystem::SearchReference($search);
        $flag = false;
        if(count($articles) > 0)
        {
            for($i = 0; $i<count($articles) and !$flag; $i++)
                $flag = $articles[$i]->Id == $Vertex->Article->Id;
        }
        //В случае если статья не содержит вхождения, то окрашиваем заголовок в серый
        if($flag)
            echo '<li><a href="?page_id=' . $Vertex->Article->Id . $query . '">' . $Vertex->Article->Caption . '</a>' . "</li>\r\n";
        else
            echo '<li><a style="color: gray;" href="?page_id=' . $Vertex->Article->Id . $query . '">' .
                $Vertex->Article->Caption . '</a>' . "</li>\r\n";
    }
    else
        //Если не задан поисковый запрос
        if(isset($_GET['page_id']) and $_GET['page_id'] == $Vertex->Article->Id)
            echo '<li><a style="text-decoration: underline" href="?page_id=' . $Vertex->Article->Id . $query . '">' . $Vertex->Article->Caption . '</a>' . "</li>\r\n";
        else
            echo '<li><a href="?page_id=' . $Vertex->Article->Id . $query . '">' . $Vertex->Article->Caption . '</a>' . "</li>\r\n";
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        echo "<ul>\r\n";
        PrintVertex($Vertex->Children[$i]);
        echo "</ul>\r\n";
    }
}
?>