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
    $query = $pdo->prepare("SELECT * FROM Model WHERE Id = :Id");
    $query->execute(['Id' => $Id]);
    $model = $query->fetch(PDO::FETCH_ASSOC);

    $errors = [
        'name' => '',
        'year' => '',
    ];

    function validate_model_data(&$errors, $name, $year) {
        if (!preg_match('/^[А-ЯЁA-Z][а-яёa-z0-9 ]{2,54}$/u', $name)) {
            $errors['name'] = "Название модели должно начинаться с заглавной буквы и содержать от 3 до 55 символов.";
        }

        if (!preg_match('/^(19|20)\d{2}$/', $year)) {
            $errors['year'] = "Год должен быть числом от 1900 до 2099.";
        }

        return !array_filter($errors);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM Model WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Модель удалена";
                echo "<script>window.location.href = '" . $base_url . "/admin/model/';</script>";
            }
        }

        if (isset($_POST['update']) || isset($_POST['save'])) {
            $name = $_POST['name'] ?? '';
            $year = $_POST['year'] ?? '';

            if (validate_model_data($errors, $name,  $year, )) {
                if (isset($_POST['update'])) {
                    $stmt = $pdo->prepare("UPDATE Model SET Name = ?, Year = ? WHERE Id = ?");
                    $stmt->execute([$name, $year, $Id]);
                    $_SESSION["log-mess-s"] = "Модель обновлена";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO Model (Name,Year) VALUES (?, ?)");
                    $stmt->execute([$name, $year]);
                    $_SESSION["log-mess-s"] = "Модель добавлена";
                }

                echo "<script>window.location.href = '" . $base_url . "/admin/model/';</script>";
            } else {
                $_SESSION['form_data'] = compact('name',  'year');
            }
        }
    }
?>

<style>footer { display: none; }</style>

<div class="container mt-5 ps2p-regular" style="font-size:13px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/model/">Модели</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $model ? "Модель_id_{$model["Id"]}" : "Новая модель" ?></li>
        </ol>
    </nav>

    <form method="post">
        <div class="row">
            <div class="col-md-8">
                <?php if ($model): ?>
                    <title>Модель: <?= htmlspecialchars($model["Name"]) ?></title>
                    <div class="mb-3">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" value="<?= $model["Id"] ?>" disabled readonly>
                    </div>
                <?php else: ?>
                    <title>Добавить новую модель</title>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Название модели</label>
                    <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>" name="name"
                           maxlength="55" required value="<?= htmlspecialchars($model["Name"] ?? ($_SESSION['form_data']['name'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="year" class="form-label">Год выпуска</label>
                    <input type="text" class="form-control <?= $errors['year'] ? 'is-invalid' : '' ?>" name="year"
                           maxlength="4" required value="<?= htmlspecialchars($model["Year"] ?? ($_SESSION['form_data']['year'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['year'] ?></div>
                </div>

                <button type="submit" class="btn btn-mb-primary" name="<?= $model ? 'update' : 'save' ?>">Сохранить</button>
                <?php if ($model): ?>
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
