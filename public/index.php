<?php
include "db_conn.php";

if (isset($_GET["msg"])) {
    $msg = $_GET["msg"];
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
    ' . $msg . '
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
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
</head>
<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
        CRUD Application
    </nav>
    <div class="container">
        <a href="add_new.php" class="btn btn-light mb-3">Add New</a>
        <table class="table table-hover text-center">
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">AMC Name</th>
                    <th scope="col">Initiator Name</th>
                    <th scope="col">AMC Cost</th>
                    <th scope="col">AMC Start Date</th>
                    <th scope="col">AMC End Date</th>
                    <th scope="col">Supplier Name</th>
                    <th scope="col">Supplier Phone</th>
                    <th scope="col">Supplier Email</th>
                    <th scope="col">AMC Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT
                            amc_details.id,
                            amc_details.amcName,
                            amc_details.initiatorName,
                            amc_details.amcCost,
                            amc_details.amcStartDate,
                            amc_details.amcEndDate,
                            amc_details.supplierName,
                            GROUP_CONCAT(DISTINCT phone_numbers.phone_number SEPARATOR ', ') AS phone_numbers,
                            GROUP_CONCAT(DISTINCT emails.email_address SEPARATOR ', ') AS email_addresses,
                            amc_details.amcStatus
                        FROM
                            amc_details
                        LEFT JOIN
                            phone_numbers ON amc_details.id = phone_numbers.amc_id
                        LEFT JOIN
                            emails ON amc_details.id = emails.amc_id
                        GROUP BY
                            amc_details.id";

                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["amcName"] ?></td>
                        <td><?php echo $row["initiatorName"] ?></td>
                        <td><?php echo $row["amcCost"] ?></td>
                        <td><?php echo $row["amcStartDate"] ?></td>
                        <td><?php echo $row["amcEndDate"] ?></td>
                        <td><?php echo $row["supplierName"] ?></td>
                        <td><?php echo $row["phone_numbers"] ?></td>
                        <td><?php echo $row["email_addresses"] ?></td>
                        <td><?php echo $row["amcStatus"] ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row["id"] ?>" class="link-dark"><i class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
                            <a href="delete.php?id=<?php echo $row["id"] ?>" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
