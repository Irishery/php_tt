<h1>Введите ссылку</h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="/shorten">
  <input name="url" placeholder="https://example.com" required>
  <button type="submit">Сократить</button>
</form>
