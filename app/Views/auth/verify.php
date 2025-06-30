<?php
// В начале страницы, если сессия не стартована:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container" style="max-width: 480px; margin: 50px auto;">
    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary" role="alert">
            Статус подтверждения неизвестен.
        </div>
    <?php endif; ?>

    <a href="/login" class="btn btn-primary">Перейти на страницу входа</a>
</div>
