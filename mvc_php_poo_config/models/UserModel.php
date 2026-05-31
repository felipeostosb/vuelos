<?php

require_once __DIR__ . '/../config/database.php';

class UserModel
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    public function insert(string $nombre, string $email, string $password): bool
    {
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('sss', $nombre, $email, $hash);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

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

    public function __destruct()
    {
        $this->conn->close();
    }
}
