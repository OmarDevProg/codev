
<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <script type="text/javascript" >
        function preventBack(){window.history.forward()};
        setTimeout("preventBack()",0);
        window.onunload=function (){null;}   </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="st.css">
    <link rel="icon" type="image/png" href="assets/img/logo.png">

    <title>SEASIDECARE</title>

    <style>
        .error-message {
            color: red;
            font-size: 14px;
            position: absolute;
            top: 165px; /* Adjust this value to position the error */
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            text-align: center;
            margin: 0;
        }
        .form-container {
            position: relative; /* Needed for positioning the error message */
            padding-top: 5px; /* Add space to accommodate the error message */
        }



    </style>

</head>

<body>

<div class="container" id="container">




    <div class="form-container sign-in">
        <form method="post" action="login.php">
            <div class="logo-container">
                <img src="assets/img/logo.png" alt="main_logo" class="img-fluid" style="max-width: 250px; height: auto;">
            </div>
</br></br>
            </br>

            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>

            <!-- Input Fields -->
            <input type="text" placeholder="Adresse e-mail " name="email" required>
            <input type="password" placeholder="Mot de passe" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>


    <div class="toggle-container">
        <div class="toggle">

            <div class="toggle-panel toggle-right">
                <h1>CODEV </h1>
                <p> International </p>

            </div>
        </div>
    </div>
</div>

<script src="s.js"></script>
</body>

</html>
