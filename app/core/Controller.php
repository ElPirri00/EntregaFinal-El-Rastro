<?php
abstract class Controller
{
    // Carga una vista dentro del layout principal.
    protected function view($view, $data = []) {
        extract($data, EXTR_SKIP);
        require APP_PATH . '/views/layouts/header.php';
        require APP_PATH . '/views/' . $view . '.php';
        require APP_PATH . '/views/layouts/footer.php';
    }

    // Redirige a otra ruta de la aplicación.
    protected function redirect($route = '') {
        header('Location: ' . app_url($route));
        exit;
    }

    // Comprueba que el usuario haya iniciado sesión.
    protected function requireLogin() {
        if (empty($_SESSION['usuario'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta sección.';
            $this->redirect('auth/login');
        }
    }

    // Comprueba si la petición actual es de tipo POST.
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}