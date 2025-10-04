<?php include __DIR__ . '/../include/header.php'; ?>

<link rel="stylesheet" href="<?= $base_url ?>/static/css/card.css">


<?php 
if (isset($_SESSION["log-session"]) && isset($_SESSION['log-session-data'])): 
    if ($_SESSION['log-session-data']["Status"] == "Администратор"):
?>
<div class="container mt-5 nunito-reg" style="margin-bottom: 5%;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=$base_url?>">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Админ-панель</li>
        </ol>
    </nav>

    <!-- Первая группа: главные категории -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <a href="<?= $base_url ?>/admin/request/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Заявки на ремонт</h5>
            <p>Ремонт</p>
            <i class="bi bi-tools icon text-teal"></i>
        </a>

        <a href="<?= $base_url ?>/admin/message/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Сообщения к заявкам</h5>
            <p>Заявки</p>
            <i class="bi bi-chat-text icon text-teal"></i>
        </a>

        <a href="<?= $base_url ?>/admin/users/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Пользователи</h5>
            <p>Отслеживайте все аккаунты сайта в системе</p>
            <i class="bi bi-people icon text-teal"></i>
        </a>
    </div>

    <hr class="my-5">

    <!-- Вторая группа: остальные категории -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <a href="<?= $base_url ?>/admin/service/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Услуги</h5>
            <p>Услуги ремонта и не только на сайте</p>
            <i class="bi bi-upc-scan icon text-teal"></i>
        </a>

        <a href="<?= $base_url ?>/admin/model/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Модели</h5>
            <p>Модели различной техники</p>
            <i class="bi bi-device-hdd icon text-teal"></i>
        </a>

        <a href="<?= $base_url ?>/admin/device/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Техника</h5>
            <p>Техник</p>
            <i class="bi bi-pc-display icon text-teal"></i>
        </a>

        <a href="<?= $base_url ?>/admin/comment/" class="text-decoration-none link-dark link-offset-2 d-block position-relative feature-card supervisor">
            <div class="top-border"></div>
            <h5>Отзывы</h5>
            <p>Отзывы</p>
            <i class="bi bi-chat-text icon text-teal"></i>
        </a>
    </div>
</div>

<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
        <?php include __DIR__ . '/../include/error/404.php'; ?>
    </div>
<?php endif; ?>
<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
        <?php include __DIR__ . '/../include/error/404.php'; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../include/footer.php'; ?>
