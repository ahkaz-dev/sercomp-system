<?php include __DIR__ . "/../include/header.php"; ?> 
<?php include __DIR__ ."/../include/message.php"; 

if (!isset($_SESSION['log-session']) || !isset($_SESSION['log-session-data'])) {
    http_response_code(404);
    include __DIR__ . '/../include/error/404.php';
    include __DIR__ . '/../include/footer.php'; 
    exit;
}

$userId = $_SESSION['log-session-data']['Id'];

// Получаем заявки пользователя с данными сервиса и последним сообщением
$stmt = $pdo->prepare("
    SELECT r.*, s.Name AS ServiceName, s.Price,
           m.Text AS MessageText, m.Status AS MessageStatus, m.Created_at AS MessageDate
    FROM Request r
    JOIN Service s ON r.Service = s.Id
    LEFT JOIN (
        SELECT m1.*
        FROM Message m1
        JOIN (
            SELECT Request, MAX(Created_at) AS MaxDate
            FROM Message
            GROUP BY Request
        ) m2 ON m1.Request = m2.Request AND m1.Created_at = m2.MaxDate
    ) m ON r.Id = m.Request
    WHERE r.Users = ?
    ORDER BY r.Id DESC
");
$stmt->execute([$userId]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    $stmt = $pdo->prepare("SELECT * FROM Request WHERE Id = ? AND Users = ?");
    $stmt->execute([$deleteId, $userId]);
    $requestToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($requestToDelete) {
                    $_SESSION["log-mess-s"] = "Заявка удалена";

        // Удаляем саму заявку
        $pdo->prepare("DELETE FROM Request WHERE Id = ?")->execute([$deleteId]);

        // Перенаправляем обратно на страницу без параметров
                echo "<script>window.location.href = '/my-request/';</script>";

    } else {
    }
}

?>

<div class="container py-4">
    <h1>Мои заявки</h1>

    <?php if (empty($requests)): ?>
        <p>Вы ещё не отправляли заявки.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($requests as $req): ?>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            Заявка #<?= $req['Id'] ?> — <?= htmlspecialchars($req['ServiceName']) ?>
                        </div>
                        <div class="card-body">
                            <p><strong>Описание проблемы:</strong><br><?= nl2br(htmlspecialchars($req['Desc_problem'])) ?></p>
                            <p><strong>Дата регистрации:</strong> <?= htmlspecialchars($req['Register_data']) ?></p>
                            <p><strong>Желаемая дата ремонта:</strong> <?= htmlspecialchars($req['What_date']) ?></p>
                            <hr>
                            <?php if ($req['MessageText']): ?>
                                <p><strong>Статус:</strong> <span class="badge bg-info"><?= htmlspecialchars($req['MessageStatus']) ?></span></p>
                                <p><strong>Комментарий:</strong><br><?= nl2br(htmlspecialchars($req['MessageText'])) ?></p>
                                <p class="text-muted">Обновлено: <?= $req['MessageDate'] ?></p>
                            <?php else: ?>
                                <p class="text-muted">Статус ещё не назначен.</p>
                            <?php endif; ?>
                            <a href="edit?id=<?= $req['Id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $req['Id'] ?>)">Удалить</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Вы точно хотите удалить эту заявку? Мы уже начали работать над ней!')) {
        window.location.href = '?delete=' + id;
    }
}
</script>

<?php include __DIR__ . '/../include/footer.php'; ?>
