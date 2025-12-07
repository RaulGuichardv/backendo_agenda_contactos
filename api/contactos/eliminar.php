<?php
// api/contactos/eliminar.php
require_once __DIR__ . "/../utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["error" => "Método no permitido"], 405);
}

$user = require_auth();

$id = $_GET['id'] ?? null;
if (!$id) {
    json_response(["error" => "Falta parámetro id"], 400);
}

global $pdo;

// Verificar que sea del usuario
$check = $pdo->prepare("SELECT id FROM contactos WHERE id = ? AND usuario_id = ?");
$check->execute([$id, $user['id']]);
if (!$check->fetch()) {
    json_response(["error" => "Contacto no encontrado"], 404);
}

$del = $pdo->prepare("DELETE FROM contactos WHERE id = ? AND usuario_id = ?");
$del->execute([$id, $user['id']]);

json_response(["message" => "Contacto eliminado correctamente"]);
