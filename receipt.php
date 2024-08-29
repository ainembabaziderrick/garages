<?php
session_start();
include 'head.php';
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar.php'; ?>
        <!-- Main Sidebar Container -->
        <?php include 'sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Print Receipt</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active"><?= $_SESSION['email'] ?? "Not Logged In" ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display Success Message -->
            <?php if (isset($_SESSION['successMessage'])): ?>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <strong><?= $_SESSION['successMessage'] ?></strong>
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['successMessage']); ?>
            <?php endif; ?>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <!-- <div class="card-header">
                                    <a href="view_sales.php" class="btn btn-danger btn-sm float-end">Back</a>
                                </div> -->
                                <div class="card-body">
                                    <div id="myBillingArea">
                                        <?php
                                        if (isset($_GET['track'])) {
                                            $trackingNo = $_GET['track'];

                                            if (!$trackingNo) {
                                                echo '<div class="text-center py-5">
                                                    <h5>No Sale Record</h5>
                                                    <a href="view_sales.php" class="btn btn-primary mt-4 w-25">Back to Sales</a>
                                                  </div>';
                                            } else {
                                                // Fetch sales data
                                                $salesQuery = "SELECT sales.*, customers.*,transactions.* FROM sales, customers, transactions WHERE sales.customer_id = customers.id AND transactions.sales_id = sales.id AND transactions.id = $trackingNo LIMIT 1";
                                                $salesResult = $connection->query($salesQuery);

                                                if ($salesResult && $salesResult->num_rows > 0) {
                                                    $salesRow = $salesResult->fetch_assoc();
                                                    $sale_id = $salesRow['sales_id'];
                                        ?>
                                                    <table class="table mb-4">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2" class="text-center">
                                                                    <h4>GARAGE MONITOR</h4>
                                                                    <p><?= $_SESSION['garage_name'] ?? "Not In a garage" ?></p>
                                                                    <p><?= $_SESSION['garage_address'] ?></p>
                                                                    <p>Phone Number: <?= $_SESSION['garage_contact'] ?></p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h4>Customer Details</h4>
                                                                    <p>Customer Name: <?= $salesRow['name'] ?></p>
                                                                    <p>Customer Phone No.: <?= $salesRow['contact'] ?></p>
                                                                    <p>Customer Email: <?= $salesRow['email'] ?></p>
                                                                </td>
                                                                <td class="text-right">
                                                                    <h4>Receipt Details</h4>
                                                                    <p>Receipt Number: <?= $salesRow['id'] ?></p>
                                                                    <p>Receipt Date: <?= $salesRow['date'] ?></p>
                                                                    <p>Address: <?= $salesRow['address'] ?></p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <?php

                                                    // Fetch sale items
                                                    $saleItemQuery = "SELECT cart.*, accounts.name as name
                                                                FROM cart
                                                                JOIN accounts ON cart.account_id = accounts.id
                                                                where sales_id = $sale_id  ";
                                                    $saleItemResult = $connection->query($saleItemQuery);

                                                    if ($saleItemResult && $saleItemResult->num_rows > 0) {
                                                        $totalAmount = 0;
                                                    ?>
                                                        <div class="table-responsive mb-3">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Product Name</th>
                                                                        <th>Price</th>
                                                                        <th>Quantity</th>
                                                                        <th>Total Price</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $i = 1;
                                                                    while ($row = $saleItemResult->fetch_assoc()) {
                                                                        $totalPrice = $row['unit_cost'] * $row['unit_quantity'];
                                                                        $totalAmount += $totalPrice;
                                                                    ?>
                                                                        <tr>
                                                                            <td><?= $i++; ?></td>
                                                                            <td><?= $row['name']; ?></td>
                                                                            <td><?= number_format($row['unit_cost'], 0); ?></td>
                                                                            <td><?= $row['unit_quantity']; ?></td>
                                                                            <td><strong><?= number_format($totalPrice, 0); ?></strong></td>
                                                                        </tr>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td colspan="4" class="text-right font-weight-bold">Grand Total:</td>
                                                                        <td class="font-weight-bold"><?= number_format($totalAmount, 0); ?></td>
                                                                    </tr>

                                                                </tbody>
                                                            </table>
                                                            <?php

                                                            if (isset($_GET['track'])) {
                                                                $trackingNo = $_GET['track'];
                                                            }
                                                            $paid = 0;
                                                            $totalAmount = $totalAmount;

                                                            $sqltransact = "SELECT SUM(amount_paid) AS totalPaid, 
                                                                            (SELECT amount_paid FROM transactions WHERE id = MAX(t.id)) AS last_amount_paid
                                                                            FROM transactions t WHERE sales_id = '$sale_id' ";
                                                            $resultTransact = $connection->query($sqltransact);



                                                            $transactRow = $resultTransact->fetch_assoc();
                                                            $paid = $transactRow['last_amount_paid'];
                                                            $totalPaid = $transactRow['totalPaid'];
                                                            $balance = $totalAmount - $totalPaid; ?>

                                                            <h4>Total Billed: <?= number_format($totalAmount) ?> </h4>
                                                            <h4>Amount Paid: <?= number_format($paid) ?> </h4>
                                                            <h4>Total Paid: <?= number_format($totalPaid) ?> </h4>
                                                            <h4> Balance : <?= number_format($balance) ?></h4>
                                                        
                                                        </div>
                                        <?php
                                                    } else {
                                                        echo '<h5>No items found for this sale.</h5>';
                                                    }
                                                } else {
                                                    echo '<h5>No data found.</h5>';
                                                }
                                            }
                                        } else {
                                            echo '<div class="text-center py-5">
                                                <h5>No Sale Record</h5>
                                                <a href="view_sales.php" class="btn btn-primary mt-4 w-25">Back to Sales</a>
                                              </div>';
                                        }
                                        ?>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->

    </div>


    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }


        window.print();
    </script>
</body>

</html>