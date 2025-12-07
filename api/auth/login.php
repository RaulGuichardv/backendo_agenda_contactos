<?php
// api/auth/login.php
require_once __DIR__ . "/../utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["error" => "Método no permitido"], 405);
}

$input = json_decode(file_get_contents("php://input"), true);
$username = $input['username'] ?? null;
$password = $input['password'] ?? null;

if (!$username || !$password) {
    json_response(["error" => "username y password son obligatorios"], 400);
}

global $pdo;
$stmt = $pdo->prepare("SELECT id, nombre_de_usuario, password FROM usuarios WHERE nombre_de_usuario = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    json_response(["error" => "Credenciales inválidas"], 401);
}

$token = create_token($user['id'], $user['nombre_de_usuario']);

// Guardar token en BD (opcional)
$upd = $pdo->prepare("UPDATE usuarios SET token = ? WHERE id = ?");
$upd->execute([$token, $user['id']]);

json_response([
    "message" => "Login exitoso",
    "token" => $token,
    "user" => [
        "id" => $user['id'],
        "nombre_de_usuario" => $user['nombre_de_usuario']
    ]
]);
