<?php


namespace ReferenceSystem\Modules\Database;
use \mysqli;
use ReferenceSystem\Modules\Database\Settings as DBSettings;

include_once(__DIR__ . '/../../vendor/autoload.php');

class Install
{
    public static function isInstalled()
    {
        $mysqli = new mysqli(DBSettings::DB_HOST,DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD,DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return $mysqli->connect_error;
        $dbName = DBSettings::DB_DATABASE;
        $sql = "SHOW TABLES FROM $dbName WHERE Tables_in_$dbName LIKE 'ref_system_data'" .
            " OR  Tables_in_$dbName LIKE 'ref_system_html_owners' " .
            "OR Tables_in_$dbName LIKE 'ref_system_users'";
        if(!$result = $mysqli->query($sql))
            return $mysqli->error;

        //Если созданы все три таблицы
        if($result->num_rows == 3) {

            $sql = "SELECT * FROM ref_system_users";
            if(!$result = $mysqli->query($sql))
                return $mysqli->error;
            //И создан пользователь администратора
            if($result->num_rows > 0)
                return true;
        }

        return false;
    }

    public static function doInstall()
    {
        $mysqli = new mysqli(DBSettings::DB_HOST,DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD,DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return $mysqli->connect_error;
        $table1_sql = 'CREATE TABLE IF NOT EXISTS `ref_system_data` (' .
                        '`id` int(11) NOT NULL AUTO_INCREMENT, ' .
                        '`type` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ' .
                        '`parent_id` int(11) DEFAULT NULL, ' .
                        '`caption` varchar(150) NOT NULL, ' .
                        '`content` text, ' .
                        'PRIMARY KEY (`id`), ' .
                        'KEY `parent_id` (`parent_id`) ' .
                        ') ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8';
        $table2_sql = 'CREATE TABLE IF NOT EXISTS `ref_system_users` ('.
                        '`id` int(11) NOT NULL AUTO_INCREMENT, '.
                        '`username` varchar(25) NOT NULL, '.
                        '`password_hash` varchar(40) NOT NULL, '.
                        'PRIMARY KEY (`id`) '.
                        ') ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8';
        $table3_sql = 'CREATE TABLE IF NOT EXISTS `ref_system_html_owners` ('.
                        '`element_id` varchar(64) NOT NULL, '.
                        '`data_id` int(11) NOT NULL, '.
                        'PRIMARY KEY (`element_id`), '.
                        'KEY `ref_system_html_owners_ibfk_1` (`data_id`), '.
                        'CONSTRAINT `ref_system_html_owners_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `ref_system_data` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT '.
                        ') ENGINE=InnoDB DEFAULT CHARSET=utf8';
        if(!$result = $mysqli->query($table1_sql))
            return $mysqli->error;
        if(!$result = $mysqli->query($table2_sql))
            return $mysqli->error;
        if(!$result = $mysqli->query($table3_sql))
            return $mysqli->error;
        return true;
    }
}