<?php
header('Content-Type: application/json');
require_once __DIR__ . '/sql-db.php';

function getUserType($username, $conn) {
    $stmt = $conn->prepare('SELECT role FROM users WHERE username = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $role = null;
    $stmt->bind_result($role);

    
    if ($stmt->fetch()) {
        $stmt->close();
        return $role;
        }

    $stmt->close();
    return false;
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        $err = isset($db_connect_error) && $db_connect_error ? $db_connect_error : 'Database connection failed';
        echo json_encode(['success' => false, 'error' => $err]);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
        echo json_encode(['success' => false, 'error' => 'Username and password are required']);
        exit;
        }
    $stmt = $conn->prepare('SELECT user_id FROM users WHERE username=? AND password=?');
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        
        $role = getUserType($username, $conn);
        if ($role !== false && $role !== null) {
            echo json_encode(['success' => true, 'role' => $role]);
        } else {
            echo json_encode(['success' => false, 'error' => 'User role not found']);
        }
        $stmt->close();
        exit;
    } else {
        
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
        $stmt->close();
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>