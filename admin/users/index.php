<?php include "../../include/header.php"; ?>
<?php include "../../include/message.php"; ?>

<style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center; 
            align-items: center; 
            text-align: center;
            padding: 20px;
            flex-direction: column;
        }

        footer {
            width: 100%;
            margin-top: auto; 
            background: #efefef;
            text-align: center;
            padding: 15px 0;
        }
</style>

<?php 
if (isset($_SESSION["log-session"]) && isset($_SESSION['log-session-data'])): 
    if ($_SESSION['log-session-data']["Status"] == "Администратор"):
        $where = [];
        $params = [];
        
        // Логин или Email
        if (!empty($_GET['search_login_email'])) {
            $where[] = '(Login LIKE :login_email OR Email LIKE :login_email)';
            $params[':login_email'] = '%' . $_GET['search_login_email'] . '%';
        }
        // Имя
        if (!empty($_GET['search_name'])) {
            $where[] = 'Name LIKE :name';
            $params[':name'] = '%' . $_GET['search_name'] . '%';
        }
        // Телефон
        if (!empty($_GET['search_phone'])) {
            $where[] = 'Phone_number LIKE :phone';
            $params[':phone'] = '%' . $_GET['search_phone'] . '%';
        }
        // Роль
        if (!empty($_GET['filter_status'])) {
            $where[] = 'Status = :status';
            $params[':status'] = $_GET['filter_status'];
        }
        
        $sql = "SELECT * FROM Users";
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $query = $pdo->prepare($sql);
        $query->execute($params);
        $user_query_result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        
?>
<title>Админ-панель | Пользователи</title>
<style>
    .login-cell {
        position: relative;
    }

    .role-icon {
        cursor: default;
        position: absolute;
        right: 0;
        top: 0;
        font-size: 1.2em; 
        opacity: 0.72;
    }

    footer {
        display: none;
    }
</style>

<style>
/* Тёмная тема */
body.dark-theme {
  background-color: #121212;
  color: #e0e0e0;
  transition: background-color 0.4s ease, color 0.4s ease;
}

/* Контейнер */
body.dark-theme .container {
  background-color: transparent;
}

/* Навигация (хлебные крошки) */
body.dark-theme .breadcrumb {
  background-color: #1f1f1f;
  border-radius: 6px;
  padding: 8px 12px;
  margin-bottom: 20px;
}

body.dark-theme .breadcrumb-item a {
  color: #64b5f6;
}

body.dark-theme .breadcrumb-item.active {
  color: #e0e0e0;
  font-weight: 600;
}

/* Форма фильтров */
body.dark-theme form .form-control,
body.dark-theme form .form-select,
body.dark-theme form .btn {
  background-color: #1f1f1f;
  color: #eee;
  border: 1px solid #444;
  border-radius: 4px;
  transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme form .form-control::placeholder {
  color: #999;
}

body.dark-theme form .form-control:focus,
body.dark-theme form .form-select:focus {
  background-color: #292929;
  border-color: #64b5f6;
  color: #fff;
  outline: none;
  box-shadow: 0 0 5px #64b5f6;
}

/* Кнопки */
body.dark-theme .btn-mb-primary,
body.dark-theme .btn-outline-primary {
  color: #64b5f6;
  border-color: #64b5f6;
  background-color: transparent;
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme .btn-mb-primary:hover,
body.dark-theme .btn-mb-primary:focus,
body.dark-theme .btn-outline-primary:hover,
body.dark-theme .btn-outline-primary:focus {
  background-color: #64b5f6;
  color: #121212;
  border-color: #64b5f6;
}

body.dark-theme .btn-outline-secondary {
  color: #999;
  border-color: #666;
  background-color: transparent;
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme .btn-outline-secondary:hover,
body.dark-theme .btn-outline-secondary:focus {
  background-color: #555;
  color: #fff;
  border-color: #64b5f6;
}

/* Карточки */
body.dark-theme .card {
  background-color: #1f1f1f;
  border: 1px solid #333;
  color: #ddd;
  transition: background-color 0.3s ease, color 0.3s ease;
  box-shadow: 0 1px 5px rgba(0,0,0,0.5);
}

body.dark-theme .card:hover {
  background-color: #292929;
  color: #fff;
}

/* Полоса сверху карточки по роли */
body.dark-theme .card .position-absolute.top-0.start-0.w-100 {
  background: none !important;
}

body.dark-theme .card .position-absolute.top-0.start-0.w-100[style*="background: var(--bs-primary)"] {
  background: #0d6efd !important;
}

body.dark-theme .card .position-absolute.top-0.start-0.w-100[style*="background: #ffc107"] {
  background: #ffc107 !important;
}

body.dark-theme .card .position-absolute.top-0.start-0.w-100[style*="background: #6c757d"] {
  background: #6c757d !important;
}

/* Карточка — footer */
body.dark-theme .card-footer {
  background-color: transparent;
  border-top: 1px solid #333;
}

/* Текст и мелкие элементы */
body.dark-theme .text-muted,
body.dark-theme .small,
body.dark-theme .fw-semibold {
  color: #bbb !important;
}

/* Информационные алерты */
body.dark-theme .alert-info {
  background-color: #2c3e50;
  color: #a0c4ff;
  border-color: #3a5068;
  box-shadow: none;
}

/* Ссылки */
body.dark-theme a {
  color: #64b5f6;
  text-decoration: none;
}

body.dark-theme a:hover,
body.dark-theme a:focus {
  color: #90caf9;
  text-decoration: underline;
}

/* Иконки ролей */
body.dark-theme .text-primary {
  color: #64b5f6 !important;
}

body.dark-theme .text-warning {
  color: #ffc107 !important;
}

body.dark-theme .text-secondary {
  color: #6c757d !important;
}

</style>

<div class="container mt-5 ps2p-regular">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>">Главная</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Пользователи в системе</li>
        </ol>
    </nav>
    <h2 class="mb-4 fs-6">Данные пользователей [Users]</h2>

    <div class="mb-4">
    <form method="GET">
        <div class="row g-3 align-items-center">
            <div class="col-lg-2 col-md-4">
                <a class="btn btn-mb-primary w-100" href="<?= $base_url ?>/admin/users/dynamic/users.php">
                    <i class="bi bi-person-plus-fill me-1"></i> Добавить запись
                </a>
            </div>
            <div class="col-lg-2 col-md-4">
                <input type="text" class="form-control" name="search_login_email" placeholder="Логин или Email" value="<?= htmlspecialchars($_GET['search_login_email'] ?? '') ?>">
            </div>
            <div class="col-lg-2 col-md-4">
                <input type="text" class="form-control" name="search_name" placeholder="Имя" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
            </div>
            <div class="col-lg-2 col-md-4">
                <input type="text" class="form-control" name="search_phone" placeholder="Телефон" value="<?= htmlspecialchars($_GET['search_phone'] ?? '') ?>">
            </div>
            <div class="col-lg-2 col-md-4">
                <select class="form-select" name="filter_status">
                    <option value="">Все роли</option>
                    <option value="Администратор" <?= (($_GET['filter_status'] ?? '') === 'Администратор') ? 'selected' : '' ?>>Администратор</option>
                    <option value="Пользователь" <?= (($_GET['filter_status'] ?? '') === 'Пользователь') ? 'selected' : '' ?>>Пользователь</option>
                </select>
            </div>
            <div class="col-lg-1 col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Найти
                </button>
            </div>
            <div class="col-lg-1 col-md-2">
                <a href="<?=$base_url?>/admin/users/" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Сбросить
                </a>
            </div>
        </div>
    </form>
</div>


    <div class="row g-4">
        <?php if (empty($user_query_result)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-2"></i>Данные отсутствуют
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($user_query_result as $user): ?>
                <?php
                    // Цвета и иконки для разных ролей
                    $role = $user["Status"];
                    if ($role === "Администратор") {
                        $border = "border-primary";
                        $icon = "bi-shield-lock";
                        $color = "text-primary";
                    } elseif ($role === "Пользователь") {
                        $border = "border-warning";
                        $icon = "bi-pencil-square";
                        $color = "text-warning";
                    } else {
                        $border = "border-secondary";
                        $icon = "bi-person";
                        $color = "text-secondary";
                    }
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 position-relative h-100">
                        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; border-radius: 16px 16px 0 0; background: var(--bs-primary); <?= $role === 'Админ' ? 'background: #0d6efd;' : ($role === 'Редактор' ? 'background: #ffc107;' : 'background: #6c757d;') ?>"></div>
                        <div class="card-body pb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi <?= $icon ?> fs-2 <?= $color ?> me-3"></i>
                                <div>
                                    <div class="fw-bold fs-5"><?= htmlspecialchars($user["Name"]) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($user["Status"] ?: "Пользователь") ?></div>
                                </div>
                            </div>
                            <div class="mb-2"><span class="fw-semibold">Логин:</span> <?= htmlspecialchars($user["Login"]) ?></div>
                            <div class="mb-2"><span class="fw-semibold">E-mail:</span> <?= htmlspecialchars($user["Email"]) ?></div>
                            <div class="mb-2"><span class="fw-semibold">Телефон:</span> <?= htmlspecialchars($user["Phone_number"]) ?></div>
                            <div class="mb-2"><span class="fw-semibold">Пароль:</span> <?= substr(htmlspecialchars($user["Password"]), 0, 8) ?>...</div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <a href="<?= $base_url ?>/admin/users/dynamic/users.php?id=<?= htmlspecialchars($user["Id"])?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-pencil"></i> Редактировать
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
    </div>
    <?php else: ?>
        <div class="container mt-5" style="padding-bottom: 12px;">
            <?php include "../../include/error/404.php"; ?> 
        </div>
    <?php
    endif;
    ?>
<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
            <?php include "../../include/error/404.php"; ?> 
    </div>
<?php
endif;
?>

<?php include "../../include/footer.php"; ?>

