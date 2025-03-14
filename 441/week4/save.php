<?php
// Database configuration
$servername = "sql308.infinityfree.com";
$username = "if0_37528986"; 
$password = "Zhanlian123456";   
$dbname = "if0_37528986_time"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the raw POST data
$data = file_get_contents('php://input');
$classes = json_decode($data, true);

// Check if data is received and not empty
if (empty($classes)) {
    echo json_encode(['status' => 'error', 'message' => 'No data received!']);
    exit;
}

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO timetable (subject, teacher, day, time) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $conn->error]);
    exit;
}

// Insert each class into the database
foreach ($classes as $class) {
    $subject = $class['subject'] ?? null;
    $teacher = $class['teacher'] ?? null;
    $day = $class['day'] ?? null;
    $time = $class['time'] ?? null;

    // Validate required fields
    if (empty($subject) || empty($teacher) || empty($day) || empty($time)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields in data!']);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("ssss", $subject, $teacher, $day, $time);

    // Execute the statement
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Error inserting data: ' . $stmt->error]);
        exit;
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return success response
echo json_encode(['status' => 'success', 'message' => 'Data saved successfully!']);
?>

