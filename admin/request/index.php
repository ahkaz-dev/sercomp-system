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

/* Поля фильтра (input, select) */
body.dark-theme input.form-control,
body.dark-theme select.form-control {
  background-color: #1f1f1f;
  border: 1px solid #444;
  color: #eee;
  transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme input.form-control::placeholder {
  color: #999;
}

body.dark-theme input.form-control:focus,
body.dark-theme select.form-control:focus {
  background-color: #292929;
  border-color: #64b5f6;
  color: #fff;
  outline: none;
  box-shadow: 0 0 5px #64b5f6;
}

/* Кнопки */
body.dark-theme .btn-outline-primary {
  color: #64b5f6;
  border-color: #64b5f6;
  background-color: transparent;
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme .btn-outline-primary:hover,
body.dark-theme .btn-outline-primary:focus {
  background-color: #64b5f6;
  color: #121212;
  border-color: #64b5f6;
}

body.dark-theme .btn-outline-secondary {
  color: #888;
  border-color: #888;
  background-color: transparent;
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme .btn-outline-secondary:hover,
body.dark-theme .btn-outline-secondary:focus {
  background-color: #555;
  color: #eee;
  border-color: #555;
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

/* Карточка — footer */
body.dark-theme .card-footer {
  background-color: transparent;
  border-top: 1px solid #333;
}

/* Текст */
body.dark-theme p,
body.dark-theme strong,
body.dark-theme .text-muted {
  color: #cfcfcf;
}

body.dark-theme .text-muted {
  color: #999999 !important;
}

/* Информационные алерты */
body.dark-theme .alert-info {
  background-color: #2c3e50;
  color: #a0c4ff;
  border-color: #3a5068;
  box-shadow: none;
}

/* Горизонтальная линия */
body.dark-theme hr {
  border-color: #444;
}

/* Значки, бейджи */
body.dark-theme .badge.bg-info {
  background-color: #64b5f6;
  color: #121212;
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

</style>

<?php 
if (isset($_SESSION["log-session"]) && isset($_SESSION['log-session-data'])): 
    if ($_SESSION['log-session-data']["Status"] === "Администратор"):

        $sql = "
        SELECT 
            r.Id AS request_id,
            r.Register_data AS request_date,
            r.What_date AS request_what_date,
            r.Desc_problem AS request_description,

            u.Id AS user_id,
            u.Name AS user_name,
            u.Login AS user_login,
            u.Email AS user_email,
            u.Phone_number AS user_phone,

            s.Id AS service_id,
            s.Name AS service_title,
            s.Price AS service_price,

            m.Text AS message_text,
            m.Status AS message_status,
            m.Created_at AS message_created

        FROM Request r
        LEFT JOIN Users u ON r.Users = u.Id
        LEFT JOIN Service s ON r.Service = s.Id
        LEFT JOIN (
            SELECT m1.*
            FROM Message m1
            JOIN (
                SELECT Request, MAX(Created_at) AS MaxDate
                FROM Message
                GROUP BY Request
            ) m2 ON m1.Request = m2.Request AND m1.Created_at = m2.MaxDate
        ) m ON r.Id = m.Request
        WHERE 1=1
        ";

        $params = [];

        if (!empty($_GET['desc_problem'])) {
            $sql .= " AND r.Desc_problem LIKE :desc_problem";
            $params['desc_problem'] = '%' . $_GET['desc_problem'] . '%';
        }

        $query = $pdo->prepare($sql);
        $query->execute($params);
        $requests = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<title>Админ-панель | Запросы</title>
<style>footer { display: none; }</style>

<div class="container mt-5 ps2p-regular" >
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>">Главная</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Запросы</li>
        </ol>
    </nav>

    <h2 class="mb-4 fs-6">Список заявок [Request]</h2>

    <div class="mb-4">
        <form method="get">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <a class="btn btn-mb-primary w-100" href="<?= $base_url ?>/admin/request/dynamic/request">
                        <i class="bi bi-plus-circle me-1"></i> Добавить заявку
                    </a>
                </div>
                <div class="col-md-3">
                    <input type="text" name="desc_problem" class="form-control" placeholder="Название проблемы" value="<?= htmlspecialchars($_GET['desc_problem'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Фильтр</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/request/" class="btn btn-outline-secondary w-100">Сброс</a>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <?php if (empty($requests)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-2"></i>Заявки отсутствуют
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($requests as $request): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 position-relative h-100">
                        <div class="card-body pb-4">
                            <h5 class="card-title mb-2"><?= htmlspecialchars($request["request_description"]) ?></h5>
                            <h6 class="text-muted mb-3">
                                Дата регистрации: <?= htmlspecialchars($request["request_date"]) ?>
                            </h6>
                            <p class="mb-2">
                                <strong>ID:</strong> <?= $request["request_id"] ?><br>
                                <strong>Клиент:</strong> <?= htmlspecialchars($request["user_name"] ? $request["user_name"] : "Аккаунт был удален" )   ?>
                                (<?= htmlspecialchars($request["user_email"] ? $request["user_email"] : "") ?>)
                            </p>
                            <p class="mb-2">
                                <strong>Услуга:</strong>
                                <?= htmlspecialchars($request["service_title"] ?? '—') ?> —
                                <?= htmlspecialchars($request["service_price"] ?? '-') ?>₽
                            </p>
                            <?php if ($request["message_text"]): ?>
                                <hr>
                                <p class="mb-1">
                                    <strong>Статус:</strong>
                                    <span class="badge bg-info"><?= htmlspecialchars($request["message_status"]) ?></span>
                                </p>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($request["message_created"]) ?></p>
                                <p><?= nl2br(htmlspecialchars($request["message_text"])) ?></p>
                            <?php else: ?>
                                <p class="text-muted">Статус: <em>ещё не установлен</em></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <a href="<?= $base_url ?>/admin/request/dynamic/request?id=<?= urlencode($request["request_id"]) ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-pencil"></i> Редактировать заявку
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
        <?php include "../../include/error/404.php"; ?>
    </div>
<?php endif; ?>
<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
        <?php include "../../include/error/404.php"; ?>
    </div>
<?php endif; ?>

<?php include "../../include/footer.php"; ?>
