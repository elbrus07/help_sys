<?php
use ReferenceSystem\Modules\RSUser;

include_once(__DIR__ . '/../../vendor/autoload.php');

if (isset($_POST['username']) AND isset($_POST['password']) AND isset($_POST['username']) != ''
    AND isset($_POST['password']) != '')
{
    $login_result = RSUser::Login($_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
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
</head>
<body>
    <div id="container">
    <div class="ItemContainer">
        <div class="ItemContainerHeader">
            <span class="ItemContainerHeaderText">Авторизация в справочной системе</span>
        </div>
        <form class="Form" method="post">
            Для продолжения работы необходимо авторизоваться. <br>Пожалуйста, введите свой логин и пароль в форму ниже.
            <br>
            <label for="username">Логин: </label>
            <br>
            <input type="text" id="username" name="username">
            <br>
            <label for="password">Пароль: </label>
            <br>
            <input type="password" id="password" name="password">
            <br>
            <input type="submit" class="mainButton" value="Войти">
        </form>
    </div>
    </div>
</body>
</html>