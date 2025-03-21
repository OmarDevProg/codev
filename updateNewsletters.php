<?php
session_start();
require 'connect.php'; // Include the database connection class

// Initialize the database connection
$db = new Dbf();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data from the form
    $id = $_POST['id'];
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $ip = !empty($_POST['ip']) ? trim($_POST['ip']) : null;
    $date_submission = !empty($_POST['date_submission']) ? $_POST['date_submission'] : null;

    // Prepare the SQL update query
    $updateQuery = "
        UPDATE `eform-inscription-newsletter`
        SET 
            email = :email, 
            ip = :ip, 
            date_submission = :date_submission
        WHERE id = :id
    ";

    // Prepare parameters for the query
    $params = [
        'email' => $email,
        'ip' => $ip,
        'date_submission' => $date_submission,
        'id' => $id
    ];

    try {
        // Execute the update query
        $result = $db->update($updateQuery, $params);

        // Check if the update was successful
        if ($result > 0) {
            $_SESSION['success'] = "Les informations du newsletters ont été mises à jour avec succès.";
        } else {
            $_SESSION['error'] = "Aucune modification effectuée ou une erreur s'est produite.";
        }
    } catch (Exception $e) {
        // Catch any errors and display them
        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour: " . $e->getMessage();
    }

    // Redirect back to the admin page with a success or error message
    header("Location: newsletters.php");
    exit();
}