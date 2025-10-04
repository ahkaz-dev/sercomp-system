<?php include "../../../include/header.php"; ?>
<?php include "../../../include/message.php"; ?>

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
if (isset($_SESSION["log-session"]) && isset($_SESSION['log-session-data']) && $_SESSION['log-session-data']["Status"] === "Администратор"):
    $Id = $_GET['id'] ?? 0;
    $query = $pdo->prepare("SELECT * FROM Message WHERE Id = :Id");
    $query->execute(['Id' => $Id]);
    $message = $query->fetch(PDO::FETCH_ASSOC);

// Получаем заявки с JOIN для вывода связанной информации
$requestStmt = $pdo->query("
    SELECT 
        r.Id AS Request_id, 
        u.Name AS UserName, 
        s.Name AS ServiceName, 
        r.Desc_problem
    FROM Request r
    LEFT JOIN Users u ON r.User_id = u.Id
    LEFT JOIN Service s ON r.Service_id = s.Id
    ORDER BY r.Id DESC
");
$requests = $requestStmt->fetchAll(PDO::FETCH_ASSOC);



    $statuses = ['Скоро приступим', 'В процессе', 'Готово'];

    $errors = [
        'status' => '',
        'text' => '',
        'created_at' => '',
        'request' => '',
    ];

    function validate_message_data(&$errors, $status, $text, $created_at, $request, $statuses, $requests) {
        $valid = true;

        if (!in_array($status, $statuses)) {
            $errors['status'] = "Выберите корректный статус.";
            $valid = false;
        }

        if (mb_strlen(trim($text)) < 3) {
            $errors['text'] = "Текст сообщения должен содержать минимум 3 символа.";
            $valid = false;
        }

        // Проверка даты и времени: формат "YYYY-MM-DD HH:MM:SS"
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $created_at)) {
            $errors['created_at'] = "Дата и время должны быть в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС.";
            $valid = false;
        } else {
            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $created_at);
            if (!$dt || $dt->format('Y-m-d H:i:s') !== $created_at) {
                $errors['created_at'] = "Некорректная дата и время.";
                $valid = false;
            }
        }

    $valid_request_ids = array_column($requests, 'Request_id');
    if (!in_array((int)$request, $valid_request_ids)) {
        $errors['request'] = "Выберите корректную заявку.";
        $valid = false;
    }

        return $valid;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete']) && $message) {
            $stmt = $pdo->prepare("DELETE FROM Message WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Сообщение удалено";
                echo "<script>window.location.href = '" . $base_url . "/admin/message/';</script>";
                exit;
            }
        }

        if (isset($_POST['update']) || isset($_POST['save'])) {
            $status = $_POST['status'] ?? '';
            $text = $_POST['text'] ?? '';

            // Преобразуем datetime-local из формы (например, 2025-05-15T14:30) в формат MySQL datetime
            $created_at_raw = $_POST['created_at'] ?? '';
            $created_at = '';
            if ($created_at_raw) {
                $dt_obj = DateTime::createFromFormat('Y-m-d\TH:i', $created_at_raw);
                if ($dt_obj) {
                    $created_at = $dt_obj->format('Y-m-d H:i:s');
                }
            }

            $request = $_POST['request'] ?? '';

            if (validate_message_data($errors, $status, $text, $created_at, $request, $statuses, $requests)) {
                if (isset($_POST['update'])) {
                    $stmt = $pdo->prepare("UPDATE Message SET Status = ?, Text = ?, Created_at = ?, Request_id = ? WHERE Id = ?");
                    $stmt->execute([$status, $text, $created_at, $request, $Id]);
                    $_SESSION["log-mess-s"] = "Сообщение обновлено";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO Message (Status, Text, Created_at, Request_id) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$status, $text, $created_at, $request]);
                    $_SESSION["log-mess-s"] = "Сообщение добавлено";
                }

                echo "<script>window.location.href = '" . $base_url . "/admin/message/';</script>";
                exit;
            } else {
                $_SESSION['form_data'] = compact('status', 'text', 'created_at_raw', 'request');
            }
        }
    }

    // Для формы: выбор значения даты для datetime-local
    $form_created_at = '';
    if (!empty($_SESSION['form_data']['created_at_raw'])) {
        $form_created_at = $_SESSION['form_data']['created_at_raw'];
    } elseif (!empty($message['Created_at'])) {
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $message['Created_at']);
        if ($dt) {
            $form_created_at = $dt->format('Y-m-d\TH:i');
        }
    }
?>

<style>footer { display: none; }</style>

<div class="container mt-5 ps2p-regular" style="font-size:13px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/message/">Сообщения</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $message ? "Сообщение_id_{$message["Id"]}" : "Новое сообщение" ?></li>
        </ol>
    </nav>

    <form method="post" style="max-width:600px; margin:auto;">
        <div class="mb-3">
            <?php if ($message): ?>
                <title>Сообщение: <?= htmlspecialchars($message["Id"]) ?></title>
                <label class="form-label">ID</label>
                <input type="text" class="form-control" value="<?= $message["Id"] ?>" disabled readonly>
            <?php else: ?>
                <title>Добавить новое сообщение</title>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Статус</label>
            <select name="status" id="status" class="form-select <?= $errors['status'] ? 'is-invalid' : '' ?>" required>
                <option value="">Выберите статус</option>
                <?php 
                $selected_status = $message["Status"] ?? ($_SESSION['form_data']['status'] ?? '');
                foreach ($statuses as $st): ?>
                    <option value="<?= htmlspecialchars($st) ?>" <?= ($selected_status === $st) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($st) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback"><?= $errors['status'] ?></div>
        </div>

        <div class="mb-3">
            <label for="text" class="form-label">Текст сообщения</label>
            <textarea name="text" id="text" class="form-control <?= $errors['text'] ? 'is-invalid' : '' ?>" rows="4" required><?= htmlspecialchars($message["Text"] ?? ($_SESSION['form_data']['text'] ?? '')) ?></textarea>
            <div class="invalid-feedback"><?= $errors['text'] ?></div>
        </div>

        <div class="mb-3">
            <label for="created_at" class="form-label">Дата и время создания</label>
            <input type="datetime-local" name="created_at" id="created_at" class="form-control <?= $errors['created_at'] ? 'is-invalid' : '' ?>" 
                   required 
                   value="<?= htmlspecialchars($form_created_at) ?>">
            <div class="invalid-feedback"><?= $errors['created_at'] ?></div>
        </div>

<div class="mb-3">
    <label for="request" class="form-label">Заявка (Request)</label>
    <select name="request" id="request" class="form-select <?= $errors['request'] ? 'is-invalid' : '' ?>" required>
        <option value="">Выберите заявку</option>
        <?php
        $selected_request = $message["Request"] ?? ($_SESSION['form_data']['request'] ?? '');
        foreach ($requests as $req): ?>
            <option value="<?= htmlspecialchars($req['Request_id']) ?>" <?= ($selected_request == $req['Request_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars("[" . $req['Request_id'] . "]") . " — Услуга: " . ($req['ServiceName'] ?? 'неизвестна') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <div class="invalid-feedback"><?= $errors['request'] ?></div>
</div>


        <button type="submit" class="btn btn-success" name="<?= $message ? 'update' : 'save' ?>">Сохранить</button>
        <?php if ($message): ?>
            <button type="submit" class="btn btn-danger" name="delete" onclick="return confirm('Вы уверены, что хотите удалить это сообщение?')">Удалить</button>
        <?php endif; ?>
    </form>

    <?php unset($_SESSION['form_data']); ?>
</div>

<?php else: ?>
    <script>window.location.href = '<?= $base_url ?>';</script>
<?php endif; ?>
<?php include "../../../include/footer.php"; ?>
