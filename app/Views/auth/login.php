<div class="container" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Вход</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>

        <?php if (!empty($resend_email)): ?>
            <form action="/resend-verification" method="post">
                <input type="hidden" name="email" value="<?= htmlspecialchars($resend_email) ?>">
                <button class="btn btn-warning btn-sm mt-2">Отправить письмо повторно</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input name="email" type="email" class="form-control" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input name="password" type="password" class="form-control" id="password" required>
        </div>

        <button class="btn btn-primary w-100">Войти</button>
    </form>

    <p class="mt-3 text-center">
        Нет аккаунта? <a href="/register">Зарегистрироваться</a>
    </p>
</div>
