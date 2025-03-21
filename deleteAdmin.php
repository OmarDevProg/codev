<?php
require 'connect.php'; // Include the Dbf class

// Instantiate the Dbf class for database interaction
$db = new Dbf();

// Get the JSON data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Check if the id is provided
if (isset($data['id'])) {
    $adminId = (int) $data['id'];

    // Validate that the ID is a positive integer
    if ($adminId > 0) {
        // Prepare the SQL query to delete the administrator
        $sql = "DELETE FROM administrateur WHERE code_administrateur = ?";

        // Attempt to execute the query
        $params = [$adminId];
        $result = $db->delete($sql, $params);

        // Check if any rows were affected
        if ($result > 0) {
            // If deletion was successful, send success response
            echo json_encode([
                'status' => 'success',
                'message' => 'L\'administrateur a été supprimé avec succès.'
            ]);
        } else {
            // If no rows were affected, send an error response
            echo json_encode([
                'status' => 'error',
                'message' => 'Aucun administrateur trouvé avec cet identifiant.'
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
