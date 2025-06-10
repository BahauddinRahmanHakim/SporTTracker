<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Add this line

use Firebase\JWT\JWT;

$host = '127.0.0.1';
$db = 'api_uas_sporttracker';
$user = 'root';
$pass = '';

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? null;
$password = $data['password'] ?? null;

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing username or password']);
    exit;
}

// Fetch user by username
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid username or password']);
    exit;
}

// Generate JWT token
$key = 'oOOYK3mKwNBnW38EGbKmWBCqxc1S7hbvGDNUuN11HmLHRfLn7gN8S9tLOe60QlLh';
$payload = [
    'sub' => $user['id'],
    'username' => $user['username'],
    'iat' => time(),
    'exp' => time() + (60 * 60 * 24) // 1 day expiration
];
$token = JWT::encode($payload, $key, 'HS256');

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'name' => $user['name'],
        'email' => $user['email']
    ]
]);