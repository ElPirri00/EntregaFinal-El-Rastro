<?php
class AdminController extends Controller
{
    // Comprueba que el usuario tenga permisos de administrador.
    private function comprobarAdmin()
    {
        if (empty($_SESSION['usuario']) || ($_SESSION['usuario']['tipo'] ?? '') !== 'administrador') {
            $_SESSION['error'] = 'No tienes permiso para entrar al panel de administración.';
            $this->redirect('producto/index');
        }
    }

    // Muestra el panel de administración con productos y usuarios.
    public function index()
    {
        $this->comprobarAdmin();

        $productos = (new Producto())->todosAdmin();
        $usuarios = (new Usuario())->todos();

        $this->view('admin/index', [
            'titulo' => 'Administración',
            'productos' => $productos,
            'usuarios' => $usuarios
        ]);
    }

    // Oculta un producto cambiando su estado a eliminado.
    public function ocultarProducto($idProducto)
    {
        $this->comprobarAdmin();
        (new Producto())->cambiarEstadoAdmin($idProducto, 'eliminado');
        $_SESSION['exito'] = 'Producto ocultado.';
        $this->redirect('admin/index');
    }

    // Vuelve a activar un producto ocultado.
    public function activarProducto($idProducto)
    {
        $this->comprobarAdmin();
        (new Producto())->cambiarEstadoAdmin($idProducto, 'disponible');
        $_SESSION['exito'] = 'Producto activado.';
        $this->redirect('admin/index');
    }

    // Bloquea un usuario para que no pueda usar la aplicación.
    public function bloquearUsuario($idUsuario)
    {
        $this->comprobarAdmin();

        if ((int)$idUsuario === (int)$_SESSION['usuario']['id_usuario']) {
            $_SESSION['error'] = 'No puedes bloquear tu propia cuenta.';
        } else {
            (new Usuario())->cambiarActivo($idUsuario, 0);
            $_SESSION['exito'] = 'Usuario bloqueado.';
        }

        $this->redirect('admin/index');
    }

    // Vuelve a activar un usuario bloqueado.
    public function activarUsuario($idUsuario)
    {
        $this->comprobarAdmin();
        (new Usuario())->cambiarActivo($idUsuario, 1);
        $_SESSION['exito'] = 'Usuario activado.';
        $this->redirect('admin/index');
    }
}