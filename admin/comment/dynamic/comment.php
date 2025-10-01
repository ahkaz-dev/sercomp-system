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
    $query = $pdo->prepare("SELECT * FROM User_comment WHERE Id = :Id");
    $query->execute(['Id' => $Id]);
    $review = $query->fetch(PDO::FETCH_ASSOC);

    $errors = [
        'creater' => '',
        'comment' => '',
        'date' => '',
        'rate' => ''
    ];

    function validate_review_data(&$errors, $creater, $comment, $date, $rate) {
        // Имя: только русские буквы, пробелы, точки, запятые, тире. Начинается с заглавной.
        if (!preg_match('/^[А-ЯЁ][а-яёЁ\s\-,\.]{2,54}$/u', $creater)) {
            $errors['creater'] = "Имя должно содержать только русские буквы, начинаться с заглавной и быть длиной от 3 до 55 символов.";
        }
    
        // Комментарий: от 3 до 255 символов
            if (mb_strlen($comment) < 3 || mb_strlen($comment) > 1000) {
                $errors['comment'] = "Комментарий должен содержать от 3 до 1000 символов.";
            }

    
        // Проверка даты
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors['date'] = "Дата должна быть в формате ГГГГ-ММ-ДД.";
        } else {
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            $minDate = new DateTime('2023-01-01');
            $maxDate = new DateTime();
    
            if (!$dateObj || $dateObj < $minDate || $dateObj > $maxDate) {
                $errors['date'] = "Дата должна быть не раньше 2023-01-01 и не позже сегодняшнего дня.";
            }
        }
    
        // Оценка от 1 до 5
        if (!in_array($rate, ['1', '2', '3', '4', '5'])) {
            $errors['rate'] = "Оценка должна быть от 1 до 5.";
        }
    
        return !array_filter($errors);
    }
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM User_comment WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Отзыв удалён";
                echo "<script>window.location.href = '" . $base_url . "/admin/comment/';</script>";
            }
        }

        if (isset($_POST['update']) || isset($_POST['save'])) {
            $creater = $_POST['creater'] ?? '';
            $comment = $_POST['comment'] ?? '';
            $date = $_POST['date'] ?? '';
            $rate = $_POST['rate'] ?? '';
            $approved = isset($_POST['approved']) ? 1 : 0;

            if (validate_review_data($errors, $creater, $comment, $date, $rate)) {
                if (isset($_POST['update'])) {
                    $stmt = $pdo->prepare("UPDATE User_comment SET Creater = ?, Comment = ?, Date = ?, Rate = ?, Approved = ? WHERE Id = ?");
                    $stmt->execute([$creater, $comment, $date, $rate, $approved, $Id]);
                    $_SESSION["log-mess-s"] = "Отзыв обновлён";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO User_comment (Creater, Comment, Date, Rate, Approved) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$creater, $comment, $date, $rate, $approved]);
                    $_SESSION["log-mess-s"] = "Отзыв добавлен";
                }

                echo "<script>window.location.href = '" . $base_url . "/admin/comment/';</script>";
            } else {
                $_SESSION['form_data'] = compact('creater', 'comment', 'date', 'rate', 'approved');
            }
        }

    }
?>

<style>footer { display: none; }</style>

<div class="container mt-5 ps2p-regular" style="font-size:13px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/comment/">Отзывы</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $review ? "Отзыв_id_{$review["Id"]}" : "Новый отзыв" ?></li>
        </ol>
    </nav>

    <form method="post">
        <div class="row">
            <div class="col-md-8">
                <?php if ($review): ?>
                    <title>Отзыв: <?= htmlspecialchars($review["Creater"]) ?></title>
                    <div class="mb-3">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" value="<?= $review["Id"] ?>" disabled readonly>
                    </div>
                <?php else: ?>
                    <title>Добавить новый отзыв</title>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="creater" class="form-label">Автор</label>
                    <input type="text" class="form-control <?= $errors['creater'] ? 'is-invalid' : '' ?>" name="creater"
                           maxlength="55" required value="<?= htmlspecialchars($review["Creater"] ?? ($_SESSION['form_data']['creater'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['creater'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Комментарий</label>
                    <textarea class="form-control <?= $errors['comment'] ? 'is-invalid' : '' ?>" name="comment" rows="3" required><?= htmlspecialchars($review["Comment"] ?? ($_SESSION['form_data']['comment'] ?? '')) ?></textarea>
                    <div class="invalid-feedback"><?= $errors['comment'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Дата</label>
                        <input type="date" class="form-control <?= $errors['date'] ? 'is-invalid' : '' ?>" name="date" value="<?= htmlspecialchars($review["Date"]) ?>">

                    <div class="invalid-feedback"><?= $errors['date'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="rate" class="form-label">Оценка (1-5)</label>
                    <input type="number" class="form-control <?= $errors['rate'] ? 'is-invalid' : '' ?>" name="rate"
                           min="1" max="5" required value="<?= htmlspecialchars($review["Rate"] ?? ($_SESSION['form_data']['rate'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['rate'] ?></div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="approved" id="approved"
                        <?= (!empty($review["Approved"]) || !empty($_SESSION['form_data']['approved'])) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="approved">Одобрено</label>
                </div>


                <button type="submit" class="btn btn-mb-primary" name="<?= $review ? 'update' : 'save' ?>">Сохранить</button>
                <?php if ($review): ?>
                    <button type="submit" class="btn btn-danger" name="delete">Удалить</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <?php unset($_SESSION['form_data']); ?>
</div>

<?php else: ?>
    <script>window.location.href = '<?= $base_url ?>';</script>
<?php endif; ?>
<?php include "../../../include/footer.php"; ?>
