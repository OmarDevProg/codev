<?php
session_start();
require 'connect.php'; // Include the database connection class

// Initialize the database connection
$db = new Dbf();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data from the form
    $id = $_POST['id'];
    $nom = !empty($_POST['nom']) ? trim($_POST['nom']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $tel = !empty($_POST['tel']) ? trim($_POST['tel']) : null;
    $message = !empty($_POST['message']) ? trim($_POST['message']) : null;
    $date_reception = !empty($_POST['date_reception']) ? $_POST['date_reception'] : null;

    // Prepare the SQL update query
    $updateQuery = "
        UPDATE `eform-contact`
        SET 
            nom = :nom,
            email = :email, 
            tel = :tel, 
            message = :message, 
            date_reception = :date_reception
        WHERE id = :id
    ";

    // Prepare parameters for the query
    $params = [
        'nom' => $nom,
        'email' => $email,
        'tel' => $tel,
        'message' => $message,
        'date_reception' => $date_reception,
        'id' => $id
    ];

    try {
        // Execute the update query
        $result = $db->update($updateQuery, $params);

        // Check if the update was successful
        if ($result > 0) {
            $_SESSION['success'] = "Les informations du participant ont été mises à jour avec succès.";
        } else {
            $_SESSION['error'] = "Aucune modification effectuée ou une erreur s'est produite.";
        }
    } catch (Exception $e) {
        // Catch any errors and display them
        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour: " . $e->getMessage();
    }

    // Redirect back to the admin page with a success or error message
    header("Location: contact.php");
    exit();
}