<?php include __DIR__ . '/include/header.php'; ?>
<?php include __DIR__ . '/include/message.php'; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/index.css">

<div class="container hero">
  <div class="hero-text col-lg-6 col-md-12 scroll-hidden">
    <h1>Проблемы с вашим<br>устройством?</h1>
    <p>Решаем любые проблемы! Большой склад и опытные мастера</p>
<button class="cta-button" onclick="location.href='contacts/'">Бесплатная консультация</button>

  </div>
  <div class="hero-img col-lg-6 col-md-12 text-center scroll-hidden">
    <img src="<?= $base_url ?>/static/img/back-image-main-content.jpg" alt="Мастер" />
  </div>
</div>

<!-- Вводный текст -->
<div class="intro-text scroll-hidden">
  <p>
    Мы заботимся о вашем устройстве так, будто это наше собственное. Наши специалисты быстро и качественно устранят любую поломку,
    а удобные сервисы и поддержка сделают процесс ремонта максимально комфортным для вас. Давайте сделаем ваш гаджет как новый!
  </p>
</div>

<div class="container text-center my-5 features">
  <div class="row">
    <div class="col-md-4 scroll-hidden">
      <div class="icon">🚚</div>
      <h5>Курьер по городу</h5>
      <p>Выезд курьера в удобное время</p>
    </div>
    <div class="col-md-4 scroll-hidden">
      <div class="icon">📱</div>
      <h5>Доступность</h5>
      <p>Быстрый и удобный сервис</p>
    </div>
    <div class="col-md-4 scroll-hidden">
      <div class="icon">🔧</div>
      <h5>Забота о вас</h5>
      <p>Незначительные поломки – бесплатно</p>
    </div>
  </div>
</div>

<div class="container">
  <h2 class="mb-4 text-center fw-bold scroll-hidden">Мы работаем с устройствами:</h2>
<div class="row">
  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-phone-fill me-2"></i>Смартфоны
      </h5>
      <p class="text-muted mb-0">iPhone, Samsung Galaxy, Google Pixel и другие популярные модели.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-laptop-fill me-2"></i>Ноутбуки
      </h5>
      <p class="text-muted mb-0">MacBook, Lenovo ThinkPad, Dell XPS — перегрев, экраны и другое.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-tablet-landscape-fill me-2"></i>Планшеты
      </h5>
      <p class="text-muted mb-0">iPad, Galaxy Tab, Huawei MediaPad — дисплеи, зарядки, корпус.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-printer-fill me-2"></i>МФУ и принтеры
      </h5>
      <p class="text-muted mb-0">Canon, HP, Epson, Brother — картриджи, замятие бумаги, механика.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-display-fill me-2"></i>Мониторы
      </h5>
      <p class="text-muted mb-0">LG, Philips, Samsung — нет изображения, битые пиксели, мигание.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-hdd-fill me-2"></i>Офисная техника
      </h5>
      <p class="text-muted mb-0">Сканеры, ИБП, клавиатуры, системные блоки — диагностика и ремонт.</p>
    </div>
  </div>

  <!-- Дополнительные категории -->
  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">
        <i class="bi bi-tv-fill me-2"></i>Телевизоры
      </h5>
      <p class="text-muted mb-0">Samsung, LG, Sony — ремонт матриц, подсветки, блоков питания.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-usb-symbol me-2"></i>Внешние накопители
      </h5>
      <p class="text-muted mb-0">Жёсткие диски и SSD — восстановление данных, перепрошивка контроллеров.</p>
    </div>
  </div>

  <div class="col-md-4 mb-4 scroll-hidden">
    <div class="device-tile p-4 border rounded shadow-sm h-100 bg-light">
      <h5 class="fw-bold mb-2" style="color: #474747;">

        <i class="bi bi-router-fill me-2"></i>Роутеры и модемы
      </h5>
      <p class="text-muted mb-0">TP-Link, Asus, MikroTik — прошивка, настройка, замена портов.</p>
    </div>
  </div>
</div>
  <!-- Новый CTA блок -->
<a href="<?= $base_url ?>/request" class="cta-block scroll-hidden">
  <h3>Готовы решить проблему с вашим устройством?</h3>
  <p>Оставьте заявку прямо сейчас, и наши мастера свяжутся с вами для консультации и быстрого ремонта.</p>
  <span class="cta-button">Сделать заявку</span>
</a>
</div>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    function revealOnScroll() {
      $('.scroll-hidden').each(function() {
        var $this = $(this);
        var elemTop = $this.offset().top;
        var windowBottom = $(window).scrollTop() + $(window).height();

        if (elemTop < windowBottom - 100) {
          if (!$this.hasClass('scroll-show')) {
            $this.addClass('scroll-show');
            // Удаляем scroll-hidden не сразу, чтобы переход сработал
            setTimeout(function() {
              $this.removeClass('scroll-hidden');
            }, 50);
          }
        }
      });
    }

    revealOnScroll(); // проверить при загрузке
    $(window).on('scroll', revealOnScroll);
  });
</script>



<?php include __DIR__ . '/include/footer.php'; ?>