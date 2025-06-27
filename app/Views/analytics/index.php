<h2>📊 Моя аналитика</h2>

<?php if (empty($stats)): ?>
    <p>Нет сокращённых ссылок.</p>
<?php else: ?>
    <?php foreach ($stats as $item): ?>
        <h3>
            <a href="<?= $item['url']['original_url'] ?>" target="_blank"><?= htmlspecialchars($item['url']['original_url']) ?></a><br>
            <small>Короткий: <a href="<?= $base_url . '/r/' . $item['url']['short_code'] ?>"> <?= htmlspecialchars($item['url']['short_code']) ?></a> — <?= count($item['clicks']) ?> переходов</small>
        </h3>
        <?php if (!empty($item['clicks'])): ?>
            <table border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>IP</th>
                    <th>Страна</th>
                    <th>Дата перехода</th>
                </tr>
                <?php foreach ($item['clicks'] as $click): ?>
                    <tr>
                        <td><?= htmlspecialchars($click['ip_address']) ?></td>
                        <td><?= htmlspecialchars($click['country'] ?? '-') ?></td>
                        <td><?= $click['redirected_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><em>Нет переходов</em></p>
        <?php endif; ?>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($markers)): ?>
    <h2>🗺️ География переходов</h2>
    <div id="map" style="height: 500px;"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        <?php foreach ($markers as $m): ?>
            L.marker([<?= $m['lat'] ?>, <?= $m['lon'] ?>])
                .addTo(map)
                .bindPopup(`<?= htmlspecialchars($m['ip']) ?><br><?= htmlspecialchars($m['country']) ?>`);
        <?php endforeach; ?>
    </script>
<?php endif; ?>
