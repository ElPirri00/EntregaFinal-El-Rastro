<?php
class MensajeController extends Controller
{
    // Muestra la bandeja de mensajes con las conversaciones recientes.
    public function index()
    {
        $this->requireLogin();

        $mensajeModel = new Mensaje();
        $conversaciones = $mensajeModel->conversaciones($_SESSION['usuario']['id_usuario']);

        $this->view('mensajes/index', [
            'conversaciones' => $conversaciones,
            'mensajes' => [],
            'chat' => null,
            'titulo' => 'Mensajes'
        ]);
    }

    // Abre un chat con el vendedor desde la página de detalle de un producto.
    public function contactar($idProducto = null)
    {
        $this->requireLogin();

        $producto = (new Producto())->porId((int)$idProducto);

        if (!$producto || $producto['estado'] != 'disponible') {
            $_SESSION['error'] = 'No se puede contactar por este producto.';
            $this->redirect('producto/index');
        }

        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $idVendedor = $producto['id_usuario'];

        if ((int)$idUsuario == (int)$idVendedor) {
            $_SESSION['error'] = 'No puedes abrir un chat contigo mismo.';
            $this->redirect('producto/detalle/' . $idProducto);
        }

        $this->cargarChat($idVendedor, $idProducto, $producto['titulo'], $producto['vendedor']);
    }

    // Muestra una conversación concreta entre el usuario y otro contacto.
    public function ver($idContacto = null, $idProducto = null)
    {
        $this->requireLogin();

        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $idContacto = (int)$idContacto;
        $idProducto = $idProducto ? (int)$idProducto : null;

        $mensajeModel = new Mensaje();

        if ($idContacto <= 0 || !$mensajeModel->existeConversacion($idUsuario, $idContacto, $idProducto)) {
            $_SESSION['error'] = 'No se ha encontrado esa conversación.';
            $this->redirect('mensaje/index');
        }

        $this->cargarChat($idContacto, $idProducto, null, null);
    }

    // Envía un mensaje al contacto seleccionado.
    public function enviar()
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('mensaje/index');
        }

        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $idContacto = (int)($_POST['id_contacto'] ?? 0);
        $idProducto = (int)($_POST['id_producto'] ?? 0);
        $contenido = trim($_POST['contenido'] ?? '');

        $mensajeModel = new Mensaje();
        $puedeEnviar = false;

        if ($idProducto > 0) {
            $producto = (new Producto())->porId($idProducto);
            if ($producto && (int)$producto['id_usuario'] == $idContacto && (int)$idUsuario != $idContacto) {
                $puedeEnviar = true;
            }
        }

        if (!$puedeEnviar && $mensajeModel->existeConversacion($idUsuario, $idContacto, $idProducto ?: null)) {
            $puedeEnviar = true;
        }

        if ($puedeEnviar && $mensajeModel->enviar($idUsuario, $idContacto, $contenido, $idProducto ?: null)) {
            $_SESSION['exito'] = 'Mensaje enviado.';
        } else {
            $_SESSION['error'] = 'No se pudo enviar el mensaje.';
        }

        if ($idProducto > 0) {
            $this->redirect('mensaje/ver/' . $idContacto . '/' . $idProducto);
        }

        $this->redirect('mensaje/ver/' . $idContacto);
    }

    // Devuelve mensajes y conversaciones en JSON para actualizarlos con JavaScript.
    public function poll($idContacto = null, $idProducto = null)
    {
        $this->requireLogin();

        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $idContacto = (int)$idContacto;
        $idProducto = $idProducto ? (int)$idProducto : null;

        $mensajeModel = new Mensaje();
        $conversaciones = $mensajeModel->conversaciones($idUsuario);
        $mensajes = [];

        if ($idContacto > 0 && $mensajeModel->existeConversacion($idUsuario, $idContacto, $idProducto)) {
            $mensajes = $mensajeModel->hilo($idUsuario, $idContacto, $idProducto);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'usuario_id' => $idUsuario,
            'conversaciones' => $conversaciones,
            'mensajes' => $mensajes
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Carga los datos necesarios para mostrar un chat concreto.
    private function cargarChat($idContacto, $idProducto, $productoTitulo, $contactoNombre)
    {
        $mensajeModel = new Mensaje();
        $idUsuario = $_SESSION['usuario']['id_usuario'];

        $conversaciones = $mensajeModel->conversaciones($idUsuario);
        $mensajes = $mensajeModel->hilo($idUsuario, $idContacto, $idProducto);

        foreach ($conversaciones as $chat) {
            $mismoContacto = (int)$chat['id_contacto'] == (int)$idContacto;
            $mismoProducto = (int)($chat['id_producto'] ?? 0) == (int)($idProducto ?? 0);

            if ($mismoContacto && $mismoProducto) {
                $contactoNombre = $contactoNombre ?: $chat['contacto'];
                $productoTitulo = $productoTitulo ?: $chat['producto'];
            }
        }

        $this->view('mensajes/index', [
            'conversaciones' => $conversaciones,
            'mensajes' => $mensajes,
            'chat' => [
                'id_contacto' => $idContacto,
                'id_producto' => $idProducto,
                'contacto' => $contactoNombre ?: 'Usuario',
                'producto' => $productoTitulo
            ],
            'titulo' => 'Mensajes'
        ]);
    }
}