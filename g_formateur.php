

<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // Stop the script if the user is not logged in
}
?>
<?php

require 'connect.php'; // Include the database class

$db = new Dbf(); // Initialize the database connection

// Query to fetch the data from the 'formation' table
$query = "SELECT * FROM formation";
$formations = $db->select($query); // Assuming the select method fetches data from the database
?>

<?php

include 'navbar.php';

?>

<main class="main-content position-relative border-radius-lg ">
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
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">Gestion des formateurs</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">Gestion des formateurs</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <input type="hidden" class="form-control" placeholder="Type here...">
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

    <div class="container-fluid py-4">
        <main class="table ms-auto" id="customers_table">

            <section class="table__header">


                <div class="d-flex justify-content-md-end" >
                    <div class="text-center mt-4"> <!-- 'mt-3' adds top margin -->
                        <button class="btn btn-light btn-sm" onclick="showAddForm()">
                            <i class="fas fa-plus me-2"></i> ajouter un formateur
                        </button>
                    </div>
                    <div class="export__file mt-4">
                        <label for="export-file" class="export__file-btn" title="Export File"> </label>
                        <input type="checkbox" id="export-file">
                        <div class="export__file-options">
                            <label>Exporter en &nbsp; &#10140;</label>
                            <label for="export-file" id="toPDF">PDF <img src="image/pdf.png" alt=""></label>
                            <label for="export-file" id="toJSON">JSON <img src="image/json.png" alt=""></label>
                            <label for="export-file" id="toCSV">CSV <img src="image/csv.png" alt=""></label>
                            <label for="export-file" id="toEXCEL">EXCEL <img src="image/excel.png" alt=""></label>

                        </div>

                    </div>

                    <div style="margin-left: 50px" >

                        <label for="yearFilter" class="text-xs font-weight-bold mb-0 text-uppercase text-secondary opacity-7">klpokokppk</label>
                        <select id="yearFilter" class="form-select form-select-sm" onchange="filterData()">
                            <option value=""> année</option>
                            <?php
                            // Dynamically list available years
                            $years = array_merge(range(1999, 2025), [2019, 2020, 2021, 2022, 2023, 2024]);
                            foreach ($years as $year) {
                                // Check if this year is selected
                                $selected = ($_GET['year'] == $year) ? 'selected' : '';
                                echo "<option value=\"$year\" $selected>$year</option>";
                            }
                            ?>
                        </select>
                    </div>

                    &nbsp;
                    <div>
                        <label for="monthFilter" class="text-xs font-weight-bold mb-0 text-uppercase text-secondary opacity-7">.</label>
                        <select id="monthFilter" class="form-select form-select-sm" onchange="filterData()">
                            <option value=""> mois</option>
                            <?php
                            // Dynamically list available months
                            $months = [
                                '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
                                '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
                                '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
                                '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                            ];
                            foreach ($months as $month => $monthName) {
                                $selected = ($_GET['month'] == $month) ? 'selected' : '';
                                echo "<option value=\"$month\" $selected>$monthName</option>";
                            }
                            ?>
                        </select>
                    </div>
                    &nbsp;
                    <div>
                        <label for="dateFilter" class="text-xs font-weight-bold mb-1 text-uppercase text-secondary"></label>
                        <input type="date" id="dateFilter" class="form-control form-control-sm border-primary">
                    </div>
                    &nbsp;
                    <!-- Filter Button -->
                    <div>
                        <button class="btn btn-primary btn-sm mt-4 " onclick="filterData()">
                            <i class="fas fa-filter text-dark"></i> Filtrer
                        </button>
                    </div>


                    <!-- Export File Section -->

                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <!-- Dropdown Menu -->
                    <div class="search-container ms-3">

                        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." style="width: 250px;">
                    </div>
                    <div class="dropdown mt-4">
                        <button type="button" class="btn btn-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-eye" style="font-size: 1.6rem; color: darkslategray"></i> <!-- Icon only -->
                        </button>

                        <ul class="dropdown-menu p-3" style="background-color: white; min-width: 100px;">
                            <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Nom</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> Prénom</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Profession</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Email</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="5" checked> Date de Formation</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="6" checked> Date de Naissance</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="7" checked> Nationalité</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="8" checked> Status</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="9" checked> Cv</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="10" checked> Actions</label></li>
                        </ul>
                    </div>




                    <!-- Search Input -->

                </div>



            </section>
            <link rel="stylesheet" type="text/css" href="style.css">

    <section class="table__body">
        <table id="formateursTable">
            <thead>
            <tr>
                <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                <th>Prénom <span class="icon-arrow">&UpArrow;</span></th>
                <th>Profession <span class="icon-arrow">&UpArrow;</span></th>
                <th>Email <span class="icon-arrow">&UpArrow;</span></th>
                <th>Date de Formation <span class="icon-arrow">&UpArrow;</span></th>
                <th>Date de Naissance <span class="icon-arrow">&UpArrow;</span></th>
                <th>Nationalité <span class="icon-arrow">&UpArrow;</span></th>
                <th>Cv <span class="icon-arrow">&UpArrow;</span></th>
                <th>Actions <span class="icon-arrow">&UpArrow;</span></th>
            </tr>
            </thead>
            <tbody id="table-body">
            <?php foreach ($formations as $formation): ?>
                <tr>

                    <td><?= htmlspecialchars($formation['nom_formation']); ?></td>
                    <td><?= htmlspecialchars($formation['prenom_formation']); ?></td>
                    <td><?= htmlspecialchars($formation['profession_formation']); ?></td>
                    <td><?= htmlspecialchars($formation['email_formation']); ?></td>
                    <td><?= htmlspecialchars($formation['date_formation']); ?></td>
                    <td><?= htmlspecialchars($formation['date_naissance_formation']); ?></td>
                    <td><?= htmlspecialchars($formation['nationalite_formation']); ?></td>

                    <td>
                        <?php
                        $fileExtension = pathinfo($formation['doc_formation'], PATHINFO_EXTENSION);
                        $icon = 'fas fa-file'; // Default icon
                        switch (strtolower($fileExtension)) {
                            case 'pdf': $icon = 'fas fa-file-pdf text-danger'; break;
                            case 'docx':
                            case 'doc': $icon = 'fas fa-file-word text-primary'; break;
                            case 'xlsx':
                            case 'xls': $icon = 'fas fa-file-excel text-success'; break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png': $icon = 'fas fa-file-image text-warning'; break;
                            case 'zip': $icon = 'fas fa-file-archive text-info'; break;
                        }
                        ?>
                        <a href="downloadFile.php?file=<?php echo urlencode($formation['doc_formation']); ?>" class="text-secondary" data-toggle="tooltip" data-original-title="Download File">
                            <i class="<?php echo $icon; ?>"></i>
                        </a>
                    </td>
                    <td>
                        <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                           onclick='showDetails(<?php echo json_encode($formation, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo $formation['code_formation']; ?>)'>
                            <i class="fas fa-eye" style="color: #007bff;"></i>
                        </a>
                        &nbsp;
                        <a href="javascript:void(0);" onclick="showEditForm(this)"
                           data-code-formation="<?php echo htmlspecialchars($formation['code_formation']); ?>"
                           data-nom="<?php echo htmlspecialchars($formation['nom_formation']); ?>"
                           data-prenom="<?php echo htmlspecialchars($formation['prenom_formation']); ?>"
                           data-profession="<?php echo htmlspecialchars($formation['profession_formation']); ?>"
                           data-email="<?php echo htmlspecialchars($formation['email_formation']); ?>"
                           data-date-formation="<?php echo htmlspecialchars($formation['date_formation']); ?>"
                           data-date-naissance="<?php echo htmlspecialchars($formation['date_naissance_formation']); ?>"
                           data-nationalite="<?php echo htmlspecialchars($formation['nationalite_formation']); ?>"
                           data-cv="<?php echo htmlspecialchars($formation['doc_formation']); ?>"
                           data-toggle="tooltip" title="Edit">
                            <i class="fas fa-pencil-alt" style="color: #ffc107;"></i>
                        </a>



                        &nbsp;
                        <a href="javascript:void(0);" data-toggle="tooltip" title="Delete"
                           onclick='deleteFormateur(<?php echo $formation['code_formation']; ?>)'>
                            <i class="fas fa-trash" style="color: #dc3545;"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <div class="pagination">
        <button id="prevPage" class="pagination-btn">Précédent</button>
        <span id="pageInfo"></span>
        <button id="nextPage" class="pagination-btn">Suivant</button>
    </div>



    <div id="detailsCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 450px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-info text-white p-3">
            <h6 class="mb-0"><i class="fas fa-user me-2"></i> Détails du Formateur</h6>
        </div>
        <div class="card-body p-3">
            <p><strong>Nom:</strong> <span id="detailNom"></span></p>
            <p><strong>Prénom:</strong> <span id="detailPrenom"></span></p>
            <p><strong>Profession:</strong> <span id="detailProfession"></span></p>
            <p><strong>Email:</strong> <span id="detailEmail"></span></p>
            <p><strong>Date de Formation:</strong> <span id="detailDateFormation"></span></p>
            <p><strong>Date de Naissance:</strong> <span id="detailDateNaissance"></span></p>
            <p><strong>Nationalité:</strong> <span id="detailNationalite"></span></p>
            <p><strong>Curriculum Vitae:</strong>  <a href="downloadFile.php?file=<?php echo urlencode($formation['doc_formation']); ?>" class="text-secondary" data-toggle="tooltip" data-original-title="Download File">
                    <i class="<?php echo $icon; ?>"></i>
                </a></p>
            <p><strong>Status:</strong>
                <span id="detailStatus" class="badge rounded-pill text-uppercase fw-bold"></span>
            </p>

            <button class="btn btn-secondary w-100 mt-2" onclick="hideDetails()">
                <i class="fas fa-times me-2"></i> Fermer
            </button>
        </div>
    </div>


            <div id="addFormateurCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
                 style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
                <div class="card-header bg-gradient-success text-white p-3">
                    <h6 class="mb-0"><i class="fas fa-plus me-2"></i> Ajouter un Formateur</h6>
                </div>

                <div class="card-body p-3">
                    <form id="addFormateurForm" method="post" action="addFormateur.php" enctype="multipart/form-data">
                        <input type="hidden" name="code_formateur" id="addCodeFormateur">

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label for="addNom">Nom<span class="text-danger"> *</span></label>
                                <input type="text" id="addNom" name="nom" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addPrenom">Prénom<span class="text-danger"> *</span></label>
                                <input type="text" id="addPrenom" name="prenom" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addProfession">Profession<span class="text-danger"> *</span></label>
                                <input type="text" id="addProfession" name="profession" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addEmail">Email<span class="text-danger"> *</span></label>
                                <input type="email" id="addEmail" name="email" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addDateFormation">Date de Formation<span class="text-danger"> *</span></label>
                                <input type="date" id="addDateFormation" name="date_formation" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addDateNaissance">Date de Naissance<span class="text-danger"> *</span></label>
                                <input type="date" id="addDateNaissance" name="date_naissance" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addNationalite">Nationalité<span class="text-danger"> *</span></label>
                                <input type="text" id="addNationalite" name="nationalite" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label for="addCv">Curriculum Vitae<span class="text-danger"> *</span></label>
                                <input type="file" id="addCv" name="doc_formation" class="form-control" accept=".pdf, .doc, .docx" required>
                            </div>


                        </div>

                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Ajouter un Formateur</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideAddForm()">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                    </form>
                </div>
            </div>



            <div id="editFormateurCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
                 style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
                <div class="card-header bg-gradient-success text-white p-3">
                    <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Modifier un Formateur</h6>
                </div>

                <div class="card-body p-3">
                    <form id="editFormateurForm" method="post" action="updateFormateur.php" enctype="multipart/form-data">
                        <input type="hidden" name="code_formation" id="editCodeFormation">

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="editNom">Nom<span class="text-danger"> *</span></label>
                                <input type="text" id="editNom" name="nom" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editPrenom">Prénom<span class="text-danger"> *</span></label>
                                <input type="text" id="editPrenom" name="prenom" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editProfession">Profession<span class="text-danger"> *</span></label>
                                <input type="text" id="editProfession" name="profession" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editEmail">Email<span class="text-danger"> *</span></label>
                                <input type="email" id="editEmail" name="email" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editDateFormation">Date de Formation<span class="text-danger"> *</span></label>
                                <input type="date" id="editDateFormation" name="date_formation" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editDateNaissance">Date de Naissance<span class="text-danger"> *</span></label>
                                <input type="date" id="editDateNaissance" name="date_naissance" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editNationalite">Nationalité<span class="text-danger"> *</span></label>
                                <input type="text" id="editNationalite" name="nationalite" class="form-control">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="editCv">Curriculum Vitae<span class="text-danger"> *</span></label>
                                <input type="file" id="editCv" name="cv" class="form-control" accept=".pdf, .doc, .docx" >
                                <!-- Display current CV if available -->
                                <div id="currentCvDiv" class="mt-2" style="display:none;">
                                    <strong>Current CV:</strong> <span id="currentCv"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Modifier un Formateur</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()">
                            <i class="fas fa-times me-2"></i> Annuler
                        </button>
                    </form>
                </div>
            </div>




            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <!-- SweetAlert2 JS -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                function deleteFormateur(participantId) {
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Voulez-vous vraiment supprimer ce formateur ? Cette action est irréversible.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Make an AJAX request to delete the participant
                            fetch('deleteFormateur.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ code_formation: participantId })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Supprimé!', data.message, 'success').then(() => {
                                            // Reload the page or remove the participant row from the table
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
                }
            </script>


            <script>
        function showDetails(data, id) {
            console.log("Data:", data); // Check the data
            console.log("ID:", id); // Check the ID

            // Show the details in the modal
            document.getElementById("detailNom").innerText = data.nom_formation;
            document.getElementById("detailPrenom").innerText = data.prenom_formation;
            document.getElementById("detailProfession").innerText = data.profession_formation;
            document.getElementById("detailEmail").innerText = data.email_formation;
            document.getElementById("detailDateFormation").innerText = data.date_formation;
            document.getElementById("detailDateNaissance").innerText = data.date_naissance_formation;
            document.getElementById("detailNationalite").innerText = data.nationalite_formation;
            const statusElement = document.getElementById("detailStatus");
            if (data.lu == 1) {
                statusElement.innerText = "Read";
                statusElement.classList.add('bg-success', 'text-white', 'px-3', 'py-2', 'shadow-sm');
                statusElement.classList.remove('bg-warning');
            } else {
                statusElement.innerText = "Unread";
                statusElement.classList.add('bg-warning', 'text-dark', 'px-3', 'py-2', 'shadow-sm');
                statusElement.classList.remove('bg-success');
            }
            document.getElementById("detailsCard").style.display = "block";

            // Make AJAX request to update the "lu" status to 1 (Read)
            console.log("Sending AJAX request to update status");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "updateStatus.php?id=" + id, true);
            xhr.onload = function () {
                console.log("AJAX request completed"); // Check if this is reached
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            console.log("Status updated to read");
                            document.getElementById("detailStatus").innerText = "Read";
                            document.querySelector('.badge').classList.remove('bg-gradient-warning');
                            document.querySelector('.badge').classList.add('bg-gradient-success');
                            document.querySelector('.badge').innerText = 'Read';
                        } else {
                            console.error('Failed to update status:', response.message);
                        }
                    } catch (e) {
                        console.error('Failed to parse response as JSON:', e);
                    }
                }
            };
            xhr.send();
        }


        function hideDetails() {
            document.getElementById("detailsCard").style.display = "none";
        }
    </script>
            <script>
                // Function to show the edit form with pre-filled data
                function showEditForm(button) {
                    // Get data attributes from the button
                    const code = button.getAttribute('data-code-formation');
                    const nom = button.getAttribute('data-nom');
                    const prenom = button.getAttribute('data-prenom');
                    const profession = button.getAttribute('data-profession');
                    const email = button.getAttribute('data-email');
                    const dateFormation = button.getAttribute('data-date-formation');
                    const dateNaissance = button.getAttribute('data-date-naissance');
                    const nationalite = button.getAttribute('data-nationalite');
                    const cv = button.getAttribute('data-cv');

                    // Fill the form fields with the existing data
                    document.getElementById('editCodeFormation').value = code;
                    document.getElementById('editNom').value = nom;
                    document.getElementById('editPrenom').value = prenom;
                    document.getElementById('editProfession').value = profession;
                    document.getElementById('editEmail').value = email;
                    document.getElementById('editDateFormation').value = dateFormation;
                    document.getElementById('editDateNaissance').value = dateNaissance;
                    document.getElementById('editNationalite').value = nationalite;

                    // If there's a CV, show the current file name
                    if (cv) {
                        document.getElementById('currentCvDiv').style.display = 'block';
                        document.getElementById('currentCv').textContent = cv; // Display the CV file name or path
                    } else {
                        document.getElementById('currentCvDiv').style.display = 'none';
                    }

                    // Show the edit form
                    document.getElementById('editFormateurCard').style.display = 'block'; // Show the form
                }

                // Function to hide the edit form
                function hideEditForm() {
                    document.getElementById('editFormateurCard').style.display = 'none'; // Hide the form
                }
            </script>
                <script>
                    // Function to show the add form
                    function showAddForm() {
                        document.getElementById('addFormateurCard').style.display = 'block'; // Show the add form
                    }

                    // Function to hide the add form
                    function hideAddForm() {
                        document.getElementById('addFormateurCard').style.display = 'none'; // Hide the add form
                    }



            </script>


            <script>
                const rowsPerPage = 7;  // Adjust the number of rows per page
                let currentPage = 1;
                const tableBody = document.querySelector('#table-body');
                const rows = tableBody.querySelectorAll('tr');
                let totalRows = rows.length;
                let totalPages = Math.ceil(totalRows / rowsPerPage);

                const prevPageBtn = document.querySelector('#prevPage');
                const nextPageBtn = document.querySelector('#nextPage');
                const pageInfo = document.querySelector('#pageInfo');
                const searchInput = document.querySelector('#searchInput');

                // Function to display the rows for the current page
                function displayTableRows() {
                    const filteredRows = tableBody.querySelectorAll('tr:not(.hide)');
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = start + rowsPerPage;

                    filteredRows.forEach((row, index) => {
                        if (index >= start && index < end) {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    pageInfo.textContent = `Page ${currentPage} sur ${totalPages}`;
                    prevPageBtn.disabled = currentPage === 1;
                    nextPageBtn.disabled = currentPage === totalPages;
                }

                // Function to go to the next page
                nextPageBtn.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        displayTableRows();
                    }
                });

                // Function to go to the previous page
                prevPageBtn.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        displayTableRows();
                    }
                });

                // Function to search the table
                function searchTable() {
                    const searchValue = searchInput.value.toLowerCase();

                    // Reset the hide class from all rows
                    rows.forEach((row) => {
                        const rowData = row.textContent.toLowerCase();
                        if (rowData.indexOf(searchValue) === -1) {
                            row.classList.add('hide');  // Hide rows that don't match the search term
                        } else {
                            row.classList.remove('hide');  // Show rows that match
                        }
                    });

                    // Recalculate total pages based on the visible rows
                    const filteredRows = tableBody.querySelectorAll('tr:not(.hide)');
                    totalRows = filteredRows.length;
                    totalPages = Math.ceil(totalRows / rowsPerPage);
                    currentPage = 1;  // Reset to the first page after a search

                    // Display the rows on the first page based on the filtered results
                    displayTableRows();
                }

                // Add event listener to the search input
                searchInput.addEventListener('input', searchTable);

                // Initial display of table rows
                displayTableRows();

            </script>
            <script>
                document.querySelectorAll('.column-toggle').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        let colIndex = this.getAttribute('data-col');
                        let isChecked = this.checked;

                        document.querySelectorAll('table tr').forEach(row => {
                            let cell = row.children[colIndex - 1];
                            if (cell) cell.style.display = isChecked ? '' : 'none';
                        });
                    });
                });
            </script>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>


            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <!-- SweetAlert2 JS -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                function deleteParticipant(participantId) {
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Voulez-vous vraiment supprimer ce participant ? Cette action est irréversible.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Make an AJAX request to delete the participant
                            fetch('deleteParticipant.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id: participantId })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Supprimé!', data.message, 'success').then(() => {
                                            // Reload the page or remove the participant row from the table
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
                }
            </script>




            <script>
                // JavaScript function to filter participants by year
                function filterData() {
                    let year = document.getElementById('yearFilter').value;
                    let month = document.getElementById('monthFilter').value;
                    let date = document.getElementById('dateFilter').value;

                    let url = "g_formateur.php?";
                    let params = [];

                    if (year) params.push("year=" + year);
                    if (month) params.push("month=" + month);
                    if (date) params.push("date=" + date);

                    window.location.href = url + params.join("&");
                }
            </script>


    <?php
include "footer.php";

?>
            <script src="script.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


