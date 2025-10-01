<?php 
session_start();

// Подключаем базу данных
include __DIR__ . '/../db/connect.php';
global $pdo;

// Базовый URL сайта (без слэша на конце)
$base_url = 'http://localhost/sercomp';

// ini_set('display_errors', 1);
// error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="dark light">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="icon" type="image/svg" href="<?= $base_url ?>/static/svg/solo-logo.svg">
  <title>SERCOMP - Сервисный центр</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= $base_url ?>/static/css/loader.css">
  <link rel="stylesheet" href="<?= $base_url ?>/static/css/header.css">
</head>
<header class="p-2 mb-3 nunito-reg fs-6 bg-white shadow-sm" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000;margin-bottom: 0px !important;">
  <div class="container">
    <nav class="navbar navbar-expand-lg">
      <!-- Логотип -->
      <a href="<?= $base_url ?>" class="navbar-brand" style="display: block; padding-top:0px!important;">
      <img id="logoDefault" src="<?= $base_url ?>/static/svg/logo.svg" alt="Логотип" height="55" class="d-block">
      </a>

      <!-- Кнопка бургера -->
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Меню">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Основное меню (desktop) -->
      <div class="collapse navbar-collapse d-none d-lg-flex justify-content-between w-100">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a href="<?= $base_url ?>/our-service" class="nav-link">Наши услуги</a></li>
          <li class="nav-item"><a href="<?= $base_url ?>/about" class="nav-link">О компании</a></li>
          <li class="nav-item"><a href="<?= $base_url ?>/review" class="nav-link">Отзывы</a></li>
          <li class="nav-item"><a href="<?= $base_url ?>/completed-orders" class="nav-link">Выполненные заказы</a></li>
          <li class="nav-item"><a href="<?= $base_url ?>/contacts" class="nav-link">Контакты</a></li>
        </ul>

        <!-- Профиль или вход (desktop) -->
        <div class="d-flex align-items-center">
          <?php if (isset($_SESSION['log-session-data'])): ?>
            <?php if ($_SESSION['log-session-data']["Status"]== "Администратор"): ?>
              <div class="align-items-end p-3">
              <div class="d-flex flex-column align-items-start">
                <a href="https://yandex.ru/maps/?text=запрос к яндексу" target="_blank" class="text-muted small d-block text-decoration-none">
                    место для адреса
                </a>
                <a href="tel:+71111111111" class="small d-block text-decoration-none">
                     7 111 111 11 11
                </a>
              </div>
            </div>
<div class="d-flex flex-column gap-2">
  <div class="dropdown">
    <a href="#" class="btn btn-outline-dark dropdown-toggle w-100" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
      <?=$_SESSION['log-session-data']["Login"] ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
      <li><a class="dropdown-item" href="<?= $base_url ?>/admin/">Админ-панель</a></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/account/">Профиль</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/auth/logout">Выход</a></li>
    </ul>
  </div>

  <a href="<?= $base_url ?>/request/" class="btn btn-outline-dark w-100">Ремонт</a>
</div>

            <?php else: ?>
              <div class="align-items-end p-3">
              <div class="d-flex flex-column align-items-start">
                <a href="https://yandex.ru/maps/?text=запрос к яндексу" target="_blank" class="text-muted small d-block text-decoration-none">
                    место для адреса
                </a>
                <a href="tel:+71111111111" class="small d-block text-decoration-none">
                    7 111 111 11 11
                </a>

              </div>
            </div>
<div class="d-flex flex-column gap-2">
  <div class="dropdown">
    <a href="#" class="btn btn-outline-dark dropdown-toggle w-100" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
      <?=$_SESSION['log-session-data']["Login"] ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
      <li><a class="dropdown-item" href="<?= $base_url ?>/my-request/">Мои заявки</a></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/account/">Профиль</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/auth/logout">Выход</a></li>
    </ul>
  </div>

  <a href="<?= $base_url ?>/request/" class="btn btn-outline-dark w-100">Ремонт</a>
</div>

            <?php endif; ?>
          <?php else: ?>
            <div class="align-items-end p-3">
              <div class="d-flex flex-column align-items-start">
                <a href="https://yandex.ru/maps/?text=запрос к яндексу" target="_blank" class="text-muted small d-block text-decoration-none">
                    место для адреса
                </a>
                <a href="tel:+71111111111" class="small d-block text-decoration-none">
                    7 111 111 11 11
                </a>

              </div>
            </div>
<div class="d-flex flex-column gap-2">
  <a href="<?= $base_url ?>/login.php" class="btn btn-primary btn-sm">Войти в аккаунт</a>
  <a href="<?= $base_url ?>/request/" class="btn btn-outline-dark w-100">Ремонт</a>
</div>


          <?php endif; ?>
        </div>
      </div>

      <!-- Offcanvas меню (mobile) -->
      <div class="offcanvas offcanvas-start text-bg-light w-100 d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
<div class="offcanvas-header">
  <h5 class="offcanvas-title me-2" id="mobileMenuLabel">Меню</h5>

  <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
</div>

        <div class="offcanvas-body d-flex flex-column">
          <ul class="navbar-nav flex-grow-1">
            <li class="nav-item"><a href="<?= $base_url ?>/our-service" class="nav-link">Наши услуги</a></li>
            <li class="nav-item"><a href="<?= $base_url ?>/about" class="nav-link">О компании</a></li>
            <li class="nav-item"><a href="<?= $base_url ?>/review" class="nav-link">Отзывы</a></li>
            <li class="nav-item"><a href="<?= $base_url ?>/completed-orders" class="nav-link">Выполненные заказы</a></li>
            <li class="nav-item"><a href="<?= $base_url ?>/contacts" class="nav-link">Контакты</a></li>

          <!-- Профиль или вход (mobile) -->
          <div class="mt-4">
            <?php if (isset($_SESSION['log-session-data'])): ?>
              <?php if ($_SESSION['log-session-data']["Status"]== "Администратор"): ?>
<div class="d-flex flex-column gap-2 w-100">
  <a href="https://yandex.ru/maps/?text=запрос к яндексу" target="_blank" class="text-muted small text-decoration-none">
    место для адреса
  </a>

  <div class="dropdown w-100">
    <a class="btn btn-outline-dark dropdown-toggle w-100" href="#" role="button" id="mobileUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
      <?=$_SESSION['log-session-data']["Login"] ?>
    </a>
    <ul class="dropdown-menu w-100" aria-labelledby="mobileUserMenu">
      <li><a class="dropdown-item" href="admin/">Админ-панель</a></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/account/">Профиль</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/auth/logout">Выход</a></li>
    </ul>
  </div>

  <a href="<?= $base_url ?>/request/" class="btn btn-outline-dark w-100">Ремонт</a>
</div>


              <?php else: ?>
<div class="d-flex flex-column gap-2 w-100">
  <a href="https://yandex.ru/maps/?text=запрос к яндексу" target="_blank" class="text-muted small text-decoration-none">
    место для адреса
  </a>

  <div class="dropdown w-100">
    <a class="btn btn-outline-dark dropdown-toggle w-100" href="#" role="button" id="mobileUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
     <?=$_SESSION['log-session-data']["Login"] ?>
    </a>
    <ul class="dropdown-menu w-100" aria-labelledby="mobileUserMenu">
      <li><a class="dropdown-item" href="<?= $base_url ?>/my-request/">Мои заявки</a></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/account/">Профиль</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="<?= $base_url ?>/auth/logout">Выход</a></li>
    </ul>
  </div>
  <a href="<?= $base_url ?>/request/" class="btn btn-outline-dark w-100">Ремонт</a>

</div>

              <?php endif; ?>
            <?php else: ?>
              <div class="text-start">
<a href="запрос к яндексу" target="_blank" class="text-muted small d-block text-decoration-none">
    место для адреса
</a>
<a href="tel:+79189322132" class="small d-block text-decoration-none">
    8 918 932 21 32
</a>

<div class="d-flex flex-column gap-2">
  <a href="<?= $base_url ?>/login.php" class="btn btn-primary btn-sm">Войти в аккаунт</a>
  <a href="<?= $base_url ?>/request/" class="btn btn-outline-dark w-100">Ремонт</a>
</div>
              </div>
            <?php endif; ?>
          </div>
          </ul>

        </div>
      </div>
    </nav>
  </div>
</header>





<div id="loader">
  <img src="<?= $base_url ?>/static/svg/gear.svg" alt="Загрузка" class="gear-spinner" />
</div>