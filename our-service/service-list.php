<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>

<style>
  .section-title {
    font-weight: 700;
    margin-bottom: 30px;
  }
</style>
<body>
<section class="py-5">
  <div class="container">
    <h1 class="text-center section-title">Полный список услуг</h1>
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle" id="serviceTable">
        <thead class="table-primary text-center">
          <tr>
            <th style="min-width: 180px;">Услуга</th>
            <th>Описание</th>
            <th id="priceHeader" style="min-width: 120px; cursor: pointer;">Цена от (₽) ▲▼</th>
          </tr>
        </thead>
        <tbody>
          <?php
          try {
              $stmt = $pdo->query("SELECT Name, About, Price FROM Service ORDER BY Price DESC");

              if ($stmt && $stmt->rowCount() > 0) {
                  foreach ($stmt as $row) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['About']) . "</td>";
                      echo "<td class='text-center'>" . 
                          htmlspecialchars($row['Name'] == "Пользовательская услуга" ? "В Зависимости от проблемы" : $row['Price']) . 
                          "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='3' class='text-center'>Услуги не найдены.</td></tr>";
              }
          } catch (PDOException $e) {
              echo "<tr><td colspan='3' class='text-danger text-center'>Ошибка загрузки данных: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('serviceTable');
    const priceHeader = document.getElementById('priceHeader');
    let asc = true; // по умолчанию сортировка по возрастанию

    function parsePrice(priceStr) {
      // Убираем все нечисловые символы и пытаемся получить число
      const num = parseFloat(priceStr.replace(/[^\d.,]/g, '').replace(',', '.'));
      return isNaN(num) ? 0 : num;
    }

    priceHeader.addEventListener('click', () => {
      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));

      rows.sort((a, b) => {
        const priceA = parsePrice(a.cells[2].textContent);
        const priceB = parsePrice(b.cells[2].textContent);
        return asc ? priceA - priceB : priceB - priceA;
      });

      // Удаляем текущие строки
      while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
      }

      // Добавляем отсортированные строки обратно
      rows.forEach(row => tbody.appendChild(row));

      asc = !asc; // инвертируем порядок сортировки для следующего клика

      // Обновим стрелки в заголовке
      priceHeader.textContent = `Цена от (₽) ${asc ? '▲' : '▼'}`;
    });
  });
</script>



<?php include __DIR__ . '/../include/footer.php'; ?>