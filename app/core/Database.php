<?php
class Database
{
    private static $conexion = null;

    public static function getConnection()
    {
        if (self::$conexion == null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            self::$conexion = new PDO($dsn, DB_USER, DB_PASS);
            self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$conexion;
    }
}
