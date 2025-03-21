<?php
session_start();
require 'connect.php'; // Include the database class

$db = new Dbf(); // Initialize the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $identifiant = trim($_POST['identifiant']);
    $email = trim($_POST['email']);
    $password = trim($_POST['mot_passe']);
    $newPassword = trim($_POST['new_password']);
    $status = isset($_POST['status']) ? $_POST['status'] : 0;

    // Check if the provided password and confirm password match
    if ($password !== $newPassword) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header("Location: g_admin.php");
        exit();
    }

    // Hash the password before inserting it into the database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the insert query
    $insertQuery = "INSERT INTO administrateur 
                    (nom_administrateur, prenom_administrateur, login_administrateur, email_administrateur, password_administrateur, valide_administrateur) 
                    VALUES 
                    (:firstName, :lastName, :identifiant, :email, :hashedPassword, :status)";

    // Prepare parameters
    $params = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'identifiant' => $identifiant,
        'email' => $email,
        'hashedPassword' => $hashedPassword,
        'status' => $status
    ];

    // Execute the insert query using the `insert` method
    $result = $db->insert($insertQuery, $params);

    // Check if the insert was successful
    if ($result) {
        $_SESSION['success'] = "Administrateur ajouté avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout de l'administrateur.";
    }

    // Redirect back to the admin page with a success or error message
    header("Location: g_admin.php");
    exit();
}
?>
