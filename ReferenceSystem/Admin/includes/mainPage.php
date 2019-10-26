<?php

use ReferenceSystem\Modules\RSUser;
use ReferenceSystem\Modules\ReferenceSystem;

include_once(__DIR__ . '/../../vendor/autoload.php');

if (!RSUser::isLoggedIn())
    die('Вы не авторизованы');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора справочной системы</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <script src="../vendor/tinymce/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            let path = $('#RS_ScriptDir').attr('value');
            tinymce.init({
                selector: '#input_content, #edit_content',
                language: 'ru',
                plugins: 'image, link, lists, autosave',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | numlist bullist | image link | restoredraft',
                images_upload_url: path + '/../images/imageUpload.php',
                images_upload_base_path: path + '/../images',
                relative_urls: false,
                remove_script_host: false,
                image_uploadtab: true,
                block_formats: 'Paragraph=p;Preformatted=pre',
                style_formats: [
                    {title: 'Heading 1', block : 'div', styles : {color : '#000000', 'font-size': '16pt', 'font-weight': 'bold' }},
                    {title: 'Heading 2', block : 'div', styles : {color : '#000000', 'font-size': '15pt', 'font-weight': 'bold' }},
                    {title: 'Heading 3', block : 'div', styles : {color : '#000000', 'font-size': '14pt', 'font-weight': 'bold' }},
                    {title: 'Heading 4', block : 'div', styles : {color : '#000000', 'font-size': '13pt', 'font-weight': 'bold' }},
                    {title: 'Heading 5', block : 'div', styles : {color : '#000000', 'font-size': '12pt', 'font-weight': 'bold' }},
                    {title: 'Heading 6', block : 'div', styles : {color : '#000000', 'font-size': '12pt', 'font-weight': 'normal' }},
                    {title: 'paragraph', block: 'p', styles : {color : '000000', 'font-size': '12pt', 'font-weight': 'normal'}}
                ],
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });

        });
    </script>

    <script type="text/javascript" src="js/admin_index.js"></script>
</head>
<body>
<header>
    <div id="header">
        <span id="headerText">Панель администратора справочной системы</span>
        <span id="LogoutButton"><a href="?action=logout">Выйти</a></span>
    </div>
</header>
<input type="hidden" id="RS_ScriptDir" value="<?= dirname($_SERVER['SCRIPT_NAME']) ?>">
<!-- Добавление материала -->
<div class="ItemContainer">
    <div class="ItemContainerHeader">
        <span class="ItemContainerHeaderText">Добавление материала</span>
    </div>
    <form class="Form" id="AddDataForm" method="POST">
        <input type="hidden" name="mode" value="article">
        <input type="hidden" name="action" value="add">
        Путь:
        <select name="path_p1">
            <option>...</option>
            <?php
            $sch = ReferenceSystem::GetReferenceScheme(array());

            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i]);
            ?>
        </select>
        /
        <input type="text" name="path_p2">
        <br>
        <label for="input_content">Содержимое:</label>
        <br>
        <textarea name="content" id="input_content"></textarea>
        <br>
        <input type="submit" value="Добавить" class="mainButton">
    </form>
</div>
<!-- Редатирование статьи -->
<div class="ItemContainer">
    <div class="ItemContainerHeader">
        <span class="ItemContainerHeaderText">Редактирование материала</span>
    </div>
    <form class="Form" id="EditDataForm" method="POST">
        <input type="hidden" name="mode" value="article">
        <input type="hidden" name="action" value="">
        Путь:
        <select id="editPath" name="path">
            <option hidden disabled selected value>Выберите статью</option>
            <?php
            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i]);
            ?>
        </select>
        <br>
        <label for="edit_caption">Заголовок:</label>
        <input type="text" name="caption" id="edit_caption">
        <br>
        <label for="edit_content">Содержимое:</label>
        <br>
        <textarea name="content" id="edit_content"></textarea>
        <br>
        <div style="display: flex; justify-content: space-between;">
            <input type="submit" value="Редактировать" class="mainButton">
            <input type="submit" value="Удалить" class="mainButton">
        </div>
        <br>
    </form>
    <!-- Связанные статьи -->
    <form class="Form" id="HTMLChildrenForm" method="POST">
        <input type="hidden" name="mode" value="html_children">
        <input type="hidden" name="action" value="">
        <input type="hidden" name="path" id="hidden_ep" value="">
        <label for="HTMLChildrenList">Список id HTML элементов, связанных со статьей:</label>
        <br>
        <select id="HTMLChildrenList" name="HTMLChildrenList">
        </select>
        <input type="submit" value="Удалить" class="Button">
        <br>
        Id HTML элемента для добавления:
        <br>
        <input type="text" name="HTMLelId">
        <br>
        <input type="submit" value="Добавить" class="Button">
    </form>
</div>

<footer>
    <div id="footer">
        <div id="footerText">(c) Emperator12@NOSU. 2019 year.</div>
    </div>
</footer>
</body>
</html>

<?php
/**
 * Крайне извращенная реализация обхода в грубину для вывода оглавления
 *
 * @param \ReferenceSystem\Modules\ReferenceSchemeItem $Vertex
 */
function PrintVertex($Vertex)
{
    echo '<option>' . $Vertex->Article->getPath() . '</option>';
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        PrintVertex($Vertex->Children[$i]);
    }
}
?>