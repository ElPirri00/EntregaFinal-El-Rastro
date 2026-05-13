<?php
class AuthController extends Controller
{
    // Inicia sesión comprobando el email, la contraseña y si la cuenta está activa.
    public function login() {
        if ($this->isPost()) {
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->buscarPorEmail(trim($_POST['email'] ?? ''));

            if ($usuario && password_verify($_POST['contrasena'] ?? '', $usuario['contrasena'])) {
                if (isset($usuario['activo']) && (int)$usuario['activo'] === 0) {
                    $_SESSION['error'] = 'Tu cuenta está bloqueada.';
                } else {
                    unset($usuario['contrasena']);
                    $_SESSION['usuario'] = $usuario;
                    $_SESSION['exito'] = 'Sesión iniciada correctamente.';
                    $this->redirect('producto/index');
                }
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas.';
            }
        }

        $this->view('auth/login', ['titulo'=>'Iniciar sesión']);
    }

    // Registra un nuevo usuario en la aplicación.
    public function registro() {
        if ($this->isPost()) {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass = $_POST['contrasena'] ?? '';

            if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
                $_SESSION['error'] = 'Introduce nombre, email válido y contraseña de al menos 6 caracteres.';
            } else {
                try {
                    (new Usuario())->crear($_POST);
                    $_SESSION['exito'] = 'Registro completado. Ya puedes iniciar sesión.';
                    $this->redirect('auth/login');
                } catch (Exception $e) {
                    $_SESSION['error'] = 'No se pudo crear el usuario. Puede que el email ya exista.';
                }
            }
        }

        $this->view('auth/registro', ['titulo'=>'Registro']);
    }

    // Cierra la sesión del usuario actual.
    public function logout() {
        session_destroy();
        session_start();
        $_SESSION['exito'] = 'Sesión cerrada correctamente.';
        $this->redirect('auth/login');
    }
}