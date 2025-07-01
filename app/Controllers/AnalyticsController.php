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

    private function getStats($userId): array
    {
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

        return [
            'stats' => $stats,
            'markers' => $markers,
            'base_url' => $this->baseUrl
        ];
    }

    public function index()
    {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];

        $data = $this->getStats($userId);

        $acceptJson = strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
        if ($acceptJson) {
            $this->respondJson($data);
        } else {
            $this->respondView('analytics/index', $data);
        }
    }

    public function apiIndex()
    {
        $this->requireApiAuth();

        $userId = $_SESSION['user_id'];

        $this->respondJson($this->getStats($userId));
    }
}
