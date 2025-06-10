<?php
// filepath: f:\xampp\htdocs\api-uas-sporttracker\public\api\add_workout.php

require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$authenticated = false;
$userId = null;

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $jwt = $matches[1];
    try {
        $jwtSecret = 'oOOYK3mKwNBnW38EGbKmWBCqxc1S7hbvGDNUuN11HmLHRfLn7gN8S9tLOe60QlLh'; // <-- Hardcoded JWT secret
        $decoded = JWT::decode($jwt, new Key($jwtSecret, 'HS256'));
        $authenticated = true;
        $userId = $decoded->sub;
    } catch (Exception $e) {
        // Invalid JWT
    }
}

if (!$authenticated && isset($_SERVER['PHP_AUTH_USER'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
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

if (!$authenticated) {
    header('WWW-Authenticate: Basic realm="My API"');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$title = $data['title'] ?? '';
$type = $data['type'] ?? '';
$date = $data['date'] ?? date('Y-m-d');
$time = $data['time'] ?? date('H:i:s');
$duration = $data['duration'] ?? 0;
$distance = $data['distance'] ?? null;
$calories = $data['calories'] ?? null;
$notes = $data['notes'] ?? null;

// Database connection
$host = '127.0.0.1';
$db = 'api_uas_sporttracker';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Insert into database
try {
    $stmt = $pdo->prepare("INSERT INTO workouts (user_id, title, type, date, time, duration, distance, calories, notes, created_at, updated_at) 
                           VALUES (:user_id, :title, :type, :date, :time, :duration, :distance, :calories, :notes, NOW(), NOW())");
    $stmt->execute([
        ':user_id' => $userId,
        ':title' => $title,
        ':type' => $type,
        ':date' => $date,
        ':time' => $time,
        ':duration' => $duration,
        ':distance' => $distance,
        ':calories' => $calories,
        ':notes' => $notes
    ]);

    echo json_encode(['success' => true, 'message' => 'Workout added successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add workout: ' . $e->getMessage()]);
}