<?php
session_start();
require 'connect.php'; // Include the database connection class

// Initialize the database connection
$db = new Dbf();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data from the form
    $id = $_POST['id'];
    $titre = !empty($_POST['titre']) ? trim($_POST['titre']) : null;
    $nom = !empty($_POST['nom']) ? trim($_POST['nom']) : null;
    $prenom = !empty($_POST['prenom']) ? trim($_POST['prenom']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $tel = !empty($_POST['tel']) ? trim($_POST['tel']) : null;
    $post = !empty($_POST['post']) ? trim($_POST['post']) : null;
    $organisme = !empty($_POST['organisme']) ? trim($_POST['organisme']) : null;
    $pays = !empty($_POST['pays']) ? trim($_POST['pays']) : null;
    $source = !empty($_POST['source']) ? trim($_POST['source']) : null;
    $formationProposition = !empty($_POST['formation_proposition']) ? trim($_POST['formation_proposition']) : null;
    $dateProposition = !empty($_POST['date_proposition']) ? $_POST['date_proposition'] : null;
    $nbrPersonne = !empty($_POST['nbr_personne']) ? $_POST['nbr_personne'] : null;
    $lieuProposition = !empty($_POST['lieu_proposition']) ? trim($_POST['lieu_proposition']) : null;
    $sourceFinancement = !empty($_POST['source_financement']) ? trim($_POST['source_financement']) : null;
    $commentaire = !empty($_POST['commentaire']) ? trim($_POST['commentaire']) : null;
    $dateInscription = !empty($_POST['date_inscription']) ? $_POST['date_inscription'] : null;

    // Prepare the SQL update query
    $updateQuery = "
        UPDATE `eform-inscription-formation-mesure`
        SET 
            titre = :titre,
            nom = :nom, 
            prenom = :prenom, 
            email = :email, 
            tel = :tel, 
            post = :post, 
            organisme = :organisme, 
            pays = :pays, 
            source = :source, 
            formation_proposition = :formationProposition, 
            date_proposition = :dateProposition, 
            nbr_personne = :nbrPersonne, 
            lieu_proposition = :lieuProposition, 
            source_financement = :sourceFinancement, 
            commentaire = :commentaire, 
            date_inscription = :dateInscription
        WHERE id = :id
    ";

    // Prepare parameters for the query
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
        'formationProposition' => $formationProposition,
        'dateProposition' => $dateProposition,
        'nbrPersonne' => $nbrPersonne,
        'lieuProposition' => $lieuProposition,
        'sourceFinancement' => $sourceFinancement,
        'commentaire' => $commentaire,
        'dateInscription' => $dateInscription,
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
    header("Location: inscriptionMesure.php");
    exit();
}
?>
