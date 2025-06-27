<?php

require_once __DIR__ . '/../Models/Analytics.php';
require_once __DIR__ . '/../Models/Url.php';
require_once __DIR__ . '/../helpers/geo.php';

function getIp()
{
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(end(explode(',', $_SERVER[$key])));
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
}

class AnalyticsController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->baseUrl = rtrim($config['base_url'], '/');
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $realip = getIp();
        echo ("ip " . $realip . "");

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
                'clicks' => $clicks
            ];

            foreach ($clicks as $click) {
                $geo = getGeoCoords($click['ip_address']);
                echo ($click['ip_address'] . "\n");
                echo ($geo);
                if ($geo) {
                    $markers[] = [
                        'lat' => $geo['lat'],
                        'lon' => $geo['lon'],
                        'ip' => $click['ip_address'],
                        'country' => $click['country'] ?? '-'
                    ];
                }
            }
        }

        $this->view('analytics/index', [
            'stats' => $stats,
            'markers' => $markers,
            'base_url' => $this->baseUrl,
        ]);
    }
}
