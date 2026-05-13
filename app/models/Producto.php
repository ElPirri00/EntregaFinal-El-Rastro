<?php
class Producto
{
    public const CATEGORIAS = ['Electrónica', 'Moda', 'Hogar', 'Deportes', 'Otros'];
    public const ESTADOS_PRODUCTO = ['Nuevo', 'Como nuevo', 'Usado', 'Reacondicionado'];

    private $db;

    // Conecta el modelo con la base de datos.
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Obtiene todos los productos disponibles, con opción de búsqueda.
    public function todos($busqueda = '')
    {
        $sql = 'SELECT p.*, u.nombre AS vendedor,
                       (SELECT i.url FROM Imagen i WHERE i.id_producto = p.id_producto LIMIT 1) AS imagen
                FROM Producto p
                INNER JOIN Usuario u ON u.id_usuario = p.id_usuario
                WHERE p.estado = "disponible"';

        $datos = [];

        if ($busqueda != '') {
            $sql .= ' AND (p.titulo LIKE :busqueda OR p.descripcion LIKE :busqueda2 OR p.categoria LIKE :busqueda3)';
            $datos[':busqueda'] = '%' . $busqueda . '%';
            $datos[':busqueda2'] = '%' . $busqueda . '%';
            $datos[':busqueda3'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY p.fecha_publicacion DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->fetchAll();
    }

    // Busca un producto por su id y obtiene también datos del vendedor.
    public function porId($id)
    {
        $sql = 'SELECT p.*, u.nombre AS vendedor, u.direccion, u.email,
                       (SELECT ROUND(AVG(v.puntuacion), 1) FROM Valoracion v WHERE v.id_receptor = u.id_usuario) AS valoracion_media,
                       (SELECT COUNT(*) FROM Valoracion v WHERE v.id_receptor = u.id_usuario) AS valoracion_total
                FROM Producto p
                INNER JOIN Usuario u ON u.id_usuario = p.id_usuario
                WHERE p.id_producto = :id AND p.estado <> "eliminado"';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Obtiene los productos publicados por un usuario concreto.
    public function porUsuario($idUsuario)
    {
        $sql = 'SELECT p.*,
                       (SELECT i.url FROM Imagen i WHERE i.id_producto = p.id_producto LIMIT 1) AS imagen
                FROM Producto p
                WHERE p.id_usuario = :id_usuario AND p.estado <> "eliminado"
                ORDER BY p.fecha_publicacion DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll();
    }

    // Crea un nuevo producto en la base de datos.
    public function crear($datos)
    {
        $sql = 'INSERT INTO Producto
                (titulo, descripcion, precio, categoria, estado_producto, estado, id_usuario)
                VALUES
                (:titulo, :descripcion, :precio, :categoria, :estado_producto, "disponible", :id_usuario)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'],
            ':precio' => $datos['precio'],
            ':categoria' => $datos['categoria'],
            ':estado_producto' => $datos['estado_producto'],
            ':id_usuario' => $datos['id_usuario']
        ]);

        return $this->db->lastInsertId();
    }

    // Actualiza los datos de un producto si pertenece al usuario y no está vendido.
    public function actualizar($idProducto, $idUsuario, $datos)
    {
        $sql = 'UPDATE Producto
                SET titulo = :titulo,
                    descripcion = :descripcion,
                    precio = :precio,
                    categoria = :categoria,
                    estado_producto = :estado_producto
                WHERE id_producto = :id_producto
                  AND id_usuario = :id_usuario
                  AND estado <> "vendido"';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'],
            ':precio' => $datos['precio'],
            ':categoria' => $datos['categoria'],
            ':estado_producto' => $datos['estado_producto'],
            ':id_producto' => $idProducto,
            ':id_usuario' => $idUsuario
        ]);
    }

    // Elimina un producto de forma lógica cambiando su estado a eliminado.
    public function eliminar($idProducto, $idUsuario)
    {
        $sql = 'UPDATE Producto
                SET estado = "eliminado"
                WHERE id_producto = :id_producto
                  AND id_usuario = :id_usuario
                  AND estado <> "eliminado"';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_producto' => $idProducto,
            ':id_usuario' => $idUsuario
        ]);
    }

    // Cambia el estado de un producto a vendido.
    public function marcarVendido($idProducto)
    {
        $stmt = $this->db->prepare('UPDATE Producto SET estado = "vendido" WHERE id_producto = :id');
        return $stmt->execute([':id' => $idProducto]);
    }

    // Obtiene todos los productos para el panel de administración.
    public function todosAdmin()
    {
        $sql = 'SELECT p.*, u.nombre AS vendedor
                FROM Producto p
                INNER JOIN Usuario u ON u.id_usuario = p.id_usuario
                ORDER BY p.fecha_publicacion DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Permite al administrador cambiar el estado de un producto.
    public function cambiarEstadoAdmin($idProducto, $estado)
    {
        $sql = 'UPDATE Producto SET estado = :estado WHERE id_producto = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $idProducto
        ]);
    }

    // Comprueba que los datos del producto sean correctos antes de guardarlo.
    public static function validarProducto($datos)
    {
        $errores = [];

        if (trim($datos['titulo'] ?? '') == '') {
            $errores[] = 'El título es obligatorio.';
        }
        if (trim($datos['descripcion'] ?? '') == '') {
            $errores[] = 'La descripción es obligatoria.';
        }
        if ((float)($datos['precio'] ?? 0) <= 0) {
            $errores[] = 'El precio debe ser mayor que 0.';
        }
        if (!in_array($datos['categoria'] ?? '', self::CATEGORIAS)) {
            $errores[] = 'Selecciona una categoría válida.';
        }
        if (!in_array($datos['estado_producto'] ?? '', self::ESTADOS_PRODUCTO)) {
            $errores[] = 'Selecciona un estado válido.';
        }

        return $errores;
    }
}