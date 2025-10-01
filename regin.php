<?php include "include/header.php"; ?>
<?php include "include/message.php"; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/regin.css">

<?php
$form_data = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$step = $_SESSION['reg_step'] ?? 1;

function generateCaptcha() {
    return substr(bin2hex(random_bytes(16)), 0, 5);
}

if (!isset($_SESSION['captcha']) || isset($_POST['refresh_captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

// Обновляем шаг регистрации по нажатию кнопки "Далее" или "Назад"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['next_step'])) {
        // Обработка валидации текущего шага
        if ($step === 1) {
            // Валидация логина и email
            $login = trim($_POST['login'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $errors = [];

            if (!preg_match('/^[a-zA-Z0-9_!]{5,25}$/', $login)) {
                $errors['login'] = "Логин должен быть 5-25 символов (A-Z, 0-9, _ и !)";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Некорректный email!";
            }

            // Проверка уникальности логина и email в базе
            if (!$errors) {
                $stmt = $pdo->prepare("SELECT 1 FROM Users WHERE Login = :login OR Email = :email");
                $stmt->execute(['login' => $login, 'email' => $email]);
                if ($stmt->fetch()) {
                    $errors['login'] = "Логин или email уже используется!";
                }
            }

            if ($errors) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = ['login' => $login, 'email' => $email];
            } else {
                $_SESSION['form_data']['login'] = $login;
                $_SESSION['form_data']['email'] = $email;
                $_SESSION['form_errors'] = [];
                $_SESSION['reg_step'] = 2;
                echo '<script type="text/javascript">window.location.href = "regin.php";</script>';
                exit;
            }
        } elseif ($step === 2) {
            // Валидация имени и телефона
            $name = trim($_POST['name'] ?? '');
            $ph_num = trim($_POST['ph_num'] ?? '');
            $errors = [];

            if (!preg_match('/^[а-яА-ЯёЁ]{5,15}$/u', $name)) {
                $errors['name'] = "Имя должно содержать только русские буквы (а-я), от 5 до 15 символов.";
            }
            if (!preg_match('/^\d{11}$/', $ph_num)) {
                $errors['ph_num'] = "Номер телефона должен содержать ровно 11 цифр.";
            }

            if ($errors) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data']['name'] = $name;
                $_SESSION['form_data']['ph_num'] = $ph_num;
            } else {
                $_SESSION['form_data']['name'] = $name;
                $_SESSION['form_data']['ph_num'] = $ph_num;
                $_SESSION['form_errors'] = [];
                $_SESSION['reg_step'] = 3;
                echo '<script type="text/javascript">window.location.href = "regin.php";</script>';
                exit;
            }
        } elseif ($step === 3) {
            // Валидация пароля, подтверждения и капчи
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $captcha_input = trim($_POST['captcha'] ?? '');
            $errors = [];

            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_])[a-zA-Z\d\W_]{8,}$/', $password)) {
                $errors['password'] = "Пароль должен содержать буквы, цифры и спецсимволы. Мин. 8 символов.";
            }
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = "Пароли не совпадают!";
            }
            if ($captcha_input !== $_SESSION['captcha']) {
                $errors['captcha'] = "Неправильная капча!";
                $_SESSION['captcha'] = generateCaptcha();
            }

            if ($errors) {
                $_SESSION['form_errors'] = $errors;
            } else {
                // Все данные валидны — регистрация пользователя
                $data = $_SESSION['form_data'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $status = "Пользователь";

                $stmt = $pdo->prepare("INSERT INTO Users (Login, Password, Email, Name, Phone_number, Status) 
                    VALUES (:login, :password, :email, :name, :phone, :stat)");
                $res = $stmt->execute([
                    'login' => $data['login'],
                    'password' => $hashed_password,
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'phone' => $data['ph_num'],
                    'stat' => $status
                ]);

                if ($res) {
                    $_SESSION['log-mess-s'] = "Регистрация успешна!";
                    $_SESSION["log-session"] = true;

                    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Login = :login");
                    $stmt->execute(['login' => $data['login']]);
                    $_SESSION['log-session-data'] = $stmt->fetch(PDO::FETCH_ASSOC);

                    unset($_SESSION['form_data'], $_SESSION['form_errors'], $_SESSION['reg_step']);
                echo '<script type="text/javascript">window.location.href = "/index.php";</script>';
                exit;
                } else {
                    $_SESSION['log-mess-e'] = "Ошибка регистрации.";
                }
            }
        }
    } elseif (isset($_POST['prev_step'])) {
        // Возврат назад
        if ($step > 1) {
            $_SESSION['reg_step'] = $step - 1;
        }
        echo '<script type="text/javascript">window.location.href = "regin.php";</script>';
        exit;
    }
}

// Данные для отображения формы на текущем шаге
$form_data = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$step = $_SESSION['reg_step'] ?? 1;

?>

<div class="main-content nunito-reg">
<title>Регистрация</title>
<!-- Кнопка переключения темы -->
<label class="theme-toggle" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
  <input type="checkbox" id="themeToggleMain" />  
  <span class="slider"></span>
</label>
<div class="login-container">
    <div class="image-section">
        <div class="image-text">
            <h2>Регистрируйтесь в наше сообщество</h2>
            <p>Зарегистрируйтесь, чтобы использовать все возможности личного кабинета: отслеживание заявок, настройку аккаунта и другое.</p>
            <br>
            <p class="other">*Мы никогда и ни при каких условиях не разглашаем личные данные клиентов.</p>
        </div>
    </div>
    <div class="form-section">
        <div class="card rounded-4 p-4" style="max-width: 100%; width: 100%;">
            <div class="card-body">
                <h3 class="text-center mb-4">Регистрация — шаг <?= $step ?> из 3</h3>
                <form method="POST" action="regin.php" novalidate>
                    <?php if ($step === 1): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="login" class="form-label">Логин</label>
                                <input type="text" maxlength="25" minlength="5" class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>" id="login" name="login" value="<?= htmlspecialchars($form_data['login'] ?? '') ?>" required>
                                <div class="invalid-feedback"><?= $errors['login'] ?? '' ?></div>
                                <small class="form-text text-muted">(5-25 символов, A-Z, 0-9, _ !)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
                                <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                                <small class="form-text text-muted">example@domain.com</small>
                            </div>
                        </div>
                        <button type="submit" name="next_step" class="btn btn-success w-100 btn-lg">Далее</button>
                    <?php elseif ($step === 2): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <input type="text" minlength="5" maxlength="15" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($form_data['name'] ?? '') ?>" required>
                                <div class="invalid-feedback"><?= $errors['name'] ?? '' ?></div>
                                <small class="form-text text-muted">(Только русские буквы, от 5 до 15 символов)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="text" maxlength="11" minlength="11" class="form-control <?= isset($errors['ph_num']) ? 'is-invalid' : '' ?>" id="phone" name="ph_num" value="<?= htmlspecialchars($form_data['ph_num'] ?? '') ?>" required>
                                <div class="invalid-feedback"><?= $errors['ph_num'] ?? '' ?></div>
                                <small class="form-text text-muted">(Ровно 11 цифр, без пробелов и символов)</small>
                            </div>
                        </div>
                        <div class="d-flex">
                            <button type="submit" name="prev_step" class="btn btn-secondary btn-lg me-3" style="flex: 1;">Назад</button>
                            <button type="submit" name="next_step" class="btn btn-success btn-lg" style="flex: 2;">Далее</button>
                        </div>

                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Пароль</label>
                                <input type="password" minlength="8" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                                <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                                <small class="form-text text-muted">Мин. 8 символов, буквы, цифры, спецсимволы</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Подтверждение пароля</label>
                                <input type="password" minlength="8" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                                <div class="invalid-feedback"><?= $errors['confirm_password'] ?? '' ?></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="captcha" class="form-label">Капча</label>
                            <div class="d-flex align-items-center mb-2">
                                <div class="captcha-image" style="font-weight:bold; font-size: 24px; user-select:none; background:#eee; padding:10px; border-radius:4px; letter-spacing: 5px;">
                                    <?= htmlspecialchars($_SESSION['captcha']) ?>
                                </div>
                                <button type="submit" name="refresh_captcha" class="btn btn-outline-secondary ms-3" title="Обновить капчу">⟳</button>
                            </div>
                            <input type="text" maxlength="5" class="form-control <?= isset($errors['captcha']) ? 'is-invalid' : '' ?>" id="captcha" name="captcha" required>
                            <div class="invalid-feedback"><?= $errors['captcha'] ?? '' ?></div>
                        </div>

                        <div class="d-flex">
                            <button type="submit" name="prev_step" class="btn btn-secondary btn-lg me-3" style="flex: 1;">Назад</button>
                            <button type="submit" name="next_step" class="btn btn-success btn-lg" style="flex: 2;">Зарегистрироваться</button>
                        </div>

                    <?php endif; ?>
                </form>
                 <div class="text-center mt-3">
                <a href="login" class="btn btn-primary w-100">Вход</a>
            </div>
            <a class="nav-link" href="./">Вернуться на главную</a>
            </div>
        </div>
    </div>
</div>

<?php include "include/footer.php"; ?>
