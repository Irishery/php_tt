<h1>Ваша короткая ссылка:</h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<a href="/r/<?= htmlspecialchars($short) ?>">/r/<?= htmlspecialchars($short) ?></a>
