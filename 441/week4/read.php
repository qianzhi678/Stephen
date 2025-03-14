<?php
// Database connection
$host = 'sql308.infinityfree.com';
$dbname = 'if0_37528986_time';
$username = 'if0_37528986';
$password = 'Zhanlian123456';

try {
    // Create a PDO connection to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling

    // Fetch all records from the timetable table
    $stmt = $conn->query("SELECT * FROM timetable");
    $timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Start building the HTML output
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Timetable</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f4f4f4;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
        </style>
    </head>
    <body>
        <h1>Timetable</h1>";

    // Check if there are any records
    if (count($timetable) > 0) {
        // Start the table
        echo "<table>
                <thead>
                    <tr>
                        <th>subject</th>
                        <th>day</th>
                        <th>time</th>
                        <th>teacher</th>
                    </tr>
                </thead>
                <tbody>";

        // Loop through each record and display it in a table row
        foreach ($timetable as $row) {
            echo "<tr>
                    <td>{$row['subject']}</td>
                    <td>{$row['day']}</td>
                    <td>{$row['time']}</td>
                    <td>{$row['teacher']}</td>
                  </tr>";
        }

        // Close the table
        echo "</tbody>
              </table>";
    } else {
        // If no records are found, display a message
        echo "<p>No timetable data available.</p>";
    }

    // Close the HTML
    echo "</body>
          </html>";
} catch (PDOException $e) {
    // Handle database connection or query errors
    echo "<h1>Error</h1>
          <p>An error occurred while fetching the timetable data: " . $e->getMessage() . "</p>";
}
?>