<?php
include "db_conn.php";

$id = $_GET["id"];

if (isset($_POST["submit"])) {
    $amcName = $_POST['amcName'];
    $initiatorName = $_POST['initiatorName'];
    $amcCost = $_POST['amcCost'];
    $amcStartDate = $_POST['amcStartDate'];
    $amcEndDate = $_POST['amcEndDate'];
    $supplierName = $_POST['supplierName'];
    $amcStatus = $_POST['amcStatus'];
    $phoneNumbersArray = $_POST['phone'];
    $emailAddressesArray = $_POST['email'];

    $sqlAmcDetails = "UPDATE `amc_details` 
                      SET 
                        `amcName` = ?, 
                        `initiatorName` = ?, 
                        `amcCost` = ?, 
                        `amcStartDate` = ?, 
                        `amcEndDate` = ?, 
                        `supplierName` = ?, 
                        `amcStatus` = ?
                      WHERE 
                        id = ?";

    $stmtAmcDetails = mysqli_prepare($conn, $sqlAmcDetails);
    mysqli_stmt_bind_param($stmtAmcDetails, "sssssssi", $amcName, $initiatorName, $amcCost, $amcStartDate, $amcEndDate, $supplierName, $amcStatus, $id);
    mysqli_stmt_execute($stmtAmcDetails);

    // Update phone numbers
    $sqlUpdatePhone = "UPDATE `phone_numbers` SET `phone_number` = ? WHERE `amc_id` = ?";
    $stmtUpdatePhone = mysqli_prepare($conn, $sqlUpdatePhone);
    mysqli_stmt_bind_param($stmtUpdatePhone, "si", $phoneNumber, $id);

    foreach ($phoneNumbersArray as $phoneNumber) {
        mysqli_stmt_execute($stmtUpdatePhone);
    }

    // Update email addresses
    $sqlUpdateEmail = "UPDATE `emails` SET `email_address` = ? WHERE `amc_id` = ?";
    $stmtUpdateEmail = mysqli_prepare($conn, $sqlUpdateEmail);
    mysqli_stmt_bind_param($stmtUpdateEmail, "si", $emailAddress, $id);

    foreach ($emailAddressesArray as $emailAddress) {
        mysqli_stmt_execute($stmtUpdateEmail);
    }

    mysqli_stmt_close($stmtAmcDetails);
    mysqli_stmt_close($stmtUpdatePhone);
    mysqli_stmt_close($stmtUpdateEmail);

    header("Location: index.php?msg=Data updated successfully");
} else {
    echo "Failed to submit form.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- Bootstrap -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Add custom styles for error messages */
        .text-danger {
            color: red;
            margin-top: 5px;
        }
    </style>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>CRUD OPERATIONS</title>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
       CRUD Application
   </nav>
   <div class="container">
    <div class="text-center mb-4">
      <h3>Edit User Information</h3>
      <p class="text-muted">Click update after changing any information</p>
    </div>
      <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 700px;">
            <div class="card-body">

                <h2 class="mt-4 mb-4 text-center" style="color: aqua;">Annual Maintenance Contract Details</h2>

                <!-- Add a loading spinner -->
                <div id="loadingSpinner" style="display: none;">Loading...</div>

                <!-- Display validation errors at the top -->
                <div id="validationErrors" class="text-danger"></div>

                <form id="amcForm" action="process_form.php" onsubmit="return validateForm()" method="post" novalidate>
                    <fieldset>
                        <!-- ... (existing form fields) ... -->
                        <div class="mb-3">
                            <label for="amcName" class="form-label">AMC Name</label>
                            <input type="text" class="form-control" id="amcName" name="amcName" maxlength="30"required>
                            <div id="amcNameError" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <label for="initiatorName" class="form-label">AMC Initiator Name</label>
                            <input type="text" class="form-control" id="initiatorName" name="initiatorName"
                                maxlength="30" required>
                            <div id="initiatorNameError" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <label for="amcCost" class="form-label">AMC Cost</label>
                            <input type="number" class="form-control" id="amcCost" name="amcCost" required>
                            <div id="amcCostError" class="text-danger"></div>
                        </div>

                         <div class="mb-3">
                           
                            <label for="amcStartDate" class="form-label">AMC Starting Date</label>
                            <input type="date" class="form-control" id="amcStartDate" name="amcStartDate" min="<?php echo date('Y-m-d'); ?>" required>
                            <div id="amcStartDateError" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            
                            <label for="amcEndDate" class="form-label">AMC Ending Date</label>
                            <input type="date" class="form-control" id="amcEndDate" name="amcEndDate" required>
                            <div id="amcEndDateError" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="supplierName" class="form-label">AMC Supplier Name</label>
                            <input type="text" class="form-control" id="supplierName" name="supplierName"
                                maxlength="30" required>
                            <div id="supplierNameError" class="text-danger"></div>
                        </div>

                        <div class="supplier-fields" id="contactFields">
                            <label for="contact" class="form-label">AMC Supplier Contact:</label>
                            <div class="contact-inputs row">
                                <div class="col-md-6 mb-3">
                                    <input type="tel" class="form-control" id="phone" name="phone[]" pattern="[0-9]{10}"
                                        placeholder="Phone Number" required>
                                    <div id="phoneError" class="text-danger"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control" id="email" name="email[]"
                                        placeholder="Email" required>
                                    <div id="emailError" class="text-danger"></div>
                                </div>
                            </div>
                            <div id="contactValidationErrors" class="text-danger"></div>
                            <div id="generalValidationErrors" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-success me-2" onclick="addContact()">
                                <i class="bi bi-plus"></i> +
                            </button>
                            <button type="button" class="btn btn-danger" onclick="removeContact()">
                                <i class="bi bi-trash"></i>-
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="amcStatus" class="form-label"> AMC Status</label>
                            <select class="form-select" id="amcStatus" name="amcStatus" required>
                               
                                <option value="" disabled selected>Select...</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div id="amcStatusError" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-success">Save</button>
                        <a href="index.php" class="btn btn-danger">Cancel</a>

                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

   </div>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script type="text/javascript">
        // Your JavaScript code goes here
        function addContact() {
            // ... (existing addContact logic) ...
            const contactFields = document.getElementById('contactFields');
    const newContact = document.createElement('div');

    newContact.innerHTML =
        '<div class="contact-inputs row">' +
        '<div class="col-md-6 mb-3">' +
        '<input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>' +
        '</div>' +
        '<div class="col-md-6 mb-3">' +
        '<input type="email" class="form-control" name="email[]" placeholder="Email" required>' +
        '</div>' +
        '</div>';

    contactFields.appendChild(newContact);
        }

        function removeContact() {
            // ... (existing removeContact logic) ...
            const contactFields = document.getElementById('contactFields');
    const lastContact = contactFields.lastElementChild;

    if (lastContact) {
        contactFields.removeChild(lastContact);
    }
        }

        function getTodaysDate() {
            // ... (existing getTodaysDate logic) ...
            const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    return today;
        }

        document.getElementById('amcStartDate').addEventListener('change', function () {
            // ... (existing amcStartDate change event logic) ...
            const selectedDate = new Date(this.value);
    if (selectedDate < getTodaysDate()) {
        alert('Please select a date equal to or after today.');
        this.value = ''; // Reset the input value
    }
        });

        document.getElementById('amcEndDate').addEventListener('change', function () {
            // ... (existing amcEndDate change event logic) ...
            const selectedDate = new Date(this.value);
    if (selectedDate < getTodaysDate()) {
        alert('Please select a date equal to or after today.');
        this.value = ''; // Reset the input value
    }
        });

        function setError(element, message) {
            // ... (existing setError logic) ...
            element.innerHTML = message;
        }

        function clearError(element) {
            // ... (existing clearError logic) ...
            element.innerHTML = '';
        }

        async function validateField(value, fieldName, minLength) {
            const errorElement = document.getElementById(`${fieldName}Error`);
            if (value.trim() === '') {
                setError(errorElement, `*Please enter ${fieldName}`);
                return false;
            } else if (value.length < minLength) {
                setError(errorElement, `*${fieldName} must contain at least ${minLength} characters`);
                return false;
            } else {
                clearError(errorElement);
                return true;
            }
        }

        async function validateForm(event) {
            // Clear previous validation errors
            document.getElementById('validationErrors').innerHTML = '';
            document.getElementById('generalValidationErrors').innerHTML = '';

            const amcName = document.getElementById('amcName').value;
            const initiatorName = document.getElementById('initiatorName').value;
            const supplierName = document.getElementById('supplierName').value;
            const amcCost = document.getElementById('amcCost').value;
            const amcStatus = document.getElementById('amcStatus').value;
            const amcStartDate = document.getElementById('amcStartDate').value;
            const amcEndDate = document.getElementById('amcEndDate').value;
            const phoneInputs = document.getElementsByName('phone[]');
            const emailInputs = document.getElementsByName('email[]');

            let isValid = true;

            isValid = isValid && await validateField(amcName, 'amcName', 6);
            isValid = isValid && await validateField(initiatorName, 'initiatorName', 6);
            isValid = isValid && await validateField(supplierName, 'supplierName', 6);
            isValid = isValid && await validateField(amcCost, 'amcCost', 1); // Adjust the minimum length as needed
            isValid = isValid && await validateField(amcStatus, 'amcStatus', 1); // Adjust the minimum length as needed

            // Validate AMC Starting Date
            const startDateValidation = await validateField(amcStartDate, 'amcStartDate', 1);
    if (startDateValidation) {
        const selectedDate = new Date(amcStartDate);
        if (selectedDate < getTodaysDate()) {
            setError(document.getElementById('amcStartDateError'), '*Please select a date equal to or after today.');
            isValid = false;
        } else {
            clearError(document.getElementById('amcStartDateError'));
        }
    } else {
        isValid = false;
    }


const endDateValidation = await validateField(amcEndDate, 'amcEndDate', 1);
    if (endDateValidation) {
        const selectedDate = new Date(amcEndDate);
        if (selectedDate < getTodaysDate()) {
            setError(document.getElementById('amcEndDateError'), '*Please select a date equal to or after today.');
            isValid = false;
        } else {
            clearError(document.getElementById('amcEndDateError'));
        }
    } else {
        isValid = false;
    }

            // Validate AMC Supplier Contact
            const contactValidationErrors = document.getElementById('contactValidationErrors');
            contactValidationErrors.innerHTML = ''; // Clear previous errors

            // Check all phone numbers and email addresses
            for (const phoneInput of phoneInputs) {
                isValid = isValid && await validateField(phoneInput.value, 'phone', 10);
            }

            for (const emailInput of emailInputs) {
                isValid = isValid && await validateField(emailInput.value, 'email', 1);
            }

            if (!isValid) {
        // Display a general validation error message
        document.getElementById('validationErrors').innerHTML = 'Please fill in all details.';
        if (event) {
            event.preventDefault();
        }
    } else {
        // Your form submission logic goes here

        // For demonstration purposes, you can log the form data to the console
        console.log('Form submitted successfully!');

        // In a real scenario, you might want to perform an AJAX request or other actions
    }
}

document.getElementById('amcForm').addEventListener('submit', function (event) {
    validateForm(event);
});
    </script>
</body>
</html>