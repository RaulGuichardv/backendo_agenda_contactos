<?php
// api/utils.php
require_once __DIR__ . "/config.php";

function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function get_bearer_token() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }

    if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $data .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

// Crear token tipo JWT (header.payload.signature)
function create_token($user_id, $username) {
    global $JWT_SECRET, $JWT_EXP_SECONDS;

    $header = [
        "alg" => "HS256",
        "typ" => "JWT"
    ];
    $payload = [
        "sub" => $user_id,
        "username" => $username,
        "iat" => time(),
        "exp" => time() + $JWT_EXP_SECONDS
    ];

    $header_encoded  = base64url_encode(json_encode($header));
    $payload_encoded = base64url_encode(json_encode($payload));

    $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $JWT_SECRET, true);
    $signature_encoded = base64url_encode($signature);

    return "$header_encoded.$payload_encoded.$signature_encoded";
}

// Validar token y devolver payload o null
function validate_token($token) {
    global $JWT_SECRET;

    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    list($header_encoded, $payload_encoded, $signature_encoded) = $parts;

    $signature = base64url_decode($signature_encoded);
    $expected  = hash_hmac('sha256', "$header_encoded.$payload_encoded", $JWT_SECRET, true);

    if (!hash_equals($expected, $signature)) return null;

    $payload = json_decode(base64url_decode($payload_encoded), true);
    if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
        return null; // expirado
    }
    return $payload;
}

// Devuelve usuario autenticado a partir del token
function require_auth() {
    global $pdo;

    $token = get_bearer_token();
    if (!$token) {
        json_response(["error" => "Token no enviado"], 401);
    }

    $payload = validate_token($token);
    if (!$payload) {
        json_response(["error" => "Token inválido o expirado"], 401);
    }

    // Opcional: verificar que el usuario exista
    $stmt = $pdo->prepare("SELECT id, nombre_de_usuario FROM usuarios WHERE id = ?");
    $stmt->execute([$payload['sub']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(["error" => "Usuario no encontrado"], 401);
    }

    return $user; // ['id' => ..., 'nombre_de_usuario' => ...]
}
