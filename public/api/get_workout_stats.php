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

// Get all workouts for this user (add user_id if you have it in workouts table)
$stmt = $pdo->prepare("SELECT * FROM workouts ORDER BY date ASC, time ASC");
$stmt->execute();
$workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare stats
$volume = [];
$distribution = [];
$consistency = [];

foreach ($workouts as $w) {
    // Volume per week/month (example: by week)
    $week = date('o-\WW', strtotime($w['date']));
    if (!isset($volume[$week])) $volume[$week] = ['duration'=>0, 'distance'=>0, 'calories'=>0];
    $volume[$week]['duration'] += (int)$w['duration'];
    $volume[$week]['distance'] += (float)$w['distance'];
    $volume[$week]['calories'] += (int)$w['calories'];

    // Distribution
    $type = $w['type'];
    if (!isset($distribution[$type])) $distribution[$type] = 0;
    $distribution[$type]++;

    // Consistency (calendar heatmap)
    $consistency[$w['date']] = ($consistency[$w['date']] ?? 0) + 1;
}

echo json_encode([
    'success' => true,
    'volume' => $volume,
    'distribution' => $distribution,
    'consistency' => $consistency
]);