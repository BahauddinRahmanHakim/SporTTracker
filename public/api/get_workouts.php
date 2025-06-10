<?php
// filepath: f:\xampp\htdocs\api-uas-sporttracker\public\api\get_workouts.php

require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// API Key validation FIRST!
$validApiKey = getenv('API_KEY');
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
if ($apiKey === $validApiKey) {
    // API Key is valid, allow access (set $authenticated = true if needed)
    $authenticated = true;
    // Optionally set $userId = null or a default
} else {
    // Continue to JWT/Basic Auth checks
    $authenticated = false;
    $userId = null;

    // JWT Bearer Token
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
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

    // Basic Auth
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
}

// Final check
if (!$authenticated) {
    header('WWW-Authenticate: Basic realm="My API"');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

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

// Fetch workouts
try {
    $stmt = $pdo->prepare("SELECT * FROM workouts WHERE user_id = :user_id ORDER BY date DESC, time DESC");
    $stmt->execute([':user_id' => $userId]);
    $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'workouts' => $workouts]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch workouts: ' . $e->getMessage()]);
}