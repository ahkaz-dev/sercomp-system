<?php

date_default_timezone_set('Europe/Moscow');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo;

if (!isset($pdo)) {
    $host = 'localhost';
    $dbname = 'sercomp';
    $username = 'sercomp_auth';
    $password = 'sercomp_auth';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Ошибка подключения к БД");
    }
}
