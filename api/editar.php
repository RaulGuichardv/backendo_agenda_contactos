<?php
// api/editar.php
require_once __DIR__ . "/utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["error" => "Método no permitido"], 405);
}

$user = require_auth();
$input = json_decode(file_get_contents("php://input"), true);

$new_username = $input['username'] ?? null;
$new_password = $input['password'] ?? null;

if (!$new_username && !$new_password) {
    json_response(["error" => "Nada que actualizar"], 400);
}

global $pdo;

// Comprobar que el nuevo username no exista en otro usuario
if ($new_username) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_de_usuario = ? AND id <> ?");
    $stmt->execute([$new_username, $user['id']]);
    if ($stmt->fetch()) {
        json_response(["error" => "El nombre de usuario ya está en uso"], 409);
    }
}

$fields = [];
$params = [];

if ($new_username) {
    $fields[] = "nombre_de_usuario = ?";
    $params[] = $new_username;
}
if ($new_password) {
    $fields[] = "password = ?";
    $params[] = password_hash($new_password, PASSWORD_DEFAULT);
}

$params[] = $user['id'];

$sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

json_response(["message" => "Perfil actualizado correctamente"]);
