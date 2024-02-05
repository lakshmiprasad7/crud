<?php
include "db_conn.php";

if (isset($_POST["submit"])) {
    // Validate and sanitize user inputs here
    $indentName = mysqli_real_escape_string($conn, $_POST['indentName']);
    $indentorName = mysqli_real_escape_string($conn, $_POST['indentorName']);
    $poValue = mysqli_real_escape_string($conn, $_POST['poValue']);
    $pdStartDate = mysqli_real_escape_string($conn, $_POST['pdStartDate']);
    $pdEndDate = mysqli_real_escape_string($conn, $_POST['pdEndDate']);
    $supplierName = mysqli_real_escape_string($conn, $_POST['supplierName']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Prepared statement for 'purchase_details' table
    $stmtPurchase = $conn->prepare("INSERT INTO purchase_details (indentName, indentorName, poValue, pdStartDate, pdEndDate, supplierName, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtPurchase->bind_param("sssssss", $indentName, $indentorName, $poValue, $pdStartDate, $pdEndDate, $supplierName, $status);

    if ($stmtPurchase->execute()) {
        $purchaseId = $stmtPurchase->insert_id;

        // Prepared statement for 'contacts' table
        $stmtContacts = $conn->prepare("INSERT INTO contacts (purchase_id, phone, email) VALUES (?, ?, ?)");
        $stmtContacts->bind_param("iss", $purchaseId, $phone, $emailContact);

        foreach ($_POST['phone'] as $key => $phone) {
            $emailContact = $_POST['email'][$key];
            $stmtContacts->execute();
        }

        header("Location: index.php?msg=New record created successfully");
    } else {
        // Handle error gracefully, potentially redirecting to an error page
        echo "An error occurred while saving data. Please try again later.";
        // Log the error for debugging purposes
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- Bootstrap -->
   
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"  crossorigin="anonymous">
    <style>
        /* Add custom styles for error messages */
        .text-danger {
            color: red;
            margin-top: 5px;
        }
    </style>
   
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Purchase Details</title>
</head>
<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
       Purchase Details Application
   </nav>
   <div class="container">
      <div class="text-center mb-4">
         <h3>Add New User</h3>
         <p class="text-muted">Complete the form below to add a new user</p>
      </div>
      <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 700px;">
            <div class="card-body">

                <h2 class="mt-4 mb-4 text-center" style="color: aqua;">Purchase Details</h2>
                <!-- Display validation errors at the top -->
                <div id="validationErrors" class="text-danger"></div>

                <form name="pdForm" class="form-floating" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validateForm(event)" novalidate>

<!-- Indent Name -->
<div class="form-floating mb-3">
    <input type="text" class="form-control" id="indentName" name="indentName" placeholder="Indent Name" required>
    <label for="indentName">Indent Name</label>
    <div id="indentNameValidationError" class="text-danger mt-1"></div>
</div>

<!-- Indentor Name -->
<div class="form-floating mb-3">
    <input type="text" class="form-control" id="indentorName" name="indentorName" placeholder="Indentor Name" required>
    <label for="indentorName">Indentor Name</label>
    <div id="indentorNameValidationError" class="text-danger mt-1"></div>
</div>

<!-- Purchase Order (PO) Value -->
<div class="form-floating mb-3">
    <input type="text" class="form-control" id="poValue" name="poValue" placeholder="Purchase Order (PO) Value" required>
    <label for="poValue">Purchase Order (PO) Value</label>
    <div id="poValueValidationError" class="text-danger mt-1"></div>
</div>
<!-- PD Starting Date -->
                      <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="pdStartDate" name="pdStartDate" placeholder="PD Starting Date" required>
                        <label for="pdStartDate">PD Starting Date</label>
                        <div id="pdStartDateValidationError" class="text-danger mt-1"></div>
                    </div>

                    <!-- PD Ending Date -->
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="pdEndDate" name="pdEndDate" placeholder="PD Ending Date" required>
                        <label for="pdEndDate">PD Ending Date</label>
                        <div id="pdEndDateValidationError" class="text-danger mt-1"></div>
                    </div>
                        <!-- Supplier Name -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="supplierName" name="supplierName" placeholder="Supplier Name" required>
                        <label for="supplierName">Supplier Name</label>
                        <div id="supplierNameValidationError" class="text-danger mt-1"></div>
                    </div>

                       <!-- Supplier Contact Fields -->
                       <div id="contactFields">
    <!-- Supplier Contact Fields -->
    <label for="contact" class="form-label">PD Supplier Contact:</label>
    <div class="contact-inputs row">
        <!-- Supplier Contact Phone -->
        <div class="form-floating col-md-6 mb-3">
            <input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>
            <label for="phone">Phone Number</label>
            <div id="phoneError" class="text-danger mt-1"></div>
        </div>

        <!-- Supplier Contact Email -->
        <div class="form-floating col-md-6 mb-3">
            <input type="email" class="form-control" name="email[]" placeholder="Email" required>
            <label for="email">Email</label>
            <div id="emailError" class="text-danger mt-1"></div>
        </div>
    </div>

    <!-- Validation Errors -->
    <div id="contactValidationErrors" class="text-danger"></div>
    <div id="generalValidationErrors" class="text-danger"></div>
</div>

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
                            <option value="" disabled selected>Select...</option>
                            <option value="new">New</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="terminated">Terminated</option>
                        </select>
                        <div id="validationError" class="text-danger mt-1"></div>
                    </div>

                        <div class="mb-3">
                        <button type="submit" class="btn btn-success" name="submit">Save</button>
                         <a href="index.php" class="btn btn-danger">Cancel</a>

                         </div>    
                    
                </form>
            </div>
        </div>
    </div>

   </div>
   <script src="script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>