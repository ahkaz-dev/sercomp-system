<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/about.css">
<title>SERCOMP | О компании</title>

<body>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>О компании</h1>
    <p>Надежный сервис по ремонту техники с опытом более 10 лет</p>
  </div>
</section>

<!-- Company Story -->
<section class="py-5">
  <div class="container">
    <h2>Наша история</h2>
    <p class="section-text">
      Мы начали с небольшой мастерской в 2013 году и за это время превратились в одну из самых уважаемых сервисных компаний в регионе. За эти годы мы отремонтировали более 25 000 устройств и заработали доверие тысяч клиентов.
    </p>
    <div class="highlight">
      Миссия: Обеспечить быстрый, честный и качественный ремонт техники с индивидуальным подходом к каждому клиенту.
    </div>
  </div>
</section>

<!-- What We Do -->
<section class="py-5 bg-white">
  <div class="container">
    <h2>Что мы предлагаем</h2>
    <div class="section-text" style="max-width: 900px; margin-bottom: 40px;">
      Мы предоставляем комплексные услуги по ремонту и обслуживанию техники: от смартфонов и ноутбуков до бытовой техники. Выездной сервис, консультации и диагностика — всё для вашего удобства и уверенности.
    </div>
    <div class="team-row" style="justify-content: space-around;">
      <div class="team-card">
        <h5>Комплексный ремонт</h5>
        <p>Работаем с широким спектром техники — от смартфонов до бытовых устройств.</p>
      </div>
      <div class="team-card">
        <h5>Выездной сервис</h5>
        <p>Мастер приедет к вам домой или в офис — удобно и быстро.</p>
      </div>
      <div class="team-card">
        <h5>Консультации и диагностика</h5>
        <p>Бесплатная диагностика и советы по уходу за техникой от экспертов.</p>
      </div>
    </div>
  </div>
</section>

<!-- Our Team -->
<section class="py-5 bg-white">
  <div class="container">
    <h2>Наша команда</h2>
    <div class="team-row">
      <div class="team-card">
        <img src="<?= $base_url ?>/static/img/Sergey_Ivanov.jpg" alt="Сергей Иванов">
        <h5>Сергей Иванов</h5>
        <p>Главный инженер. Более 12 лет опыта в ремонте техники.</p>
      </div>
      <div class="team-card">
        <img src="<?= $base_url ?>/static/img/Olga_Smirnova.jpg" alt="Ольга Смирнова">
        <h5>Ольга Смирнова</h5>
        <p>Менеджер по работе с клиентами. Гарантирует отличное обслуживание.</p>
      </div>
      <div class="team-card">
        <img src="<?= $base_url ?>/static/img/Dmitry_Solovyov.jpg" alt="Дмитрий Соловьев">
        <h5>Дмитрий Соловьев</h5>
        <p>Мастер-универсал. Решит любую техническую проблему.</p>
      </div>
      <div class="team-card">
        <img src="<?= $base_url ?>/static/img/Anna_Krylova.jpg" alt="Анна Крылова">
        <h5>Анна Крылова</h5>
        <p>Сервис-координатор. Следит за сроками и качеством работы.</p>
      </div>
    </div>
  </div>
</section>

<!-- Stats and Achievements -->
<section class="stats-section">
  <div class="container">
    <div class="stats-row">
      <div class="stat-item">
        <h3>10+</h3>
        <p>лет на рынке</p>
      </div>
      <div class="stat-item">
        <h3>25 000+</h3>
        <p>устройств отремонтировано</p>
      </div>
      <div class="stat-item">
        <h3>98%</h3>
        <p>довольных клиентов</p>
      </div>
      <div class="stat-item">
        <h3>150+</h3>
        <p>партнеров и поставщиков</p>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
  <h2>Готовы доверить ремонт профессионалам?</h2>
  <p>Свяжитесь с нами, и мы быстро решим вашу проблему с техникой.</p>
  <a href="<?= $base_url ?>/contacts" class="btn">Связаться с нами</a>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
