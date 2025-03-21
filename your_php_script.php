<?php
// Include the necessary files
session_start();
require 'connect.php';

// Database connection
$db = new Dbf();

// Set default limit and offset
$limit = isset($_GET['length']) ? (int)$_GET['length'] : 10;
$offset = isset($_GET['start']) ? (int)$_GET['start'] : 0;

// Get the search filter values from the request
$year = isset($_GET['year']) ? $_GET['year'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Build the query with filters
$conditions = [];
$params = [];

if ($year) {
    $conditions[] = "YEAR(date_inscription) = :year";
    $params[':year'] = $year;
}

if ($month) {
    $conditions[] = "MONTH(date_inscription) = :month";
    $params[':month'] = $month;
}

if ($date) {
    $conditions[] = "DATE(date_inscription) = :date";
    $params[':date'] = $date;
}

// Construct the base query
$query = "SELECT * FROM eform-inscription-formation-specifique";

// Add conditions if any filters are applied
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Apply pagination
$query .= " LIMIT :limit OFFSET :offset";

// Prepare the statement
$stmt = $db->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Bind additional filter parameters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

// Execute the query
$stmt->execute();

// Fetch the results
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of records (without filters)
$totalQuery = "SELECT COUNT(*) FROM eform-inscription-formation-specifique";
$totalStmt = $db->query($totalQuery);
$totalRecords = $totalStmt->fetchColumn();

// Prepare the response in the expected DataTables format
$response = [
    "draw" => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords, // You can adjust this if you want to show filtered total count
    "data" => $inscriptions
];

// Output the JSON response
echo json_encode($response);
