<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/classes/ReferenceSystem.php";

if(!(isset($_POST['html_id']) and $_POST['html_id'] != '' AND isset($_POST['uniqueClass'])))
    die('error');
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Тест входных форм</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/RS_ContextHelp/RS_MiniForms/css/addForm.css">
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <script src="/RS_ContextHelp/RS_MiniForms/js/editForm.js"></script>
    <script>
        $(document).ready(function () {
            $('#aF_NewArticle').click(function (event) {
                event.preventDefault();
                $('#aF_AddContainer').css('display','block');
                $('.aF_Menu').css('display', 'none');
            });

            $('#aF_NewParent').click(function (event) {
                event.preventDefault();
                $('#aF_HTMLChildrenContainer').css('display','block');
                $('.aF_Menu').css('display', 'none');
            });

            $('#aF_AddContainer .aF_ItemForm input[type=submit]').click(function (event) {
                event.preventDefault();
                sendFormOnServer('#aF_AddContainer .aF_ItemForm','/RS_ContextHelp/RS_MiniForms/miniFormsActions.php')
                    .done(function (article_id) {
                        let html_id = $('#aF_HTML_ID').attr('value');
                        $.ajax({
                            method: 'POST',
                            url: '/RS_ContextHelp/RS_MiniForms/miniFormsActions.php',
                            data: {
                                'mode': 'HTMLChildren',
                                'action': 'Добавить',
                                'HTMLelId': html_id,
                                'uniqueClass': $('#aF_uniqueClass').attr('value'),
                                // путь файлу (т.к. мы подключаем этот js, то путь будет к фалу, к которому
                                // подключен js)
                                'pathname': $(location).attr('pathname'),
                                'parent': article_id
                            }
                        });
                        location.reload();
                    });
            });

            $('#aF_HTMLChildrenContainer .aF_ItemForm input[type=submit]').click(function (event) {
                event.preventDefault();
                $('<input>', {
                    type: 'hidden',
                    name: 'pathname',
                    value: $(location).attr('pathname')
                }).appendTo($('#aF_HTMLChildrenContainer .aF_ItemForm'));
                sendFormOnServer('#aF_HTMLChildrenContainer .aF_ItemForm','/RS_ContextHelp/RS_MiniForms/miniFormsActions.php')
                    .done(function () {
                        location.reload();
                    });
            });
        });
    </script>
</head>
<body>
<div class="aF_Menu">
    <a id="aF_NewArticle" href="/">Новая статья</a>
    <br>
    <a id="aF_NewParent" href="/">Привязать элемент к статье справки</a>
</div>
<input type="hidden" id="aF_HTML_ID" value="<?php echo $_POST['html_id']; ?>">
<input type="hidden" id="aF_uniqueClass" value="<?php echo $_POST['uniqueClass']; ?>">
<!-- Контейнер для добавления материала -->
<div class="aF_ItemContainer" id="aF_AddContainer">
    <div class="aF_ItemHeader">
        Добавление материала
    </div>
    <form class="aF_ItemForm" method="POST" action="/RS_ContextHelp/RS_MiniForms/miniFormsActions.php">
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
        <br>
        <label for="input_content">Содержимое</label>
        <br>
        <textarea name="content" id="input_content"></textarea>
        <br>
        <input type="submit" value="Добавить">
    </form>
</div>
<!-- Контейнер для привязки к родителю -->
<div class="aF_ItemContainer" id="aF_HTMLChildrenContainer">
    <div class="aF_ItemHeader">
        Привязать родителя
    </div>
    <form class="aF_ItemForm" method="POST" action="/RS_ContextHelp/RS_MiniForms/miniFormsActions.php">
        <input type="hidden" name="mode" value="HTMLChildren">
        <input type="hidden" name="action" value="Добавить">
        <input type="hidden" name="HTMLelId" value="<?php echo $_POST['html_id']; ?>">
        <input type="hidden" name="uniqueClass" value="<?php echo $_POST['uniqueClass']; ?>">
        Родитель:
        <select name="parent">
            <?php
            $sch = ReferenceSystem\ReferenceSystem::GetReferenceScheme(array());
            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i], 0);
            ?>
        </select>
        <br>
        <input type="submit" value="Привязать">
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