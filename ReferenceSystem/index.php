<?php
use ReferenceSystem\Modules\ReferenceSchemeItem;
use ReferenceSystem\Modules\ReferenceSystem;
use ReferenceSystem\Modules\RSArticle;
use ReferenceSystem\Modules\RSArticleModes;

include_once(__DIR__ . '/vendor/autoload.php');
?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>Тест форм</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#downloadButton').click(function (event) {
                event.preventDefault();
                let el = $('#downloadMenuContainer');
                let sum =parseInt($('.ItemHeader').css('height')) + 21;
                el.css('top',sum+"px");
                if(el.css('display') !== 'none')
                    el.css('display', 'none');
                else
                    el.css('display', 'block');

            });
        });
    </script>
</head>
<body>
    <div class="ItemContainer">
        <div class="ItemHeader" id="header">
            <span class="ItemHeaderText">
                Справка
            </span>
            <div id="headerItemContainer">
                <form class="headerItem">
                    <label for="searchText"><span style="color: white">Поиск: </span></label>
                    <input type="text" name="searchText" id="searchText" value="<?= (isset($_GET['searchText'])) ? $_GET['searchText'] : '' ?>">
                    <input type="submit" value="Найти" id="searchButton">
                </form>
                <div id="downloadButton" class="headerItem"></div>
            </div>
        </div>
        <div id="RefContainer">
            <div id="TableOfContentsContainer">
                <ul id="TableOfContents">
                    <?php
                    $sch = ReferenceSystem::GetReferenceScheme(array());
                    for($i = 0; $i<count($sch); $i++)
                        PrintVertex($sch[$i]);
                    ?>
                </ul>
            </div>
            <div id="Content">
                <?php
                if(isset($_GET['page_id']) and $_GET['page_id'] != '')
                {
                    $article = (new RSArticle(intval($_GET['page_id']), RSArticleModes::ID))->getArticle();
                    echo "<h2>" . $article['Caption'] . "</h2>\r\n";
                    echo $article['Content'];
                }
                ?>
            </div>
        </div>
    </div>
    <div id="downloadMenuContainer">
        <ul>
            <li class="downloadMenuItem"><a href="<?="download.php?mode=all"?>" target="_blank">Всю справку</a></li>
            <li class="downloadMenuItem"><a href="<?=(isset($_GET['page_id'])) ? "download.php?mode=this&article_id=".$_GET['page_id']
                : '#'?>" target="_blank">Данную статью</a></li>
            <li class="downloadMenuItem"><a href="<?=(isset($_GET['page_id'])) ? "download.php?mode=branch&article_id=".$_GET['page_id']
                : '#'?>" target="_blank">Статью и подстатьи</a></li>
        </ul>
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
    $caption = $Vertex->Article->getCaption();
    $id = $Vertex->Article->getId();
    //Сохраняем текущий запрос
    $query = '';
    if(isset($_GET['searchText']) != '')
        $query = '&' . http_build_query(array_merge(array('searchText' => $_GET['searchText'])));
    //Если задан поисковый запрос, то проверяем каждую статью на содержание вхождения
    if(isset($_GET['searchText']) AND trim($_GET['searchText']) != '')
    {
        $search = trim($_GET['searchText']);
        $articles = ReferenceSystem::SearchReference($search);
        $flag = false;
        if(count($articles) > 0)
        {
            for($i = 0; $i<count($articles) and !$flag; $i++)
                $flag = $articles[$i]->getId() == $id;
        }
        //В случае если статья не содержит вхождение, то окрашиваем заголовок в серый
        if($flag)
            echo '<li><a href="?page_id=' . $id . $query . '">' . $caption . '</a>' . "</li>\r\n";
        else
            echo '<li><a style="color: gray;" href="?page_id=' . $id . $query . '">' .
                $caption . '</a>' . "</li>\r\n";
    }
    else
        //Если не задан поисковый запрос
        if(isset($_GET['page_id']) and $_GET['page_id'] == $id)
            echo '<li><a style="text-decoration: underline" href=?page_id="' . $id . $query . '">' . $caption . '</a>' . "</li>\r\n";
        else
            echo '<li><a href="?page_id=' . $id . $query . '">' . $caption . '</a>' . "</li>\r\n";
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        echo "<ul>\r\n";
        PrintVertex($Vertex->Children[$i]);
        echo "</ul>\r\n";
    }
}
?>