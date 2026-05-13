DROP DATABASE IF EXISTS el_rastro_mvc;
CREATE DATABASE el_rastro_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE el_rastro_mvc;

CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
    metodo_pago VARCHAR(50),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(20) NOT NULL DEFAULT 'usuario',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT chk_usuario_tipo CHECK (tipo IN ('administrador', 'usuario'))
) ENGINE=InnoDB;

CREATE TABLE Producto (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    estado_producto VARCHAR(50) NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'disponible',
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_producto_usuario FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    CONSTRAINT chk_producto_precio CHECK (precio > 0),
    CONSTRAINT chk_producto_categoria CHECK (categoria IN ('Electrónica', 'Moda', 'Hogar', 'Deportes', 'Otros')),
    CONSTRAINT chk_producto_estado_producto CHECK (estado_producto IN ('Nuevo', 'Como nuevo', 'Usado', 'Reacondicionado')),
    CONSTRAINT chk_producto_estado CHECK (estado IN ('disponible', 'vendido', 'eliminado'))
) ENGINE=InnoDB;

CREATE TABLE Imagen (
    id_imagen INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    id_producto INT NOT NULL,
    CONSTRAINT fk_imagen_producto FOREIGN KEY (id_producto) REFERENCES Producto(id_producto) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Compra (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) NOT NULL DEFAULT 'pagado',
    metodo_pago VARCHAR(50) NOT NULL,
    id_comprador INT NOT NULL,
    id_producto INT NOT NULL UNIQUE,
    CONSTRAINT fk_compra_usuario FOREIGN KEY (id_comprador) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_compra_producto FOREIGN KEY (id_producto) REFERENCES Producto(id_producto),
    CONSTRAINT chk_compra_estado CHECK (estado IN ('pagado', 'fallido'))
) ENGINE=InnoDB;

CREATE TABLE Mensaje (
    id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
    contenido TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_emisor INT NOT NULL,
    id_receptor INT NOT NULL,
    id_producto INT NULL,
    CONSTRAINT fk_mensaje_emisor FOREIGN KEY (id_emisor) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_mensaje_receptor FOREIGN KEY (id_receptor) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_mensaje_producto FOREIGN KEY (id_producto) REFERENCES Producto(id_producto) ON DELETE SET NULL,
    CONSTRAINT chk_mensaje_distinto CHECK (id_emisor <> id_receptor)
) ENGINE=InnoDB;

CREATE TABLE Valoracion (
    id_valoracion INT AUTO_INCREMENT PRIMARY KEY,
    puntuacion INT NOT NULL,
    comentario TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_emisor INT NOT NULL,
    id_receptor INT NOT NULL,
    id_compra INT NOT NULL UNIQUE,
    CONSTRAINT fk_valoracion_emisor FOREIGN KEY (id_emisor) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_valoracion_receptor FOREIGN KEY (id_receptor) REFERENCES Usuario(id_usuario),
    CONSTRAINT fk_valoracion_compra FOREIGN KEY (id_compra) REFERENCES Compra(id_compra),
    CONSTRAINT chk_valoracion_puntuacion CHECK (puntuacion BETWEEN 1 AND 5)
) ENGINE=InnoDB;

INSERT INTO Usuario (nombre, email, contrasena, direccion, metodo_pago, tipo, activo) VALUES
('Ana López', 'ana@demo.com', '$2y$12$/aNRoYUfizDaVC64aXhhqeh9j9oBweTx46RUuaFWGuVJTFwBy2rre', 'Cuenca', 'Tarjeta', 'usuario', 1),
('Pedro Picapiedra', 'pedro@demo.com', '$2y$12$/aNRoYUfizDaVC64aXhhqeh9j9oBweTx46RUuaFWGuVJTFwBy2rre', 'Madrid', 'PayPal', 'usuario', 1),
('Administrador', 'admin@demo.com', '$2y$12$/aNRoYUfizDaVC64aXhhqeh9j9oBweTx46RUuaFWGuVJTFwBy2rre', 'Toledo', 'Tarjeta', 'administrador', 1);

INSERT INTO Producto (titulo, descripcion, precio, categoria, estado_producto, estado, id_usuario) VALUES
('Bicicleta urbana', 'Bicicleta de paseo en buen estado. Ideal para moverse por la ciudad.', 120.00, 'Deportes', 'Usado', 'disponible', 1),
('Silla de escritorio', 'Silla cómoda con respaldo regulable. Presenta marcas de uso normales.', 35.50, 'Hogar', 'Usado', 'disponible', 2),
('Cámara analógica', 'Cámara antigua para coleccionistas. Funciona correctamente.', 80.00, 'Electrónica', 'Como nuevo', 'disponible', 1),
('Mesa auxiliar', 'Mesa pequeña de madera, perfecta para salón o dormitorio.', 28.00, 'Hogar', 'Usado', 'disponible', 2),
('Pack de libros', 'Lote de novelas y libros técnicos en buen estado.', 20.00, 'Otros', 'Como nuevo', 'disponible', 1),
('Lámpara verde', 'Lámpara decorativa con diseño sencillo y moderno.', 18.00, 'Hogar', 'Nuevo', 'vendido', 2);

INSERT INTO Imagen (url, id_producto) VALUES
('assets/img/productos/prod1.svg', 1),
('assets/img/productos/prod2.svg', 2),
('assets/img/productos/prod3.svg', 3),
('assets/img/productos/prod4.svg', 4),
('assets/img/productos/prod5.svg', 5),
('assets/img/productos/prod6.svg', 6),
('assets/img/productos/prod1.svg', 1),
('assets/img/productos/prod7.svg', 1),
('assets/img/productos/prod8.svg', 3);

INSERT INTO Mensaje (contenido, id_emisor, id_receptor, id_producto) VALUES
('Hola, ¿sigue disponible la bicicleta?', 2, 1, 1),
('Sí, puedes verla esta tarde.', 1, 2, 1);

INSERT INTO Compra (estado, metodo_pago, id_comprador, id_producto) VALUES
('pagado', 'Bizum', 1, 6);

INSERT INTO Valoracion (puntuacion, comentario, id_emisor, id_receptor, id_compra) VALUES
(5, 'Todo perfecto, vendedor muy recomendable y comunicación rápida.', 1, 2, 1);

