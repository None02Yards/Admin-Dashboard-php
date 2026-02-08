<?php
class Controller
{
    protected function view($path, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            echo "View $path not found.";
            return;
        }
        require $viewFile;
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}