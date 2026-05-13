<?php
class ValoracionController extends Controller
{
    // Muestra las compras del usuario y sus valoraciones.
    public function index() {
        $this->requireLogin();

        $compras = (new Compra())->porComprador((int)$_SESSION['usuario']['id_usuario']);

        $this->view('valoraciones/index', [
            'compras'=>$compras, 
            'titulo'=>'Mis valoraciones'
        ]);
    }

    // Permite valorar al vendedor después de realizar una compra.
    public function crear($idCompra = null) {
        $this->requireLogin();

        $idCompra = (int)$idCompra;
        $idUsuario = (int)$_SESSION['usuario']['id_usuario'];

        $compraModel = new Compra();
        $compra = $compraModel->buscarValorable($idCompra, $idUsuario);

        if (!$compra) {
            $_SESSION['error'] = 'No se ha encontrado una compra válida para valorar.';
            $this->redirect('valoracion/index');
        }

        if (!empty($compra['id_valoracion'])) {
            $_SESSION['error'] = 'Esta compra ya ha sido valorada.';
            $this->redirect('valoracion/index');
        }

        if ((int)$compra['id_vendedor'] === $idUsuario) {
            $_SESSION['error'] = 'No puedes valorarte a ti mismo.';
            $this->redirect('valoracion/index');
        }

        if ($this->isPost()) {
            $puntuacion = (int)($_POST['puntuacion'] ?? 0);
            $comentario = trim($_POST['comentario'] ?? '');

            if ($puntuacion < 1 || $puntuacion > 5) {
                $_SESSION['error'] = 'Selecciona una puntuación entre 1 y 5.';
            } else {
                $ok = (new Valoracion())->crear($idCompra, $idUsuario, (int)$compra['id_vendedor'], $puntuacion, $comentario);

                if ($ok) {
                    $_SESSION['exito'] = 'Valoración guardada correctamente.';
                    $this->redirect('valoracion/index');
                }

                $_SESSION['error'] = 'No se pudo guardar la valoración. Puede que ya exista.';
            }
        }

        $this->view('valoraciones/crear', [
            'compra'=>$compra, 
            'titulo'=>'Valorar usuario'
        ]);
    }
}