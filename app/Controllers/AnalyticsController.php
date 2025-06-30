<?php

require_once __DIR__ . '/../Models/Analytics.php';
require_once __DIR__ . '/../Models/Url.php';
require_once __DIR__ . '/../helpers/geo.php';

class AnalyticsController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->baseUrl = $config['base_url'];
    }

    public function index()
    {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $urlModel = new Url();
        $analytics = new Analytics();

        $userLinks = $urlModel->getAllByUser($userId);
        $stats = [];
        $markers = [];

        foreach ($userLinks as $link) {
            $clicks = $analytics->getClicks($link['id']);
            $stats[] = [
                'url' => $link,
                'clicks' => $clicks,
            ];

            foreach ($clicks as $click) {
                $geo = getGeoCoords($click['ip_address']);
                if ($geo) {
                    $markers[] = [
                        'lat' => $geo['lat'],
                        'lon' => $geo['lon'],
                        'ip' => $click['ip_address'],
                        'country' => $click['country'] ?? '-',
                    ];
                }
            }
        }

        $data = [
            'stats' => $stats,
            'markers' => $markers,
            'base_url' => $this->baseUrl,
        ];

        $acceptJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
        if ($acceptJson) {
            $this->respondJson($data);
        } else {
            $this->respondView('analytics/index', $data);
        }
    }

    public function apiIndex()
    {
        $this->requireApiAuth(); // проверка токена
        $userId = $_SESSION['user_id'];

        $urlModel = new Url();

        $userLinks = $urlModel->getAllByUser($userId);
        $stats = [];

        foreach ($userLinks as $link) {
            $stats[] = [
                'url' => $link,
                'short_url' => $this->baseUrl . '/r/' . $link['short_code'],
            ];
        }

        $this->respondJson(['data' => $stats]);
    }
}
