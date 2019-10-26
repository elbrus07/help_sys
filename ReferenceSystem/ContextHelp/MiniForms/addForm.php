<?php

use ReferenceSystem\Modules\ReferenceSchemeItem;
use ReferenceSystem\Modules\ReferenceSystem;

include_once(__DIR__ . '/../../vendor/autoload.php');

if(!isset($_POST['html_id']))
    die('error');
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>Тест входных форм</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= $scriptPath ?>/css/addForm.css">
    <script src="<?= $scriptPath ?>/../../vendor/tinymce/tinymce/tinymce.min.js"></script>
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
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <script src="<?= $scriptPath ?>/js/editForm.js"></script>
    <script>
        $(document).ready(function () {
            //Обработка клика на добавление статьи
            $('#aF_NewArticle').click(function (event) {
                event.preventDefault();
                $('#aF_AddContainer').css('display','block');
                $('.aF_Menu').css('display', 'none');
            });

            //Обработка клика на привязку к родителю
            $('#aF_NewParent').click(function (event) {
                event.preventDefault();
                $('#aF_HTMLChildrenContainer').css('display','block');
                $('.aF_Menu').css('display', 'none');
            });

            //Клик на добавление статьи
            $('#aF_AddContainer .aF_ItemForm input[type=submit]').click(function (event) {
                event.preventDefault();
                let dir = $('#RS_ScriptDir').attr('value');
                sendFormOnServer('#aF_AddContainer .aF_ItemForm',dir + '/../../API/api.php')
                    .done(function (article_id) {
                        $('<option>', {
                            value: article_id,
                            selected: true
                        }).appendTo($('#aF_HTMLChildrenContainer select'));

                        $('#aF_HTMLChildrenContainer .aF_ItemForm input[type=submit]').trigger('click');
                    });
            });

            $('#aF_HTMLChildrenContainer .aF_ItemForm input[type=submit]').click(function (event) {
                event.preventDefault();
                let dir = $('#RS_ScriptDir').attr('value');
                $('<input>', {
                    type: 'hidden',
                    name: 'pathname',
                    value: $(location).attr('pathname')
                }).appendTo($('#aF_HTMLChildrenContainer .aF_ItemForm'));
                sendFormOnServer('#aF_HTMLChildrenContainer .aF_ItemForm',dir + '/../../API/api.php')
                    .done(function () {
                        location.reload();
                    });
            });
        });
    </script>
</head>
<body>
<input type="hidden" id="RS_ScriptDir" value="<?= $scriptPath ?>">
<div class="aF_Menu">
    <a id="aF_NewArticle" href="/">Новая статья</a>
    <br>
    <a id="aF_NewParent" href="/">Привязать элемент к статье справки</a>
</div>
<!-- Контейнер для добавления материала -->
<div class="aF_ItemContainer" id="aF_AddContainer">
    <div class="aF_ItemHeader">
        <span class="aF_ItemContainerHeaderText">Добавление материала</span>
    </div>
    <form class="aF_ItemForm" method="POST">
        <input type="hidden" name="mode" value="article">
        <input type="hidden" name="action" value="add">
        <select name="path_p1">
            <option>...</option>
            <?php
            $sch = ReferenceSystem::GetReferenceScheme(array());
            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i], false);
            ?>
        </select>
        /
        <input type="text" name="path_p2">
        <br>
        <label for="input_content">Содержимое</label>
        <br>
        <textarea name="content" id="input_content"></textarea>
        <br>
        <input type="submit" value="Добавить" class="aF_mainButton">
    </form>
</div>
<!-- Контейнер для привязки к родителю -->
<div class="aF_ItemContainer" id="aF_HTMLChildrenContainer">
    <div class="aF_ItemHeader">
        Привязать родителя
    </div>
    <form class="aF_ItemForm" method="POST">
        <input type="hidden" name="mode" value="html_children">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="HTMLelId" value="<?php echo $_POST['html_id']; ?>">
        Родитель:
        <select name="id">
            <?php
            $sch = ReferenceSystem::GetReferenceScheme(array());
            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i]);
            ?>
        </select>
        <br>
        <input type="submit" value="Привязать"  class="aF_mainButton">
    </form>
</div>
</body>
</html>


<?php
/**
 * Крайне извращенная реализация обхода в грубину для вывода оглавления
 *
 * @param ReferenceSchemeItem $Vertex
 * @param bool $withTag
 */
function PrintVertex($Vertex, $withTag = true)
{
    if($withTag)
        echo '<option value="'. $Vertex->Article->getId() .'">' . $Vertex->Article->getPath() . '</option>';
    else
        echo '<option>' . $Vertex->Article->getPath() . '</option>';
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        PrintVertex($Vertex->Children[$i],$withTag);
    }
}
?>