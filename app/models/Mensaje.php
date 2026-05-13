<?php
class Mensaje
{
    private $db;

    // Conecta el modelo con la base de datos.
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Obtiene las conversaciones recientes del usuario.
    public function conversaciones($idUsuario)
    {
        $sql = 'SELECT m.*, p.titulo AS producto,
                       emisor.nombre AS nombre_emisor,
                       receptor.nombre AS nombre_receptor
                FROM Mensaje m
                INNER JOIN Usuario emisor ON emisor.id_usuario = m.id_emisor
                INNER JOIN Usuario receptor ON receptor.id_usuario = m.id_receptor
                LEFT JOIN Producto p ON p.id_producto = m.id_producto
                WHERE m.id_emisor = :id1 OR m.id_receptor = :id2
                ORDER BY m.fecha DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id1' => $idUsuario,
            ':id2' => $idUsuario
        ]);

        $mensajes = $stmt->fetchAll();
        $chats = [];

        foreach ($mensajes as $m) {
            if ((int)$m['id_emisor'] == (int)$idUsuario) {
                $idContacto = $m['id_receptor'];
                $nombreContacto = $m['nombre_receptor'];
            } else {
                $idContacto = $m['id_emisor'];
                $nombreContacto = $m['nombre_emisor'];
            }

            $idProducto = $m['id_producto'] ?: 0;
            $clave = $idContacto . '-' . $idProducto;

            if (!isset($chats[$clave])) {
                $chats[$clave] = [
                    'id_contacto' => $idContacto,
                    'contacto' => $nombreContacto,
                    'id_producto' => $m['id_producto'],
                    'producto' => $m['producto'],
                    'ultima_fecha' => $m['fecha'],
                    'ultimo_mensaje' => $m['contenido']
                ];
            }
        }

        return array_values($chats);
    }

    // Obtiene todos los mensajes entre dos usuarios, opcionalmente sobre un producto concreto.
    public function hilo($usuarioA, $usuarioB, $idProducto = null)
    {
        $sql = 'SELECT m.*, emisor.nombre AS emisor, receptor.nombre AS receptor, p.titulo AS producto
                FROM Mensaje m
                INNER JOIN Usuario emisor ON emisor.id_usuario = m.id_emisor
                INNER JOIN Usuario receptor ON receptor.id_usuario = m.id_receptor
                LEFT JOIN Producto p ON p.id_producto = m.id_producto
                WHERE ((m.id_emisor = :a1 AND m.id_receptor = :b1)
                    OR (m.id_emisor = :b2 AND m.id_receptor = :a2))';

        $datos = [
            ':a1' => $usuarioA,
            ':b1' => $usuarioB,
            ':b2' => $usuarioB,
            ':a2' => $usuarioA
        ];

        if ($idProducto != null && $idProducto > 0) {
            $sql .= ' AND m.id_producto = :id_producto';
            $datos[':id_producto'] = $idProducto;
        }

        $sql .= ' ORDER BY m.fecha ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->fetchAll();
    }

    // Envía un mensaje de un usuario a otro.
    public function enviar($emisor, $receptor, $contenido, $idProducto = null)
    {
        $contenido = trim($contenido);

        if ($emisor == $receptor || $contenido == '') {
            return false;
        }

        $sql = 'INSERT INTO Mensaje (contenido, id_emisor, id_receptor, id_producto)
                VALUES (:contenido, :emisor, :receptor, :producto)';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':contenido' => $contenido,
            ':emisor' => $emisor,
            ':receptor' => $receptor,
            ':producto' => $idProducto ?: null
        ]);
    }

    // Comprueba si ya existe una conversación entre dos usuarios.
    public function existeConversacion($idUsuario, $idContacto, $idProducto = null)
    {
        $sql = 'SELECT COUNT(*) FROM Mensaje
                WHERE ((id_emisor = :u1 AND id_receptor = :c1)
                    OR (id_emisor = :c2 AND id_receptor = :u2))';

        $datos = [
            ':u1' => $idUsuario,
            ':c1' => $idContacto,
            ':c2' => $idContacto,
            ':u2' => $idUsuario
        ];

        if ($idProducto != null && $idProducto > 0) {
            $sql .= ' AND id_producto = :producto';
            $datos[':producto'] = $idProducto;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->fetchColumn() > 0;
    }
}