<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$localConfig = [];
$localConfigPath = __DIR__ . "/config.local.php";
if (is_file($localConfigPath)) {
    $localConfig = require $localConfigPath;
}

$host     = getenv("DB_HOST") ?: ($localConfig["DB_HOST"] ?? "localhost");
$db       = getenv("DB_NAME") ?: ($localConfig["DB_NAME"] ?? "estacionamiento_dev");
$user     = getenv("DB_USER") ?: ($localConfig["DB_USER"] ?? "root");
$password = getenv("DB_PASSWORD") ?: ($localConfig["DB_PASSWORD"] ?? "");

$conexion = mysqli_connect($host, $user, $password, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

if (!defined('ESTACIONAMIENTO_HELPERS_LOADED')) {
define('ESTACIONAMIENTO_HELPERS_LOADED', true);

function db_query(string $sql, string $types = "", ...$params)
{
    global $conexion;

    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        throw new RuntimeException("Error preparando consulta: " . mysqli_error($conexion));
    }

    if ($types !== "") {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new RuntimeException("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
    }

    return $stmt;
}

function db_result(string $sql, string $types = "", ...$params)
{
    $stmt = db_query($sql, $types, ...$params);
    return mysqli_stmt_get_result($stmt);
}

function db_one(string $sql, string $types = "", ...$params): ?array
{
    $result = db_result($sql, $types, ...$params);
    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function db_all(string $sql, string $types = "", ...$params): array
{
    $result = db_result($sql, $types, ...$params);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function clean_text(string $value, int $maxLength = 100): string
{
    $value = trim($value);
    $value = preg_replace('/\s+/', ' ', $value);
    return substr($value, 0, $maxLength);
}

function clean_plate(string $value): string
{
    $value = strtoupper(clean_text($value, 20));
    return preg_replace('/[^A-Z0-9-]/', '', $value);
}

function clean_role($value): int
{
    $role = (int) $value;
    $exists = db_one("SELECT rol_id FROM roles WHERE rol_id = ? AND activo = 1", "i", $role);
    return $exists ? $role : 4;
}

function clean_tamano(string $value): string
{
    return $value === 'Grande' ? 'Grande' : 'Chico';
}

function clean_cajon_id($value): int
{
    $id = (int) $value;
    return ($id >= 1 && $id <= 24) ? $id : 0;
}

function clean_datetime_local(string $value): string
{
    $date = DateTime::createFromFormat('Y-m-d\TH:i', $value);
    return $date ? $date->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
}

function flash(string $message, string $type = "info"): void
{
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function csrf_token(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(?string $token = null): void
{
    $token = $token ?? ($_POST["csrf_token"] ?? "");
    if (!hash_equals($_SESSION["csrf_token"] ?? "", (string) $token)) {
        http_response_code(419);
        exit("Sesion invalida. Regresa e intenta de nuevo.");
    }
}

function user_is_within_schedule(?string $inicio, ?string $fin): bool
{
    if (!$inicio || !$fin) {
        return true;
    }

    $now = date("H:i:s");
    if ($inicio <= $fin) {
        return $now >= $inicio && $now <= $fin;
    }

    return $now >= $inicio || $now <= $fin;
}

function set_login_session(array $user): void
{
    $_SESSION["usuario_id"] = (int) $user["id"];
    $_SESSION["usuario"] = $user["Usuario"];
    $_SESSION["rol"] = (int) $user["rol_id"];
    $_SESSION["rol_nombre"] = $user["rol_nombre"] ?? "Usuario";
    $_SESSION["cliente_id"] = isset($user["cliente_id"]) ? (int) $user["cliente_id"] : null;
    $_SESSION["cliente_nombre"] = $user["cliente_nombre"] ?? "General";
    $_SESSION["admin_general"] = (int) ($user["es_admin_general"] ?? 0);
    $_SESSION["horario_inicio"] = $user["horario_inicio"] ?? null;
    $_SESSION["horario_fin"] = $user["horario_fin"] ?? null;
    $_SESSION["permissions"] = load_permissions((int) $user["rol_id"]);
}

function load_permissions(int $rolId): array
{
    $rows = db_all(
        "SELECT p.clave
           FROM rol_permisos rp
           INNER JOIN permisos p ON p.id = rp.permiso_id
          WHERE rp.rol_id = ?",
        "i",
        $rolId
    );

    return array_column($rows, "clave");
}

function current_user_id(): ?int
{
    return isset($_SESSION["usuario_id"]) ? (int) $_SESSION["usuario_id"] : null;
}

function current_client_id(): ?int
{
    return isset($_SESSION["cliente_id"]) ? (int) $_SESSION["cliente_id"] : null;
}

function active_client_id(): int
{
    if (is_general_admin() && isset($_GET["cliente_id"])) {
        $clienteId = (int) $_GET["cliente_id"];
        $cliente = db_one("SELECT id, nombre FROM clientes WHERE id = ? AND activo = 1", "i", $clienteId);
        if ($cliente) {
            $_SESSION["cliente_id"] = (int) $cliente["id"];
            $_SESSION["cliente_nombre"] = $cliente["nombre"];
        }
    }

    return current_client_id() ?: 1;
}

function is_general_admin(): bool
{
    return !empty($_SESSION["admin_general"]);
}

function can(string $permission): bool
{
    if (is_general_admin()) {
        return true;
    }

    return in_array($permission, $_SESSION["permissions"] ?? [], true);
}

function require_permission(string $permission): void
{
    require_login();

    if (!user_is_within_schedule($_SESSION["horario_inicio"] ?? null, $_SESSION["horario_fin"] ?? null)) {
        audit_log("access.denied", "sesion", current_user_id(), "Acceso fuera de horario");
        session_unset();
        session_destroy();
        header("Location: login.php?error=horario");
        exit();
    }

    if (!can($permission)) {
        http_response_code(403);
        exit("No tienes permiso para acceder a esta seccion.");
    }
}

function tenant_clause(string $column = "cliente_id", string $prefix = " WHERE "): string
{
    if (is_general_admin()) {
        return "";
    }

    return $prefix . $column . " = ?";
}

function tenant_types(string $types = ""): string
{
    return is_general_admin() ? $types : $types . "i";
}

function tenant_params(array $params = []): array
{
    if (!is_general_admin()) {
        $params[] = current_client_id() ?? 0;
    }

    return $params;
}

function tenant_query(string $sql, string $types = "", array $params = [])
{
    return db_query($sql, tenant_types($types), ...tenant_params($params));
}

function tenant_result(string $sql, string $types = "", array $params = [])
{
    $stmt = tenant_query($sql, $types, $params);
    return mysqli_stmt_get_result($stmt);
}

function tenant_one(string $sql, string $types = "", array $params = []): ?array
{
    $result = tenant_result($sql, $types, $params);
    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function tenant_all(string $sql, string $types = "", array $params = []): array
{
    $result = tenant_result($sql, $types, $params);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function audit_log(string $accion, string $entidad = "", $entidadId = null, string $descripcion = ""): void
{
    try {
        db_query(
            "INSERT INTO bitacora (cliente_id, usuario_id, accion, entidad, entidad_id, descripcion, ip, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            "iississs",
            current_client_id(),
            current_user_id(),
            $accion,
            $entidad,
            $entidadId !== null ? (int) $entidadId : null,
            clean_text($descripcion, 255),
            $_SERVER["REMOTE_ADDR"] ?? "",
            substr($_SERVER["HTTP_USER_AGENT"] ?? "", 0, 255)
        );
    } catch (Throwable $e) {
        error_log("No se pudo escribir bitacora: " . $e->getMessage());
    }
}

function authenticate_user(string $usuario, string $clave): ?array
{
    $user = db_one(
        "SELECT u.*, r.nombre AS rol_nombre, r.es_admin_general, c.nombre AS cliente_nombre
           FROM usuarios u
           INNER JOIN roles r ON r.rol_id = u.rol_id
           LEFT JOIN clientes c ON c.id = u.cliente_id
          WHERE u.Usuario = ? AND u.activo = 1 AND r.activo = 1",
        "s",
        $usuario
    );

    if (!$user) {
        return null;
    }

    if (!user_is_within_schedule($user["horario_inicio"] ?? null, $user["horario_fin"] ?? null)) {
        return null;
    }

    $storedPassword = (string) $user['password'];
    if (password_verify($clave, $storedPassword)) {
        return $user;
    }

    // Compatibilidad temporal con cuentas antiguas guardadas en texto plano.
    if (hash_equals($storedPassword, $clave)) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        db_query("UPDATE usuarios SET password = ? WHERE id = ?", "si", $hash, (int) $user['id']);
        $user['password'] = $hash;
        return $user;
    }

    return null;
}

function require_login(): void
{
    if (!isset($_SESSION['usuario'], $_SESSION['rol'], $_SESSION["usuario_id"])) {
        header("Location: login.php");
        exit();
    }
}
}
?>
