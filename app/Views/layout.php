<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Приложение' ?></title>
    <style>
        body {
            font-family: sans-serif;
            margin: 40px;
        }

        nav {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <nav>
        <a href="/">🏠 Главная</a> |
        <a href="/analytics/example">📊 Аналитика</a> |
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/logout">🚪 Выйти</a>
        <?php else: ?>
            <a href="/login">🔐 Войти</a>
        <?php endif; ?>
    </nav>

    <main>
        <?= $content ?>
    </main>

</body>

</html>
