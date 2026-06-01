<?php
/* =========================================================
MODELO DE USUARIO (INACTIVO)
Este archivo se encarga de hablar con la tabla 'usuarios' 
en la base de datos.
=========================================================

require_once __DIR__ . '/../config/database.php';

class UserModel
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    // 1. REGISTRAR UN NUEVO USUARIO
    public function insert(string $nombre, string $email, string $password): bool
    {
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        // ¡Excelente práctica de seguridad! Encriptar la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('sss', $nombre, $email, $hash);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    // 2. VERIFICAR SI EL CORREO YA EXISTE (Útil al registrarse)
    public function emailExiste(string $email): bool
    {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        $existe = $stmt->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    // =========================================================
    // NUEVA MEJORA: FUNCIÓN PARA EL LOGIN (INICIAR SESIÓN)
    // Como VuelaIA tiene un botón de Iniciar Sesión, 
    // necesitaremos validar que el usuario y la clave coincidan.
    // =========================================================
    public function verificarLogin(string $email, string $password): array|false
    {
        $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        
        if ($fila = $resultado->fetch_assoc()) {
            // Comparamos la contraseña que escribió el usuario con la encriptada
            if (password_verify($password, $fila['password'])) {
                // Borramos la clave del arreglo por seguridad antes de devolver la data
                unset($fila['password']);
                return $fila; // Devuelve los datos (id, nombre) si el login es exitoso
            }
        }
        
        $stmt->close();
        return false; // Devuelve falso si falla el correo o la contraseña
    }

    public function __destruct()
    {
        // Pequeña mejora: verificamos que la conexión exista antes de cerrarla
        if (isset($this->conn)) {
            $this->conn->close();
        }
    }
} 
*/
?>