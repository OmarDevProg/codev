<?php
session_start();
require 'connect.php'; // Include the database class

$db = new Dbf(); // Initialize the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $id = $_POST['id'];
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $identifiant = trim($_POST['identifiant']);
    $email = trim($_POST['email']);
    $currentPassword = trim($_POST['mot_passe']);
    $newPassword = trim($_POST['new_password']);
    $status = isset($_POST['status']) ? $_POST['status'] : 0;

    // Fetch the current password hash from the database
    $query = "SELECT password_administrateur FROM administrateur WHERE code_administrateur = :id";
    $params = ['id' => $id];
    $admin = $db->select($query, $params);

    if (!$admin) {
        $_SESSION['error'] = "Administrateur introuvable.";
        header("Location: g_admin.php");
        exit();
    }

    // Verify current password
    if (!password_verify($currentPassword, $admin[0]['password_administrateur'])) {
        $_SESSION['error'] = "Mot de passe actuel incorrect.";
        header("Location: g_admin.php");
        exit();
    }

    // Prepare the update query
    $updateQuery = "UPDATE administrateur SET 
                    nom_administrateur = :firstName, 
                    prenom_administrateur = :lastName, 
                    login_administrateur = :identifiant, 
                    email_administrateur = :email, 
                    valide_administrateur = :status";

    // Prepare parameters
    $params = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'identifiant' => $identifiant,
        'email' => $email,
        'status' => $status,
        'id' => $id
    ];

    // If new password is provided, hash it and include in the update query
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateQuery .= ", password_administrateur = :newPassword";
        $params['newPassword'] = $hashedPassword;
    }

    // Finalize the update query
    $updateQuery .= " WHERE code_administrateur = :id";

    // Execute the update query using the `update` method
    $result = $db->update($updateQuery, $params);

    // Check if the update was successful
    if ($result > 0) {
        $_SESSION['success'] = "Administrateur mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
    }

    // Redirect back to the admin page with a success or error message
    header("Location: g_admin.php");
    exit();
}
?>
