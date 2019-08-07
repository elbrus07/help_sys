-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 07 2019 г., 19:38
-- Версия сервера: 8.0.15
-- Версия PHP: 7.1.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `reference_system`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ref_system_data`
--

CREATE TABLE `ref_system_data` (
  `id` int(11) NOT NULL,
  `type` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `caption` varchar(150) NOT NULL,
  `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ref_system_data`
--

INSERT INTO `ref_system_data` (`id`, `type`, `parent_id`, `caption`, `content`) VALUES
(17, 'Chapter', NULL, 'dlabla', 'asdasdas<br>\r\nновая строка от редактирования!'),
(18, 'Chapter', NULL, 'тестру', ''),
(22, 'Section', 18, 'секция1', 'рузге текст'),
(23, 'Section', 18, 'секция2', 'еще текст'),
(27, 'Subsection', 23, 'раздел1', 'буквы'),
(28, 'Subsection', 23, 'раздел2', '<h1>Заголовок<br>первого уровня</h1>\r\n<h2>Заголовок второго уровня</h2>\r\n<h3>Заголовок третьего уровня</h3>\r\n<h4>Заголовок четвертого уровня</h4>\r\n<h5>Заголовок пятого уровня</h5>\r\n<h6>Заголовок шестого уровня</h6>'),
(32, 'Subsection', 22, 'раздел1', 'asdadasd'),
(34, 'Chapter', NULL, 'Сказки', 'Тут будут сказки'),
(35, 'Section', 34, 'А. С. Пушкин', 'Пушкин вообще топ автор<br>\r\nТут его биография'),
(36, 'Subsection', 35, 'Сказка о рыбаке и рыбке', '                                                <b>Сказка о рыбаке и рыбке</b>\r\nЖил старик со своею старухой<br>\r\nУ самого синего моря;<br>\r\nОни жили в ветхой землянке<br>\r\nРовно тридцать лет и три года.<br>\r\nСтарик ловил неводом рыбу,<br>\r\nСтаруха пряла свою пряжу.<br>\r\nРаз он в море закинул невод, —<br>\r\nПришел невод с одною тиной.<br>\r\nОн в другой раз закинул невод,<br>\r\nПришел невод с травой морскою.<br>\r\nВ третий раз закинул он невод, —<br>\r\nПришел невод с одною рыбкой,<br>\r\nС непростою рыбкой, — золотою.<br>\r\nКак взмолится золотая рыбка!<br>\r\nГолосом молвит человечьим:<br>\r\n«Отпусти ты, старче, меня в море,<br>\r\nДорогой за себя дам откуп:<br>\r\nОткуплюсь чем только пожелаешь.»<br>\r\nУдивился старик, испугался:<br>\r\nОн рыбачил тридцать лет и три года<br>\r\nИ не слыхивал, чтоб рыба говорила.<br>                                '),
(45, 'Section', 34, 'Тест', ''),
(79, 'Chapter', NULL, 'Аэропорт', ''),
(80, 'Section', 79, 'Ввод данных', ''),
(81, 'Section', 79, 'Вывод данных', ''),
(82, 'Subsection', 81, 'Меню вывода данных', 'Здесь что-то о меню вывода данных<br>\r\nasdasdadadadadasdasdasd'),
(83, 'Subsection', 80, 'Меню ввода данных', 'asdasdaddsadasdasdsadasdasdsad<br>\r\n<b>asdasdadasdczczxczczcxzcvbcvbxcvb</b>'),
(84, 'Subsection', 81, 'Работа с содержимым', 'ячсмячсмячсмячмячсмячсмячмямяямчясмячмячмчямчясм'),
(85, 'Subsection', 80, 'Работа с содержимым', 'еншегншенгшенгшенгшенгшеншнегшшенгшенгшгнешгеншенгш');

-- --------------------------------------------------------

--
-- Структура таблицы `ref_system_html_owners`
--

CREATE TABLE `ref_system_html_owners` (
  `element_id` varchar(64) NOT NULL,
  `uniqueClass` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pathname` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '$(location).attr(''pathname'')',
  `data_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ref_system_html_owners`
--

INSERT INTO `ref_system_html_owners` (`element_id`, `uniqueClass`, `pathname`, `data_id`) VALUES
('menu1', '', '/test_context_help.php', 82),
('menu2', '', '/test_context_help.php', 83),
('dataeditor', 'RS_D1', '/test_context_help.php', 84),
('dataeditor', 'RS_D2', '/test_context_help.php', 85);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ref_system_data`
--
ALTER TABLE `ref_system_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `ref_system_html_owners`
--
ALTER TABLE `ref_system_html_owners`
  ADD PRIMARY KEY (`element_id`,`uniqueClass`,`pathname`),
  ADD KEY `ref_system_html_owners_ibfk_1` (`data_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ref_system_data`
--
ALTER TABLE `ref_system_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `ref_system_html_owners`
--
ALTER TABLE `ref_system_html_owners`
  ADD CONSTRAINT `ref_system_html_owners_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `ref_system_data` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
