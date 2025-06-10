<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$authenticated = false;
$userId = null;

// 1. JWT Bearer Token
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $jwt = $matches[1];
    try {
        $jwtSecret = 'oOOYK3mKwNBnW38EGbKmWBCqxc1S7hbvGDNUuN11HmLHRfLn7gN8S9tLOe60QlLh';
        $decoded = JWT::decode($jwt, new Key($jwtSecret, 'HS256'));
        $authenticated = true;
        $userId = $decoded->sub;
    } catch (Exception $e) {
        // Invalid JWT, will try Basic Auth next
    }
}

// 2. Basic Auth
if (!$authenticated && isset($_SERVER['PHP_AUTH_USER'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    // DB connection for user check
    $host = '127.0.0.1';
    $db = 'api_uas_sporttracker';
    $user = 'root';
    $pass = '';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userRow && password_verify($password, $userRow['password'])) {
            $authenticated = true;
            $userId = $userRow['id'];
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
}

// 3. Unauthorized
if (!$authenticated) {
    header('WWW-Authenticate: Basic realm="My API"');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$host = '127.0.0.1';
$db = 'api_uas_sporttracker';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? '';
$description = $data['description'] ?? '';
$target_value = $data['target_value'] ?? null;
$unit = $data['unit'] ?? null;
$start_value = null;

// Get start_value for weight/heart_rate
if ($type === 'weight' || $type === 'heart_rate') {
    $metricType = $type === 'weight' ? 'weight' : 'heart_rate';
    $stmt = $pdo->prepare("SELECT value FROM health_metrics WHERE user_id = :user_id AND type = :type ORDER BY date DESC, time DESC LIMIT 1");
    $stmt->execute([':user_id' => $userId, ':type' => $metricType]);
    $start_value = $stmt->fetchColumn();
}

// Limit to 5 goals
$stmt = $pdo->prepare("SELECT COUNT(*) FROM current_goals WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
if ($stmt->fetchColumn() >= 5) {
    echo json_encode(['error' => 'Maximum 5 goals allowed']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO current_goals (user_id, type, description, target_value, start_value, unit, created_at) VALUES (:user_id, :type, :description, :target_value, :start_value, :unit, NOW())");
$stmt->execute([
    ':user_id' => $userId,
    ':type' => $type,
    ':description' => $description,
    ':target_value' => $target_value,
    ':start_value' => $start_value,
    ':unit' => $unit
]);
echo json_encode(['success' => true, 'message' => 'Goal added']);