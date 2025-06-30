<h1>Ваша короткая ссылка:</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div style="display: flex; align-items: center; gap: 10px;">
    <a id="short-link" href="/r/<?= htmlspecialchars($short_code) ?>" target="_blank">
        /r/<?= htmlspecialchars($short_code) ?>
    </a>
    <button id="copy-btn" class="btn btn-sm btn-outline-primary" type="button">📋 Скопировать</button>
</div>

<script>
    document.getElementById('copy-btn').addEventListener('click', () => {
        const linkElem = document.getElementById('short-link');
        const link = window.location.origin + linkElem.getAttribute('href'); // полный URL

        navigator.clipboard.writeText(link).then(() => {
            const btn = document.getElementById('copy-btn');
            const originalText = btn.textContent;
            btn.textContent = '✔ Скопировано!';
            btn.disabled = true;
            setTimeout(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            }, 2000);
        }).catch(() => {
            alert('Не удалось скопировать ссылку');
        });
    });
</script>
