<?php 
session_start();

if (isset($_SESSION["log-session"])) {

    session_unset();
    session_destroy(); 

    session_start();
    $_SESSION['log-mess-warn'] = 'Вы вышли из аккаунта'; 

    header("Location: /"); 
    exit();
}
