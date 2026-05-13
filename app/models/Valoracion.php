<?php
class Valoracion
{
    private $db;

    // Conecta el modelo con la base de datos.
    public function __construct() { 
        $this->db = Database::getConnection(); 
    }

    // Crea una valoración para un usuario después de una compra.
    public function crear($idCompra, $idEmisor, $idReceptor, $puntuacion, $comentario = '') {
        if ($puntuacion < 1 || $puntuacion > 5 || $idEmisor === $idReceptor) return false;

        $sql = 'INSERT INTO Valoracion (puntuacion, comentario, id_emisor, id_receptor, id_compra)
                VALUES (:puntuacion, :comentario, :emisor, :receptor, :compra)';

        try {
            return $this->db->prepare($sql)->execute([
                ':puntuacion'=>$puntuacion,
                ':comentario'=>trim($comentario),
                ':emisor'=>$idEmisor,
                ':receptor'=>$idReceptor,
                ':compra'=>$idCompra
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Comprueba si una compra ya tiene una valoración.
    public function existePorCompra($idCompra) {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM Valoracion WHERE id_compra=:id');
        $stmt->execute([':id'=>$idCompra]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // Obtiene todas las valoraciones recibidas por un usuario.
    public function porUsuario($idUsuario) {
        $sql = 'SELECT v.*, ue.nombre AS autor, p.titulo AS producto
                FROM Valoracion v
                INNER JOIN Usuario ue ON ue.id_usuario = v.id_emisor
                INNER JOIN Compra c ON c.id_compra = v.id_compra
                INNER JOIN Producto p ON p.id_producto = c.id_producto
                WHERE v.id_receptor=:id
                ORDER BY v.fecha DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id'=>$idUsuario]);

        return $stmt->fetchAll();
    }

    // Obtiene las últimas valoraciones recibidas por un usuario.
    public function ultimasPorUsuario($idUsuario, $limite = 3) {
        $limite = max(1, min(10, $limite));

        $sql = 'SELECT v.puntuacion, v.comentario, v.fecha, ue.nombre AS autor, p.titulo AS producto
                FROM Valoracion v
                INNER JOIN Usuario ue ON ue.id_usuario = v.id_emisor
                INNER JOIN Compra c ON c.id_compra = v.id_compra
                INNER JOIN Producto p ON p.id_producto = c.id_producto
                WHERE v.id_receptor = :id
                ORDER BY v.fecha DESC
                LIMIT ' . $limite;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idUsuario]);

        return $stmt->fetchAll();
    }

    // Calcula la media y el número total de valoraciones de un usuario.
    public function resumenUsuario($idUsuario) {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total, ROUND(AVG(puntuacion), 1) AS media FROM Valoracion WHERE id_receptor=:id');
        $stmt->execute([':id'=>$idUsuario]);

        $res = $stmt->fetch() ?: ['total'=>0, 'media'=>null];

        return [
            'total'=>(int)$res['total'], 
            'media'=>$res['media'] !== null ? (float)$res['media'] : null
        ];
    }
}