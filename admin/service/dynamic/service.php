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
if (isset($_SESSION["log-session"]) && isset($_SESSION['log-session-data'])):
    if ($_SESSION['log-session-data']["Status"] == "Администратор"):
        $Id = $_GET['id'] ?? 0;
        $query = $pdo->prepare("SELECT * FROM Service WHERE Id = :Id");
        $query->execute(['Id' => $Id]);
        $service_query_result = $query->fetch(PDO::FETCH_ASSOC);

        $errors = [
            'name' => '',
            'price' => '',
            'about' => '',
            'short_desc' => '',
            'full_desc' => '',
        ];
        
        function validate_service_data(&$errors, $name, $price, $about, $short_desc, $full_desc) {
            // Name: 5–55 символов, первая заглавная буква, только русский или английский, пробелы допустимы
            if (!preg_match('/^[А-ЯЁA-Z][а-яёa-z ]{4,54}$/u', $name)) {
                $errors['name'] = "Название должно быть от 5 до 55 символов, начинаться с заглавной буквы, только на русском или английском. Пробелы разрешены.";
            }
        
            // Price: числовая строка до 25 символов (например: "1500" или "1500.00 руб.")
            if (!preg_match('/^[\d.,\sрубРР]+$/u', $price) || mb_strlen($price) > 25) {
                $errors['price'] = "Цена должна быть числом (можно с пробелами, точкой, запятой и 'руб') и не более 25 символов.";
            }
        
            // About: до 50 символов, любое непустое содержимое
            if (mb_strlen($about) < 3 || mb_strlen($about) > 50) {
                $errors['about'] = "Поле 'О сервисе' должно содержать от 3 до 50 символов.";
            }
        
            // Short_desc: до 150 символов
            if (mb_strlen($short_desc) < 10 || mb_strlen($short_desc) > 150) {
                $errors['short_desc'] = "Краткое описание должно быть от 10 до 150 символов.";
            }
        
            // Full_desc: до 320 символов
            if (mb_strlen($full_desc) < 20 || mb_strlen($full_desc) > 320) {
                $errors['full_desc'] = "Полное описание должно быть от 20 до 320 символов.";
            }
        
            return !array_filter($errors);
        }
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            $stmt = $pdo->prepare("DELETE FROM Service WHERE Id = :Id");
            $stmt->bindParam(':Id', $Id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $_SESSION["log-mess-warn"] = "Запись удалена";
                echo "<script>window.location.href = '" . $base_url . "/admin/service/';</script>";
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = $service_query_result["Id"];
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? '';
            $about = $_POST['about'] ?? '';
            $short_desc = $_POST['short_desc'] ?? '';
            $full_desc = $_POST['full_desc'] ?? '';
        
            if (validate_service_data($errors, $name, $price, $about, $short_desc, $full_desc)) {
                // Проверка на дубликат
                $stmt = $pdo->prepare("SELECT * FROM Service WHERE Name = :name AND Price = :price AND About = :about AND Short_desc = :short_desc AND Full_desc = :full_desc AND Id != :id");
                $stmt->execute([
                    'name' => $name,
                    'price' => $price,
                    'about' => $about,
                    'short_desc' => $short_desc,
                    'full_desc' => $full_desc,
                    'id' => $id
                ]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($existing) {
                    $_SESSION['log-mess-e'] = "Такая услуга уже существует.";
                    echo "<script>window.location.href = '". $base_url ."/admin/service/dynamic/service.php';</script>";
                    exit;
                }
        
                // Обновление данных
                $stmt = $pdo->prepare("UPDATE Service SET Name = ?, Price = ?, About = ?, Short_desc = ?, Full_desc = ? WHERE Id = ?");
                if ($stmt->execute([$name, $price, $about, $short_desc, $full_desc, $id])) {
                    $_SESSION["log-mess-s"] = "Услуга успешно обновлена";
                    echo "<script>window.location.href = '". $base_url ."/admin/service/';</script>";
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка при обновлении";
                    echo "<script>window.location.href = '". $base_url ."/admin/service/dynamic/service.php';</script>";
                }
            }
        }
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? '';
            $about = $_POST['about'] ?? '';
            $short_desc = $_POST['short_desc'] ?? '';
            $full_desc = $_POST['full_desc'] ?? '';
        
            if (validate_service_data($errors, $name, $price, $about, $short_desc, $full_desc)) {
                // Проверка на дубликат
                $stmt = $pdo->prepare("SELECT * FROM Service WHERE Name = :name AND Price = :price AND About = :about AND Short_desc = :short_desc AND Full_desc = :full_desc");
                $stmt->execute([
                    'name' => $name,
                    'price' => $price,
                    'about' => $about,
                    'short_desc' => $short_desc,
                    'full_desc' => $full_desc
                ]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($existing) {
                    $_SESSION['log-mess-e'] = "Такая услуга уже существует.";
                    echo "<script>window.location.href = '". $base_url ."/admin/service/dynamic/service.php';</script>";
                    exit;
                }
        
                // Добавление новой записи
                $stmt = $pdo->prepare("INSERT INTO Service (Name, Price, About, Short_desc, Full_desc) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $price, $about, $short_desc, $full_desc])) {
                    $_SESSION["log-mess-s"] = "Услуга успешно добавлена";
                    echo "<script>window.location.href = '". $base_url ."/admin/service/';</script>";
                } else {
                    $_SESSION["log-mess-e"] = "Ошибка при добавлении";
                    echo "<script>window.location.href = '". $base_url ."/admin/service/dynamic/service.php';</script>";
                }
            } else {
                $_SESSION['form_data'] = [
                    'name' => $name,
                    'price' => $price,
                    'about' => $about,
                    'short_desc' => $short_desc,
                    'full_desc' => $full_desc
                ];
            }
        }
        
?>

<style>
    footer {
        display: none;
    }
</style>

<div class="container mt-5 ps2p-regular" style="font-size:13px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="<?= $base_url ?>/admin/service">Все услуги</a></li>
        <li class="breadcrumb-item active" aria-current="page">Категория_id_<?php echo (isset($category_query_result["Id"])) ? $category_query_result["Id"] : ''; ?></li>
    </ol>
    </nav>
    <?php if ($service_query_result): ?>
    <title>Услуга: <?= htmlspecialchars($service_query_result["Name"]) ?></title>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="form-container">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" disabled name="id"
                               value="<?= htmlspecialchars($service_query_result["Id"]) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Название услуги</label>
                        <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>" id="name"
                               name="name" maxlength="55" required
                               value="<?= htmlspecialchars($service_query_result["Name"]) ?>">
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Цена</label>
                        <input type="text" class="form-control <?= $errors['price'] ? 'is-invalid' : '' ?>" id="price"
                               name="price" maxlength="25" required
                               value="<?= htmlspecialchars($service_query_result["Price"]) ?>">
                        <div class="invalid-feedback"><?= $errors['price'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="about" class="form-label">Кратко об услуге</label>
                        <input type="text" class="form-control <?= $errors['about'] ? 'is-invalid' : '' ?>" id="about"
                               name="about" maxlength="50" required
                               value="<?= htmlspecialchars($service_query_result["About"]) ?>">
                        <div class="invalid-feedback"><?= $errors['about'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="short_desc" class="form-label">Краткое описание</label>
                        <input type="text" class="form-control <?= $errors['short_desc'] ? 'is-invalid' : '' ?>" id="short_desc"
                               name="short_desc" maxlength="150" required
                               value="<?= htmlspecialchars($service_query_result["Short_desc"]) ?>">
                        <div class="invalid-feedback"><?= $errors['short_desc'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="full_desc" class="form-label">Полное описание</label>
                        <textarea class="form-control <?= $errors['full_desc'] ? 'is-invalid' : '' ?>" id="full_desc"
                                  name="full_desc" maxlength="320" rows="5" required><?= htmlspecialchars($service_query_result["Full_desc"]) ?></textarea>
                        <div class="invalid-feedback"><?= $errors['full_desc'] ?></div>
                    </div>
                    <button type="submit" class="btn btn-mb-primary" name="update">Сохранить</button>
                    <form method="post">
                        <button class="btn btn-danger" name="delete">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
<?php elseif ($service_query_result == 0): ?>
    <title>Создать новую услугу</title>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="form-container">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Название услуги</label>
                        <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>" id="name"
                               name="name" maxlength="55" required
                               value="<?= isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Цена</label>
                        <input type="text" class="form-control <?= $errors['price'] ? 'is-invalid' : '' ?>" id="price"
                               name="price" maxlength="25" required
                               value="<?= isset($_SESSION['form_data']['price']) ? htmlspecialchars($_SESSION['form_data']['price']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['price'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="about" class="form-label">Кратко об услуге</label>
                        <input type="text" class="form-control <?= $errors['about'] ? 'is-invalid' : '' ?>" id="about"
                               name="about" maxlength="50" required
                               value="<?= isset($_SESSION['form_data']['about']) ? htmlspecialchars($_SESSION['form_data']['about']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['about'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="short_desc" class="form-label">Краткое описание</label>
                        <input type="text" class="form-control <?= $errors['short_desc'] ? 'is-invalid' : '' ?>" id="short_desc"
                               name="short_desc" maxlength="150" required
                               value="<?= isset($_SESSION['form_data']['short_desc']) ? htmlspecialchars($_SESSION['form_data']['short_desc']) : '' ?>">
                        <div class="invalid-feedback"><?= $errors['short_desc'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="full_desc" class="form-label">Полное описание</label>
                        <textarea class="form-control <?= $errors['full_desc'] ? 'is-invalid' : '' ?>" id="full_desc"
                                  name="full_desc" maxlength="320" rows="5" required><?= isset($_SESSION['form_data']['full_desc']) ? htmlspecialchars($_SESSION['form_data']['full_desc']) : '' ?></textarea>
                        <div class="invalid-feedback"><?= $errors['full_desc'] ?></div>
                    </div>
                    <button type="submit" class="btn btn-mb-primary" name="save">Сохранить</button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['form_data']); ?>
    </form>

            <?php else: ?>
            <p class="text-muted">Данной услуги не существует :(</p>
        <?php endif; ?>
    <?php else: ?>
        <?php
            echo '<script type="text/javascript">';
            echo "window.location.href = '" . $base_url . "';";
            echo '</script>';
        ?>
    <?php endif; ?>
</div>
</div>
<?php else: ?>
    <?php
        echo '<script type="text/javascript">';
        echo "window.location.href = '" . $base_url . "';";
        echo '</script>';
        ?>
    <?php
    endif; ?>
<?php include "../../../include/footer.php"; ?>