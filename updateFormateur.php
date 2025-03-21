<?php
session_start();
require 'connect.php'; // Inclusion de la classe de connexion à la base de données

$db = new Dbf(); // Initialisation de la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire pour le formateur
    $code_formation            = $_POST['code_formation'];
    $nom_formation             = trim($_POST['nom']) ?: null;
    $prenom_formation          = trim($_POST['prenom']) ?: null;
    $profession_formation      = trim($_POST['profession']) ?: null;
    $email_formation           = trim($_POST['email']) ?: null;
    $date_formation            = trim($_POST['date_formation']) ?: null;
    $date_naissance_formation  = trim($_POST['date_naissance']) ?: null;
    $nationalite_formation     = trim($_POST['nationalite']) ?: null;

    // Handle file upload for CV
    $doc_formation = null;
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        // Handle the file upload and move it to the uploads directory
        $doc_formation = 'uploads/' . basename($_FILES['cv']['name']);
        move_uploaded_file($_FILES['cv']['tmp_name'], $doc_formation);
    }

    // Préparation de la requête de mise à jour
    $updateQuery = "UPDATE formation SET 
        nom_formation = :nom_formation, 
        prenom_formation = :prenom_formation, 
        profession_formation = :profession_formation, 
        email_formation = :email_formation, 
        date_formation = :date_formation, 
        date_naissance_formation = :date_naissance_formation, 
        nationalite_formation = :nationalite_formation, 
        doc_formation = :doc_formation
    WHERE code_formation = :code_formation";

    // Préparation des paramètres
    $params = [
        'nom_formation'            => $nom_formation,
        'prenom_formation'         => $prenom_formation,
        'profession_formation'     => $profession_formation,
        'email_formation'          => $email_formation,
        'date_formation'           => $date_formation,
        'date_naissance_formation' => $date_naissance_formation,
        'nationalite_formation'    => $nationalite_formation,
        'doc_formation'            => $doc_formation,
        'code_formation'           => $code_formation
    ];

    // Exécution de la requête de mise à jour
    $result = $db->update($updateQuery, $params);

    if ($result > 0) {
        $_SESSION['success'] = "Formateur mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
    }

    // Redirection vers la page de gestion des formateurs
    header("Location: g_formateur.php");
    exit();
}
?>
