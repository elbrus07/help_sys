<?php
namespace ReferenceSystem;
session_start();
use \mysqli;
include_once ($_SERVER['DOCUMENT_ROOT'].'/settings/dblogin.php');

class RSUser
{

    /**
     * Добавление администратора справочной системы
     *
     * @param string $login
     * @param string $password
     * @return bool|string
     */
    public static function Create($login, $password)
    {
        $mysqli = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return false;

        $login = $mysqli->real_escape_string($login);
        $password = $mysqli->real_escape_string($password);
        $password = sha1($password);

        $sql = "SELECT password_hash FROM ref_system_users WHERE username = '$login'";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        if($result->num_rows === 0)
        {
            $sql = "INSERT INTO ref_system_users(username,password_hash) VALUES ('$login','$password')";
            if (!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
            return true;
        }
        else
            return 'Пользователь с данным логином уже существует.';
    }

    /**
     * Авторизация
     *
     * @param string $login
     * @param string $password
     * @return string bool
     */
    public static function Login($login, $password)
    {
        $mysqli = new mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return false;

        $login = $mysqli->real_escape_string($login);
        $password = $mysqli->real_escape_string($password);
        $password = sha1($password);

        $sql = "SELECT password_hash FROM ref_system_users WHERE username = '$login'";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        if($result->num_rows === 0)
            return "неправильный логин или пароль";
        $arr = $result->fetch_assoc();
        if(!($arr['password_hash'] == $password))
            return "неправильный логин или пароль";
        $_SESSION['RS_user'] = $login;
        return true;
    }

    /**
     *  Выход из системы
     */
    public static function Logout()
    {
        unset($_SESSION['RS_user']);
        //session_destroy();
    }

    /**
     * Проверка на авторизацию
     *
     * @return bool
     */
    public static function isLoggedIn()
    {
        return (isset($_SESSION['RS_user']) AND $_SESSION['RS_user'] != '') ? true : false;
    }
}