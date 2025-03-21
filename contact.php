<?php
session_start();
require 'connect.php';

$db = new Dbf();

$conditions = [];
$params = [];

// Filter by year
if (!empty($_GET['year'])) {
    $conditions[] = "YEAR(	
date_reception) = :year";
    $params[':year'] = $_GET['year'];
}

// Filter by month
if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(date_reception) = :month";
    $params[':month'] = $_GET['month'];
}

// Filter by exact date
if (!empty($_GET['date'])) {
    $conditions[] = "DATE(date_reception) = :date";
    $params[':date'] = $_GET['date'];
}


// Query to fetch data from 'eform-inscription-newsletter'


// Construct the query with filters
$query = "SELECT * FROM  `eform-contact`";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Fetch results
$submissions = $db->select($query,$params);
?>


<?php

include 'navbar.php';

?>
    <link rel="stylesheet" type="text/css" href="style.css">

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

                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-6">
        <main class="table ms-auto" id="customers_table">

            <section class="table__header">


                <div class="d-flex justify-content-md-end" >
                    <div class="text-center mt-4"> <!-- 'mt-3' adds top margin -->

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
                            <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Code Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Fin</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Pays Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="5" checked> Email Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="6" checked> Adresse Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="7" checked> Email Coordonnateur</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="8" checked> Thème Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="9" checked> Date Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="10" checked> Site Projet</label></li>
                        </ul>
                    </div>


                    <!-- Search Input -->

                </div>



            </section>
            <section class="table__body">
                <table>
                    <thead>
                    <tr>
                        <th> Nom <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Email <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Téléphone <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Date Réception <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Action <span class="icon-arrow">&UpArrow;</span></th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= htmlspecialchars($submission['nom']) ?></td>
                                <td><?= htmlspecialchars($submission['email']) ?></td>
                                <td><?= htmlspecialchars($submission['tel']) ?></td>
                                <td><?= date("Y-m-d", strtotime($submission['date_reception'])) ?></td>
                                <td>
                                    <!-- View Action -->
                                    <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                                       onclick='showDetails(<?php echo json_encode($submission, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                        <i class="fas fa-eye" style="color: #007bff;"></i>
                                    </a>
                                    &nbsp;
                                    <!-- Edit Action -->
                                    <a href="javascript:void(0);" onclick="showEditForm(this)"
                                       data-id="<?= htmlspecialchars($submission['id']); ?>"
                                       data-nom="<?= htmlspecialchars($submission['nom']); ?>"
                                       data-email="<?= htmlspecialchars($submission['email']); ?>"
                                       data-tel="<?= htmlspecialchars($submission['tel']); ?>"
                                       data-message="<?= htmlspecialchars($submission['message']); ?>"
                                       data-date-reception="<?= date("Y-m-d", strtotime($submission['date_reception'])); ?>"
                                       data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-pencil-alt" style="color: #ffc107;"></i>
                                    </a>
                                    &nbsp;
                                    <!-- Delete Action -->
                                    <a href="javascript:void(0);" data-toggle="tooltip" title="Delete"
                                       onclick='deleteNewsletter(<?php echo $submission['id']; ?>)'>
                                        <i class="fas fa-trash" style="color: #dc3545;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No records found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>


            <!-- Pagination Section -->
            <div class="pagination">
                <button id="prevPage" class="pagination-btn">Précédent</button>
                <span id="pageInfo"></span>
                <button id="nextPage" class="pagination-btn">Suivant</button>
            </div>
        </main>
    </div>
    <div id="detailsCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
        <div class="card-header bg-info text-white p-4 rounded-top" style="border-bottom: 1px solid #e0e0e0;">
            <!-- Card Header: Title of the Registration Details -->
            <h5 class="mb-0"><i class="fas fa-user me-2"></i> Détails de la soumission</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <!-- Left Column: Submission Information -->
                <div class="col-md-6">
                    <!-- ID -->
                    <div class="mb-3">
                        <p><strong>ID:</strong> <span id="detailId" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Nom -->
                    <div class="mb-3">
                        <p><strong>Nom:</strong> <span id="detailNom" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Email -->
                    <div class="mb-3">
                        <p><strong>Email:</strong> <span id="detailEmail" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Téléphone -->
                    <div class="mb-3">
                        <p><strong>Téléphone:</strong> <span id="detailTel" class="fw-normal text-muted"></span></p>
                    </div>
                </div>

                <!-- Right Column: Submission Details -->
                <div class="col-md-6">
                    <!-- Message -->
                    <div class="mb-3">
                        <p><strong>Message:</strong></p>
                        <p id="detailMessage" class="fw-normal text-muted"></p>
                    </div>
                    <!-- Date de Réception -->
                    <div class="mb-3">
                        <p><strong>Date de Réception:</strong> <span id="detailDateReception" class="fw-normal text-muted"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Footer with Close Button -->
        <div class="card-footer bg-light p-3 text-center rounded-bottom">
            <button class="btn btn-secondary w-50" onclick="hideDetails()">
                <i class="fas fa-times me-2"></i> Fermer
            </button>
        </div>
    </div>

    <div id="editCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Modifier Soumission</h6>
        </div>

        <div class="card-body p-3">
            <form id="editForm" method="post" action="updateSubmissions.php">
                <input type="hidden" name="id" id="editId">

                <div class="row">
                    <!-- Nom -->
                    <div class="col-md-4 mb-2">
                        <label for="editNom">Nom<span class="text-danger"> *</span></label>
                        <input type="text" id="editNom" name="nom" class="form-control" required>
                    </div>

                    <!-- Email -->
                    <div class="col-md-4 mb-2">
                        <label for="editEmail">Email<span class="text-danger"> *</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control" required>
                    </div>

                    <!-- Téléphone -->
                    <div class="col-md-4 mb-2">
                        <label for="editTel">Téléphone<span class="text-danger"> *</span></label>
                        <input type="text" id="editTel" name="tel" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Message -->
                    <div class="col-md-6 mb-2">
                        <label for="editMessage">Message</label>
                        <textarea id="editMessage" name="message" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Date de Réception -->
                    <div class="col-md-6 mb-2">
                        <label for="editDateReception">Date de Réception<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateReception" name="date_reception" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Valider</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()"><i class="fas fa-times me-2"></i> Annuler</button>
            </form>
        </div>
    </div>



    <script>
        function showDetails(data, id) {
            console.log("Data:", data);
            console.log("ID:", id);

            // Populate the detail fields with data
            document.getElementById("detailId").innerText = data.id || '';
            document.getElementById("detailNom").innerText = data.nom || '';
            document.getElementById("detailEmail").innerText = data.email || '';
            document.getElementById("detailTel").innerText = data.tel || '';
            document.getElementById("detailMessage").innerText = data.message || '';
            document.getElementById("detailDateReception").innerText = data.date_reception || '';

            // Show the details card
            document.getElementById("detailsCard").style.display = "block";

            // Sending AJAX request to update status (if required)
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
        function showEditForm(button) {
            // Retrieve data from the button's data attributes
            const id = button.getAttribute('data-id');
            const nom = button.getAttribute('data-nom');
            const email = button.getAttribute('data-email');
            const tel = button.getAttribute('data-tel');
            const message = button.getAttribute('data-message');
            const dateReception = button.getAttribute('data-date-reception');

            // Populate the form fields with the selected participant's details
            document.getElementById('editId').value = id;
            document.getElementById('editNom').value = nom;
            document.getElementById('editEmail').value = email;
            document.getElementById('editTel').value = tel;
            document.getElementById('editMessage').value = message;
            document.getElementById('editDateReception').value = dateReception;

            // Show the edit card
            document.getElementById('editForm').action = 'updateContact.php'; // Adjust the action if needed
            document.getElementById('editCard').style.display = 'block';
        }

        function hideEditForm() {
            // Hide the edit card
            document.getElementById('editCard').style.display = 'none';
        }

        function showAddForm() {
            document.getElementById('addParticipantCard').style.display = 'block';
        }

        function hideAddForm() {
            document.getElementById('addParticipantCard').style.display = 'none';
        }

    </script>

    <script>
        $(document).ready(function() {
            // Check if DataTable already exists and destroy if it does
            if ($.fn.dataTable.isDataTable('#participantsTable')) {
                $('#participantsTable').DataTable().destroy();
            }

            // Initialize DataTable
            var table = $('#participantsTable').DataTable({
                "paging": true,
                "ordering": true,
                "searching": true,
                "lengthChange": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "lengthMenu": [
                    [7, 15, 25, 30, -1],
                    [7, 15, 25, 30, "All"]
                ],
                "dom": '<"top"Bf>rt<"bottom"ilp><"clear">',
                "language": {
                    "paginate": {
                        "first": '<i class="fas fa-angle-double-left"></i>',
                        "last": '<i class="fas fa-angle-double-right"></i>',
                        "next": '<i class="fas fa-angle-right"></i>',
                        "previous": '<i class="fas fa-angle-left"></i>'
                    }
                },
                "buttons": [
                    {
                        extend: 'copy',
                        className: 'btn btn-copy',
                        text: '<i class="fas fa-copy"></i>'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-csv',
                        text: '<i class="fas fa-file-csv"></i>'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-excel',
                        text: '<i class="fas fa-file-excel"></i>'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-pdf',
                        text: '<i class="fas fa-file-pdf"></i>'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-print',
                        text: '<i class="fas fa-print"></i>'
                    },
                    {
                        text: '<i class="fas fa-columns"></i>',
                        className: 'dropdown-button btn btn-copy',
                        action: function(e, dt, node, config) {
                            $('.dropdown-content').toggle();
                        }
                    }
                ]


            });

            // Function to save checkbox states to localStorage
            function saveCheckboxState() {
                $('.dropdown-content input[type="checkbox"]').each(function() {
                    var columnIndex = $(this).closest('label').index();
                    localStorage.setItem('columnVisibility_' + columnIndex, this.checked);
                });
            }

            // Function to load checkbox states from localStorage
            function loadCheckboxState() {
                $('.dropdown-content input[type="checkbox"]').each(function() {
                    var columnIndex = $(this).closest('label').index();
                    var isChecked = localStorage.getItem('columnVisibility_' + columnIndex);

                    // Set the checkbox state
                    $(this).prop('checked', isChecked === 'true');

                    // Show/hide the column
                    var column = table.column(columnIndex);
                    column.visible(isChecked === 'true');
                });
            }

            // Event listener for checkbox change
            $('.dropdown-content input[type="checkbox"]').on('change', function() {
                var column = table.column($(this).closest('label').index());
                column.visible(this.checked);
                saveCheckboxState();
            });

            // Load the checkbox state when the page loads
            loadCheckboxState();
        });
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

    <script src="script.js"></script>

    <script>
        // JavaScript function to filter participants by year
        function filterData() {
            let year = document.getElementById('yearFilter').value;
            let month = document.getElementById('monthFilter').value;
            let date = document.getElementById('dateFilter').value;

            let url = "contact.php?";
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
        function deleteNewsletter(participantId) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment supprimer ce contact ? Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an AJAX request to delete the participant
                    fetch('deleteContact.php', {
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




<?php
include "footer.php";

?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>

