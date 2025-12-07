<?php
// api/contactos/index.php
require_once __DIR__ . "/../utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(["error" => "Método no permitido"], 405);
}

$user = require_auth();

global $pdo;

$stmt = $pdo->prepare("SELECT id, nombre, apellido, telefono, email, direccion, notas, fecha_creacion
                       FROM contactos
                       WHERE usuario_id = ?
                       ORDER BY fecha_creacion DESC");
$stmt->execute([$user['id']]);
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

json_response($contactos);
