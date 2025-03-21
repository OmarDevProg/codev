<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_administrateur = $_POST['login_administrateur'];
    $email_administrateur = $_POST['email_administrateur'];
    $password_administrateur = $_POST['password_administrateur'];
    $nom_administrateur = $_POST['nom_administrateur'];
    $prenom_administrateur = $_POST['prenom_administrateur'];

    // Mot de passe brut
    $id =$_SESSION["user_id"]; // ID caché transmis depuis le formulaire
    $job=$_SESSION['user_job'];



    // Hachage du mot de passe
    $hashedPassword = password_hash($password_administrateur, PASSWORD_DEFAULT);


    $d = new Dbf();
    $sql = "UPDATE administrateur SET login_administrateur = :login_administrateur, email_administrateur = :email_administrateur, password_administrateur = :hashedPassword, nom_administrateur = :nom_administrateur, prenom_administrateur = :prenom_administrateur WHERE code_administrateur = :code_administrateur";

    $stmt = $d->conF->prepare($sql);
    $stmt->bindParam(':login_administrateur', $login_administrateur, PDO::PARAM_STR);
    $stmt->bindParam(':email_administrateur', $email_administrateur, PDO::PARAM_STR);
    $stmt->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':nom_administrateur', $nom_administrateur, PDO::PARAM_STR);
    $stmt->bindParam(':prenom_administrateur', $prenom_administrateur, PDO::PARAM_STR);
    $stmt->bindParam(':code_administrateur', $id, PDO::PARAM_INT); // Updated to match the placeholder



    if ($stmt->execute()) {
        if($job=='1'){
            header("Location: profile.php");
            exit();}

    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>
