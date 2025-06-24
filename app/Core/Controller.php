<?php
class Controller {
    protected function view($name, $data = []) {
        extract($data);
        require __DIR__ . "/../Views/$name.php";
    }
}
