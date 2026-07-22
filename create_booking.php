<?php
/**
 * API: Create Hotel Booking
 * 
 * Receives booking data from the frontend and inserts it into the hotel_bookings table.
 * The employee_id is populated from the dropdown selection on the frontend.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

// Load database configuration
$conn = require_once __DIR__ . '/../db_config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['employee_id', 'guest_name', 'room_number', 'check_in', 'check_out'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit();
}

// Sanitize inputs
$employee_id = intval($input['employee_id']);
$guest_name = $conn->real_escape_string(trim($input['guest_name']));
$room_number = $conn->real_escape_string(trim($input['room_number']));
$check_in = $conn->real_escape_string(trim($input['check_in']));
$check_out = $conn->real_escape_string(trim($input['check_out']));
$status = isset($input['status']) ? $conn->real_escape_string(trim($input['status'])) : 'confirmed';

// Validate employee exists
$check_employee = $conn->query("SELECT id FROM employees WHERE id = $employee_id");
if (!$check_employee || $check_employee->num_rows === 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid employee selected. Employee does not exist.'
    ]);
    exit();
}

// Validate date range
$check_in_date = new DateTime($check_in);
$check_out_date = new DateTime($check_out);
if ($check_out_date <= $check_in_date) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Check-out date must be after check-in date.'
    ]);
    exit();
}

// Validate status
$valid_statuses = ['confirmed', 'checked_in', 'checked_out', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    $status = 'confirmed';
}

// Insert booking
$sql = "INSERT INTO hotel_bookings (employee_id, guest_name, room_number, check_in, check_out, status) 
        VALUES ($employee_id, '$guest_name', '$room_number', '$check_in', '$check_out', '$status')";

if ($conn->query($sql) === TRUE) {
    $booking_id = $conn->insert_id;
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully!',
        'data' => [
            'id' => $booking_id,
            'employee_id' => $employee_id,
            'guest_name' => $guest_name,
            'room_number' => $room_number,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'status' => $status
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create booking: ' . $conn->error
    ]);
}

$conn->close();
?>

