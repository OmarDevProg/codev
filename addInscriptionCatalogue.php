<?php
session_start();
require 'connect.php'; // Include the database connection class

// Initialize the database connection
$db = new Dbf();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data from the form
    $titre = !empty($_POST['titre']) ? trim($_POST['titre']) : null;
    $nom = !empty($_POST['nom']) ? trim($_POST['nom']) : null;
    $prenom = !empty($_POST['prenom']) ? trim($_POST['prenom']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $tel = !empty($_POST['tel']) ? trim($_POST['tel']) : null;
    $post = !empty($_POST['post']) ? trim($_POST['post']) : null;
    $organisme = !empty($_POST['organisme']) ? trim($_POST['organisme']) : null;
    $pays = !empty($_POST['pays']) ? trim($_POST['pays']) : null;
    $source = !empty($_POST['source']) ? trim($_POST['source']) : null;
    $formation = !empty($_POST['formation']) ? trim($_POST['formation']) : null;
    $session = !empty($_POST['session']) ? trim($_POST['session']) : null;
    $commentaire = !empty($_POST['commentaire']) ? trim($_POST['commentaire']) : null;
    $dateInscription = !empty($_POST['date_inscription']) ? $_POST['date_inscription'] : null;

    // Prepare the insert query
    $insertQuery = "
        INSERT INTO `eform-inscription-formation-specifique`
            (titre, nom, prenom, email, tel, post, organisme, pays, source, formation, session, commentaire, date_inscription) 
        VALUES 
            (:titre, :nom, :prenom, :email, :tel, :post, :organisme, :pays, :source, :formation, :session, :commentaire, :dateInscription)";

    // Prepare parameters
    $params = [
        'titre' => $titre,
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'tel' => $tel,
        'post' => $post,
        'organisme' => $organisme,
        'pays' => $pays,
        'source' => $source,
        'formation' => $formation,
        'session' => $session,
        'commentaire' => $commentaire,
        'dateInscription' => $dateInscription
    ];

    try {
        // Execute the insert query
        $result = $db->insert($insertQuery, $params);

        // Get the inserted participant's ID
        $participantId = $db->conF->lastInsertId();

        // If insert is successful, store success message
        if ($result && $participantId) {
            $_SESSION['success'] = "catalogue ajouté avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de l'inscription.";
        }
    } catch (Exception $e) {
        // Catch any errors and display them
        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout: " . $e->getMessage();
    }

    // Redirect back to the participant list or another relevant page
    header("Location: inscriptionCatalogue.php");
    exit();
}
?>
