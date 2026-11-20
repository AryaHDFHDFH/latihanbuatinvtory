<?php

class Auth
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register(string $namaLengkap, string $username, string $password): array
    {
        $namaLengkap = trim($namaLengkap);
        $username = strtolower(trim($username));

        if ($namaLengkap === "" || $username === "" || strlen($password) < 6) {
            return ["ok" => false, "message" => "Lengkapi data dan gunakan password minimal 6 karakter."];
        }

        $cek = $this->db->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $cek->execute(["username" => $username]);
        if ($cek->fetch()) {
            return ["ok" => false, "message" => "Username sudah terdaftar."];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (nama_lengkap, username, password, role) VALUES (:nama, :username, :password, 'user')"
        );
        $stmt->execute([
            "nama" => $namaLengkap,
            "username" => $username,
            "password" => $hash,
        ]);

        return ["ok" => true, "message" => "Registrasi berhasil. Silakan login."];
    }

    public function login(string $username, string $password): array
    {
        $username = strtolower(trim($username));
        $stmt = $this->db->prepare("SELECT id, nama_lengkap, username, password, role FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user["password"])) {
            return ["ok" => false, "message" => "Username atau password salah."];
        }

        session_regenerate_id(true);
        $_SESSION["user"] = [
            "id" => (int) $user["id"],
            "nama_lengkap" => $user["nama_lengkap"],
            "username" => $user["username"],
            "role" => $user["role"],
        ];

        return ["ok" => true, "message" => "Login berhasil."];
    }

    public function checkLogin(): void
    {
        if (!isset($_SESSION["user"])) {
            header("Location: index.php");
            exit;
        }
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION["user"]) && $_SESSION["user"]["role"] === "admin";
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        header("Location: index.php");
        exit;
    }
}