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
        $jwtSecret = 'JWT_SECRET';
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

// DB connection
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

// Get all metrics for user, grouped by type (current and previous)
$stmt = $pdo->prepare("
    SELECT t1.*
    FROM health_metrics t1
    INNER JOIN (
        SELECT type, MAX(id) as max_id
        FROM health_metrics
        WHERE user_id = :user_id
        GROUP BY type
    ) t2 ON t1.type = t2.type AND t1.id = t2.max_id
    WHERE t1.user_id = :user_id
    ORDER BY t1.type
");
$stmt->execute([':user_id' => $userId]);
$currentMetrics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get previous for each type
$previousMetrics = [];
foreach ($currentMetrics as $metric) {
    $stmt = $pdo->prepare("
        SELECT * FROM health_metrics
        WHERE user_id = :user_id
          AND type = :type
          AND (
            date < :date
            OR (date = :date AND time < :time)
            OR (date = :date AND time = :time AND id < :id)
          )
        ORDER BY date DESC, time DESC, id DESC
        LIMIT 1
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':type' => $metric['type'],
        ':date' => $metric['date'],
        ':time' => $metric['time'],
        ':id' => $metric['id'],
    ]);
    $previous = $stmt->fetch(PDO::FETCH_ASSOC);
    $previousMetrics[$metric['type']] = $previous;
}

echo json_encode([
    'success' => true,
    'current' => $currentMetrics,
    'previous' => $previousMetrics
]);