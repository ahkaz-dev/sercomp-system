<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/contacts.css">

<!-- Hero -->
<section class="hero text-center">
  <div class="container">
    <h1>Контакты</h1>
    <p>Свяжитесь с нами — мы рядом, чтобы помочь</p>
  </div>
</section>

<!-- Contact Info -->
<section class="py-5">
  <div class="container">
    <h2 class="mb-4 text-center">Как нас найти</h2>
    <div class="row g-4">

      <div class="col-md-6">
        <div class="contact-card shadow-sm h-100">
          <h5>Наш офис</h5>
          <p>место для адреса</p>

          <h5>Телефон</h5>
          <p><a href="tel:+74951234567">+7 111 111 11 11</a></p>

          <h5>Email</h5>
          <p><a href="mailto:info@remont-tech.ru">info@remont-tech.ru</a></p>

          <h5>Режим работы</h5>
          <p>Пн–Пт: 10:00–20:00<br />Сб–Вс: 11:00–17:00</p>
        </div>
      </div>

      <div class="col-md-6">
        <div style="position:relative;overflow:hidden;"><a href="https://yandex.ru/maps?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Яндекс Карты</a><a href="https://yandex.ru/maps/geo/moskva/53000094/?ll=37.385272%2C55.584227&utm_medium=mapframe&utm_source=maps&z=9" style="color:#eee;font-size:12px;position:absolute;top:14px;">Москва — Яндекс Карты</a><iframe src="https://yandex.ru/map-widget/v1/?ll=37.385272%2C55.584227&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgg1MzAwMDA5NBIa0KDQvtGB0YHQuNGPLCDQnNC-0YHQutCy0LAiCg2GeBZCFQEGX0I%2C&z=9" width="560" height="400" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe></div>
      </div>

    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="bg-light py-5 text-center">
  <div class="container">
    <h2>Есть вопрос?</h2>
    <p>Напишите нам через форму отзывов — мы быстро ответим!</p>
    <a href="<?= $base_url?>/review/" class="btn btn-primary">Форма отзыва</a>
  </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
