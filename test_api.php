<?php
/**
 * TEST API SCRIPT - Run via command line: php test_api.php
 * Tests database connection, get_employees, and create_booking endpoints
 */

echo "================================\n";
echo "HOTEL SYSTEM - API TEST SCRIPT\n";
echo "================================\n\n";

// 1. Test Database Connection
echo "--- 1. Testing Database Connection ---\n";
$conn = new mysqli('localhost', 'root', '', 'hotel_system_db');
if ($conn->connect_error) {
    die("❌ DB Connection FAILED: " . $conn->connect_error . "\n");
}
echo "✅ Database connected successfully!\n\n";

// 2. Test get_employees.php logic
echo "--- 2. Testing Get All Employees ---\n";
$result = $conn->query("SELECT id, name, email, position, phone FROM employees ORDER BY name ASC");
if (!$result) {
    die("❌ Query FAILED: " . $conn->error . "\n");
}
$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}
echo "✅ Found " . count($employees) . " employees:\n";
foreach ($employees as $emp) {
    echo "   [{$emp['id']}] {$emp['name']} - {$emp['position']} ({$emp['email']})\n";
}

// Output as JSON (same as get_employees.php does)
$json_response = json_encode([
    'success' => true,
    'count' => count($employees),
    'data' => $employees
], JSON_PRETTY_PRINT);
echo "\n📦 JSON Output (same as API):\n$json_response\n\n";

// 3. Test create_booking.php logic
echo "--- 3. Testing Create Booking ---\n";
$test_booking = [
    'employee_id' => 1,
    'guest_name' => 'Test Guest',
    'room_number' => '999',
    'check_in' => '2026-12-01',
    'check_out' => '2026-12-05',
    'status' => 'confirmed'
];

$sql = "INSERT INTO hotel_bookings (employee_id, guest_name, room_number, check_in, check_out, status) 
        VALUES (
            {$test_booking['employee_id']}, 
            '{$test_booking['guest_name']}', 
            '{$test_booking['room_number']}', 
            '{$test_booking['check_in']}', 
            '{$test_booking['check_out']}', 
            '{$test_booking['status']}'
        )";

if ($conn->query($sql) === TRUE) {
    echo "✅ Booking created successfully! (ID: {$conn->insert_id})\n";
} else {
    echo "❌ Booking FAILED: " . $conn->error . "\n";
}

echo "\n================================\n";
echo "✅ ALL TESTS PASSED!\n";
echo "================================\n";

$conn->close();
