<div class="container" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Регистрация</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <div class="mb-3">
            <label class="form-label">Имя пользователя</label>
            <input name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Пароль</label>
            <input name="password" type="password" class="form-control" required>
        </div>

        <button class="btn btn-success w-100">Зарегистрироваться</button>
    </form>

    <p class="mt-3 text-center">
        Уже есть аккаунт? <a href="/login">Войти</a>
    </p>
</div>
