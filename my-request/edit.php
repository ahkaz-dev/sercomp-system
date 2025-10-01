<?php include __DIR__ . "/../include/header.php"; ?>
<?php include __DIR__ ."/../include/message.php"; ?>
<?php
if (!isset($_SESSION['log-session']) || !isset($_SESSION['log-session-data'])) {
    http_response_code(404);
    include __DIR__ . '/../include/error/404.php';
     include __DIR__ . '/../include/footer.php'; 

    exit;
}

$userId = $_SESSION['log-session-data']['Id'];
$requestId = $_GET['id'] ?? null;

if (!is_numeric($requestId)) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
     include __DIR__ . '/../include/footer.php'; 
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM Request WHERE Id = ? AND Users = ?");
$stmt->execute([$requestId, $userId]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
     include __DIR__ . '/../include/footer.php'; 

    exit;
}

$services = $pdo->query("SELECT Id, Name FROM Service ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registerDate = date('Y-m-d'); // устанавливаем текущую дату при редактировании

    $whatDate = trim($_POST['what_date'] ?? '');
    $descProblem = trim($_POST['desc_problem'] ?? '');
    $serviceId = intval($_POST['service'] ?? 0);

    if (!$registerDate) {
        $errors[] = "Дата регистрации обязательна.";
    }
    if (!$descProblem) {
        $errors[] = "Описание проблемы обязательно.";
    }
    if ($serviceId <= 0) {
        $errors[] = "Выберите услугу.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE Request SET Register_data = ?, What_date = ?, Desc_problem = ?, Service = ? WHERE Id = ? AND Users = ?");
        $updated = $stmt->execute([$registerDate, $whatDate, $descProblem, $serviceId, $requestId, $userId]);

        if ($updated) {
            $success = true;
            $request = array_merge($request, [
                'Register_data' => $registerDate,
                'What_date' => $whatDate,
                'Desc_problem' => $descProblem,
                'Service' => $serviceId,
            ]);
                    $_SESSION["log-mess-s"] = "Заявка обновлена";

                echo "<script>window.location.href = '/my-request/';</script>";

        } else {
            $errors[] = "Ошибка при обновлении. Попробуйте позже.";
        }
    }
}
?>

<div class="container py-4">
    <h1>Редактировать заявку #<?= htmlspecialchars($requestId) ?></h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Заявка успешно обновлена.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach($errors as $error): ?>
                    <li><?=htmlspecialchars($error)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-3">
<div class="mb-3">
    <label for="register_date" class="form-label">Дата регистрации <span class="text-danger">*</span></label>
    <input type="date" id="register_date" name="register_date" class="form-control"
           value="<?= date('Y-m-d') ?>" readonly>
</div>


        <div class="mb-3">
            <label for="what_date" class="form-label">Желаемая дата ремонта</label>
            <input type="date" id="what_date" name="what_date" class="form-control"
                   value="<?= htmlspecialchars($_POST['what_date'] ?? $request['What_date']) ?>">
        </div>

        <div class="mb-3">
            <label for="desc_problem" class="form-label">Описание проблемы <span class="text-danger">*</span></label>
            <textarea id="desc_problem" name="desc_problem" class="form-control" rows="4" maxlength="155" required><?= htmlspecialchars($_POST['desc_problem'] ?? $request['Desc_problem']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="service" class="form-label">Выберите услугу <span class="text-danger">*</span></label>
            <select id="service" name="service" class="form-select" required>
                <option value="">-- Выберите услугу --</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= $service['Id'] ?>"
                        <?= (($_POST['service'] ?? $request['Service']) == $service['Id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service['Name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="/my-request/" class="btn btn-secondary">Отмена</a>
    </form>
</div>

<?php include __DIR__ . '/../include/footer.php'; ?>
