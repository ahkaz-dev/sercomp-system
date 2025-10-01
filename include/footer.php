<?php
// Получаем список услуг
$query_services = $pdo->prepare("SELECT Id, Name FROM Service ORDER BY Id DESC LIMIT 4");
$query_services->execute();
$services = $query_services->fetchAll(PDO::FETCH_ASSOC);

// Получаем список моделей
$query_models = $pdo->prepare("SELECT Id, Name FROM Model ORDER BY Id DESC LIMIT 4");
$query_models->execute();
$models = $query_models->fetchAll(PDO::FETCH_ASSOC);

// Получаем список девайсов
$query_devices = $pdo->prepare("SELECT Id, Name FROM Device ORDER BY Id DESC LIMIT 4");
$query_devices->execute();
$devices = $query_devices->fetchAll(PDO::FETCH_ASSOC);
?>
<footer class="footer bg-light text-center text-lg-start">
    <div class="container p-4">
        <div class="row">
            <!-- О компании -->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mooli-regular">Ремонт Сервис</h5>
                <p>
                    Мы предоставляем качественные услуги ремонта электроники. Обращаясь к нам, вы получаете профессиональный подход, честные цены и гарантию на выполненные работы.
                </p>
                <p><strong>Телефон:</strong> <a href="tel:+71111111111">+7 111 111 11 11</a></p>
                <p><strong>Электронная почта:</strong> <a href="mailto:info@repairservice.ru">info@repairservice.ru</a></p>
                <p><strong>Адрес:</strong> <a href="https://yandex.ru/maps/?text=запрос к яндексу" target="_blank">место для адреса</a></p>

                <p><strong>Время работы:</strong> Пн–Пт: 09:00–18:00</p>
            </div>

            <!-- Услуги -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase comfortaa-regular">Наши услуги</h5>
                <ul class="list-unstyled mb-0">
                    <?php
                    foreach ($services as $service) {
                        echo "<li><a href='$base_url/our-service/' class='text-dark'>{$service['Name']}</a></li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Модели -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase comfortaa-regular">Что чиним</h5>
                <ul class="list-unstyled mb-0">
                    <?php
                    foreach ($models as $model) {
                        echo "<li class='text-dark'>{$model['Name']}</a></li>";
                    }
                    ?>
                </ul>
            </div>



            <!-- Устройства -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase comfortaa-regular">С чем работаем</h5>
                <ul class="list-unstyled mb-0">
                    <?php
                    foreach ($devices as $device) {
                        echo "<li class='text-dark'>{$device['Name']}</a></li>";
                    }
                    ?>
                </ul>
            </div>

        </div>
    </div>

    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.08);">
        © 2025 Copyright:
        <a class="text-dark" href="<?= $base_url ?>">SerComp</a>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert-toast");

  let index = 0;

  function showNextAlert() {
    if (index >= alerts.length) return;

    const alert = alerts[index];
    alert.style.zIndex = 9999;

    // Показываем
    requestAnimationFrame(() => {
      alert.style.top = "20px";
      alert.style.opacity = "1";
    });

    // Ждём 3.5 секунды + 0.6 на скрытие, затем показываем следующий
    setTimeout(() => {
      alert.style.top = "-100px";
      alert.style.opacity = "0";

      setTimeout(() => {
        alert.remove();
        index++;
        showNextAlert(); // Показать следующий
      }, 600); // Плавное скрытие
    }, 3500); // Время показа
  }

  showNextAlert(); // Стартуем
});
</script>


<script>
    window.addEventListener('load', function () {
        // После полной загрузки страницы
        const loader = document.getElementById('loader');
        loader.classList.add('hidden');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script> 

</footer>
</body>