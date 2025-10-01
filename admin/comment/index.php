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
    if ($_SESSION['log-session-data']["Status"] === "Администратор"):

        // Подготовка фильтров
        $sql = "SELECT * FROM User_comment WHERE 1=1";
        $params = [];

        if (!empty($_GET['creater'])) {
            $sql .= " AND Creater LIKE :creater";
            $params['creater'] = '%' . $_GET['creater'] . '%';
        }

        $query = $pdo->prepare($sql);
        $query->execute($params);
        $reviews = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<title>Админ-панель | Отзывы</title>
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
            <li class="breadcrumb-item active" aria-current="page">Отзывы</li>
        </ol>
    </nav>

    <h2 class="mb-4 fs-6">Список отзывов [Review]</h2>

    <div class="mb-4">
    <form method="get">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <a class="btn btn-mb-primary w-100" href="<?= $base_url ?>/admin/comment/dynamic/comment">
                    <i class="bi bi-plus-circle me-1"></i> Добавить отзыв
                </a>
            </div>
            <div class="col-md-3">
                <input type="text" name="creater" class="form-control" placeholder="Автор отзыва" value="<?= htmlspecialchars($_GET['creater'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Фильтровать</button>
            </div>
            <div class="col-md-2">
                <a href="/admin/comment/" class="btn btn-outline-secondary w-100">Сбросить</a>
            </div>
        </div>
    </form>
</div>


    <div class="row g-4 mt-2">
        <?php if (empty($reviews)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm rounded-3">
                    <i class="bi bi-info-circle me-2"></i>Отзывы отсутствуют
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 position-relative h-100">
                        <div class="card-body pb-4">
                            <h5 class="card-title mb-2">ID: <?= htmlspecialchars($review["Id"]) ?></h5>
                            <h6 class="text-muted">Автор: <?= htmlspecialchars($review["Creater"]) ?></h6>
                            <p class="mb-1">Комментарий: <?= htmlspecialchars($review["Comment"]) ?></p>
                            <p class="mb-1">Дата: <?= htmlspecialchars($review["Date"]) ?></p>
                            <p class="mb-1">Оценка: <?= htmlspecialchars($review["Rate"]) ?></p>
                            <?= $review['Approved'] ? '<span class="text-success">✔ Одобрен</span>' : '<span class="text-danger">⛔ Не одобрен</span>' ?>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <a href="<?= $base_url ?>/admin/comment/dynamic/comment?id=<?= htmlspecialchars($review["Id"]) ?>" class="btn btn-outline-primary w-100">
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
