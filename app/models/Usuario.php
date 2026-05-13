<?php
class Usuario
{
    private $db;

    // Conecta el modelo con la base de datos.
    public function __construct() { 
        $this->db = Database::getConnection(); 
    }

    // Crea un nuevo usuario en la base de datos.
    public function crear($data) {
        $sql = 'INSERT INTO Usuario (nombre, email, contrasena, direccion, metodo_pago, tipo, activo)
                VALUES (:nombre, :email, :contrasena, :direccion, :metodo_pago, :tipo, 1)';
        return $this->db->prepare($sql)->execute([
            ':nombre' => $data['nombre'],
            ':email' => $data['email'],
            ':contrasena' => password_hash($data['contrasena'], PASSWORD_DEFAULT),
            ':direccion' => $data['direccion'] ?? '',
            ':metodo_pago' => $data['metodo_pago'] ?? '',
            ':tipo' => $data['tipo'] ?? 'usuario',
        ]);
    }

    // Busca un usuario por su email.
    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare('SELECT * FROM Usuario WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    // Busca un usuario por su id.
    public function buscarPorId($id) {
        $stmt = $this->db->prepare('SELECT id_usuario, nombre, email, direccion, metodo_pago, fecha_registro, tipo, activo FROM Usuario WHERE id_usuario = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // Actualiza los datos básicos del perfil del usuario.
    public function actualizarPerfil($id, $data) {
        $sql = 'UPDATE Usuario SET nombre=:nombre, direccion=:direccion WHERE id_usuario=:id';
        return $this->db->prepare($sql)->execute([
            ':nombre'=>$data['nombre'] ?? '',
            ':direccion'=>$data['direccion'] ?? '',
            ':id'=>$id
        ]);
    }

    // Obtiene todos los usuarios para el panel de administración.
    public function todos() {
        $sql = 'SELECT id_usuario, nombre, email, direccion, fecha_registro, tipo, activo
                FROM Usuario
                ORDER BY fecha_registro DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Activa o bloquea un usuario desde el panel de administración.
    public function cambiarActivo($idUsuario, $activo) {
        $sql = 'UPDATE Usuario SET activo = :activo WHERE id_usuario = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':activo' => $activo,
            ':id' => $idUsuario
        ]);
    }
}