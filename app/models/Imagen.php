<?php
class Imagen
{
    private $db;

    // Conecta el modelo con la base de datos.
    public function __construct() { 
        $this->db = Database::getConnection(); 
    }

    // Obtiene todas las imágenes asociadas a un producto.
    public function porProducto($idProducto) {
        $stmt = $this->db->prepare('SELECT * FROM Imagen WHERE id_producto=:id ORDER BY id_imagen ASC');
        $stmt->execute([':id'=>$idProducto]);
        return $stmt->fetchAll();
    }

    // Guarda una nueva imagen asociada a un producto.
    public function crear($idProducto, $url) {
        $stmt = $this->db->prepare('INSERT INTO Imagen (url, id_producto) VALUES (:url, :id_producto)');
        return $stmt->execute([':url'=>$url, ':id_producto'=>$idProducto]);
    }
}