<?php include "../include/header.php"; ?>
<?php include "../include/message.php"; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/complete-orders.css">

<title>SERCOMP | Выполненные заказы</title>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
</head>
<body>

<!-- Hero -->
<section class="hero text-center">
  <div class="container">
    <h1>Выполненные заказы</h1>
    <p>Посмотрите, какие работы мы уже выполнили для наших клиентов</p>
  </div>
</section>

<!-- Completed Orders -->
<section class="py-5">
  <div class="container">
    <h2 class="mb-4 text-center">Примеры наших заказов</h2>
    <div class="row g-4">

      <div class="col-md-6">
        <div class="order-card shadow-sm">
          <h5>Замена экрана iPhone 13 Pro</h5>
          <p class="mb-1">Срок выполнения: 1 день</p>
          <p class="mb-2">Стоимость: 10 500 ₽</p>
          <span class="tag">Мобильные устройства</span>
        </div>
      </div>

      <div class="col-md-6">
        <div class="order-card shadow-sm">
          <h5>Ремонт материнской платы ноутбука ASUS</h5>
          <p class="mb-1">Срок выполнения: 3 дня</p>
          <p class="mb-2">Стоимость: 7 200 ₽</p>
          <span class="tag">Ноутбуки</span>
        </div>
      </div>

      <div class="col-md-6">
        <div class="order-card shadow-sm">
          <h5>Диагностика и замена помпы стиральной машины</h5>
          <p class="mb-1">Срок выполнения: 2 дня</p>
          <p class="mb-2">Стоимость: 5 300 ₽</p>
          <span class="tag">Бытовая техника</span>
        </div>
      </div>

      <div class="col-md-6">
        <div class="order-card shadow-sm">
          <h5>Чистка и замена термопасты игрового ПК</h5>
          <p class="mb-1">Срок выполнения: 1 день</p>
          <p class="mb-2">Стоимость: 2 800 ₽</p>
          <span class="tag">Компьютеры</span>
        </div>
      </div>

      <div class="col-md-6">
        <div class="order-card shadow-sm">
          <h5>Восстановление данных с жёсткого диска</h5>
          <p class="mb-1">Срок выполнения: 4 дня</p>
          <p class="mb-2">Стоимость: 12 000 ₽</p>
          <span class="tag">Хранение данных</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="bg-light py-5 text-center">
  <div class="container">
    <h2>Хотите, чтобы и ваш заказ стал успешным кейсом?</h2>
    <p>Свяжитесь с нами — мы профессионально решим любую задачу!</p>
    <a href="/request/" class="btn btn-primary">Оставить заявку</a>
  </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
</body>
</html>
