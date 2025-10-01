<?php 
include __DIR__ . '/../include/header.php';
include __DIR__ . '/../include/message.php';

// Проверка авторизации
if (!isset($_SESSION["log-session"]) || !isset($_SESSION['log-session-data'])) {
    ?>
    <div class="container py-5 text-center">
        <h2 class="mb-4">Чтобы отправить заявку на ремонт, необходимо войти в аккаунт</h2>
        <a href="<?= $base_url ?>/login.php" class="btn btn-primary btn-lg">Войти</a>
    </div>
    <?php
    include __DIR__ . '/../include/footer.php';
    exit;
}

// Пользователь авторизован — получаем его ID
$userId = $_SESSION['log-session-data']['Id'];

// Получаем список услуг
$stmt = $pdo->prepare("SELECT Id, Name, Price FROM Service ORDER BY Name ASC");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = false;

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registerDate = date('Y-m-d'); // автоматически устанавливаем текущую дату
    $whatDate = trim($_POST['what_date'] ?? '');
    $descProblem = trim($_POST['desc_problem'] ?? '');
    $serviceId = intval($_POST['service'] ?? 0);

    if (!$registerDate) $errors[] = "Дата регистрации заявки обязательна.";
    if (!$descProblem) $errors[] = "Описание проблемы обязательно.";
    if ($serviceId <= 0) $errors[] = "Выберите услугу.";

    if (empty($errors)) {
        $insert = $pdo->prepare("INSERT INTO Request (Register_data, What_date, Desc_problem, Users, Service) VALUES (?, ?, ?, ?, ?)");
        $result = $insert->execute([$registerDate, $whatDate, $descProblem, $userId, $serviceId]);
        if ($result) {
            $success = true;
        } else {
            $errors[] = "Ошибка при сохранении заявки. Попробуйте ещё раз.";
        }
    }
}
?>
<style>
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(100%) sepia(100%) saturate(500%) hue-rotate(180deg);
}

.date {
        width: 100%; 
    padding: 10px 12px; 
    cursor: pointer;  
}
</style>
<div class="container py-5">
    <h2 class="mb-4">Оформить заявку на ремонт</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Заявка успешно отправлена! Мы свяжемся с вами.
        </div>
        <a href="<?= $base_url ?>/index.php" class="btn btn-primary">На главную</a>
    <?php else: ?>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="register_date" class="form-label">Дата регистрации заявки</label>
                <input type="date" id="register_date" name="register_date" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label" for="what_date">
                    Желаемая дата ремонта
                    <input type="date" id="what_date" name="what_date" class="form-control date" value="<?= htmlspecialchars($_POST['what_date'] ?? '') ?>">
                </label>
            </div>



            <div class="mb-3">
                <label for="desc_problem" class="form-label">Описание проблемы <span class="text-danger">*</span></label>
                <textarea id="desc_problem" name="desc_problem" class="form-control" rows="4" maxlength="155" required><?= htmlspecialchars($_POST['desc_problem'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="service" class="form-label">Выберите услугу <span class="text-danger">*</span></label>
                <select id="service" name="service" class="form-select" required>
                    <option value="">-- Выберите услугу --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['Id'] ?>" <?= (isset($_POST['service']) && $_POST['service'] == $service['Id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['Name']) ?> (<?= htmlspecialchars($service['Price']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success w-100">Отправить заявку</button>
        </form>

    <?php endif; ?>
</div>

<?php include __DIR__ . '/../include/footer.php'; ?>
