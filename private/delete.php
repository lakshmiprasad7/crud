<?php
include "db_conn.php";

// Assuming you have validated and sanitized the ID before using it in the query
$id = $_GET["id"];

// Check if there are related records in the 'emails' table
$sqlCheckEmails = "SELECT COUNT(*) FROM `emails` WHERE `amc_id` = ?";
$stmtCheckEmails = mysqli_prepare($conn, $sqlCheckEmails);

if ($stmtCheckEmails === false) {
    die("Error preparing statement to check related emails: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtCheckEmails, "i", $id);
mysqli_stmt_execute($stmtCheckEmails);
mysqli_stmt_bind_result($stmtCheckEmails, $emailCount);
mysqli_stmt_fetch($stmtCheckEmails);
mysqli_stmt_close($stmtCheckEmails);

// If there are related records in the 'emails' table, delete them first
if ($emailCount > 0) {
    $sqlDeleteEmails = "DELETE FROM `emails` WHERE `amc_id` = ?";
    $stmtDeleteEmails = mysqli_prepare($conn, $sqlDeleteEmails);

    if ($stmtDeleteEmails === false) {
        die("Error preparing statement to delete related emails: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtDeleteEmails, "i", $id);
    mysqli_stmt_execute($stmtDeleteEmails);
    mysqli_stmt_close($stmtDeleteEmails);
}

// Now, you can safely delete the record from the 'amc_details' table
$sqlDeleteAmcDetails = "DELETE FROM `amc_details` WHERE id = ?";
$stmtDeleteAmcDetails = mysqli_prepare($conn, $sqlDeleteAmcDetails);

if ($stmtDeleteAmcDetails === false) {
    die("Error preparing statement to delete AMC details: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmtDeleteAmcDetails, "i", $id);
$resultDeleteAmcDetails = mysqli_stmt_execute($stmtDeleteAmcDetails);
mysqli_stmt_close($stmtDeleteAmcDetails);

if ($resultDeleteAmcDetails) {
    header("Location: index.php?msg=Data deleted successfully");
} else {
    echo "Failed: " . mysqli_error($conn);
}
?>