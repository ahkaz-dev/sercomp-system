<?php include __DIR__ . '/../include/header.php'; ?>
<?php include __DIR__ . '/../include/message.php'; ?>
<link rel="stylesheet" href="<?= $base_url ?>/static/css/review.css">
<title>SERCOMP | Отзывы</title>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['log-session']) || empty($_SESSION['log-session-data'])) {
        $_SESSION['feedback'] = 'Вы должны войти в систему, чтобы оставить отзыв.';
        echo "<script>location.href = '{$base_url}/review/';</script>";
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $rating = $_POST['rating'] ?? '';
    $review = trim($_POST['review'] ?? '');
    $date = date('Y-m-d H:i:s');
    $userHash = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

    if (!empty($_SESSION['review_submitted']) || isset($_COOKIE['review_submitted'])) {
        $_SESSION['feedback'] = 'Вы уже отправили отзыв.';
        echo "<script>location.href = '{$base_url}/review/';</script>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT 1 FROM `user_comment` WHERE Creater = ? AND Comment = ?");
    $stmt->execute([$name ?: $userHash, $review]);

    if ($stmt->rowCount() == 0) {
        $insert = $pdo->prepare("INSERT INTO `user_comment` (Creater, Comment, Date, Rate) VALUES (?, ?, ?, ?)");
        $insert->execute([$name ?: $userHash, $review, $date, $rating]);

        $_SESSION['review_submitted'] = true;
        setcookie('review_submitted', '1', time() + 60 * 60 * 24 * 365, "/");
        $_SESSION['feedback'] = 'Спасибо за ваш отзыв! В скором времени мы рассмотрим его!';
    } else {
        $_SESSION['feedback'] = 'Такой отзыв уже существует.';
    }

    echo "<script>location.href = '{$base_url}/review/';</script>";
    exit;
}
?>

<section class="hero">
  <div class="container">
    <h1>Отзывы клиентов</h1>
    <p>Мы ценим мнение каждого — вот что говорят наши клиенты</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h2 class="mb-4">Нам доверяют</h2>
    <div class="row g-4">
      <?php
      $stmt = $pdo->query("SELECT * FROM `user_comment` ORDER BY `Date` DESC");
      if ($stmt && $stmt->rowCount() > 0) {
          foreach ($stmt as $review) {
              $rate = (int)$review['Rate'];
              $stars = str_repeat('★', $rate) . str_repeat('☆', 5 - $rate);
              $user = htmlspecialchars($review['Creater']);
              $comment = htmlspecialchars($review['Comment']);
              $date = date('d.m.Y', strtotime($review['Date']));
              
              echo "<div class='col-md-6 col-lg-4'>";
              echo "  <article class='card h-100 shadow-sm border-0'>";
              echo "    <div class='card-body'>";
              echo "      <h5 class='card-title mb-1'>{$user}</h5>";
              echo "      <div class='text-warning mb-2' aria-label='Оценка {$rate} из 5'>{$stars}</div>";
              echo "      <p class='card-text'>{$comment}</p>";
              echo "      <small class='text-muted'>{$date}</small>";
              echo "    </div>";
              echo "  </article>";
              echo "</div>";
          }
      } else {
          echo "<p class='text-center fs-5 text-muted'>Пока нет отзывов. Станьте первым!</p>";
      }
      ?>
    </div>
  </div>
</section>



<section class="py-5">
  <div class="container">
    <h2 class="mb-4 text-center">Оставьте свой отзыв</h2>
    <div class="submit-card shadow-sm">
      <?php if (!empty($_SESSION['feedback'])): ?>
          <div class="alert alert-info"><?php echo $_SESSION['feedback']; unset($_SESSION['feedback']); ?></div>
      <?php endif; ?>
      <form method="POST" novalidate>
        <div class="mb-4">
          <label for="name" class="form-label">Ваше имя</label>
          <input type="text" class="form-control" id="name" name="name" required placeholder="Иван Иванов" />
        </div>
        <div class="mb-4">
          <label for="rating" class="form-label">Оценка</label>
          <select class="form-select" id="rating" name="rating" required>
            <option value="" disabled selected>Выберите оценку</option>
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="review" class="form-label">Ваш отзыв</label>
          <textarea class="form-control" id="review" name="review" rows="5" required placeholder="Напишите ваш отзыв здесь..."></textarea>
        </div>
        <button type="submit" class="btn-submit">Отправить</button>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>