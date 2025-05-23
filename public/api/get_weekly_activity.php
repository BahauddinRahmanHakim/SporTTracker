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

// Get start and end of this week (Monday-Sunday)
$today = new DateTime();
$startOfWeek = clone $today;
$startOfWeek->modify('monday this week');
$endOfWeek = clone $startOfWeek;
$endOfWeek->modify('+6 days');

$labels = [];
$steps = [];
$calories = [];
$active_minutes = [];

for ($i = 0; $i < 7; $i++) {
    $date = clone $startOfWeek;
    $date->modify("+$i days");
    $labels[] = $date->format('D');
    $dateStr = $date->format('Y-m-d');

    $stmt = $pdo->prepare("SELECT SUM(distance) as total_distance, SUM(calories) as calories, SUM(duration) as active_minutes FROM workouts WHERE user_id = :user_id AND date = :date");
    $stmt->execute([':user_id' => $userId, ':date' => $dateStr]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Estimate steps from distance
    $steps[] = $row['total_distance'] ? intval($row['total_distance'] * 1300) : 0;
    $calories[] = $row['calories'] ? intval($row['calories']) : 0;
    $active_minutes[] = $row['active_minutes'] ? intval($row['active_minutes']) : 0;
}

echo json_encode([
    'success' => true,
    'labels' => $labels,
    'steps' => $steps,
    'calories' => $calories,
    'active_minutes' => $active_minutes
]);