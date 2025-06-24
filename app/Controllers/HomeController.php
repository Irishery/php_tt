<?php
class HomeController extends Controller {
    public function index() {
        $this->view('form');
    }

    public function shorten() {
        $original = $_POST['url'] ?? '';
        $short = substr(md5($original . time()), 0, 6);
        $model = new Url();
        $model->save($original, $short);
        $this->view('result', ['short' => $short]);
    }

    public function redirect($code) {
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
