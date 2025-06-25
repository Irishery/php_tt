<?php
class UrlController extends Controller
{
    public function index()
    {
        $this->view('form');
    }

    public function shorten()
    {
        $original = $_POST['url'] ?? '';

        if (empty($original) || !filter_var($original, FILTER_VALIDATE_URL)) {
            return $this->respond(['error' => 'Invalid URL'], 400);
        }

        $short = substr(md5($original . time() . random_bytes(4)), 0, 6);
        $model = new Url();
        $model->save($original, $short);

        $this->respond([
            'original_url' => $original,
            'short_url' => "https://your-site.com/$short"
        ]);
    }

    public function redirect($code)
    {
        $model = new Url();
        $url = $model->findByCode($code);
        if ($url) {
            header("Location: $url");
        } else {
            http_response_code(404);
            echo "URL not found";
        }
    }
}
