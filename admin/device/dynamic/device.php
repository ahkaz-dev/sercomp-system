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
    $query = $pdo->prepare("SELECT * FROM Device WHERE Id = :Id");
    $query->execute(['Id' => $Id]);
    $device = $query->fetch(PDO::FETCH_ASSOC);

    $errors = [
        'name' => '',
        'desc' => '',
        'serial_num' => ''
    ];

    function validate_device_data(&$errors, $name, $desc, $serial_num) {
        if (!preg_match('/^[А-ЯЁA-Z][а-яёa-z0-9 ]{2,54}$/u', $name)) {
            $errors['name'] = "Название должно начинаться с заглавной буквы и содержать от 3 до 55 символов.";
        }

        if (!preg_match('/^.{3,55}$/u', $desc)) {
            $errors['desc'] = "Описание должно содержать от 3 до 55 символов.";
        }

        if (!preg_match('/^[A-Za-z0-9\-]{3,55}$/', $serial_num)) {
            $errors['serial_num'] = "Серийный номер должен содержать от 3 до 55 символов (буквы, цифры, дефис).";
        }

        return !array_filter($errors);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM Device WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Устройство удалено";
                echo "<script>window.location.href = '" . $base_url . "/admin/device/';</script>";
            }
        }

        if (isset($_POST['update']) || isset($_POST['save'])) {
            $name = $_POST['name'] ?? '';
            $desc = $_POST['desc'] ?? '';
            $serial_num = $_POST['serial_num'] ?? '';
            $model = $_POST['model'] ?? null;

            if (validate_device_data($errors, $name, $desc, $serial_num)) {
                if (isset($_POST['update'])) {
                    $stmt = $pdo->prepare("UPDATE Device SET Name = ?, `Description` = ?, Serial_num = ?, Model = ? WHERE Id = ?");
                    $stmt->execute([$name, $desc, $serial_num, $model ?: null, $Id]);
                    $_SESSION["log-mess-s"] = "Устройство обновлено";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO Device (Name, `Description`, Serial_num, Model) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $desc, $serial_num, $model ?: null]);
                    $_SESSION["log-mess-s"] = "Устройство добавлено";
                }

                echo "<script>window.location.href = '" . $base_url . "/admin/device/';</script>";
            } else {
                $_SESSION['form_data'] = compact('name', 'desc', 'serial_num', 'model');
            }
        }
    }

    // Получение списка моделей
    $model_list = $pdo->query("SELECT Id, Name FROM Model ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>footer { display: none; }</style>

<div class="container mt-5 ps2p-regular" style="font-size:13px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/device/">Устройства</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $device ? "Устройство_id_{$device["Id"]}" : "Новое устройство" ?></li>
        </ol>
    </nav>

    <form method="post">
        <div class="row">
            <div class="col-md-8">
                <?php if ($device): ?>
                    <title>Устройство: <?= htmlspecialchars($device["Name"]) ?></title>
                    <div class="mb-3">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" value="<?= $device["Id"] ?>" disabled readonly>
                    </div>
                <?php else: ?>
                    <title>Добавить новое устройство</title>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Название устройства</label>
                    <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>" name="name" maxlength="55" required
                           value="<?= htmlspecialchars($device["Name"] ?? ($_SESSION['form_data']['name'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="desc" class="form-label">Описание</label>
                    <input type="text" class="form-control <?= $errors['desc'] ? 'is-invalid' : '' ?>" name="desc" maxlength="55" required
                           value="<?= htmlspecialchars($device["Description"] ?? ($_SESSION['form_data']['desc'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['desc'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="serial_num" class="form-label">Серийный номер</label>
                    <input type="text" class="form-control <?= $errors['serial_num'] ? 'is-invalid' : '' ?>" name="serial_num" maxlength="55" required
                           value="<?= htmlspecialchars($device["Serial_num"] ?? ($_SESSION['form_data']['serial_num'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= $errors['serial_num'] ?></div>
                </div>

                <div class="mb-3">
                    <label for="model" class="form-label">Модель</label>
                    <select name="model" class="form-select">
                        <option value="">Без модели</option>
                        <?php foreach ($model_list as $m): ?>
                            <option value="<?= $m['Id'] ?>" <?= ((($device["Model"] ?? ($_SESSION['form_data']['model'] ?? '')) == $m['Id']) ? 'selected' : '') ?>>
                                <?= htmlspecialchars($m['Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-mb-primary" name="<?= $device ? 'update' : 'save' ?>">Сохранить</button>
                <?php if ($device): ?>
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
