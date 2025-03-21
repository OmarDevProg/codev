<?php
session_start();
require 'connect.php'; // Include the database class

$db = new Dbf(); // Initialize the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $id = $_POST['id'];
    $nom = trim($_POST['nom']) ?: null;
    $org = trim($_POST['org']) ?: null;
    $lieuFormation = trim($_POST['lieu_formation']) ?: null;
    $pays = trim($_POST['pays']) ?: null;
    $titreFormation = trim($_POST['titre_formation']) ?: null;
    $dateNaissance = $_POST['date_naissance'] ?: null;
    $email = trim($_POST['email']) ?: null;
    $tel = trim($_POST['tel']) ?: null;
    $adresse = trim($_POST['adresse']) ?: null;
    $projet = trim($_POST['projet']) ?: null;
    $fax = trim($_POST['fax']) ?: null;
    $fonction = trim($_POST['fonction']) ?: null;
    $typeFormation = trim($_POST['type_formation']) ?: null;
    $dateDebutSession = $_POST['date_debut_session'] ?: null;
    $dateFinSession = $_POST['date_fin_session'] ?: null;
    $preinscription = trim($_POST['preinscription']) ?: null;
    $facture = trim($_POST['facture']) ?: null;
    $paiement = trim($_POST['paiement']) ?: null;

    // Debugging output
    echo "ID: " . $id . "<br>";
    echo "Nom: " . $nom . "<br>";
    echo "Organisation: " . $org . "<br>";
    echo "Lieu de Formation: " . $lieuFormation . "<br>";
    echo "Pays: " . $pays . "<br>";
    echo "Titre de Formation: " . $titreFormation . "<br>";
    echo "Date de Naissance: " . $dateNaissance . "<br>";
    echo "Email: " . $email . "<br>";
    echo "Téléphone: " . $tel . "<br>";
    echo "Adresse: " . $adresse . "<br>";
    echo "Projet: " . $projet . "<br>";
    echo "Fax: " . $fax . "<br>";
    echo "Fonction: " . $fonction . "<br>";
    echo "Type de Formation: " . $typeFormation . "<br>";
    echo "Date Début Session: " . $dateDebutSession . "<br>";
    echo "Date Fin Session: " . $dateFinSession . "<br>";
    echo "Preinscription: " . $preinscription . "<br>";
    echo "Facture: " . $facture . "<br>";
    echo "Paiement: " . $paiement . "<br>";

    // Prepare the update query
    $updateQuery = "UPDATE ci_participants SET 
        nom = :nom, 
        org = :org, 
        lieu_formation = :lieuFormation, 
        pays = :pays, 
        titre_formation = :titreFormation, 
        date_naissance = :dateNaissance, 
        email = :email, 
        tel = :tel, 
        adresse = :adresse, 
        projet = :projet, 
        fax = :fax, 
        fonction = :fonction, 
        type_formation = :typeFormation, 
        date_debut_session = :dateDebutSession, 
        date_fin_session = :dateFinSession, 
        preinscription = :preinscription, 
        facture = :facture, 
        paiement = :paiement
    WHERE id = :id";

    // Prepare parameters
    $params = [
        'nom' => $nom,
        'org' => $org,
        'lieuFormation' => $lieuFormation,
        'pays' => $pays,
        'titreFormation' => $titreFormation,
        'dateNaissance' => $dateNaissance,
        'email' => $email,
        'tel' => $tel,
        'adresse' => $adresse,
        'projet' => $projet,
        'fax' => $fax,
        'fonction' => $fonction,
        'typeFormation' => $typeFormation,
        'dateDebutSession' => $dateDebutSession,
        'dateFinSession' => $dateFinSession,
        'preinscription' => $preinscription,
        'facture' => $facture,
        'paiement' => $paiement,
        'id' => $id
    ];

    // Execute the update query using the `update` method
    $result = $db->update($updateQuery, $params);

    if ($result > 0) {
        $_SESSION['success'] = "Participant mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
    }

    // Redirect back to the admin page with a success or error message
    header("Location: g_participants.php");
    exit();
}

?>
