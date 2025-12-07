<?php
// api/config.php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Responder preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$DB_HOST = "localhost";
$DB_NAME = "agenda_jwt";
$DB_USER = "root";
$DB_PASS = "Milanesa00?"; // Cambia según tu hosting

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit;
}

// Clave secreta para firmar tokens (cámbiala en producción)
$JWT_SECRET = "MI_SECRETO_SUPER_SEGURO_123";
$JWT_EXP_SECONDS = 3600; // 1 hora
