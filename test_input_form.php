<?php
include_once "classes/ReferenceSystem.php";
?>
<?php
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
                ReferenceSystem\ReferenceSystem::AddReference($path, $content);
            }
        }
        //Обработка запроса на редактирование статьи
        elseif ($_POST['mode'] == 'editReference')
        {
            if ($_POST['action'] == 'Редактировать')
            {
                if (isset($_POST['path']) AND isset($_POST['caption']) AND isset($_POST['content'])
                    AND $_POST['path'] != '' AND $_POST['caption'] != '' AND $_POST['content']!='')
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
        elseif ($_POST['mode'] == 'HTMLChildren')
        {
            if($_POST['action'] == 'Добавить')
            {
                if (isset($_POST['hidden_ep']) AND isset($_POST['HTMLelId'])
                    AND $_POST['hidden_ep'] != '' AND $_POST['HTMLelId'] != '' AND isset($_POST['uniqueClass'])
                    AND isset($_POST['pathname']) AND $_POST['pathname'] != '')
                {
                    $path = $_POST['hidden_ep'];
                    $HTMLel = $_POST['HTMLelId'];
                    $uniqueClass = $_POST['uniqueClass'];
                    $pathName = $_POST['pathname'];
                    ReferenceSystem\ReferenceSystem::AddReferenceToHTMLItem($HTMLel,$uniqueClass,$pathName,$path);
                }
            }
            elseif ($_POST['action'] == 'Удалить')
            {
                if (isset($_POST['hidden_ep']) AND isset($_POST['HTMLChildrenList'])
                    AND $_POST['hidden_ep'] != '' AND $_POST['HTMLChildrenList'] != '')
                {
                    $path = $_POST['hidden_ep'];
                    $arr = explode('||',$_POST['HTMLChildrenList']);
                    $HTMLel = $arr[0];
                    $uniqueClass = $arr[1];
                    $pathName = $arr[2];
                    ReferenceSystem\ReferenceSystem::RemoveReferenceOfHTMLItem($HTMLel,$uniqueClass,$pathName,$path);
                }
            }
        }


    }


?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Тест входных форм</title>
    <meta charset="UTF-8">
    <style type="text/css">
        html,
        body {
            height: 97%;
            margin: 0;
            padding: 0;
        }
        #ItemContainer {

            border: 3px solid black;
            margin: 10px 5px 10px 5px;
            padding: 20px 0px 20px 0px;
            height: 40%;
            overflow: auto;

        }
        #ItemHeader {
            border-bottom: 3px solid black;
            text-align: center;
            vertical-align: center;
            font-size: 35px;
            font-weight: bold;
            height: 15%;
        }
        #ItemForm {
            padding: 5px;
            margin: 5px;
            height: 85%;
            overflow: hidden;
        }
        #ItemForm select, #ItemForm input, #ItemForm textarea
        {
            margin-bottom: 3px;
        }
        #ItemForm textarea {
            width: 99%;
            height: 60%;
        }

    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            let ep = $('#editPath');
            ep.change(function () {
                $('#hidden_ep').val(ep.val());
                //Свойства статьи
                $.ajax({
                    url: 'input_ajax.php',
                    method: 'POST',
                    data: {
                        'mode': 'getArticle',
                        'path': ep.val()
                    }
                })
                .done(function (result) {
                    result = JSON.parse(result);
                    $('#edit_content').val(result.Content);
                    $('#edit_caption').val(result.Caption);
                })
                .fail(function () {
                    $('#edit_content').val = 'Ошибка';
                    });
                //Список HTML элементов, связанных со статьей
                $.ajax({
                    url: 'input_ajax.php',
                    method: 'POST',
                    data: {
                        'mode': 'getHTMLChildren',
                        'path': ep.val()
                    }
                })
                    .done(function (result) {
                        result = JSON.parse(result);
                        for (let i in result.element_id)
                            $('#HTMLChildrenList').append('<option value="'+ result.element_id[i] +
                                '||' + result.uniqueClass[i] + '||' + result.pathname[i] +
                                '">'+result.element_id[i]+'</option>');
                    })
                    .fail(function () {

                    });
            });
        });

    </script>
</head>
<body>
    <!-- Форма добавления материала -->
    <div id="ItemContainer">
        <div id="ItemHeader">
            Добавление материала
        </div>
        <form id="ItemForm" method="POST">
            <input type="hidden" name="mode" value="addReference">
            Путь:
            <select name="path_p1">
                <option>...</option>
                <?php
                $sch = ReferenceSystem\ReferenceSystem::GetReferenceScheme(array());
                for($i = 0; $i<count($sch); $i++)
                    PrintVertex($sch[$i], 0);
                ?>
            </select>
            /
            <input type="text" name="path_p2">
            <!--<br> На не нужен заголовок, т.к. он совпадает с последним элементом пути
            Заголовок <input type="text" name="caption">-->
            <br>
            <label for="input_content">Содержимое</label>
            <br>
            <textarea name="content" id="input_content"></textarea>
            <br>
            <input type="submit" value="Добавить">
        </form>
    </div>
    <!-- Редактироование материала -->
    <div id="ItemContainer">
        <div id="ItemHeader">
            Редактирование
        </div>
        <form id="ItemForm" method="POST">
            <input type="hidden" name="mode" value="editReference">
            Путь:
            <select id="editPath" name="path">
                <option hidden disabled selected value>Выберите статью</option>
                <?php
                for($i = 0; $i<count($sch); $i++)
                    PrintVertex($sch[$i], 0);
                ?>
            </select>
            <br>
            Заголовок <input type="text" name="caption" id="edit_caption">
            <br>
            <label for="edit_content">Содержимое</label>
            <br>
            <textarea name="content" id="edit_content"></textarea>
            <br>
            <div style="display: flex; justify-content: space-between;">
                <input type="submit" value="Редактировать" name="action">
                <input type="submit" value="Удалить" name="action">
            </div>
            <br>
        </form>
        <!-- Связанные статьи -->
        <form id="ItemForm" method="POST">
            <input type="hidden" name="mode" value="HTMLChildren">
            <input type="hidden" name="hidden_ep" id="hidden_ep" value="">
            Список id HTML элементов, связанных со статьей:
            <br>
            <select id="HTMLChildrenList" name="HTMLChildrenList">

            </select>
            <input type="submit" value="Удалить" name="action">
            <br>
            Id HTML элемента для добавления:
            <br>
            <input type="text" name="HTMLelId">
            <br>
            Уникальный класс (если Id элемента не единственный на странице<br>
            в формате RS_D&lt;число&gt;:
            <br>
            <input type="text" name="uniqueClass" placeholder="RS_D">
            <br>
            Путь к файлу, содержащий HTML элемент<br>
            (напр.: /, или /subfolder/subsubfolder/file.php, или /file.php):
            <br>
            <input type="text" name="pathname" value="/">
            <br>
            <input type="submit" value="Добавить" name="action">
        </form>
    </div>
</body>
</html>



<?php
/**
 * Крайне извращенная реализация обхода в грубину для вывода оглавления
 *
 * @param ReferenceSystem\ReferenceSchemeItem $Vertex
 * @param int $depth глубина, по молчанию ноль
 */
function PrintVertex($Vertex, $depth)
{
    echo '<option>' . $Vertex->Path . '</option>';
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        PrintVertex($Vertex->Children[$i],$depth+1);
    }
}
?>