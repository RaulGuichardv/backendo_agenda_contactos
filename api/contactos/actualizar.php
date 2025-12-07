<?php
// api/contactos/actualizar.php
require_once __DIR__ . "/../utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["error" => "Método no permitido"], 405);
}

$user = require_auth();

$id = $_GET['id'] ?? null;
if (!$id) {
    json_response(["error" => "Falta parámetro id"], 400);
}

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

// Verificar que el contacto sea del usuario
$check = $pdo->prepare("SELECT id FROM contactos WHERE id = ? AND usuario_id = ?");
$check->execute([$id, $user['id']]);
if (!$check->fetch()) {
    json_response(["error" => "Contacto no encontrado"], 404);
}

$upd = $pdo->prepare("UPDATE contactos
                      SET nombre = ?, apellido = ?, telefono = ?, email = ?, direccion = ?, notas = ?
                      WHERE id = ? AND usuario_id = ?");
$upd->execute([$nombre, $apellido, $telefono, $email, $direccion, $notas, $id, $user['id']]);

json_response(["message" => "Contacto actualizado correctamente"]);
