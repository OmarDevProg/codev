<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // Stop the script if the user is not logged in
}
?>


<?php
include  "navbar.php";
?>
<?php
include 'connect.php';

$db = new Dbf();


?>


<main class="main-content position-relative border-radius-lg ">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success position-fixed top-0 end-50 m-3 fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger position-fixed top-0 end-50 m-3 fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <script>
        setTimeout(() => {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => alert.classList.add('fade'));
            setTimeout(() => alerts.forEach(alert => alert.remove()), 500);
        }, 3000);
    </script>

    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">Gestion des utilisateurs</li>
                </ol>
                <h6 class="font-weight-bolder text-white mb-0">Gestion des administrateurs</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <div class="input-group">
                        <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" placeholder="Type here...">
                    </div>
                </div>
                <ul class="navbar-nav  justify-content-end">
                    <li class="nav-item d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none">Administrateur</span>
                        </a>
                    </li>
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                            </div>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <?php
    // Check if a status and message are passed in the URL (query string)
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $message = isset($_GET['message']) ? $_GET['message'] : '';

    if ($status && $message) {
        // Output the toastr script if there is a message to display
        echo "<script>
            toastr.options = {
                'closeButton': true,
                'debug': false,
                'newestOnTop': true,
                'progressBar': true,
                'positionClass': 'toast-top-right', 
                'preventDuplicates': true,
                'showDuration': '300',
                'hideDuration': '1000',
                'timeOut': '5000',
                'extendedTimeOut': '1000',
                'showEasing': 'swing',
                'hideEasing': 'linear',
                'showMethod': 'fadeIn',
                'hideMethod': 'fadeOut'
            };

            // Check if the status is success or error
            if ('{$status}' === 'success') {
                toastr.success('{$message}', 'Succès');
            } else if ('{$status}' === 'error') {
                toastr.error('{$message}', 'Erreur');
            }
        </script>";
    }
    ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>


    <?php
    // SQL query to fetch client data
    $sql = "SELECT * FROM administrateur "; // Replace 'clients' with your actual table name

    // Execute the query
    $clients = $db->select($sql);
    // Check if clients were found
    if ($clients) { ?>

        <div class="container-fluid py-3">
            <div class="row">
                <div class="col-md-8 mt-4">
                    <div class="card border-0 rounded-sm shadow-sm">
                        <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center p-3">
                            <h6 class="mb-0"><i class="fas fa-users me-2"></i> Liste des Administrateurs</h6>
                            <button class="btn btn-outline-secondary btn-sm" onclick="showAddForm()">
                                <i class="fas fa-plus me-2"></i> ajouter un Administrateur
                            </button>
                        </div>

                        <form method="post">
                            <div class="card-body p-2">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($clients as $client) { ?>
                                        <li class="list-group-item border-0 p-3 d-flex justify-content-between align-items-center">
                                            <div class="d-flex flex-column w-75">
                                                <h6 class="mb-1 text-dark">
                                                    <?php echo htmlspecialchars($client['nom_administrateur']) . ' ' . $client['prenom_administrateur']; ?>
                                                </h6>
                                                <p class="mb-1 text-muted text-sm">
                                                    <strong>Email:</strong>
                                                    <span class="text-dark"><?php echo htmlspecialchars($client['email_administrateur']); ?></span>
                                                </p>
                                                <p class="mb-1 text-muted text-sm">
                                                    <strong>Identifiant:</strong>
                                                    <span class="text-dark"><?php echo htmlspecialchars($client['login_administrateur']); ?></span>
                                                </p>
                                                <p class="mb-2 text-muted text-xs">
                                                    <strong>Status:</strong>
                                                    <span class="text-dark font-weight-bold">
                                                <?php
                                                if ($client['valide_administrateur'] == 1) {
                                                    echo '<i class="fas fa-check-circle text-success"></i>';
                                                } else {
                                                    echo '<i class="fas fa-times-circle text-danger"></i>';                                                }
                                                ?>
                                            </span>
                                                </p>
                                            </div>
                                            <div class="d-flex">
                                                <!-- Delete Button -->
                                                <a class="btn btn-outline-danger btn-sm px-3 py-1 delete-user" href="#"
                                                   data-id="<?php echo htmlspecialchars($client['code_administrateur']); ?>">
                                                    <i class="far fa-trash-alt me-1"></i>Delete
                                                </a>


                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-outline-warning btn-sm px-3 py-1 ms-2"
                                                        onclick="showEditForm(this)"
                                                        data-id="<?php echo htmlspecialchars($client['code_administrateur']); ?>"
                                                        data-first-name="<?php echo htmlspecialchars($client['nom_administrateur']); ?>"
                                                        data-last-name="<?php echo htmlspecialchars($client['prenom_administrateur']); ?>"
                                                        data-identifiant="<?php echo htmlspecialchars($client['login_administrateur']); ?>"
                                                        data-email="<?php echo htmlspecialchars($client['email_administrateur']); ?>"
                                                        data-mot-passe="<?php echo htmlspecialchars($client['password_administrateur']); ?>"
                                                        data-Nmot-passe="<?php echo htmlspecialchars($client['password_administrateur']); ?>"

                                                        data-valide="<?php echo (int) $client['valide_administrateur']; ?>"> <!-- Ensures value is "1" or "0" -->
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </button>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    <?php } ?>

    <!-- Edit Admin Form -->
    <div id="editCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 450px; width: 90%; background-color: #f8f9fa;">        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Edit Administrator</h6>
        </div>
        <div class="card-body p-3">
            <form id="editForm" method="post" action="updateAdministrateur.php">
                <input type="hidden" name="id" id="editId">
                <div class="mb-2">
                    <label for="editFirstName">Nom<span class="text-danger"> *</span></label>
                    <input type="text" id="editFirstName" name="firstName" class="form-control">
                </div>
                <div class="mb-2">
                    <label for="editLastName">Prenom<span class="text-danger"> *</span></label>
                    <input type="text" id="editLastName" name="lastName" class="form-control">
                </div>

                <div class="mb-2">
                    <label for="editIdentifiant">Identifiant<span class="text-danger"> *</span></label>
                    <input type="text" id="editIdentifiant" name="identifiant" class="form-control">
                </div>
                <div class="mb-2">
                    <label for="editEmail">Email<span class="text-danger"> *</span></label>
                    <input type="email" id="editEmail" name="email" class="form-control">
                </div>
                <div class="mb-2">
                    <label for="editMotPasse" class="form-label">Mot de passe actuel<span class="text-danger"> *</span></label>
                    <div class="position-relative">
                        <input type="password" id="editMotPasse" name="mot_passe" class="form-control pe-5" required>
                        <i class="fas fa-eye position-absolute top-50 end-0 translate-middle-y me-3"
                           style="cursor: pointer;"
                           onclick="togglePassword('editMotPasse', this)"></i>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="editNmotPasse" class="form-label">Nouveau Mot de passe<span class="text-danger"> *</span></label>
                    <div class="position-relative">
                        <input type="password" id="editNmotPasse" name="new_password" class="form-control pe-5" required>
                        <i class="fas fa-eye position-absolute top-50 end-0 translate-middle-y me-3"
                           style="cursor: pointer;"
                           onclick="togglePassword('editNmotPasse', this)"></i>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="editStatusOui" value="1">
                        <label class="form-check-label" for="editStatusOui">Oui</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="editStatusNon" value="0">
                        <label class="form-check-label" for="editStatusNon">Non</label>
                    </div>
                </div>
                </div>


                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Modifier un Administrateur</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()"><i class="fas fa-times me-2"></i> Annuler</button>
            </form>
        </div>
    </div>

    <!-- Add Admin Form -->
    <div id="addCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 450px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i> Add Administrator</h6>
        </div>
        <div class="card-body p-3">
            <form id="addForm" method="post" action="addAdministrateur.php">
                <div class="mb-2">
                    <label for="addFirstName" class="form-label">Nom<span class="text-danger"> *</span></label>
                    <input type="text" id="addFirstName" name="firstName" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="addLastName" class="form-label">Prénom<span class="text-danger"> *</span></label>
                    <input type="text" id="addLastName" name="lastName" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="addEmail" class="form-label">Email<span class="text-danger"> *</span></label>
                    <input type="email" id="addEmail" name="email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="addIdentifiant" class="form-label">Identifiant<span class="text-danger"> *</span></label>
                    <input type="text" id="addIdentifiant" name="identifiant" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="editMotPasse" class="form-label">Mot de passe actuel<span class="text-danger"> *</span></label>
                    <input type="password" id="editMotPasse" name="mot_passe" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label for="editNmotPasse" class="form-label">Confirmer Mot de passe<span class="text-danger"> *</span></label>
                    <input type="password" id="editNmotPasse" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger"> *</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="statusOui" name="status" value="oui" required>
                            <label class="form-check-label" for="statusOui">Oui</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="statusNon" name="status" value="non" required>
                            <label class="form-check-label" for="statusNon">Non</label>
                        </div>
                    </div>
                </div>


                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Add Administrator</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideAddForm()"><i class="fas fa-times me-2"></i> Cancel</button>
            </form>
        </div>
    </div>

    <script>
        let currentClientId = null;

        function showEditForm(button) {
            const id = button.getAttribute('data-id');
            const firstName = button.getAttribute('data-first-name');
            const lastName = button.getAttribute('data-last-name');
            const email = button.getAttribute('data-email');
            const identifiant = button.getAttribute('data-identifiant');
            const motPasse = '';
            const NmotPasse = '';

            const valide = button.getAttribute('data-valide');

            // Populate the form fields with the existing client details
            document.getElementById('editId').value = id;
            document.getElementById('editFirstName').value = firstName;
            document.getElementById('editLastName').value = lastName;
            document.getElementById('editEmail').value = email;
            document.getElementById('editIdentifiant').value =identifiant;
            document.getElementById('editMotPasse').value =motPasse;
            document.getElementById('editNmotPasse').value =NmotPasse;



            if (valide === '1') {
                document.getElementById('editStatusOui').checked = true;
            } else {
                document.getElementById('editStatusNon').checked = true;
            }

            // Show the edit card
            document.getElementById('editForm').action = 'updateAdministrateur.php';
            document.getElementById('editCard').style.display = 'block';
        }

        function hideEditForm() {
            // Hide the edit card
            document.getElementById('editCard').style.display = 'none';
        }

        function showAddForm() {
            // Show the add form card
            document.getElementById('addCard').style.display = 'block';
        }

        function hideAddForm() {
            // Hide the add form card
            document.getElementById('addCard').style.display = 'none';
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Make sure to include FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add click event to delete button
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const userId = this.getAttribute('data-id');

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Voulez-vous vraiment supprimer cet client ? Cette action est irréversible.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Make an AJAX request to delete the user
                            fetch('deleteAdmin.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id: userId })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Supprimé!', data.message, 'success').then(() => {
                                            // Reload the page or remove the user row from the table
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Erreur!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire('Erreur!', 'Une erreur s\'est produite lors de la suppression.', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>


    <script>
        function togglePassword(inputId, icon) {
            var input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

    <?php
include "footer.php";

?>

