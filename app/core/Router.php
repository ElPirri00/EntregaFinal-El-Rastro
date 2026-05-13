<?php
class Router
{
    // Lee la URL y decide qué controlador y método se deben ejecutar.
    public function dispatch()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : 'producto/index';
        $trozos = explode('/', trim($url, '/'));

        $nombreControlador = !empty($trozos[0]) ? ucfirst($trozos[0]) . 'Controller' : 'ProductoController';
        $metodo = isset($trozos[1]) ? $trozos[1] : 'index';
        $parametros = array_slice($trozos, 2);

        $archivo = APP_PATH . '/controllers/' . $nombreControlador . '.php';

        // Si el controlador no existe, carga por defecto el listado de productos.
        if (!file_exists($archivo)) {
            $nombreControlador = 'ProductoController';
            $metodo = 'index';
            $parametros = [];
            $archivo = APP_PATH . '/controllers/ProductoController.php';
        }

        require_once $archivo;
        $controlador = new $nombreControlador();

        // Si el método no existe, usa el método index por defecto.
        if (!method_exists($controlador, $metodo)) {
            $metodo = 'index';
            $parametros = [];
        }

        // Ejecuta el método del controlador y le pasa los parámetros de la URL.
        call_user_func_array([$controlador, $metodo], $parametros);
    }
}