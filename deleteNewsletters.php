<?php
require 'connect.php'; // Include the Dbf class

// Instantiate the Dbf class for database interaction
$db = new Dbf();

// Get the JSON data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Check if the id is provided
if (isset($data['id'])) {
    $participantId = (int) $data['id'];

    // Validate that the ID is a positive integer
    if ($participantId > 0) {
        // Prepare the SQL query to delete the participant
        $sql = "DELETE FROM `eform-inscription-newsletter` WHERE id = ?";

        // Attempt to execute the query
        $params = [$participantId];
        $result = $db->delete($sql, $params);

        // Check if any rows were affected
        if ($result > 0) {
            // If deletion was successful, send success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Le newslettre a été supprimé avec succès.'
            ]);
        } else {
            // If no rows were affected, send an error response
            echo json_encode([
                'status' => 'error',
                'message' => 'Aucun newsletter trouvé avec cet identifiant.'
            ]);
        }
    } else {
        // Invalid ID, send an error response
        echo json_encode([
            'status' => 'error',
            'message' => 'Identifiant invalide.'
        ]);
    }
} else {
    // Missing ID, send an error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Identifiant manquant.'
    ]);
}
?>
