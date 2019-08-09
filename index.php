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
        }
        .ItemContainer
        {
            background-color: #bad0f1;
            box-shadow: inset 0px 4px 9px 0px rgba(0, 0, 0, 0.35);
            border-radius: 20px;
            margin: 10px 30px 10px 30px;
            padding: 0 0 10px 0;
            height: 600px;
        }

        .ItemHeader {
            border-radius: 20px 20px 0px 0px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid gray;
            background-color: rgb(106, 160, 240);
            text-align: right;
            vertical-align: center;
            padding: 10px 18px 10px 18px;
            height: 50px;
        }

        .ItemHeaderText {
            font-family: "Segoe UI", sans-serif;
            font-size: 24pt;
            color: white;
        }

        #RefContainer {
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            border: 1px solid gray;
            border-radius: 0px 0px 20px 20px;
            height: 538px;

        }
        #TableOfContentsContainer {
            font-family: "Segoe UI", sans-serif;
            font-size: 16pt;
            overflow: auto;
            width: 20%;
            padding-left: 5px;
            padding-right: 5px;
            border-right: 1px solid gray;
        }
        #Content {
            overflow: auto;
            width:80%;
            padding: 0px 8px 0px 8px;
            border-left: 1px solid gray;
        }
        #TableOfContents {
            list-style-type: none;
            margin: 5px 0px 5px 0px;
            padding: 0px;
        }
        #TableOfContents li {
            border-bottom: 1px solid gray;
        }
        #TableOfContents ul {
            list-style-type: none;
        }
        #searchText {
            border: 1px solid gray;
            border-radius: 10px;
            background-color: white;
            box-shadow: inset 0px 0px 6.75px 2.25px rgba(0, 0, 0, 0.23);
            padding: 3px 10px 3px 10px;
            height: 20px;
        }
        #searchButton {
            border-radius: 10px;
            border: 1px solid gray;
            background-color: white;
            height: 28px;
            padding: 3px 10px 3px 10px;
            cursor: pointer;
        }
        #searchButton:active {
            outline: none;
        }
        #searchButton:focus,#searchText:focus {
            outline:0;
            border-color: black;
        }
        a {
            color: black;
            text-decoration: none;
        }
        a:hover {
            color: black;
        }
    </style>
</head>
<body>
    <!-- Когда-нибудь здесь будет выходная форма для тестов -->
    <div class="ItemContainer">
        <div class="ItemHeader" id="header">
            <span class="ItemHeaderText">
                Справка
            </span>
            <form>
                <span style="color: white">Поиск: </span>
                <input type="text" name="searchText" id="searchText">
                <input type="submit" value="Найти" id="searchButton">
            </form>
        </div>
        <div id="RefContainer">
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