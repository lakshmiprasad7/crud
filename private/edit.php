<?php
include "db_conn.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = $_GET["id"];
$sql = "SELECT * FROM `purchase_details` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Function to check and replace invalid dates
function validateDate($date)
{
    return ($date === '0000-00-00') ? date('Y-m-d') : $date;
}

// Validate and sanitize input function
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {

    $indentName = sanitizeInput($_POST['indentName']);
    $indentorName = sanitizeInput($_POST['indentorName']);
    $poValue = sanitizeInput($_POST['poValue']);
    $pdStartDate = validateDate(sanitizeInput($_POST['pdStartDate']));
    $pdEndDate = validateDate(sanitizeInput($_POST['pdEndDate']));
    $supplierName = sanitizeInput($_POST['supplierName']);
    $status = sanitizeInput($_POST['status']);
    $phoneNumbersArray = array_map('sanitizeInput', $_POST['phone']);
    $emailAddressesArray = array_map('sanitizeInput', $_POST['email']);

    // Update Purchase Details
    $sqlPurchaseDetails = "UPDATE `purchase_details`
                          SET
                            `indentName` = ?,
                            `indentorName` = ?,
                            `poValue` = ?,
                            `pdStartDate` = ?,
                            `pdEndDate` = ?,
                            `supplierName` = ?,
                            `status` = ?
                          WHERE
                            `id` = ?";

    $stmtPurchaseDetails = mysqli_prepare($conn, $sqlPurchaseDetails);

    if ($stmtPurchaseDetails === false) {
        die("Error preparing Purchase Details statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtPurchaseDetails, "ssdssssi", $indentName, $indentorName, $poValue, $pdStartDate, $pdEndDate, $supplierName, $status, $id);
    $executePurchaseDetails = mysqli_stmt_execute($stmtPurchaseDetails);

    if ($executePurchaseDetails === false) {
        die("Error executing Purchase Details statement: " . mysqli_stmt_error($stmtPurchaseDetails));
    }

    updateContactDetails($conn, $phoneNumbersArray, 'phone', $id);
    updateContactDetails($conn, $emailAddressesArray, 'email', $id);

    mysqli_stmt_close($stmtPurchaseDetails);

    header("Location: index.php?msg=Data updated successfully");
    exit();
}

function updateContactDetails($conn, $valuesArray, $field, $id)
{
    $sqlUpdateContact = "UPDATE `contacts` SET `$field` = ? WHERE `purchase_id` = ?";
    $stmtUpdateContact = mysqli_prepare($conn, $sqlUpdateContact);

    if ($stmtUpdateContact === false) {
        die("Error preparing $field statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtUpdateContact, "si", $value, $id);

    foreach ($valuesArray as $value) {
        $executeUpdateContact = mysqli_stmt_execute($stmtUpdateContact);

        if ($executeUpdateContact === false) {
            die("Error executing $field statement: " . mysqli_stmt_error($stmtUpdateContact));
        }
    }

    mysqli_stmt_close($stmtUpdateContact);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Purchase Details</title>
    <style>
        /* Add custom styles for error messages */
        .text-danger {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
        CRUD Application
    </nav>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h2 class="mt-4 mb-4 text-center" style="color: aqua;">Purchase Details</h2>
                <!-- Display validation errors at the top -->
                <div id="validationErrors" class="text-danger"></div>

                <form name="pdForm" class="form-floating" action="edit.php?id=<?php echo $id; ?>" method="post" onsubmit="return validateForm(event)" novalidate>

                    <!-- Indent Name -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="indentName" name="indentName" placeholder="Indent Name" value="<?php echo $row['indentName']; ?>" required>
                        <label for="indentName">Indent Name</label>
                        <div id="indentNameValidationError" class="text-danger mt-1"></div>
                    </div>
                    <!-- Indentor Name -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="indentorName" name="indentorName" placeholder="Indentor Name" value="<?php echo $row['indentorName']; ?>" required>
                        <label for="indentorName">Indentor Name</label>
                        <div id="indentorNameValidationError" class="text-danger mt-1"></div>
                    </div>

                    <!-- Purchase Order (PO) Value -->
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="poValue" name="poValue" placeholder="Purchase Order (PO) Value" value="<?php echo $row['poValue']; ?>" step="0.01" required>
                        <label for="poValue">Purchase Order (PO) Value</label>
                        <div id="poValueValidationError" class="text-danger mt-1"></div>
                    </div>

                    <!-- PD Starting Date -->
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="pdStartDate" name="pdStartDate" placeholder="PD Starting Date" value="<?php echo $row['pdStartDate']; ?>" required>
                        <label for="pdStartDate">PD Starting Date</label>
                        <div id="pdStartDateValidationError" class="text-danger mt-1"></div>
                    </div>

                    <!-- PD Ending Date -->
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="pdEndDate" name="pdEndDate" placeholder="PD Ending Date" value="<?php echo $row['pdEndDate']; ?>" required>
                        <label for="pdEndDate">PD Ending Date</label>
                        <div id="pdEndDateValidationError" class="text-danger mt-1"></div>
                    </div>

                    <!-- Supplier Name -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="supplierName" name="supplierName" placeholder="Supplier Name" value="<?php echo $row['supplierName']; ?>" required>
                        <label for="supplierName">Supplier Name</label>
                        <div id="supplierNameValidationError" class="text-danger mt-1"></div>
                    </div>

                    <!-- Check if 'contacts' key exists and is an array -->
                    <?php if (isset($row['contacts']) && is_array($row['contacts'])) : ?>
                        <!-- Loop through each contact and display input fields -->
                        <?php foreach ($row['contacts'] as $contact) : ?>
                            <div class="contact-inputs row">
                                <!-- Supplier Contact Phone -->
                                <div class="form-floating col-md-6 mb-3">
                                    <input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" value="<?= $contact['phone'] ?? ''; ?>" required>
                                    <label for="phone">Phone Number</label>
                                    <div class="text-danger mt-1"></div>
                                </div>

                                <!-- Supplier Contact Email -->
                                <div class="form-floating col-md-6 mb-3">
                                    <input type="email" class="form-control" name="email[]" placeholder="Email" value="<?= $contact['email'] ?? ''; ?>" required>
                                    <label for="email">Email</label>
                                    <div class="text-danger mt-1"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Display a default set of input fields if 'contacts' is not set or not an array -->
                        <div class="contact-inputs row">
                            <div class="form-floating col-md-6 mb-3">
                                <input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>
                                <label for="phone">Phone Number</label>
                                <div class="text-danger mt-1"></div>
                            </div>
                            <div class="form-floating col-md-6 mb-3">
                                <input type="email" class="form-control" name="email[]" placeholder="Email" required>
                                <label for="email">Email</label>
                                <div class="text-danger mt-1"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Validation Errors -->
                    <div id="contactValidationErrors" class="text-danger"></div>
                    <div id="generalValidationErrors" class="text-danger"></div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-success me-2" onclick="addContact()">
                            <i class="bi bi-plus"></i> Add Contact
                        </button>
                        <button type="button" class="btn btn-danger" onclick="removeContact()">
                            <i class="bi bi-trash"></i> Remove Contact
                        </button>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="" disabled>Select...</option>
                            <option value="new" <?= (isset($row['status']) && $row['status'] === 'new') ? 'selected' : '' ?>>New</option>
                            <option value="ongoing" <?= (isset($row['status']) && $row['status'] === 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
                            <option value="terminated" <?= (isset($row['status']) && $row['status'] === 'terminated') ? 'selected' : '' ?>>Terminated</option>
                        </select>
                        <div id="validationError" class="text-danger mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success" name="submit">Update</button>
                        <a href="index.php" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJ"></script>

</body>

</html>
