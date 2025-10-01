<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/our-service.css">
<title>SERCOMP | Наши Услуги </title>

<section class="hero">
  <div class="container">
    <h1>Наши услуги</h1>
    <p>Профессиональный ремонт и обслуживание бытовой, компьютерной и мобильной техники</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h2 class="mb-4 text-center">Категории услуг</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card service-card h-100">
          <img src="<?= $base_url ?>/static/img/service-phone.jpg" alt="Ремонт смартфонов" />
          <div class="card-body">
            <h5 class="card-title">Ремонт смартфонов</h5>
            <p class="card-text">Замена экранов, аккумуляторов, разъемов зарядки, программное восстановление.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card service-card h-100">
          <img src="<?= $base_url ?>/static/img/service-pc.jpg" alt="Ремонт ноутбуков и ПК" />
          <div class="card-body">
            <h5 class="card-title">Ремонт ноутбуков и ПК</h5>
            <p class="card-text">Апгрейд, замена компонентов, удаление вирусов, настройка ПО и очистка системы охлаждения.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card service-card h-100">
          <img src="<?= $base_url ?>/static/img/service-other.jpg" alt="Бытовая техника" />
          <div class="card-body">
            <h5 class="card-title">Специализированная техника</h5>
            <p class="card-text">Ремонт принтеров, копировальных аппаратов, сканеров, факсов и другой офисной техники.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="bg-light py-5">
  <div class="container">
    <h2 class="mb-4 text-center fw-bold">Примерные цены</h2>
    <div class="row g-4">
<?php
try {
    $stmt = $pdo->query("SELECT Name, About, Price FROM Service LIMIT 6");
    $servicesShown = 0;

    if ($stmt && $stmt->rowCount() > 0) {
        foreach ($stmt as $row) {
            $servicesShown++;
            echo <<<HTML
              <div class="col-md-6 col-lg-4">
                <div class="service-card border rounded p-4 shadow-sm bg-white h-100 d-flex flex-column justify-content-between service-cent">
                  <div>
                    <h5 class="fw-bold mb-2">{$row['Name']}</h5>
                    <p class="text-muted mb-3">{$row['About']}</p>
                  </div>
                  <div class="mt-auto">
                    <span class="badge bg-success fs-6">от {$row['Price']} ₽</span>
                  </div>
                </div>
              </div>

            HTML;
        }
    }

    echo <<<HTML
    <div class="col-md-6 col-lg-4">
      <a href="service-list.php" class="service-cent-l">
        <div class="border rounded p-4 shadow-sm bg-white h-100 d-flex flex-column justify-content-center text-center hover-shadow service-cent-l">
          <h5 class="fw-bold mb-2">Все услуги</h5>
          <p class="text-muted mb-3">Посмотреть полный список</p>
          <span class="btn btn-success mt-3 fs-6">Перейти</span>
        </div>
      </a>
    </div>
    HTML;

} catch (PDOException $e) {
    echo '<div class="col-12 text-danger">Ошибка загрузки данных</div>';
}
?>
    </div>
  </div>
</section>


<section class="benefits-section py-5">
  <div class="container">
    <h2 class="mb-4 text-center">Почему выбирают нас</h2>
    <div class="row text-center">
      <div class="col-md-4">
        <i class="bi bi-shield-check benefits-icon"></i>
        <h5>Гарантия на все работы</h5>
        <p>До 6 месяцев гарантии на выполненный ремонт.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-person-badge benefits-icon"></i>
        <h5>Опытные мастера</h5>
        <p>Только сертифицированные специалисты с опытом от 5 лет.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-lightning-charge benefits-icon"></i>
        <h5>Быстрое выполнение</h5>
        <p>Большинство ремонтов в течение 1 дня.</p>
      </div>
    </div>
  </div>
</section>

<section class="faq-section py-5">
  <div class="container">
    <h2 class="mb-4 text-center">Часто задаваемые вопросы</h2>
    <div class="accordion" id="faqAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="faq1">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
            Сколько времени занимает ремонт?
          </button>
        </h2>
        <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            В большинстве случаев ремонт выполняется в течение одного рабочего дня.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="faq2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
            Вы работаете с выездом на дом?
          </button>
        </h2>
        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Да, наши мастера могут приехать к вам домой или в офис для диагностики и ремонта.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<section class="cta-section mx-auto">
  <div class="container">
    <h2>Готовы отремонтировать вашу технику?</h2>
    <p>Свяжитесь с нами прямо сейчас и получите бесплатную консультацию от специалиста!</p>
    <a href="<?= $base_url ?>/request/" class="btn btn-light btn-lg">Оставить заявку</a>
  </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>

