<?php
include 'connect.php';
$db = new Dbf();  // Create an instance of the Dbf class

header('Content-Type: application/json'); // Ensure the response is JSON

if (isset($_GET['id'])) {
    $formationId = $_GET['id'];

    // Prepare the update query
    $sql = "UPDATE formation SET lu = 1 WHERE code_formation = :id";

    // Execute the query using the update method of the Dbf class
    $result = $db->update($sql, ['id' => $formationId]);

    if ($result) {
        // Return success response
        echo json_encode(['status' => 'success']);
    } else {
        // Return error response if the update failed
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
} else {
    // Return error if no ID is provided
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
}
?>
