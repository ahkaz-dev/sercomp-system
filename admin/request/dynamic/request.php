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
if (isset($_SESSION["log-session"]) && isset($_SESSION['log-session-data'])):
    if ($_SESSION['log-session-data']["Status"] == "Администратор"):
        $Id = $_GET['id'] ?? 0;
        $query = $pdo->prepare("SELECT * FROM Request WHERE Id = :Id");
        $query->execute(['Id' => $Id]);
        $request_query_result = $query->fetch(PDO::FETCH_ASSOC);


        // Получаем сообщение, связанное с этим Request
        $message_query = $pdo->prepare("SELECT * FROM Message WHERE Id = :RequestId ORDER BY Created_at DESC LIMIT 1");
        $message_query->execute(['RequestId' => $Id]);
        $message_result = $message_query->fetch(PDO::FETCH_ASSOC);

        $errors = [
            'register_data' => '',
            'what_date' => '',
            'desc_problem' => '',
            'users' => '',
            'service' => '',
        ];

        // Регулярные выражения
        $regex_russian = '/^[а-яА-ЯёЁ\s]+$/u'; // Для проверки только русских букв

        function validate_request_data(&$errors, $register_data, $what_date, $desc_problem, $users, $service) {
            global $regex_russian;

            // Проверка даты регистрации
            if (mb_strlen($register_data) < 3 || mb_strlen($register_data) > 55) {
                $errors['register_data'] = "Дата регистрации должна содержать от 3 до 55 символов.";
            }

            // Проверка даты события
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $what_date)) {
                $errors['what_date'] = "Неверный формат даты.";
            } else {
                $today = date("Y-m-d");
                $week_ago = date("Y-m-d", strtotime("-7 days"));
                if ($what_date > $today || $what_date < $week_ago) {
                    $errors['what_date'] = "Дата должна быть в пределах последней недели и не позже текущей даты.";
                }
            }

            // Проверка описания проблемы (только русские буквы)
            if (mb_strlen($desc_problem) < 10 || mb_strlen($desc_problem) > 155 || !preg_match($regex_russian, $desc_problem)) {
                $errors['desc_problem'] = "Описание проблемы должно содержать от 10 до 155 символов и состоять только из русских букв.";
            }

            // Проверка пользователей и сервиса
            if (!is_numeric($users)) {
                $errors['users'] = "Пользователь должен быть числом.";
            }

            if (!is_numeric($service)) {
                $errors['service'] = "Сервис должен быть числом.";
            }

            return !array_filter($errors);  // Возвращаем true, если нет ошибок
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            $stmt = $pdo->prepare("DELETE FROM Request WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Запись удалена";
                echo "<script>window.location.href = '" . $base_url . "/admin/request/';</script>";
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = $request_query_result["Id"];
            $register_data = $_POST['register_data'] ?? '';
            $what_date = $_POST['what_date'] ?? '';
            $desc_problem = $_POST['desc_problem'] ?? '';
            $users = $_POST['users'] ?? '';
            $service = $_POST['service'] ?? '';

            if (validate_request_data($errors, $register_data, $what_date, $desc_problem, $users, $service)) {
                // Проверка на дубликат
                $stmt = $pdo->prepare("SELECT * FROM Request WHERE Register_date = :register_data AND What_date = :what_date AND Desc_problem = :desc_problem AND Users = :users AND Service_id = :service AND Id != :id");
                $stmt->execute([
                    'register_data' => $register_data,
                    'what_date' => $what_date,
                    'desc_problem' => $desc_problem,
                    'users' => $users,
                    'service' => $service,
                    'id' => $id
                ]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $_SESSION['log-mess-e'] = "Такая заявка уже существует.";
                    echo "<script>window.location.href = '". $base_url ."/admin/request/dynamic/request.php';</script>";
                    exit;
                }

                // Обновление данных
                $stmt = $pdo->prepare("UPDATE Request SET Register_date = ?, What_date = ?, Desc_problem = ?, Users = ?, Service_id = ? WHERE Id = ?");
                if ($stmt->execute([$register_data, $what_date, $desc_problem, $users, $service, $id])) {
                    $_SESSION["log-mess-s"] = "Заявка успешно обновлена";
                    echo "<script>window.location.href = '". $base_url ."/admin/request/';</script>";
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка при обновлении";
                    echo "<script>window.location.href = '". $base_url ."/admin/request/dynamic/request.php';</script>";
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
            $register_data = $_POST['register_data'] ?? '';
            $what_date = $_POST['what_date'] ?? '';
            $desc_problem = $_POST['desc_problem'] ?? '';
            $users = $_POST['users'] ?? '';
            $service = $_POST['service'] ?? '';

            if (validate_request_data($errors, $register_data, $what_date, $desc_problem, $users, $service)) {
                // Проверка на дубликат
                $stmt = $pdo->prepare("SELECT * FROM Request WHERE Register_date = :register_data AND What_date = :what_date AND Desc_problem = :desc_problem AND Users = :users AND Service_id = :service");
                $stmt->execute([
                    'register_data' => $register_data,
                    'what_date' => $what_date,
                    'desc_problem' => $desc_problem,
                    'users' => $users,
                    'service' => $service
                ]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $_SESSION['log-mess-e'] = "Такая заявка уже существует.";
                    echo "<script>window.location.href = '". $base_url ."/admin/request/dynamic/request.php';</script>";
                    exit;
                }

                // Добавление новой записи
                $stmt = $pdo->prepare("INSERT INTO Request (Register_date, What_date, Desc_problem, User_id, Service_id) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$register_data, $what_date, $desc_problem, $users, $service])) {
                    $_SESSION["log-mess-s"] = "Заявка успешно добавлена";
                    echo "<script>window.location.href = '". $base_url ."/admin/request/';</script>";
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка при добавлении";
                    echo "<script>window.location.href = '". $base_url ."/admin/request/dynamic/request.php';</script>";
                }
            } else {
                $_SESSION['form_data'] = [
                    'register_data' => $register_data,
                    'what_date' => $what_date,
                    'desc_problem' => $desc_problem,
                    'users' => $users,
                    'service' => $service
                ];
            }
        }
?>

<style>
    footer {
        display: none;
    }
</style>

<div class="container mt-5 ps2p-regular" style="font-size:13px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="<?= $base_url ?>/admin/request">Все заявки</a></li>
        <li class="breadcrumb-item active" aria-current="page">Заявка_id_<?php echo (isset($request_query_result["Id"])) ? $request_query_result["Id"] : ''; ?></li>
    </ol>
    </nav>
    <?php if ($request_query_result): ?>
    <title>Заявка: <?= htmlspecialchars($request_query_result["Register_date"]) ?></title>
    <form method="post">
        <div class="row">
            <div class="form-container">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" disabled name="id"
                               value="<?= htmlspecialchars($request_query_result["Id"]) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="register_data" class="form-label">Дата регистрации</label>
                        <input type="text" class="form-control <?= $errors['register_data'] ? 'is-invalid' : '' ?>" id="register_data"
                               name="register_data" maxlength="55" required
                               value="<?= htmlspecialchars($request_query_result["Register_date"]) ?>">
                        <div class="invalid-feedback"><?= $errors['register_data'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="what_date" class="form-label">Дата события</label>
                        <input type="date" class="form-control <?= $errors['what_date'] ? 'is-invalid' : '' ?>" id="what_date"
                               name="what_date" required
                               value="<?= htmlspecialchars($request_query_result["What_date"]) ?>">
                        <div class="invalid-feedback"><?= $errors['what_date'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="desc_problem" class="form-label">Описание проблемы</label>
                        <textarea class="form-control <?= $errors['desc_problem'] ? 'is-invalid' : '' ?>" id="desc_problem"
                               name="desc_problem" maxlength="155" required
                               ><?= htmlspecialchars($request_query_result["Desc_problem"]) ?></textarea>
                        <div class="invalid-feedback"><?= $errors['desc_problem'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="users" class="form-label">Пользователь</label>
                        <select class="form-control <?= $errors['users'] ? 'is-invalid' : '' ?>" id="users" name="users" required>
                            <option value="">Выберите пользователя</option>
                            <?php
                            $users_query = $pdo->query("SELECT Id, Name FROM Users");
                            while ($user = $users_query->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?= $user['Id'] ?>" <?= ($user['Id'] == $request_query_result['User_id']) ? 'selected' : '' ?>><?= htmlspecialchars($user['Name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['users'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Сервис</label>
                        <select class="form-control <?= $errors['service'] ? 'is-invalid' : '' ?>" id="service" name="service" required>
                            <option value="">Выберите сервис</option>
                            <?php
                            $services_query = $pdo->query("SELECT Id, Name FROM Service");
                            while ($service_item = $services_query->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?= $service_item['Id'] ?>" <?= ($service_item['Id'] == $request_query_result['Service_id']) ? 'selected' : '' ?>><?= htmlspecialchars($service_item['Name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['service'] ?></div>
                    </div>
                    <button type="submit" class="btn btn-mb-primary" name="update">Сохранить</button>
                    <form method="post">
                        <button class="btn btn-danger" name="delete">Удалить</button>
                    </form>
                    <a href="/admin/message/dynamic/message?id=<?=$message_result["Id"] ?>" class="btn btn-warning" name="update">Добавить сообщение</a>
                </div>
            </div>
        </div>
    <?php elseif ($request_query_result == 0): ?>
    <title>Создать новую заявку</title>
    <form method="post">
        <div class="row">
            <div class="form-container">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="register_data" class="form-label">Дата регистрации</label>
                        <input type="text" class="form-control <?= $errors['register_data'] ? 'is-invalid' : '' ?>" id="register_data"
                               name="register_data" maxlength="55" required
                               value="<?= isset($_SESSION['form_data']['register_data']) ? htmlspecialchars($_SESSION['form_data']['register_data']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['register_data'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="what_date" class="form-label">Дата события</label>
                        <input type="date" class="form-control <?= $errors['what_date'] ? 'is-invalid' : '' ?>" id="what_date"
                               name="what_date" required
                               value="<?= isset($_SESSION['form_data']['what_date']) ? htmlspecialchars($_SESSION['form_data']['what_date']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['what_date'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="desc_problem" class="form-label">Описание проблемы</label>
                        <input type="text" class="form-control <?= $errors['desc_problem'] ? 'is-invalid' : '' ?>" id="desc_problem"
                               name="desc_problem" maxlength="155" required
                               value="<?= isset($_SESSION['form_data']['desc_problem']) ? htmlspecialchars($_SESSION['form_data']['desc_problem']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['desc_problem'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="users" class="form-label">Пользователь</label>
                        <select class="form-control <?= $errors['users'] ? 'is-invalid' : '' ?>" id="users" name="users" required>
                            <option value="">Выберите пользователя</option>
                            <?php
                            $users_query = $pdo->query("SELECT Id, Name FROM Users");
                            while ($user = $users_query->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?= $user['Id'] ?>" <?= (isset($_SESSION['form_data']['users']) && $_SESSION['form_data']['users'] == $user['Id']) ? 'selected' : '' ?>><?= htmlspecialchars($user['Name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['users'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Сервис</label>
                        <select class="form-control <?= $errors['service'] ? 'is-invalid' : '' ?>" id="service" name="service" required>
                            <option value="">Выберите сервис</option>
                            <?php
                            $services_query = $pdo->query("SELECT Id, Name FROM Service");
                            while ($service_item = $services_query->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?= $service_item['Id'] ?>" <?= (isset($_SESSION['form_data']['service']) && $_SESSION['form_data']['service'] == $service_item['Id']) ? 'selected' : '' ?>><?= htmlspecialchars($service_item['Name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['service'] ?></div>
                    </div>
                    <button type="submit" class="btn btn-mb-primary" name="save">Сохранить</button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['form_data']); ?>
    </form>


        <?php else: ?>
        <p class="text-muted">Данной услуги не существует :(</p>
    <?php endif; ?>
<?php else: ?>
    <?php
        echo '<script type="text/javascript">';
        echo "window.location.href = '" . $base_url . "';";
        echo '</script>';
    ?>
<?php endif; ?>


</div>
</div>
<?php else: ?>
    <?php
        echo '<script type="text/javascript">';
        echo "window.location.href = '" . $base_url . "';";
        echo '</script>';
        ?>
    <?php
    endif; ?>
<?php include "../../../include/footer.php"; ?>
