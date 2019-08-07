<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/classes/ReferenceSystem.php";

if(!(isset($_POST['aid']) and $_POST['aid'] != '' and  isset($_POST['aCaption']) and $_POST['aCaption'] != '' and
    isset($_POST['aContent']) AND isset($_POST['html_id']) and $_POST['html_id'] != '' AND isset($_POST['uniqueClass'])))
    die('error');
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Тест входных форм</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/RS_ContextHelp/RS_MiniForms/css/editForm.css">
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <script src="/RS_ContextHelp/RS_MiniForms/js/editForm.js"></script>
    <script>
        $(document).ready(function () {
            $('#eF_EditForm input[type=submit]').click(function (event) {
                event.preventDefault();
                $('<input>', {
                    type: 'hidden',
                    name: 'action',
                    value: $(event.currentTarget).attr('value')
                }).appendTo($('#eF_EditForm'));
                sendFormOnServer('#eF_EditForm','/RS_ContextHelp/RS_MiniForms/miniFormsActions.php')
                    .done(function (result) {
                        location.reload();
                    });
            });

            $('#eF_EditForm2 input[type=submit]').click(function (event) {
                event.preventDefault();
                $('<input>', {
                    type: 'hidden',
                    name: 'pathname',
                    value: $(location).attr('pathname')
                }).appendTo($('#eF_EditForm2'));
                sendFormOnServer('#eF_EditForm2','/RS_ContextHelp/RS_MiniForms/miniFormsActions.php')
                    .done(function (result) {
                        location.reload();
                    });
            });
        });
    </script>
</head>
<body>
<div class="eF_ItemContainer">
    <div class="eF_ItemHeader">
        Редактирование
    </div>
    <form class="eF_ItemForm" id="eF_EditForm" method="POST" action="/RS_ContextHelp/RS_MiniForms/miniFormsActions.php">
        <input type="hidden" name="mode" value="editReference">
        <input type="hidden" name="path" value="<?php echo $path = ReferenceSystem\ReferenceSystem::IdToPath($_POST['aid']); ?>">
        <br>
        Путь:
        <p>
        <?php
            echo $path;
        ?>
        </p>
        <br>
        Заголовок: <input type="text" name="caption" id="edit_caption" value="<?php echo $_POST['aCaption']; ?>">
        <br>
        <label for="edit_content">Содержимое</label>
        <br>
        <textarea name="content" id="edit_content"><?php
                echo $_POST['aContent'];
                ?></textarea>
        <br>
        <div style="display: flex; justify-content: space-between;">
            <input type="submit" value="Редактировать">
            <input type="submit" value="Удалить">
        </div>
    </form>
    <form class="eF_ItemForm" id="eF_EditForm2" method="POST" action="/RS_ContextHelp/RS_MiniForms/miniFormsActions.php">
        <input type="submit" value="Отвязать элемент от статьи">
        <input type="hidden" name="mode" value="HTMLChildren">
        <input type="hidden" name="action" value="Удалить">
        <input type="hidden" name="pathOrId" value="<?php echo $_POST['aid']; ?>">
        <input type="hidden" name="HTMLelId" value="<?php echo $_POST['html_id']; ?>">
        <input type="hidden" name="uniqueClass" value="<?php echo $_POST['uniqueClass']; ?>">
    </form>
    <!--<form id="ItemForm" method="POST">
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
        <input type="text" name="HTMLelName">
        <input type="submit" value="Добавить" name="action">
    </form>-->
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