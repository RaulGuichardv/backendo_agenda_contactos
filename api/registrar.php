<?php
// api/registrar.php
require_once __DIR__ . "/utils.php";

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

// Verificar único
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_de_usuario = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    json_response(["error" => "El nombre de usuario ya existe"], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$ins = $pdo->prepare("INSERT INTO usuarios (nombre_de_usuario, password) VALUES (?, ?)");
$ins->execute([$username, $hash]);

json_response(["message" => "Usuario registrado correctamente"]);
