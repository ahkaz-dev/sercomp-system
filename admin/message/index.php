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

/* Форма фильтров */
body.dark-theme form.d-flex > * {
  background-color: #1f1f1f;
  color: #eee;
  border: 1px solid #444;
  border-radius: 4px;
  transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme form.d-flex .form-control,
body.dark-theme form.d-flex .form-select {
  background-color: #1f1f1f;
  color: #eee;
  border: 1px solid #444;
  min-height: 38px;
}

body.dark-theme form.d-flex .form-control::placeholder {
  color: #999;
}

body.dark-theme form.d-flex .form-control:focus,
body.dark-theme form.d-flex .form-select:focus {
  background-color: #292929;
  border-color: #64b5f6;
  color: #fff;
  outline: none;
  box-shadow: 0 0 5px #64b5f6;
}

/* Кнопки */
body.dark-theme .btn-primary,
body.dark-theme .btn-outline-primary {
  color: #64b5f6;
  border-color: #64b5f6;
  background-color: transparent;
  transition: background-color 0.3s ease, color 0.3s ease;
}

body.dark-theme .btn-primary:hover,
body.dark-theme .btn-primary:focus,
body.dark-theme .btn-outline-primary:hover,
body.dark-theme .btn-outline-primary:focus {
  background-color: #64b5f6;
  color: #121212;
  border-color: #64b5f6;
}

body.dark-theme .btn-link {
  color: #64b5f6;
}

body.dark-theme .btn-link.text-secondary {
  color: #999;
}

body.dark-theme .btn-link.text-secondary:hover,
body.dark-theme .btn-link.text-secondary:focus {
  color: #ccc;
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

/* Текст и мелкие элементы */
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

        // Статусы для фильтра
        $statuses = ['Скоро приступим', 'В процессе', 'Готово'];

        // Получаем заявки для фильтра в селекте
        $requestStmt = $pdo->query("
            SELECT r.Id, u.Name AS UserName, s.Name AS ServiceName
            FROM Request r
            LEFT JOIN Users u ON r.Users = u.Id
            LEFT JOIN Service s ON r.Service = s.Id
            ORDER BY r.Id DESC
        ");
        $requests = $requestStmt->fetchAll(PDO::FETCH_ASSOC);

        // Запрос с фильтрами
        $sql = "SELECT m.*, r.Desc_problem, u.Name AS UserName, s.Name AS ServiceName
                FROM Message m
                LEFT JOIN Request r ON m.Request = r.Id
                LEFT JOIN Users u ON r.Users = u.Id
                LEFT JOIN Service s ON r.Service = s.Id
                WHERE 1=1";

        $params = [];

        if (!empty($_GET['status']) && in_array($_GET['status'], $statuses)) {
            $sql .= " AND m.Status = :status";
            $params['status'] = $_GET['status'];
        }

        if (!empty($_GET['created_at'])) {
            $sql .= " AND DATE(m.Created_at) = :created_at";
            $params['created_at'] = $_GET['created_at'];
        }

        if (!empty($_GET['text'])) {
            $sql .= " AND m.Text LIKE :text";
            $params['text'] = '%' . $_GET['text'] . '%';
        }

        if (!empty($_GET['request'])) {
            $sql .= " AND m.Request = :request";
            $params['request'] = $_GET['request'];
        }

        $sql .= " ORDER BY m.Created_at DESC";

        $query = $pdo->prepare($sql);
        $query->execute($params);
        $messages = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<title>Админ-панель | Сообщения</title>
<style>footer { display: none; }</style>

<div class="container mt-5 ps2p-regular">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>">Главная</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Сообщения</li>
        </ol>
    </nav>

    <h2 class="mb-4 fs-6">Список сообщений [Message]</h2>

    <div class="mb-4">
<form method="get" class="d-flex flex-wrap align-items-center gap-2 mb-4">

    <a href="<?= $base_url ?>/admin/message/dynamic/message" class="btn btn-primary d-flex align-items-center gap-1">
        <i class="bi bi-plus-circle"></i> Добавить сообщение
    </a>

    <select name="status" class="form-select" style="width: 180px;">
        <option value="">Статус (все)</option>
        <?php foreach ($statuses as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= (isset($_GET['status']) && $_GET['status'] === $status) ? 'selected' : '' ?>>
                <?= htmlspecialchars($status) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="date" name="created_at" class="form-control" style="width: 140px;" value="<?= htmlspecialchars($_GET['created_at'] ?? '') ?>" placeholder="дд.мм.гггг">

    <input type="text" name="text" class="form-control" style="min-width: 220px;" placeholder="Текст сообщения" value="<?= htmlspecialchars($_GET['text'] ?? '') ?>">

    <select name="request" class="form-select" style="width: 220px;">
        <option value="">Заявка (все)</option>
        <?php foreach ($requests as $req): 
            $req_label = "[" . $req['Id'] . "] " . 
                         ($req['UserName'] ?? 'Пользователь не найден') . " — " . 
                         ($req['ServiceName'] ?? 'Услуга не найдена'); 
        ?>
            <option value="<?= htmlspecialchars($req['Id']) ?>" <?= (isset($_GET['request']) && $_GET['request'] == $req['Id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($req_label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-link px-3">Фильтровать</button>
    <a href="/admin/message/" class="btn btn-link px-3 text-secondary">Сбросить</a>

</form>

    </div>

    <div class="row g-4 mt-2">
        <?php if (empty($messages)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-2"></i>Сообщения отсутствуют
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 position-relative h-100">
                        <div class="card-body pb-4">
                            <h5 class="card-title mb-2">ID: <?= htmlspecialchars($message["Id"]) ?></h5>
                            <h6 class="text-muted">Статус: <?= htmlspecialchars($message["Status"]) ?></h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($message["Text"])) ?></p>
                            <small class="text-muted">Дата создания: <?= htmlspecialchars($message["Created_at"]) ?></small><br>
                            <small class="text-muted">
                                Заявка: <?= "[" . htmlspecialchars($message["Request"]) . "] " ?>
                                <?= htmlspecialchars($message["Desc_problem"] ?? '—') ?>
                            </small>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <a href="<?= $base_url ?>/admin/message/dynamic/message?id=<?= htmlspecialchars($message["Id"]) ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-pencil"></i> Редактировать
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
