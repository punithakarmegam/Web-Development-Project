<?php
include 'dbconnection.php';

// Enable MySQLi error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json');

// Define the unique page identifier
$page_id = 1; // Replace with the appropriate identifier for this page

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if the page ID exists in the table
    $stmt = $conn->prepare("SELECT visit_count FROM page_visits WHERE page_id = ?");
    $stmt->bind_param('i', $page_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Page ID exists, fetch current visit count
        $row = $result->fetch_assoc();
        $current_count = $row['visit_count'];

        // Update visit count
        $stmt = $conn->prepare("UPDATE page_visits SET visit_count = ? WHERE page_id = ?");
        $new_count = $current_count + 1;
        $stmt->bind_param('ii', $new_count, $page_id);
    } else {
        // Page ID does not exist, insert new record with visit count of 1
        $stmt = $conn->prepare("INSERT INTO page_visits (page_id, visit_count) VALUES (?, 1)");
        $stmt->bind_param('i', $page_id);
    }

    // Execute the update or insert statement
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Output success message with updated visit count
    echo json_encode(["status" => "success", "visit_count" => isset($new_count) ? $new_count : 1]);
} catch (mysqli_sql_exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    error_log('Database error: ' . $e->getMessage());

    // Output error message
    echo json_encode(["status" => "error", "message" => "Database error occurred."]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
