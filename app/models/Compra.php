<?php
class Compra
{
    private $db;

    // Conecta el modelo con la base de datos.
    public function __construct() { 
        $this->db = Database::getConnection(); 
    }

    // Crea una compra y marca el producto como vendido.
    public function crear($idComprador, $idProducto, $metodo) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('INSERT INTO Compra (estado, metodo_pago, id_comprador, id_producto)
                                        VALUES ("pagado", :metodo, :comprador, :producto)');
            $stmt->execute([':metodo'=>$metodo, ':comprador'=>$idComprador, ':producto'=>$idProducto]);

            $idCompra = (int)$this->db->lastInsertId();

            $this->db->prepare('UPDATE Producto SET estado="vendido" WHERE id_producto=:id')
                     ->execute([':id'=>$idProducto]);

            $this->db->commit();
            return $idCompra;
        } catch (Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    // Obtiene todas las compras realizadas por un usuario.
    public function porComprador($idComprador) {
        $sql = 'SELECT c.*, p.titulo, p.precio, p.id_usuario AS id_vendedor, u.nombre AS vendedor,
                       v.id_valoracion, v.puntuacion, v.comentario
                FROM Compra c
                INNER JOIN Producto p ON p.id_producto = c.id_producto
                INNER JOIN Usuario u ON u.id_usuario = p.id_usuario
                LEFT JOIN Valoracion v ON v.id_compra = c.id_compra
                WHERE c.id_comprador=:id
                ORDER BY c.fecha DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id'=>$idComprador]);

        return $stmt->fetchAll();
    }

    // Busca una compra concreta para comprobar si puede ser valorada.
    public function buscarValorable($idCompra, $idComprador) {
        $sql = 'SELECT c.*, p.titulo, p.precio, p.id_usuario AS id_vendedor, u.nombre AS vendedor,
                       v.id_valoracion
                FROM Compra c
                INNER JOIN Producto p ON p.id_producto = c.id_producto
                INNER JOIN Usuario u ON u.id_usuario = p.id_usuario
                LEFT JOIN Valoracion v ON v.id_compra = c.id_compra
                WHERE c.id_compra=:compra AND c.id_comprador=:comprador AND c.estado="pagado"
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':compra'=>$idCompra, ':comprador'=>$idComprador]);

        return $stmt->fetch() ?: null;
    }
}