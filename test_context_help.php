<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Тестирование контекстной справки</title>
    <style type="text/css">
        #header {
            border: 2px solid black;
            text-align: center;
            font-size: 25px;
        }

        #contentBox1, #contentBox2, #contentBox3 {
            margin: 5px 0px 5px 0px;
            display: flex;
            border: 2px solid black;
            border-radius: 20px;
            justify-content: space-between;
            padding: 1%;
            max-height: 450px;
        }
        .menu {
            display: block;
            border: 1px solid black;
            width: 20%;
            text-align: center;
            /*overflow: auto;*/
        }
        #menuitems {
            list-style: none;
        }
        .menuitem {
            color: black;
        }
        #dataeditor {
            border: 1px solid black;
            width: 79%;
            text-align: left;
            /*overflow: auto;*/
            padding:2%;
        }
        #list, #list1 {
            display: inline-block;
            border: 1px dotted black;
            overflow: auto;
            font-size: 20px;
            width: 47%;
            height: 100%;
            text-align: center;
            padding: 1%;
            vertical-align: center;
        }

        #filterlist {
            display: flex;
            margin: 10px;
        }

        #list table, #list table tr, #list table tbody {
            width: 100%;
            border: 1px solid green;
        }
        #list table tbody
        {
            white-space: nowrap;
        }

        #list table tr td{
            border: 1px solid blue;
            min-width: 100px;
        }
        #list table caption{
            border: 2px solid blue;
        }
        #list input, #list select {
            margin: 0px 3px 0px 3px;
        }
    </style>

    <link rel="stylesheet" href="/ReferenceSystem/ContextHelp/css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="/ReferenceSystem/ContextHelp/js/context_reference.js"></script>
</head>
<body>
<header><div id="header"><h1>Добро пожаловать в редактор базы данных аэропорта</h1></div></header>
<div id="contentBox1">
    <nav class="menu RS_Help" id="menu1">
        <h3>Получить информацию</h3>
        <ul id="menuitems">
            <li><a class="menuitem" href="/">Все работники аэропорта</a></li>
            <li><a class="menuitem" href="/">Данные по бригадам</a></li>
            <li><a class="menuitem" href="/">Медосмотры</a></li>
            <li><a class="menuitem" href="/">Приписанные самолеты</a></li>
            <li><a class="menuitem" href="/">Статистика по самолетам</a></li>
            <!--<li><a class="menuitem" href="/">Рейсы по маршруту</a></li>
            <li><a class="menuitem" href="/">Отмененные рейсы</a></li>
            <li><a class="menuitem" href="/">Задержанные рейсы</a></li>
            <li><a class="menuitem" href="/">Рейсы по типу самолетов</a></li>
            <li><a class="menuitem" href="/">Другая информация по рейсам</a></li>
            <li><a class="menuitem" href="/">Информация о пассажирах</a></li>
            <li><a class="menuitem" href="/">Места</a></li>
            <li><a class="menuitem" href="/">Сданные билеты</a></li>-->
        </ul>
    </nav>
    <div id="dataeditor" class="RS_Help RS_D1">
        <span style="color: gray;">Выберите пункт меню слева</span>
    </div>
</div>
<div id="contentBox2">
    <nav class="menu RS_Help" id="menu2">
        <h3>Ввести информацию в таблицу</h3>
        <ul id="menuitems">
            <li><a class="menuitem" href="/">Работники</a></li>
            <li><a class="menuitem" href="/">Бригады</a></li>
            <li><a class="menuitem" href="/">Расписание</a></li>
            <li><a class="menuitem" href="/">Рейсы</a></li>
            <!--<li><a class="menuitem" href="/">Билеты</a></li>-->
            <li><a class="menuitem" href="/">Пользователи</a></li>
            <li><a class="menuitem" href="/">Отделы</a></li>
            <li><a class="menuitem" href="/">Начальники отделов</a></li>
            <li><a class="menuitem" href="/">Самолеты</a></li>
            <li><a class="menuitem" href="/">Медосмотры</a></li>
            <li><a class="menuitem" href="/">Техосмотры</a></li>
        </ul>
    </nav>
    <div id="dataeditor" class="RS_Help RS_D2">

    </div>
</div>
</body>
</html>