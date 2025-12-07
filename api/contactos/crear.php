<?php
// api/contactos/crear.php
require_once __DIR__ . "/../utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["error" => "Método no permitido"], 405);
}

$user = require_auth();
$input = json_decode(file_get_contents("php://input"), true);

$nombre = trim($input['nombre'] ?? '');
$apellido = $input['apellido'] ?? null;
$telefono = trim($input['telefono'] ?? '');
$email = $input['email'] ?? null;
$direccion = $input['direccion'] ?? null;
$notas = $input['notas'] ?? null;

if ($nombre === '' || $telefono === '') {
    json_response(["error" => "nombre y telefono son obligatorios"], 400);
}

global $pdo;
$stmt = $pdo->prepare("INSERT INTO contactos (usuario_id, nombre, apellido, telefono, email, direccion, notas)
                       VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $user['id'], $nombre, $apellido, $telefono, $email, $direccion, $notas
]);

json_response(["message" => "Contacto creado correctamente"]);
