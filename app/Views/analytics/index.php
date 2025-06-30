<div class="container">
    <h2 class="mb-4">Аналитика</h2>

    <?php if (empty($stats)): ?>
        <p class="text-muted">Переходов пока нет.</p>
    <?php else: ?>
        <?php foreach ($stats as $index => $item): ?>
            <h3>
                <a href="<?= htmlspecialchars($item['url']['original_url']) ?>" target="_blank">
                    <?= htmlspecialchars($item['url']['original_url']) ?>
                </a><br>
                <small>
                    Короткий:
                    <a id="short-link-<?= $index ?>" href="<?= $base_url . '/r/' . htmlspecialchars($item['url']['short_code']) ?>" target="_blank">
                        <?= htmlspecialchars($item['url']['short_code']) ?>
                    </a> — <?= count($item['clicks']) ?> переходов
                </small>
            </h3>


            <button class="btn btn-sm btn-outline-primary mb-3 copy-btn" data-copy-target="short-link-<?= $index ?>">
                📋 Скопировать короткую ссылку
            </button>

            <?php if (!empty($item['clicks'])): ?>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Страна</th>
                            <th>Время перехода</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($item['clicks'] as $click): ?>
                            <tr>
                                <td><?= htmlspecialchars($click['ip_address']) ?></td>
                                <td><?= htmlspecialchars($click['country'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($click['redirected_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button class="toggle-map-btn" data-map-id="map-<?= $index ?>">
                    Показать карту переходов
                </button>

                <div id="map-<?= $index ?>" class="link-map" style="height: 400px; margin-top: 10px; display: none; border: 1px solid #ccc;"></div>
            <?php else: ?>
                <p><em>Нет переходов</em></p>
            <?php endif; ?>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    document.querySelectorAll('.toggle-map-btn').forEach(button => {
        button.addEventListener('click', () => {
            const mapId = button.getAttribute('data-map-id');
            const mapDiv = document.getElementById(mapId);

            if (mapDiv.style.display === 'none' || mapDiv.style.display === '') {
                mapDiv.style.display = 'block';
                button.textContent = 'Скрыть карту переходов';

                // Если карта еще не инициализирована — инициализируем
                if (!mapDiv._leafletMap) {
                    // Получим данные кликов для этой ссылки из атрибута data-clicks
                    const clicks = JSON.parse(button.getAttribute('data-clicks') || '[]');
                    const map = L.map(mapId).setView([20, 0], 2);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    clicks.forEach(click => {
                        if (click.lat && click.lon) {
                            L.marker([click.lat, click.lon])
                                .addTo(map)
                                .bindPopup(`${click.ip_address || ''}<br>${click.country || 'Страна не определена'}`);
                        }
                    });

                    mapDiv._leafletMap = map;
                }
            } else {
                mapDiv.style.display = 'none';
                button.textContent = 'Показать карту переходов';
            }
        });
    });
</script>

<script>
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-copy-target');
            const linkElem = document.getElementById(targetId);
            if (!linkElem) return;

            // Формируем полный URL с доменом
            const link = linkElem.getAttribute('href');

            navigator.clipboard.writeText(link).then(() => {
                const originalText = button.textContent;
                button.textContent = '✔ Скопировано!';
                button.disabled = true;

                setTimeout(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                }, 2000);
            }).catch(() => {
                alert('Не удалось скопировать ссылку');
            });
        });
    });
</script>
