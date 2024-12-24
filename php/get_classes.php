<?php
require_once 'config.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT c.*, CONCAT(t.first_name, ' ', t.last_name) as teacher_name 
        FROM classes c 
        LEFT JOIN teachers t ON c.teacher_id = t.id 
        ORDER BY c.class_name
    ");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($classes);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>