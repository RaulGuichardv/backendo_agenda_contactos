<?php
// api/perfil.php
require_once __DIR__ . "/utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(["error" => "Método no permitido"], 405);
}

$user = require_auth(); // ['id', 'nombre_de_usuario']

json_response([
    "id" => $user['id'],
    "nombre_de_usuario" => $user['nombre_de_usuario']
]);
