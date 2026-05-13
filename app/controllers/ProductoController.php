<?php
class ProductoController extends Controller
{
    private $productoModel;
    private $imagenModel;

    // Carga los modelos de producto e imagen.
    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->imagenModel = new Imagen();
    }

    // Muestra la página principal con el listado de productos.
    public function index()
    {
        $q = trim($_GET['q'] ?? '');
        $productos = $this->productoModel->todos($q);

        $this->view('productos/index', [
            'productos' => $productos,
            'q' => $q,
            'titulo' => 'Inicio'
        ]);
    }

    // Muestra la página de detalle de un producto.
    public function detalle($id = null)
    {
        $producto = $this->productoModel->porId((int)$id);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado o eliminado.';
            $this->redirect('producto/index');
        }

        $imagenes = $this->imagenModel->porProducto((int)$id);
        $valoracionModel = new Valoracion();
        $valoracionesVendedor = $valoracionModel->ultimasPorUsuario($producto['id_usuario'], 3);

        $this->view('productos/detalle', [
            'producto' => $producto,
            'imagenes' => $imagenes,
            'valoracionesVendedor' => $valoracionesVendedor,
            'titulo' => $producto['titulo']
        ]);
    }

    // Permite publicar un nuevo producto.
    public function vender()
    {
        $this->requireLogin();

        if ($this->isPost()) {
            $errores = Producto::validarProducto($_POST);

            if (empty($errores)) {
                $idProducto = $this->productoModel->crear([
                    'titulo' => trim($_POST['titulo']),
                    'descripcion' => trim($_POST['descripcion']),
                    'precio' => (float)$_POST['precio'],
                    'categoria' => $_POST['categoria'],
                    'estado_producto' => $_POST['estado_producto'],
                    'id_usuario' => $_SESSION['usuario']['id_usuario']
                ]);

                $this->guardarImagenes($idProducto);
                $_SESSION['exito'] = 'Producto publicado correctamente.';
                $this->redirect('producto/detalle/' . $idProducto);
            }

            $_SESSION['error'] = implode(' ', $errores);
        }

        $this->view('productos/vender', [
            'titulo' => 'Vender',
            'categorias' => Producto::CATEGORIAS,
            'estadosProducto' => Producto::ESTADOS_PRODUCTO
        ]);
    }

    // Permite editar un producto propio.
    public function editar($id = null)
    {
        $this->requireLogin();
        $producto = $this->productoModel->porId((int)$id);
        $idUsuario = $_SESSION['usuario']['id_usuario'];

        if (!$producto || (int)$producto['id_usuario'] != (int)$idUsuario) {
            $_SESSION['error'] = 'No tienes permiso para editar este producto.';
            $this->redirect('usuario/perfil');
        }

        if ($this->isPost()) {
            $errores = Producto::validarProducto($_POST);

            if (empty($errores)) {
                $this->productoModel->actualizar($id, $idUsuario, [
                    'titulo' => trim($_POST['titulo']),
                    'descripcion' => trim($_POST['descripcion']),
                    'precio' => (float)$_POST['precio'],
                    'categoria' => $_POST['categoria'],
                    'estado_producto' => $_POST['estado_producto']
                ]);

                $_SESSION['exito'] = 'Producto actualizado.';
                $this->redirect('usuario/perfil');
            }

            $_SESSION['error'] = implode(' ', $errores);
        }

        $this->view('productos/editar', [
            'producto' => $producto,
            'titulo' => 'Editar producto',
            'categorias' => Producto::CATEGORIAS,
            'estadosProducto' => Producto::ESTADOS_PRODUCTO
        ]);
    }

    // Elimina un producto propio de forma lógica.
    public function eliminar($id = null)
    {
        $this->requireLogin();

        $ok = $this->productoModel->eliminar((int)$id, $_SESSION['usuario']['id_usuario']);
        $_SESSION[$ok ? 'exito' : 'error'] = $ok ? 'Producto eliminado correctamente.' : 'No se pudo eliminar el producto.';

        $this->redirect('usuario/perfil');
    }

    // Muestra y procesa la compra de un producto.
    public function comprar($id = null)
    {
        $this->requireLogin();
        $producto = $this->productoModel->porId((int)$id);

        if (!$producto || $producto['estado'] != 'disponible') {
            $_SESSION['error'] = 'Este producto no está disponible.';
            $this->redirect('producto/index');
        }

        if ((int)$producto['id_usuario'] == (int)$_SESSION['usuario']['id_usuario']) {
            $_SESSION['error'] = 'No puedes comprar tu propio producto.';
            $this->redirect('producto/detalle/' . $id);
        }

        if ($this->isPost()) {
            $errores = $this->validarPago($_POST);

            if (empty($errores)) {
                $compra = new Compra();
                $idCompra = $compra->crear($_SESSION['usuario']['id_usuario'], $id, $_POST['metodo_pago']);

                if ($idCompra) {
                    $_SESSION['exito'] = 'Compra realizada correctamente. Ahora puedes valorar al vendedor.';
                    $this->redirect('valoracion/crear/' . $idCompra);
                }

                $_SESSION['error'] = 'No se pudo procesar la compra.';
            } else {
                $_SESSION['error'] = implode(' ', $errores);
            }
        }

        $this->view('productos/comprar', [
            'producto' => $producto,
            'titulo' => 'Confirmar compra'
        ]);
    }

    // Valida los datos del método de pago seleccionado.
    private function validarPago($datos)
    {
        $errores = [];
        $metodo = $datos['metodo_pago'] ?? '';
        $permitidos = ['Tarjeta', 'PayPal', 'Bizum', 'Efectivo'];

        if (!in_array($metodo, $permitidos)) {
            return ['Selecciona un método de pago válido.'];
        }

        if ($metodo == 'Tarjeta') {
            if (trim($datos['titular_tarjeta'] ?? '') == '') $errores[] = 'Introduce el titular de la tarjeta.';
            if (!preg_match('/^[0-9 ]{13,23}$/', $datos['numero_tarjeta'] ?? '')) $errores[] = 'Introduce un número de tarjeta válido.';
            if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $datos['caducidad_tarjeta'] ?? '')) $errores[] = 'Introduce la caducidad en formato MM/AA.';
            if (!preg_match('/^\d{3,4}$/', $datos['cvv_tarjeta'] ?? '')) $errores[] = 'Introduce un CVV válido.';
        }

        if ($metodo == 'PayPal' && !filter_var($datos['email_paypal'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Introduce un email de PayPal válido.';
        }

        if ($metodo == 'Bizum' && !preg_match('/^[0-9]{9}$/', $datos['telefono_bizum'] ?? '')) {
            $errores[] = 'Introduce un teléfono Bizum de 9 dígitos.';
        }

        if ($metodo == 'Efectivo' && trim($datos['direccion_envio'] ?? '') == '') {
            $errores[] = 'Introduce una dirección para quedar.';
        }

        return $errores;
    }

    // Guarda las imágenes subidas y las asocia al producto.
    private function guardarImagenes($idProducto)
    {
        if (empty($_FILES['imagenes']['name'][0])) {
            $this->imagenModel->crear($idProducto, 'assets/img/producto-placeholder.svg');
            return;
        }

        $carpeta = ROOT_PATH . '/public/uploads/';
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        foreach ($_FILES['imagenes']['name'] as $i => $nombre) {
            if ($_FILES['imagenes']['error'][$i] != UPLOAD_ERR_OK) {
                continue;
            }

            $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
            if (!in_array($extension, $permitidas)) {
                continue;
            }

            $nuevoNombre = 'producto_' . $idProducto . '_' . time() . '_' . $i . '.' . $extension;
            $destino = $carpeta . $nuevoNombre;

            if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $destino)) {
                $this->imagenModel->crear($idProducto, 'uploads/' . $nuevoNombre);
            }
        }
    }
}