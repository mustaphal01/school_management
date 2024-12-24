<?php
require_once 'config.php';
require_once 'functions.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, grade) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['firstName'],
        $data['lastName'],
        $data['email'],
        $data['grade']
    ]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>