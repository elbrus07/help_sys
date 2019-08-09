<?php

use ReferenceSystem\RSUser;

include_once ($_SERVER['DOCUMENT_ROOT'].'/classes/RSUser.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'/classes/ReferenceSystem.php');

if (!RSUser::isLoggedIn())
    die('Вы не авторизованы');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора справочной системы</title>
    <link rel="stylesheet" href="/RS_Admin/css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/admin_index.js"></script>
</head>
<body>
<header>
    <div id="header">
        <span id="headerText">Панель администратора справочной системы</span>
        <span id="LogoutButton"><a href="/RS_Admin/?action=logout">Выйти</a></span>
    </div>
</header>
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
            $sch = ReferenceSystem\ReferenceSystem::GetReferenceScheme(array());

            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i], 0);
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
        <select id="editPath" name="pathOrId">
            <option hidden disabled selected value>Выберите статью</option>
            <?php
            for($i = 0; $i<count($sch); $i++)
                PrintVertex($sch[$i]);
            ?>
        </select>
        <br>
        Заголовок: <input type="text" name="caption" id="edit_caption">
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
        <input type="hidden" name="pathOrId" id="hidden_ep" value="">
        <label for="HTMLChildrenList">Список id HTML элементов, связанных со статьей:</label>
        <br>
        <select id="HTMLChildrenList" name="HTMLChildrenList">
            <option disabled selected value></option>
        </select>
        <input type="submit" value="Удалить" class="Button">
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
        <input type="submit" value="Добавить" class="Button">
    </form>
</div>
<!--
<?php
if(!RSUser::isLoggedIn())
    echo '
    <form method="POST">
        <input type="hidden" name="action" value="login">
        Для продолжения работы необходимо войти в систему
        <br>
        <label for="login_field">Логин:</label>
        <input type="text" name="username" id="login_field">
        <br>
        <label for="login_field">Пароль</label>
        <input type="text" name="password" id="pwd_field">
        <br>
        <input type="submit" value="Войти">
    </form>
    '
?>
 -->

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
 * @param ReferenceSystem\ReferenceSchemeItem $Vertex
 * @param int $depth глубина, по молчанию ноль
 */
function PrintVertex($Vertex)
{
    echo '<option>' . $Vertex->Path . '</option>';
    for($i = 0; $i<count($Vertex->Children); $i++)
    {
        PrintVertex($Vertex->Children[$i]);
    }
}
?>