<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
header('Location: index.php');
exit; // Stop the script if the user is not logged in
}
?>

<?php



require 'connect.php';

$db = new Dbf();

$conditions = [];
$params = [];


// Filter by year
if (!empty($_GET['year'])) {
    $conditions[] = "YEAR(date_debut_session) = :year";
    $params[':year'] = $_GET['year'];
}

// Filter by month
if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(date_debut_session) = :month";
    $params[':month'] = $_GET['month'];
}

// Filter by exact date
if (!empty($_GET['date'])) {
    $conditions[] = "DATE(date_debut_session) = :date";
    $params[':date'] = $_GET['date'];
}


// Query to fetch data from 'eform-inscription-newsletter'


// Construct the query with filters
$query = "SELECT * FROM  `ci_participants`";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Fetch results
$formations = $db->select($query,$params);
?>


<?php

include 'navbar.php';

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
    <link rel="stylesheet" type="text/css" href="style.css">

    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">Evenement</li>
                </ol>
                <h6 class="font-weight-bolder text-white mb-0">Liste des evenements</h6>
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
                            <i class="fas fa-plus me-2"></i> ajouter une formateur
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
                    <div class="dropdown mt-4"  >
                        <button type="button" class="btn btn-transparent dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-eye" style="font-size: 1.6rem; color: darkslategray"></i> <!-- Icon only -->
                        </button>

                        <ul class="dropdown-menu p-3" style="background-color: white; min-width: 100px;">
                            <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Photo</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> Nom et prénom</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Email</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Fonction</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="5" checked> Action</label></li>
                        </ul>
                    </div>

                    <!-- Search Input -->

                </div>



            </section>




            <section class="table__body">

                <table id="customers_table">


                    <thead>
                    <tr>
                        <th>Photo<span class="icon-arrow">&UpArrow;</span></th>

                        <th>Nom et prenom<span class="icon-arrow">&UpArrow;</span></th>
                        <th>Email <span class="icon-arrow">&UpArrow;</span></th>

                        <th>Fonction <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Action <span class="icon-arrow">&UpArrow;</span></th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <?php if (!empty($formations)): ?>
                        <?php foreach ($formations as $formation): ?>
                            <tr>
                                <td class="fd_blanc" style="float: left; height: 100px; width: 100px; display: flex; align-items: center; justify-content: center; border: none; position: relative; overflow: hidden; border-radius: 50%; ">
                                    <img style="width: 100%; height: 100%; object-fit: cover;  display: block; "
                                         src="./image/img_data-base/<?php echo $formation['id']; ?>.jpg"
                                         alt="Photo">
                                </td>
                                <td> <?= htmlspecialchars($formation['nom']) ?> </td>

                                <td> <?= htmlspecialchars($formation['email']) ?> </td>

                                <td> <?= htmlspecialchars($formation['fonction']) ?> </td>
                                <td>
                                    <!-- View Action -->
                                    <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                                       onclick='showDetails(<?php echo json_encode($formation, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo $formation['id']; ?>)'>
                                        <i class="fas fa-eye" style="transition: transform 0.3s ease, color 0.3s ease; color: #007bff;"
                                           onmouseover="this.style.transform='scale(1.2)'; this.style.color='#1e90ff';"
                                           onmouseout="this.style.transform='scale(1)'; this.style.color='#007bff';"></i>
                                    </a>
                                    &nbsp;
                                    <!-- Edit Action -->

                                    &nbsp;

                                    <!-- Delete Action -->

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="18">No records found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>

            </section>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

            <div class="pagination">
                <button id="prevPage" class="pagination-btn">Précédent</button>
                <span id="pageInfo"></span>
                <button id="nextPage" class="pagination-btn">Suivant</button>
            </div>

        </main>

    </div>

    <div id="detailsCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-info text-white p-3">
            <h6 class="mb-0"><i class="fas fa-user me-2"></i> Détails du formation </h6>
        </div>
        <div class="card-body p-3 d-flex">
            <!-- Text details section -->
            <div class="me-3 flex-grow-1">
                <p><strong>Nom:</strong> <span id="detailNom"></span></p>
                <p><strong>Organisation:</strong> <span id="detailOrg"></span></p>
                <p><strong>Lieu de Formation:</strong> <span id="detailLieuFormation"></span></p>
                <p><strong>Pays:</strong> <span id="detailPays"></span></p>
                <p><strong>Titre de Formation:</strong> <span id="detailTitreFormation"></span></p>
                <p><strong>Date de Début:</strong> <span id="detailDateDebutSession"></span></p>
                <p><strong>Date de Fin:</strong> <span id="detailDateFinSession"></span></p>
                <p><strong>Type de Formation:</strong> <span id="detailTypeFormation"></span></p>
                <p><strong>Date de Naissance:</strong> <span id="detailDateNaissance"></span></p>
                <p><strong>Email:</strong> <span id="detailEmail"></span></p>
                <p><strong>Téléphone:</strong> <span id="detailTel"></span></p>
                <p><strong>Adresse:</strong> <span id="detailAdresse"></span></p>
                <p><strong>Projet:</strong> <span id="detailProjet"></span></p>
                <p><strong>Fax:</strong> <span id="detailFax"></span></p>
                <p><strong>Fonction:</strong> <span id="detailFonction"></span></p>
                <p><strong>Status:</strong>
                    <span id="detailStatus" class="badge rounded-pill text-uppercase fw-bold"></span>
                </p>
            </div>

            <!-- Photo section -->
            <div class="text-center">
                <img style="width: 410px; height: auto; object-fit: cover; border-radius: 20px;"
                     src="./image/img_data-base/<?php echo $id; ?>.jpg"
                     alt="Photo">
</br></br>
                </br>

                <!-- Action Buttons: Edit and Delete -->

            </div>
        </div>

        <!-- Close Button -->
        <div class="d-flex justify-content-center mt-3">
            <button class="btn btn-link p-0" onclick="hideDetails()">
                <i class="fas fa-times" style="font-size: 24px;"></i>
            </button>
        </div>

    </div>







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
        // JavaScript function to filter formations by year
        function filterData() {
            let year = document.getElementById('yearFilter').value;
            let month = document.getElementById('monthFilter').value;
            let date = document.getElementById('dateFilter').value;

            let url = "evenement.php?";
            let params = [];

            if (year) params.push("year=" + year);
            if (month) params.push("month=" + month);
            if (date) params.push("date=" + date);

            window.location.href = url + params.join("&");
        }
    </script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteNewsletter(formationId) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment supprimer ce newsletter ? Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an AJAX request to delete the formation
                    fetch('deleteNewsletters.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: formationId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Supprimé!', data.message, 'success').then(() => {
                                    // Reload the page or remove the formation row from the table
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
            console.log("Data:", data);
            console.log("ID:", id);

            document.getElementById("detailNom").innerText = data.nom;
            document.getElementById("detailOrg").innerText = data.org;
            document.getElementById("detailLieuFormation").innerText = data.lieu_formation;
            document.getElementById("detailPays").innerText = data.pays;
            document.getElementById("detailTitreFormation").innerText = data.titre_formation;
            document.getElementById("detailDateDebutSession").innerText = data.date_debut_session;
            document.getElementById("detailDateFinSession").innerText = data.date_fin_session;
            document.getElementById("detailTypeFormation").innerText = data.type_formation;
            document.getElementById("detailDateNaissance").innerText = data.date_naissance;
            document.getElementById("detailEmail").innerText = data.email;
            document.getElementById("detailTel").innerText = data.tel;
            document.getElementById("detailAdresse").innerText = data.adresse;
            document.getElementById("detailProjet").innerText = data.Projet;
            document.getElementById("detailFax").innerText = data.fax;
            document.getElementById("detailFonction").innerText = data.fonction;

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
            document.querySelector('#detailsCard img').src = './image/img_data-base/' + id + '.jpg';

            document.getElementById("detailsCard").style.display = "block";

            console.log("Sending AJAX request to update status");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "updateStatus.php?id=" + id, true);
            xhr.onload = function () {
                console.log("AJAX request completed");
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            console.log("Status updated to read");
                            statusElement.innerText = "Read";
                            statusElement.classList.remove('bg-warning');
                            statusElement.classList.add('bg-success');
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
    <div id="editCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Edit formation</h6>
        </div>

        <div class="card-body p-3">
            <form id="editForm" method="post" action="updateformation.php">
                <input type="hidden" name="id" id="editId">

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="editNom">Nom<span class="text-danger"> *</span></label>
                        <input type="text" id="editNom" name="nom" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editOrg">Organisation<span class="text-danger"> *</span></label>
                        <input type="text" id="editOrg" name="org" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editLieuFormation">Lieu de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="editLieuFormation" name="lieu_formation" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editPays">Pays<span class="text-danger"> *</span></label>
                        <input type="text" id="editPays" name="pays" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editTitreFormation">Titre de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="editTitreFormation" name="titre_formation" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editDateNaissance">Date de Naissance<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateNaissance" name="date_naissance" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editEmail">Email<span class="text-danger"> *</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editTel">Téléphone<span class="text-danger"> *</span></label>
                        <input type="text" id="editTel" name="tel" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editAdresse">Adresse<span class="text-danger"> *</span></label>
                        <input type="text" id="editAdresse" name="adresse" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editProjet">Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editProjet" name="projet" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editFax">Fax<span class="text-danger"> *</span></label>
                        <input type="text" id="editFax" name="fax" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editFonction">Fonction<span class="text-danger"> *</span></label>
                        <input type="text" id="editFonction" name="fonction" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editTypeFormation">Type de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="editTypeFormation" name="type_formation" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editDateDebutSession">Date Début Session<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateDebutSession" name="date_debut_session" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editDateFinSession">Date Fin Session<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateFinSession" name="date_fin_session" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editPreinscription">Preinscription<span class="text-danger"> *</span></label>
                        <input type="text" id="editPreinscription" name="preinscription" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editFacture">Facture<span class="text-danger"> *</span></label>
                        <input type="text" id="editFacture" name="facture" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editPaiement">Paiement<span class="text-danger"> *</span></label>
                        <input type="text" id="editPaiement" name="paiement" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Modifier Evenement</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()"><i class="fas fa-times me-2"></i> Annuler</button>
            </form>
        </div>
    </div>
<script>
    document.querySelectorAll('.column-toggle input').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const colIndex = this.getAttribute('data-col');
            const table = document.querySelector('table');

            // Toggle header (th)
            table.querySelectorAll(`th:nth-child(${colIndex})`).forEach(th => {
                th.style.display = this.checked ? '' : 'none';
            });

            // Toggle data cells (td)
            table.querySelectorAll('tr').forEach(row => {
                row.querySelectorAll(`td:nth-child(${colIndex})`).forEach(td => {
                    td.style.display = this.checked ? '' : 'none';
                });
            });
        });
    });


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

    <script src="script.js"></script>

<?php
include "footer.php";

?>