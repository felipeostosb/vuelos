<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($email, $password) {
        $query = "SELECT id, nombre, email, rol, password FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                // No devolver la contraseña en la sesión
                unset($row['password']);
                return $row;
            }
        }
        return false;
    }

    public function registrar($nombre, $email, $password) {
        $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')";
        $stmt = $this->conn->prepare($query);
        
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hash);
        
        try {
            if($stmt->execute()) {
                return true;
            }
        } catch(PDOException $e) {
            // Error, possibly duplicate email
            return false;
        }
        return false;
    }
}
?>
