<?php
// This endpoint should be called periodically or after relevant data changes
// It checks if a goal is achieved, moves it to completed_goals, and deletes from current_goals

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

// Fetch all current goals
$stmt = $pdo->prepare("SELECT * FROM current_goals WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For each goal, calculate progress and move to completed if 100%
foreach ($goals as $goal) {
    $progress = 0;
    $currentValue = 0;

    if ($goal['type'] === 'weight') {
        $stmt2 = $pdo->prepare("SELECT value FROM health_metrics WHERE user_id = :user_id AND type = 'weight' ORDER BY date DESC, time DESC LIMIT 1");
        $stmt2->execute([':user_id' => $userId]);
        $currentValue = $stmt2->fetchColumn();
        $start = $goal['start_value'] ?? $currentValue;
        $target = $goal['target_value'];
        if ($start && $target && $currentValue) {
            // If losing weight
            if ($start > $target) {
                $progress = min(100, round(100 * ($start - $currentValue) / ($start - $target)));
            } else { // If gaining weight
                $progress = min(100, round(100 * ($currentValue - $start) / ($target - $start)));
            }
        }
    } elseif ($goal['type'] === 'heart_rate') {
        $stmt2 = $pdo->prepare("SELECT value FROM health_metrics WHERE user_id = :user_id AND type = 'heart_rate' ORDER BY date DESC, time DESC LIMIT 1");
        $stmt2->execute([':user_id' => $userId]);
        $currentValue = $stmt2->fetchColumn();
        $target = $goal['target_value'];
        if ($currentValue && $target) {
            // Lower is better
            $progress = min(100, round(100 * ($goal['start_value'] - $currentValue) / ($goal['start_value'] - $target)));
        }
    } elseif ($goal['type'] === 'workout') {
        // Example: workouts this month
        $monthStart = date('Y-m-01');
        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM workouts WHERE user_id = :user_id AND date >= :monthStart");
        $stmt2->execute([':user_id' => $userId, ':monthStart' => $monthStart]);
        $currentValue = $stmt2->fetchColumn();
        $target = $goal['target_value'];
        if ($target) {
            $progress = min(100, round(100 * $currentValue / $target));
        }
    } else { // Custom
        $progress = $goal['progress'] ?? 0;
        $currentValue = $goal['current_value'] ?? null;
    }

    // Update progress
    $stmt2 = $pdo->prepare("UPDATE current_goals SET current_value = :current_value, progress = :progress WHERE id = :id");
    $stmt2->execute([
        ':current_value' => $currentValue,
        ':progress' => $progress,
        ':id' => $goal['id']
    ]);

    // If achieved, move to completed
    if ($progress >= 100) {
        $stmt2 = $pdo->prepare("INSERT INTO completed_goals (user_id, type, description, target_value, unit, final_value, completed_at) VALUES (:user_id, :type, :description, :target_value, :unit, :final_value, NOW())");
        $stmt2->execute([
            ':user_id' => $userId,
            ':type' => $goal['type'],
            ':description' => $goal['description'],
            ':target_value' => $goal['target_value'],
            ':unit' => $goal['unit'],
            ':final_value' => $currentValue
        ]);
        $stmt2 = $pdo->prepare("DELETE FROM current_goals WHERE id = :id");
        $stmt2->execute([':id' => $goal['id']]);
    }
}

echo json_encode(['success' => true]);