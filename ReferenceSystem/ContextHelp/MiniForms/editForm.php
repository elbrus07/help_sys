<?php

use ReferenceSystem\Modules\ReferenceSystem;

include_once(__DIR__ . '/../../vendor/autoload.php');

if(!(isset($_POST['aid']) and $_POST['aid'] != '' and  isset($_POST['aCaption']) and $_POST['aCaption'] != '' and
    isset($_POST['aContent']) AND isset($_POST['html_id']) and $_POST['html_id'] != '' AND isset($_POST['uniqueClass'])))
    die('error');
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Тест входных форм</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/ReferenceSystem/ContextHelp/MiniForms/css/editForm.css">
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <script src="/ReferenceSystem/ContextHelp/MiniForms/js/editForm.js"></script>
    <script>
        $(document).ready(function () {
            $('#eF_EditForm input[type=submit]').click(function (event) {
                event.preventDefault();
                let item = $(event.currentTarget);
                let hiddenAction = $('#eF_EditForm input[type=hidden][name="action"]');
                if(item.attr('value') === 'Редактировать')
                    hiddenAction.attr('value','edit');
                else
                    hiddenAction.attr('value','remove');
                sendFormOnServer('#eF_EditForm','/ReferenceSystem/API/api.php')
                    .done(function () {
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
                sendFormOnServer('#eF_EditForm2','/ReferenceSystem/API/api.php')
                    .done(function () {
                        location.reload();
                    });
            });
        });
    </script>
</head>
<body>
<div class="eF_ItemContainer">
    <div class="eF_ItemHeader">
        <span class="eF_ItemContainerHeaderText">Редактирование</span>
    </div>
    <form class="eF_ItemForm" id="eF_EditForm" method="POST">
        <input type="hidden" name="mode" value="article">
        <input type="hidden" name="action" value="">
        <input type="hidden" name="pathOrId" value="<?php echo $path = ReferenceSystem::IdToPath($_POST['aid']); ?>">
        <br>
        Путь:
        <p>
        <?php
            echo $path;
        ?>
        </p>
        <br>
        Заголовок:
        <br>
        <input type="text" name="caption" id="edit_caption" value="<?php echo $_POST['aCaption']; ?>">
        <br>
        <label for="edit_content">Содержимое:</label>
        <br>
        <textarea name="content" id="edit_content"><?php
                echo $_POST['aContent'];
                ?></textarea>
        <br>
        <div style="display: flex; justify-content: space-between;">
            <input type="submit" value="Редактировать" class="eF_mainButton">
            <input type="submit" value="Удалить" class="eF_mainButton">
        </div>
    </form>
    <form class="eF_ItemForm" id="eF_EditForm2" method="POST">
        <input type="submit" value="Отвязать элемент от статьи" class="eF_mainButton">
        <input type="hidden" name="mode" value="html_children">
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="pathOrId" value="<?php echo $_POST['aid']; ?>">
        <input type="hidden" name="HTMLelId" value="<?php echo $_POST['html_id']; ?>">
        <input type="hidden" name="uniqueClass" value="<?php echo $_POST['uniqueClass']; ?>">
    </form>
</div>

</body>
</html>


<?php
/**
 * Крайне извращенная реализация обхода в грубину для вывода оглавления
 *
 * @param \ReferenceSystem\Modules\ReferenceSchemeItem $Vertex
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