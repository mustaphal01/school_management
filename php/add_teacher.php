<?php
// add_teacher.php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['firstName'], $data['lastName'], $data['email'], $data['subject'])) {
    // Simulate adding to the database
    $response = [
        'status' => 'success',
        'message' => 'Teacher added successfully'
    ];
    echo json_encode($response);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}
?>
