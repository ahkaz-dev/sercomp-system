<?php include "../../include/header.php"; ?>
<?php include "../../include/message.php"; ?>

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

        $sql = "SELECT * FROM Service WHERE 1=1";
        $params = [];
        
        if (!empty($_GET['name'])) {
            $sql .= " AND Name LIKE :name";
            $params['name'] = '%' . $_GET['name'] . '%';
        }
        if (!empty($_GET['price_from'])) {
            $sql .= " AND Price >= :price_from";
            $params['price_from'] = $_GET['price_from'];
        }
        if (!empty($_GET['price_to'])) {
            $sql .= " AND Price <= :price_to";
            $params['price_to'] = $_GET['price_to'];
        }
        
        $query = $pdo->prepare($sql);
        $query->execute($params);
        $services = $query->fetchAll(PDO::FETCH_ASSOC);
        
?>

<title>Админ-панель | Услуги</title>
<style>
    footer {
        display: none;
    }
</style>

<div class="container mt-5 ps2p-regular">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $base_url ?>">Главная</a></li>
            <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/">Админ-панель</a></li>
            <li class="breadcrumb-item active" aria-current="page">Услуги</li>
        </ol>
    </nav>

    <h2 class="mb-4 fs-6">Список услуг [Service]</h2>

    <div class="mb-4">
        <form method="get">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <a class="btn btn-mb-primary w-100" href="<?= $base_url ?>/admin/service/dynamic/service">
                        <i class="bi bi-plus-circle me-1"></i> Добавить услугу
                    </a>
                </div>
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Название услуги" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="price_from" class="form-control" placeholder="Цена от" min="0" value="<?= htmlspecialchars($_GET['price_from'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="price_to" class="form-control" placeholder="Цена до" min="0" value="<?= htmlspecialchars($_GET['price_to'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">Фильтр</button>
                </div>
                <div class="col-md-1">
                    <a href="/admin/service/" class="btn btn-outline-secondary w-100">Сброс</a>
                </div>
            </div>
        </form>
    </div>


    <div class="row g-4">
        <?php if (empty($services)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-2"></i>Услуги отсутствуют
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 position-relative h-100">
                        <div class="card-body pb-4">
                            <h5 class="card-title mb-2"><?= htmlspecialchars($service["Name"]) ?></h5>
                            <h6 class="text-muted mb-3">Цена: <?= htmlspecialchars($service["Price"]) ?> ₽</h6>
                            <p class="mb-2"><strong>Кратко:</strong> <?= htmlspecialchars($service["Short_desc"]) ?></p>
                            <p class="mb-2"><strong>Описание:</strong> <?= htmlspecialchars($service["Full_desc"]) ?></p>
                            <p class="mb-2"><strong>О сервисе:</strong> <?= htmlspecialchars($service["About"]) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <a href="<?= $base_url ?>/admin/service/dynamic/service?id=<?= htmlspecialchars($service["Id"])?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-pencil"></i> Редактировать
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
        <?php include "../../include/error/404.php"; ?>
    </div>
<?php endif; ?>
<?php else: ?>
    <div class="container mt-5" style="padding-bottom: 12px;">
        <?php include "../../include/error/404.php"; ?>
    </div>
<?php endif; ?>

<?php include "../../include/footer.php"; ?>
