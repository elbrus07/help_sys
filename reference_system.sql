-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 02 2019 г., 19:27
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
(17, 'Chapter', NULL, 'dlabla', 'asdasdas'),
(18, 'Chapter', NULL, 'тестру', ''),
(22, 'Section', 18, 'секция1', 'рузге текст'),
(23, 'Section', 18, 'секция2', 'еще текст'),
(27, 'Subsection', 23, 'раздел1', 'буквы'),
(28, 'Subsection', 23, 'раздел2', '<h1>Заголовок<br>первого уровня</h1>\r\n<h2>Заголовок второго уровня</h2>\r\n<h3>Заголовок третьего уровня</h3>\r\n<h4>Заголовок четвертого уровня</h4>\r\n<h5>Заголовок пятого уровня</h5>\r\n<h6>Заголовок шестого уровня</h6>'),
(31, 'Section', 17, 'razd', 'sdadsd'),
(32, 'Subsection', 22, 'раздел1', 'asdadasd');

-- --------------------------------------------------------

--
-- Структура таблицы `ref_system_html_owners`
--

CREATE TABLE `ref_system_html_owners` (
  `element_id` varchar(64) NOT NULL,
  `data_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ref_system_html_owners`
--

INSERT INTO `ref_system_html_owners` (`element_id`, `data_id`) VALUES
('textBoxInput1', 23),
('textBoxInput2', 27);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ref_system_data`
--
ALTER TABLE `ref_system_data`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ref_system_html_owners`
--
ALTER TABLE `ref_system_html_owners`
  ADD PRIMARY KEY (`element_id`),
  ADD KEY `data_id` (`data_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ref_system_data`
--
ALTER TABLE `ref_system_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `ref_system_html_owners`
--
ALTER TABLE `ref_system_html_owners`
  ADD CONSTRAINT `ref_system_html_owners_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `ref_system_data` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
