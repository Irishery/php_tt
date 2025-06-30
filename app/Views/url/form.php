<div class="container" style="max-width: 600px;">
  <h2 class="mb-4">Сократить ссылку</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($shortUrl)): ?>
    <div class="alert alert-success">
      Сокращённая ссылка: <a href="<?= htmlspecialchars($shortUrl) ?>" target="_blank"><?= htmlspecialchars($shortUrl) ?></a>
    </div>
  <?php endif; ?>

  <form method="POST" action="/shorten">
    <div class="mb-3">
      <label for="url" class="form-label">Оригинальная ссылка</label>
      <input name="url" type="url" class="form-control" id="url" required placeholder="https://example.com">
    </div>

    <button class="btn btn-primary">Сократить</button>
  </form>
</div>
