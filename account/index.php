<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>
<?php
$userData = $_SESSION['log-session-data'];
// Получение истории заявок
$stmt = $pdo->prepare("
  SELECT r.Id, r.Register_data, r.What_date, r.Desc_problem,
         s.Name AS ServiceName,
         d.Name AS DeviceName,
         m.Name AS ModelName
  FROM Request r
  LEFT JOIN Service s ON r.Service = s.Id
  LEFT JOIN Users u ON r.Users = u.Id
  LEFT JOIN Device d ON d.Id = r.Id
  LEFT JOIN Model m ON d.Model = m.Id
  WHERE r.Users = ?
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

<style>
/* --- стили, в основном оставил те же, добавил форму --- */

.profile-container {
  max-width: 800px;
  margin: 40px auto;
  padding: 20px;
  background: var(--card-bg, #fff);
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.user-info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  gap: 10px;
  flex-wrap: wrap;
}

.user-info-row label {
  min-width: 100px;
  font-weight: 600;
}

.user-info-row span, .user-info-row input {
  flex-grow: 1;
  padding: 6px 10px;
  background-color: #f0f0f0;
  border-radius: 6px;
  min-width: 150px;
  border: 1px solid transparent;
  transition: border-color 0.3s;
  color: black;
}

.user-info-row input {
  background-color: #fff;
  border-color: #ccc;
}

.user-info-row input:focus {
  outline: none;
  border-color: var(--primary, #0066ff);
}

.user-info-row button {
  background-color: var(--button-bg, #0066ff);
  color: var(--button-text, #fff);
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
  min-width: 90px;
}

.user-info-row button:hover {
  background-color: #0052cc;
}

.status {
  margin-top: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-value {
  font-weight: bold;
}

.tooltip-icon {
  display: inline-block;
  margin-left: 8px;
  width: 18px;
  height: 18px;
  background-color: var(--primary, #0066ff);
  color: #fff;
  font-size: 13px;
  line-height: 18px;
  text-align: center;
  border-radius: 50%;
  position: relative;
  cursor: pointer;
}

.tooltip-text {
  visibility: hidden;
  background-color: var(--primary, #0066ff);
  color: #fff;
  text-align: left;
  padding: 10px;
  border-radius: 6px;
  position: absolute;
  z-index: 1;
  bottom: 140%;
  left: 50%;
  transform: translateX(-50%);
  min-width: 220px;
  opacity: 0;
  transition: opacity 0.3s;
  font-size: 13px;
}

.tooltip-icon:hover .tooltip-text {
  visibility: visible;
  opacity: 1;
}

.order-history {
  margin-top: 20px;
  list-style-type: none;
  padding: 0;
}

.order-history li {
  background: var(--bg-color, #f4f4f4);
  padding: 10px 15px;
  margin-bottom: 10px;
  border-left: 5px solid var(--primary, #0066ff);
  border-radius: 8px;
}



.delete-btn {
  margin-top: 40px;
  background-color: #e03e2f;
  border: none;
  color: white;
  padding: 10px 24px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}
.btn-primary {
  margin-top: 40px;
  border: none;
  color: white;
  background-color: #3391ff;
  padding: 10px 24px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}
.delete-btn:hover {
  background-color: #b73024;
}
.modal {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  transition: opacity 0.3s ease, visibility 0.3s ease;
  z-index: 999;
}

.modal.show {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

.modal-content {
  background: #fff;
  padding: 30px 25px;
  border-radius: 16px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 420px;
  text-align: center;
  position: relative;
  transform: translateY(-30px);
  opacity: 0;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal.show .modal-content {
  transform: translateY(0);
  opacity: 1;
}

.modal-icon {
  font-size: 40px;
  margin-bottom: 10px;
}

.modal-content h3 {
  margin-top: 0;
  font-size: 22px;
  margin-bottom: 10px;
}

.modal-content p {
  font-size: 16px;
  margin-bottom: 25px;
  color: #444;
}


.confirm {
  background-color: #e03e2f;
  color: #fff;
}

.confirm:hover {
  background-color: #c03527;
}

.cancel {
  background-color: #ccc;
  color: #333;
}

.cancel:hover {
  background-color: #bbb;
}


@media (max-width: 600px) {
  .user-info-row {
    flex-direction: column;
    align-items: stretch;
  }

  .user-info-row button {
    width: 100%;
    margin-top: 8px;
  }

  .status {
    flex-direction: column;
    align-items: flex-start;
  }

  .tooltip-text {
    min-width: 100%;
    left: 0;
    transform: none;
  }
    .modal-content {
    padding: 20px;
  }
}



/* === Dark Theme Overrides === */

body.dark-theme {
  background-color: #121212;
  color: #f0f0f0;
}

body.dark-theme .profile-container {
  background: #1e1e1e;
  box-shadow: 0 8px 20px rgba(255, 255, 255, 0.05);
}

body.dark-theme .user-info-row span,
body.dark-theme .user-info-row input {
  background-color: #2a2a2a;
  color: #f0f0f0;
}

body.dark-theme .user-info-row input {
  border-color: #444;
}

body.dark-theme .user-info-row input:focus {
  border-color: #3391ff;
  background-color: #1e1e1e;
}

body.dark-theme .user-info-row button {
  background-color: #3391ff;
  color: #fff;
}

body.dark-theme .user-info-row button:hover {
  background-color: #2274cc;
}

body.dark-theme .tooltip-icon {
  background-color: #3391ff;
}

body.dark-theme .tooltip-text {
  background-color: #3391ff;
  color: #fff;
}

body.dark-theme .order-history li {
  background-color: #2a2a2a;
  color: #e0e0e0;
  border-left-color: #3391ff;
}

body.dark-theme .btn-primary {
  background-color: #0a4d9b;
  color: white;
}

body.dark-theme .btn-primary:hover {
  background-color: #2274cc;
}

body.dark-theme .delete-btn {
  background-color: #b73024;
}

body.dark-theme .delete-btn:hover {
  background-color: #8a241b;
}

/* Modal */
body.dark-theme .modal-content {
  background-color: #2b2b2b;
  color: #f0f0f0;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
}

body.dark-theme .modal-content p {
  color: #ccc;
}

body.dark-theme .confirm {
  background-color: #e03e2f !important;
  color: #fff;
}

body.dark-theme .confirm:hover {
  background-color: #992016 !important;
}

body.dark-theme .cancel {
  background-color: #555;
  color: #eee;
}

body.dark-theme .cancel:hover {
  background-color: #666;
}



</style>

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

  <h3 style="margin-top: 40px;">🛠 История ремонтов</h3>
  <ul class="order-history">
    <?php if ($requests): ?>
      <?php foreach ($requests as $r): ?>
        <li>
          №<?= $r['Id'] ?> — <?= htmlspecialchars($r['ServiceName']) ?>
          — <?= htmlspecialchars($r['Register_data']) ?>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <li>История ремонтов пуста.</li>
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
