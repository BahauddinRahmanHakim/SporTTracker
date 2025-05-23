<?php
// filepath: f:\xampp\htdocs\api-uas-sporttracker\public\api\delete_workout.php

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

// Database connection (if not already connected)
if (!isset($pdo)) {
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
}

// Get workout ID from POST data
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Workout ID is required']);
    exit;
}

// Only allow deleting workout that belongs to this user
try {
    $stmt = $pdo->prepare("DELETE FROM workouts WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Workout deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Workout not found or not yours']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete workout: ' . $e->getMessage()]);
}