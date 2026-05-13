<?php
class UsuarioController extends Controller
{
    // Muestra el perfil del usuario y permite actualizar sus datos básicos.
    public function perfil() {
        $this->requireLogin();

        $usuarioModel = new Usuario();
        $productoModel = new Producto();

        if ($this->isPost()) {
            $usuarioModel->actualizarPerfil($_SESSION['usuario']['id_usuario'], $_POST);

            $_SESSION['usuario'] = array_merge($_SESSION['usuario'], [
                'nombre'=>$_POST['nombre'] ?? '',
                'direccion'=>$_POST['direccion'] ?? ''
            ]);

            $_SESSION['exito'] = 'Perfil actualizado correctamente.';
        }

        $usuario = $usuarioModel->buscarPorId($_SESSION['usuario']['id_usuario']);
        $productos = $productoModel->porUsuario($_SESSION['usuario']['id_usuario']);

        $this->view('usuarios/perfil', [
            'usuario'=>$usuario, 
            'productos'=>$productos, 
            'titulo'=>'Mi perfil'
        ]);
    }
}