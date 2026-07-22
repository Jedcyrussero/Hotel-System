<?php
/**
 * API: Get All Employees
 * 
 * Fetches all employees from the database and returns them as JSON.
 * Used by the frontend to populate the employee dropdown.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use GET.'
    ]);
    exit();
}

// Load database configuration
$conn = require_once __DIR__ . '/../db_config.php';

// Prepare and execute query
$sql = "SELECT id, name, email, position, phone FROM employees ORDER BY name ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database query failed: ' . $conn->error
    ]);
    exit();
}

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

// Return JSON response
echo json_encode([
    'success' => true,
    'count' => count($employees),
    'data' => $employees
]);

$conn->close();
?>

