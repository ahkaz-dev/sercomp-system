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
        $query = $pdo->prepare("SELECT * FROM Users WHERE Id = :Id");
        $query->execute(['Id' => $Id]);
        $user_query_result = $query->fetch(PDO::FETCH_ASSOC);

        $errors = [
            'login' => '',
            'email' => '',
            'password' => '',
            'name' => '',
            'phone' => '',
        ];

        function validate_user_data(&$errors, $login, $password, $email, $name, $phone) {
            if (!preg_match('/^[a-zA-Z0-9_!]{5,25}$/', $login)) {
                $errors['login'] = "Логин должен быть 5-25 символов (A-Z, 0-9, _ и !)";
            }

            if (!preg_match('/^[А-ЯЁ][а-яё]{4,24}$/u', $name)) {
                $errors['name'] = "Имя должно быть от 5 до 25 символов, только на русском и с заглавной буквы";
            }

            if (!preg_match('/^\d{11}$/', $phone)) {
                $errors['phone'] = "Номер телефона должен содержать ровно 11 цифр";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@(gmail|yandex|mail)\.(ru|com)$/i', $email)) {
                $errors['email'] = "Email должен быть только на gmail, yandex или mail";
            }

            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]{6,}$/', $password)) {
                $errors['password'] = "Пароль должен содержать буквы, цифры и спецсимволы (например: ?, !, _, #). Мин. 6 символов.";
            }

            return !array_filter($errors);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
            $stmt = $pdo->prepare("UPDATE Request SET User_id = NULL WHERE User_id = ?");
            $stmt->execute([$Id]);

            $stmt = $pdo->prepare("DELETE FROM Users WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Запись удалена";
                echo "<script>location.href = '$base_url/admin/users/';</script>";
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = $user_query_result["Id"];
            
            $old_pass = $user_query_result["Password"];

            // Проверяем наличие и непустое значение (с учетом пробелов)
            $new_password = isset($_POST['password']) ? trim($_POST['password']) : '';
            
            // Логика обработки пароля
            if ($new_password !== '') {
                // Новый пароль введен - хешируем
                $password_to_save = password_hash($new_password, PASSWORD_DEFAULT);
            } else {
                // Поле пустое - сохраняем старый хеш
                $password_to_save = $old_pass;
            }
        

            $login = $_POST['login'];
            $email = $_POST['email'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            
            if ($user_query_result["Id"] == $_SESSION['log-session-data']['Id']) {
                $status = $user_query_result["Status"];
            } else {
                $status = $_POST['admin'] ?  "Администратор" : "Пользователь";
            }

            if ($status == "Администратор") {
                if (validate_user_data($errors, $login, $password_to_save, $email, $name, $phone)) {
                    $stmt = $pdo->prepare("UPDATE Users SET Login = ?, Password = ?, Email = ?, Status = ?, Phone_number = ?, Name = ? WHERE Id = ?");
                    if ($stmt->execute([$login, $password_to_save, $email, $status, $phone, $name, $id])) {
                        $_SESSION["log-mess-s"] = "Запись обновлена";
                        echo "<script>location.href = '$base_url/admin/users/';</script>";
                    } else {
                        $_SESSION["log-mess-e"] = "Ошибка запроса админ";
                    }
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка валидации";
                }
            } else {
                if (validate_user_data($errors, $login, $password_to_save, $email, $name, $phone)) {
                    
                    $stmt = $pdo->prepare("UPDATE Users SET Login = ?, Password = ?, Email = ?, Status = ?, Phone_number = ?, Name = ? WHERE Id = ?");
                    if ($stmt->execute([$login, $password_to_save, $email, $status, $phone, $name, $id])) {
                        $_SESSION["log-mess-s"] = "Запись обновлена";
                        echo "<script>location.href = '$base_url/admin/users/';</script>";
                    } else {
                        $_SESSION["log-mess-e"] = "Ошибка запроса";
                    }
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка валидации";
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];

            $admin = isset($_POST['admin']) && $_POST['admin'] == '1' ? 1 : 0;
            $role = $admin ? 'Администратор' : 'Пользователь';
            

            if (validate_user_data($errors, $login, $password, $email, $name, $phone)) {
                $stmt = $pdo->prepare("SELECT * FROM Users WHERE Login = :login OR Email = :email");
                $stmt->execute(['login' => $login, 'email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $_SESSION['log-mess-e'] = "Логин или email уже используется!";
                    echo "<script>window.location.href = '$base_url/admin/users/';</script>";
                    exit;
                }

                $password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO Users (Login, Password, Email, Name, Phone_number, Status) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$login, $password, $email, $name, $phone, $role])) {
                    $_SESSION["log-mess-s"] = "Запись сохранена";
                    echo "<script>location.href = '$base_url/admin/users/';</script>";
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка добавления";
                    echo "<script>location.href = '$base_url/admin/users/dynamic/users.php';</script>";
                }
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
        <li class="breadcrumb-item active" aria-current="page"><a href="<?= $base_url ?>/admin/users">Юзера</a></li>
        <li class="breadcrumb-item active" aria-current="page">Аккаунт_id_<?php echo (isset($user_query_result["Id"])) ? $user_query_result["Id"] : ''; ?></li>
    </ol>
    </nav>
        <?php if ($user_query_result): ?>
            <title>Пользователь: <?= $user_query_result["Login"] ?></title>
            <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="form-container">
                    <div class="col-md-8">
                            <div class="mb-3">
                                <label for="id" class="form-label">ID</label>
                                <input type="text" class="form-control" id="id" disabled name="id" value="<?= htmlspecialchars($user_query_result["Id"]) ?>" readonly>
                            </div>
                            <?php if ($user_query_result["Id"] == $_SESSION['log-session-data']['Id']): ?>
                                <div class="mb-3" style="pointer-events: none;opacity: 0.6;">
                                    <label for="id" class="form-label">Роль доступа</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="admin" name="admin" <?php echo ($user_query_result["Status"]=="Администратор") ? 'checked' : ''; ?> >
                                        <label class="form-check-label" for="admin">
                                            Админ
                                        </label>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="id" class="form-label">Роль доступа</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="admin" id="admin" name="admin" <?= $user_query_result["Status"] == "Администратор" ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="admin">
                                            Админ
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="login" class="form-label">Логин пользователя</label>
                                <input type="text" class="form-control <?= $errors['login'] ? 'is-invalid' : '' ?>" id="login" name="login" value="<?= htmlspecialchars($user_query_result["Login"]) ?>" maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['login'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя пользователя</label>
                                <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($user_query_result["Name"]) ?>" maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Новый пароль</label>
                                <input class="form-control <?= $errors['password'] ? 'is-invalid' : '' ?>" id="password" name="password" maxlength="25" ?>
                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                <small>Оставьте поле пустым, если не хотите менять пароль</small>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Электронная почта</label>
                                <input type="email" class="form-control <?= $errors['email'] ? 'is-invalid' : '' ?>" name="email" id="email" maxlength="320" required value="<?= htmlspecialchars($user_query_result["Email"]) ?>">
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Номер телефона</label>
                                <input type="text" class="form-control <?= $errors['phone'] ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars($user_query_result["Phone_number"]) ?>" maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            </div>
                            <button type="submit" class="btn btn-mb-primary" name="update">Сохранить</button>
                            <?php if (!($user_query_result["Id"] == $_SESSION['log-session-data']['Id'])): ?>
                                <form method="post">
                                    <button class="btn btn-danger" name="delete">Удалить</button>
                                </form>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php elseif($user_query_result == 0): ?>
            <title>Создать нового пользователя</title>
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="form-container">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="id" class="form-label">Роль доступа</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="admin" id="admin" name="admin">
                                    <label class="form-check-label" for="admin">
                                        Админ
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="login" class="form-label">Логин пользователя</label>
                                <input type="text" class="form-control <?= $errors['login'] ? 'is-invalid' : '' ?>" id="login" name="login" maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['login'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Имя пользователя</label>
                                <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>" id="name" name="name"  maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Новый пароль</label>
                                <input class="form-control <?= $errors['password'] ? 'is-invalid' : '' ?>" id="password" name="password" maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Электронная почта</label>
                                <input type="email" class="form-control <?= $errors['email'] ? 'is-invalid' : '' ?>" name="email" id="email" maxlength="320" required>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Номер телефона</label>
                                <input type="text" class="form-control <?= $errors['phone'] ? 'is-invalid' : '' ?>"  id="phone" name="phone"  maxlength="25" required>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            </div>
                            <button type="submit" class="btn btn-mb-primary" name="save">Сохранить</button>
                        </div>
                    </div>
                </div>
            </form>

            <?php else: ?>
            <p class="text-muted">Данного юзера не существует :(</p>
        <?php endif; ?>
    <?php else: ?>
        <?php
            echo '<script type="text/javascript">';
            echo "window.location.href = '$base_url'';";
            echo '</script>';
        ?>
    <?php endif; ?>
</div>
</div>
<?php else: ?>
    <?php
            echo '<script type="text/javascript">';
            echo "window.location.href = '$base_url'';";
            echo '</script>';
        ?>
    <?php
    endif; ?>
<?php include "../../../include/footer.php"; ?>