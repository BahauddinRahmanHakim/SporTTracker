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

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? null;
$value = $data['value'] ?? null;
$previous_value = $data['previous_value'] ?? null;
$systolic = $data['systolic'] ?? null;
$diastolic = $data['diastolic'] ?? null;
$date = $data['date'] ?? date('Y-m-d');
$time = $data['time'] ?? date('H:i:s');
$notes = $data['notes'] ?? null;

// Fetch previous metric for this type
$stmt = $pdo->prepare("SELECT * FROM health_metrics WHERE user_id = :user_id AND type = :type ORDER BY date DESC, time DESC LIMIT 1");
$stmt->execute([':user_id' => $userId, ':type' => $type]);
$prev = $stmt->fetch(PDO::FETCH_ASSOC);

$change = null;
$status = '';
$status_color = '';
if ($type === 'heart_rate') {
    $change = $prev ? $value - $prev['value'] : 0;
    if ($value < 60) {
        $status = 'Deteriorate';
        $status_color = 'danger';
    } elseif ($value <= 100) {
        $status = 'Improving';
        $status_color = 'success';
    } else {
        $status = 'Needs Attention';
        $status_color = 'warning';
    }
} elseif ($type === 'weight') {
    $change = $prev ? $value - $prev['value'] : 0;
    if ($change < 0) {
        $status = 'Deteriorate';
        $status_color = 'danger';
    } else {
        $status = 'Improving';
        $status_color = 'success';
    }
} elseif ($type === 'sleep') {
    $change = $prev ? $value - $prev['value'] : 0;
    if ($value >= 7 && $value <= 9) {
        $status = 'Improving';
        $status_color = 'success';
    } elseif ($value < 7) {
        $status = 'Deteriorate';
        $status_color = 'danger';
    } else {
        $status = 'Needs Attention';
        $status_color = 'warning';
    }
} elseif ($type === 'hydration') {
    $change = $prev ? $value - $prev['value'] : 0;
    if ($value >= 2.5 && $value <= 3.5) {
        $status = 'Improving';
        $status_color = 'success';
    } elseif ($value >= 1.5 && $value < 2.5) {
        $status = 'Needs Attention';
        $status_color = 'warning';
    } else {
        $status = 'Deteriorate';
        $status_color = 'danger';
    }
} elseif ($type === 'blood_pressure') {
    $change = $prev ? (($systolic - $prev['systolic']) . '/' . ($diastolic - $prev['diastolic'])) : null;
    if ($systolic >= 90 && $systolic <= 120 && $diastolic >= 60 && $diastolic <= 80) {
        $status = 'Improving';
        $status_color = 'success';
    } elseif (($systolic >= 121 && $systolic <= 139) || ($diastolic >= 81 && $diastolic <= 89)) {
        $status = 'Needs Attention';
        $status_color = 'warning';
    } else {
        $status = 'Deteriorate';
        $status_color = 'danger';
    }
}

// Insert new metric
$stmt = $pdo->prepare("INSERT INTO health_metrics (user_id, type, value, previous_value, `change`, status, status_color, systolic, diastolic, date, time, notes, last_updated) VALUES (:user_id, :type, :value, :previous_value, :change, :status, :status_color, :systolic, :diastolic, :date, :time, :notes, NOW())");
$stmt->execute([
    ':user_id' => $userId,
    ':type' => $type,
    ':value' => $value,
    ':previous_value' => $prev['value'] ?? null,
    ':change' => $change,
    ':status' => $status,
    ':status_color' => $status_color,
    ':systolic' => $systolic,
    ':diastolic' => $diastolic,
    ':date' => $date,
    ':time' => $time,
    ':notes' => $notes
]);

echo json_encode(['success' => true, 'message' => 'Metric recorded successfully']);