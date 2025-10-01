<?php include "include/header.php"; ?>
<?php include "include/message.php"; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/login.css">

<?php 

if (isset($_POST['login-button'])) {
    if (!empty($_POST['login']) && !empty($_POST['password'])) {
        if (!isset($_POST['not_robot'])) { // Проверка чекбокса
            $_SESSION['log-mess-e'] = "Вы должны подтвердить, что вы не робот!";
            echo '<script type="text/javascript">';
            echo 'window.location.href = "login.php";';
            echo '</script>';
            exit;
        }

        $login = $_POST['login'];
        $password = $_POST['password']; 
        
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE Login = :login");
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['Password'])) {
                $_SESSION['log-session-data'] = $user;
                $_SESSION["log-session"] = true;

                $stmt = $pdo->prepare("
                    SELECT r.Id AS RequestId, m.Status
                    FROM Message m
                    JOIN Request r ON m.Request = r.Id
                    WHERE r.Users = ? AND m.Notified = 0
                    ORDER BY m.Created_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$_SESSION['log-session-data']['Id']]);
                $newMessage = $stmt->fetch(PDO::FETCH_ASSOC);


                if ($newMessage) {
                    $_SESSION['log-mess-warn'] = "У заявки #{$newMessage['RequestId']} новый статус: {$newMessage['Status']}";

                    $update = $pdo->prepare("
                        UPDATE Message SET Notified = 1
                        WHERE Request = ? AND Notified = 0
                    ");
                    $update->execute([$newMessage['RequestId']]);
                }


                $_SESSION["log-mess-s"] = "Вы вошли в аккаунт";
                echo '<script type="text/javascript">';
                echo 'window.location.href = "/";';
                echo '</script>';
            } else {
                $_SESSION['log-mess-e'] = "Ошибка ввода";
            }
        } else {
            $_SESSION['log-mess-e'] = "Пользователь не найден";
        }
    } else {
        $_SESSION['log-mess-e'] = "Заполните все поля";
    }
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php";';
    echo '</script>';
}

?>
<title>Авторизация</title>
<div class="main-content">

<div class="login-container">
        <div class="image-section">
            <div class="image-text">
                <img src="<?= $base_url ?>/static/svg/invert-logo.svg" alt="Логотип" viewBox="0 0 24 24">
                <h2>Авторизуйтесь чтобы открыть больше возможностей</h2>
                <p>Отслеживайте свои заявки в нашей системе!</p>
            </div>
        </div>
        <div class="form-section">
            <div class="card rounded-4 p-4" style="max-width: 400px; width: 100%;">
                <div class="card-body">
                    <h3 class="text-center mb-4">Войти в аккаунт</h3>
                    <form method="post">
                        <div class="mb-3">
                            <label for="inputLogin" class="form-label">Логин</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" pattern=".+" class="form-control" id="inputLogin" name="login" placeholder="Введите логин" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Пароль</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" pattern=".+" class="form-control" id="inputPassword" name="password" placeholder="Введите пароль" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check d-flex align-items-center">
                            <input class="form-check-input me-2" type="checkbox" id="not_robot" name="not_robot" required>
                            <label class="form-check-label fw-bold" for="not_robot">Я не робот</label>
                        </div>
                        <button type="submit" name="login-button" class="btn btn-success w-100 btn-lg">Войти</button>
                        <div class="text-center mt-3">
                            <a href="regin.php" class="btn btn-secondary w-100">Регистрация</a>
                        </div>
                        <a class="nav-link" href="./">Вернуться на главную</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "include/footer.php"; ?>