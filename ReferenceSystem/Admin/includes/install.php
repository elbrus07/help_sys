<?php
use ReferenceSystem\Modules\RSUser;
use ReferenceSystem\Modules\Database\Install as DBInstall;

include_once(__DIR__ . '/../../vendor/autoload.php');

if(DBInstall::isInstalled())
    die('Функционал справочной системы уже установлен');

if(isset($_POST['action']))
{
    if($_POST['action'] == 'installDB')
        DBInstall::doInstall();
    elseif ($_POST['action'] == 'register')
    {
        if(isset($_POST['username']) and $_POST['username'] != '' and isset($_POST['password'])
            and  $_POST['password'] != '')
            RSUser::Create($_POST['username'], $_POST['password']);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Установка</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        html, body {
            height: 100%;
        }
        .ItemContainer {
            display: inline-block;
            width: 35em;

        }
        .Form input[type='text'], .Form input[type='password'] {
            width: 15em;
        }

        #container {
            height: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            background-image: linear-gradient(45deg,rgba(0, 0, 0, 0) 48%,rgba(0, 0, 0, 0.2) 50%,rgba(0, 0, 0, 0) 52%),
            linear-gradient(-45deg,rgba(0, 0, 0, 0) 48%,
                rgba(0, 0, 0, 0.2) 50%,rgba(0, 0, 0, 0) 52%);
            background-size: 1em 1em;
            background-color: #fff;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.Form input[type=submit]').click(function (event) {
                event.preventDefault();
                $('#statusBar').val('Статус: создание табилц БД');
                //Запрос на создание таблиц
                $.post($(document).href,{'action': 'installDB'}, function () {
                    $('#statusBar').val('Статус: создание пользователя');
                    //Запрос на регистрацию пользователя
                    $.post($(document).href, $('.Form').serialize(), function () {
                        location.reload();
                    });
                });
            });
        });
    </script>
</head>
<body>
<div id="container">
    <div class="ItemContainer">
        <div class="ItemContainerHeader">
            <span class="ItemContainerHeaderText">Установка справочной системы</span>
        </div>
        <form class="Form" method="post">
            Похоже, что это первый запуск панели администратора справочной системы.
            <br>
            Для начала установки введите желаемые данные для доступа в панель администратора:
            <br>
            <label for="username">Логин: </label>
            <br>
            <input type="text" id="username" name="username">
            <br>
            <label for="password">Пароль: </label>
            <br>
            <input type="password" id="password" name="password">
            <br>
            <input type="hidden" name="action" value="register">
            <input type="submit" class="mainButton" value="Регистрация">
            <br>
            <span id="statusBar">Статус: ожидание...</span>
        </form>
    </div>
</div>
</body>
</html>