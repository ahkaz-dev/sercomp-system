<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/account.css">

<?php
$userData = $_SESSION['log-session-data'];
// Получение истории заявок
$stmt = $pdo->prepare("
  SELECT r.Id, r.Register_date, r.What_date, r.Desc_problem,
         s.Name AS ServiceName,
         d.Name AS DeviceName,
         m.Name AS ModelName
  FROM Request r
  LEFT JOIN Service s ON r.Service_id = s.Id
  LEFT JOIN Users u ON r.User_id = u.Id
  LEFT JOIN Device d ON d.Id = r.Id
  LEFT JOIN Model m ON d.Model_id = m.Id
  WHERE r.User_id = ?
  ORDER BY r.Id DESC
");
$stmt->execute([$userData["Id"]]);
$requests = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if ($_POST['action'] === 'update_profile') {
    $fields = ['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone_number'];
    $updateFields = [];
    $params = [];

    // Валидация имени (только русские буквы, пробелы и дефис)
    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
        if (!preg_match('/^[А-Яа-яЁё\s\-]+$/u', $name)) {
            $_SESSION["log-mess-e"] = "Имя должно содержать только русские буквы.";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        }
        $updateFields[] = "Name = ?";
        $params[] = $name;
    }

    // Валидация телефона (только 11 цифр)
    if (isset($_POST['phone'])) {
        $phone = trim($_POST['phone']);
        if (!preg_match('/^\d{11}$/', $phone)) {
            $_SESSION["log-mess-e"] = "Телефон должен содержать только 11 цифр.";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        }
        $updateFields[] = "Phone_number = ?";
        $params[] = $phone;
    }

    // Валидация email и проверка на уникальность
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["log-mess-e"] = "Некорректный формат email.";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        }

        // Проверка, не занят ли уже email другим пользователем
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE Email = ? AND Id != ?");
        $checkStmt->execute([$email, $userData['Id']]);
        if ($checkStmt->fetchColumn() > 0) {
            $_SESSION["log-mess-e"] = "Этот email уже используется другим пользователем.";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        }

        $updateFields[] = "Email = ?";
        $params[] = $email;
    }

    if ($updateFields) {
        $params[] = $userData['Id'];
        $sql = "UPDATE Users SET " . implode(', ', $updateFields) . " WHERE Id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            // Обновляем данные в сессии
            foreach ($fields as $postKey => $dbCol) {
                if (isset($_POST[$postKey])) {
                    $userData[$dbCol] = trim($_POST[$postKey]);
                }
            }
            $_SESSION['log-session-data'] = $userData;
            $_SESSION["log-mess-s"] = "Профиль успешно обновлен.";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        } else {
            $_SESSION["log-mess-e"] = "Ошибка при обновлении данных.";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        }
    }
}


    if ($_POST['action'] === 'delete_account') {
        try {
            // Обновим все заявки пользователя: укажем, что аккаунт был удалён
            $stmt = $pdo->prepare("UPDATE Request SET Users = NULL WHERE Users = ?");
            $stmt->execute([$userData['Id']]);

            // Теперь можно безопасно удалить пользователя
            $stmt = $pdo->prepare("DELETE FROM Users WHERE Id = ?");
            $stmt->execute([$userData['Id']]);

            session_destroy();
            $_SESSION["log-mess-s"] = "Аккаунт был удален";
            echo "<script>window.location.href = '/';</script>";
            exit;
        } catch (PDOException $e) {
            $_SESSION["log-mess-e"] = "Ошибка при удалении аккаунта";
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        }
    }

}

?>

<div class="profile-container">
  <h2>👤 Профиль пользователя</h2>

  <?php if (!empty($successMsg)): ?>
    <div style="color: green; font-weight: bold; margin-bottom: 10px;"><?= htmlspecialchars($successMsg) ?></div>
  <?php endif; ?>
  <?php if (!empty($errorMsg)): ?>
    <div style="color: red; font-weight: bold; margin-bottom: 10px;"><?= htmlspecialchars($errorMsg) ?></div>
  <?php endif; ?>

  <form method="POST" id="profile-form">
    <input type="hidden" name="action" value="update_profile">

    <div class="user-info">
      <div class="user-info-row">
        <label for="name">Имя:</label>
        <input id="name" name="name" type="text" value="<?= htmlspecialchars($userData['Name']) ?>" required>
      </div>

      <div class="user-info-row">
        <label for="email">Email:</label>
        <input id="email" name="email" type="email" value="<?= htmlspecialchars($userData['Email']) ?>" required>
      </div>

      <div class="user-info-row">
        <label for="phone">Телефон:</label>
        <input id="phone" name="phone" type="tel" value="<?= htmlspecialchars($userData['Phone_number']) ?>">
      </div>

      <div class="user-info-row">
        <label>Логин:</label>
        <span><?= htmlspecialchars($userData['Login']) ?></span>
        <span class="readonly">Неизменяемый</span>
      </div>

      <div class="status">
        <label>Статус:</label>
        <span class="status-value"><?= htmlspecialchars($userData['Status']) ?></span>
        <div class="tooltip-icon">?
          <div class="tooltip-text">
            <?= match ($userData['Status']) {
              'Пользователь' => 'Вы управляете своими заявками',
              'Администратор' => 'Полный контроль над системой и пользователями.',
            } ?>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-primary" style="margin-top: 30px;">Сохранить изменения</button>
    </div>
  </form>

  <h3 style="margin-top: 40px;">🛠 Мои заявки</h3>
  <ul class="order-history">
    <?php if ($requests): ?>
      <?php foreach ($requests as $r): ?>
        <li>
          №<?= $r['Id'] ?> — <?= htmlspecialchars($r['ServiceName']) ?>
          — <?= htmlspecialchars($r['Register_date']) ?>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <li>Заявок нет.</li>
    <?php endif; ?>
  </ul>

  <button class="delete-btn" id="delete-account-btn">Удалить аккаунт</button>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="delete-modal" class="modal">
  <div class="modal-content">
    <div class="modal-icon">⚠️</div>
    <h3>Удаление аккаунта</h3>
    <p>Вы уверены, что хотите удалить аккаунт? Это действие <strong>необратимо</strong>.</p>
    <form method="POST" style="display:inline;">
      <input type="hidden" name="action" value="delete_account">
      <button type="submit" class="btn confirm">Да, удалить</button>
    </form>
    <br>
    <button class="btn cancel" id="cancel-delete">Отмена</button>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
  const deleteBtn = document.getElementById('delete-account-btn');
  const modal = document.getElementById('delete-modal');
  const cancelBtn = document.getElementById('cancel-delete');
  const modalContent = modal.querySelector('.modal-content');

  if (!deleteBtn || !modal || !cancelBtn) return; // защита от ошибок

  deleteBtn.addEventListener('click', () => {
    modal.classList.add('show');
  });

  cancelBtn.addEventListener('click', () => {
    modal.classList.remove('show');
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.remove('show');
    }
  });
});
</script>



<?php include __DIR__ . '/../include/footer.php'; ?>
