<?php
include "db_conn.php";
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

    <title>Purchase Details Database</title>
</head>
<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
        Purchase Details Application
    </nav>
    <div class="container">
        <?php
        if (isset($_GET["msg"])) {
            $msg = $_GET["msg"];
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            ' . $msg . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
        ?>
        <a href="add_new.php" class="btn btn-light mb-3">Add New</a>
        <table class="table table-striped text-center">
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Indent Name</th>
                    <th scope="col">Indentor Name</th>
                    <th scope="col">PO Value</th>
                    <th scope="col">PD Start Date</th>
                    <th scope="col">PD End Date</th>
                    <th scope="col">Supplier Name</th>
                    <th scope="col">Supplier Phone</th>
                    <th scope="col">Supplier Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT
                    pd.id,
                    pd.indentName,
                    pd.indentorName,
                    pd.poValue,
                    DATE_FORMAT(pd.pdStartDate, '%M %d, %Y') AS formattedStartDate,
                    DATE_FORMAT(pd.pdEndDate, '%M %d, %Y') AS formattedEndDate,
                    pd.supplierName,
                    GROUP_CONCAT(DISTINCT c.phone ORDER BY c.id SEPARATOR ', ') AS phone_numbers,
                    GROUP_CONCAT(DISTINCT c.email ORDER BY c.id SEPARATOR ', ') AS email_addresses,
                    pd.status
                    FROM
                    purchase_details pd
                    LEFT JOIN
                    contacts c ON pd.id = c.purchase_id
                    GROUP BY
                    pd.id";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("Error in SQL query: " . mysqli_error($conn));
                }
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row["id"] ?></td>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($row["indentName"]) ?></span></td>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars($row["indentorName"]) ?></span></td>
                        <td><span class="badge bg-success"><?php echo htmlspecialchars($row["poValue"]) ?></span></td>
                        <td><span class="badge bg-warning text-dark"><?php echo $row["formattedStartDate"] ?></span></td>
                        <td><span class="badge bg-danger"><?php echo $row["formattedEndDate"] ?></span></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row["supplierName"]) ?></span></td>
                        <td><span class="badge bg-dark"><?php echo nl2br($row["phone_numbers"]) ?></span></td>
                        <td><span class="badge bg-light text-dark"><?php echo nl2br($row["email_addresses"]) ?></span></td>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars($row["status"]) ?></span></td>
                        <td>
                        <a href="edit.php?id=<?php echo $row["id"] ?>" class="btn btn-warning"><i class="fas fa-pen-to-square me-2"></i>Edit</a>

                            <a href="delete.php?id=<?php echo $row["id"] ?>" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete</a>
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
