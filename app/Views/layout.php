<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Приложение') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 2rem;
        }

        nav a {
            margin-right: 1rem;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <nav class="mb-4">
        <a href="/" class="btn btn-outline-primary">🏠 Главная</a>
        <a href="/analytics" class="btn btn-outline-info">📊 Аналитика</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/logout" class="btn btn-outline-danger">🚪 Выйти</a>
        <?php else: ?>
            <a href="/login" class="btn btn-outline-success">🔐 Войти</a>
        <?php endif; ?>
    </nav>

    <main>
        <?= $content ?>
    </main>

</body>

</html>
