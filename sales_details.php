<?php
session_start();
include 'head.php';
include 'db.php';
$successMessage = "";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['track'])) {
    $trackingNo = $_GET['track'];
}
$salesQuery = "SELECT sales.*, customers.* FROM sales, customers WHERE sales.customer_id = customers.id AND sales.id = $trackingNo ";
$salesResult = $connection->query($salesQuery);

if ($salesResult && $salesResult->num_rows > 0) {
    $salesRow = $salesResult->fetch_assoc();
}

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
                            <h1 class="m-0">Sale Details For <?= $salesRow['name'] ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="view_sales.php">Back</a></li>
                                <li class="breadcrumb-item active"><a href="sales_details.php">Sales Details</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if (isset($_SESSION['successMessage'])) {
                echo "
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>{$_SESSION['successMessage']}</strong>
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            ";
                unset($_SESSION['successMessage']);
            }
            ?>

            <!-- Main content -->
            <section class="content">
                <div class="container">
                    <?php
                    echo '
                             <script>
                             function addCommas(x) {
                             //remove commas
                             retVal = x ? parseFloat(x.replace(/,/g, \'\')) : 0;
                             //apply formatting
                             return retVal.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ",");
                             }
                             </script>';
                    ?>
                    <div class="row">

                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">

                                    Items
                                    </h3>

                                </div><!-- /.card-header -->

                                <div class="card-body">
                                   
                                    <table class="table table-bordered" id="cartItemsTable">
                                        <thead>
                                            <tr>

                                                <th>Item</th>
                                                <th>Unit Cost</th>
                                                <th>Quantity</th>
                                                <th>Total</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($_GET['track'])) {
                                                $trackingNo = $_GET['track'];
                                            }
                                            // Initialize total sum
                                            $finalTotal = 0;

                                            // Read all rows from the database table
                                            $sql = "SELECT cart.*, accounts.name as name
                        FROM cart
                        JOIN accounts ON cart.account_id = accounts.id
                        where sales_id = $trackingNo ";
                                            $result = $connection->query($sql);

                                            if (!$result) {
                                                die("Invalid query: " . $connection->error);
                                            }

                                            // Read data of each row
                                            while ($row = $result->fetch_assoc()) :
                                                $total = $row['unit_cost'] * $row['unit_quantity'];
                                                // Accumulate total sum
                                                $finalTotal += $total;
                                            ?>
                                                <tr>

                                                    <td><?= $row['name'] ?></td>
                                                    <td><?= number_format($row['unit_cost']) ?></td>
                                                    <td><?= number_format($row['unit_quantity']) ?></td>
                                                    <td><?= number_format($total) ?></td>

                                                </tr>
                                            <?php endwhile ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="text-align:right"><strong>Final Total:</strong></td>
                                                <td><strong><?= number_format($finalTotal) ?></strong></td>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                    Transactions
                                    </h3>

                                </div><!-- /.card-header -->

                                <div class="card-body">
                                   
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>

                                                <th>Sale Date</th>
                                                <th>Amount Paid</th>
                                                <th>Balance</th>
                                                <th>Mode</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($_GET['track'])) {
                                                $trackingNo = $_GET['track'];
                                            }
                                            // Initialize total sum
                                            $finalTotal = $finalTotal;

                                            // Read all rows from the database table
                                            $sql = "SELECT transactions.*, payment_modes.mode as mode
                                                FROM transactions
                                                JOIN payment_modes ON transactions.payment_mode_id = payment_modes.id
                                                where sales_id = $trackingNo ";
                                            $result = $connection->query($sql);

                                            if (!$result) {
                                                die("Invalid query: " . $connection->error);
                                            }

                                            // Read data of each row
                                            $paid = 0;
                                            while ($row = $result->fetch_assoc()) :
                                                $paid = $paid + $row['amount_paid'];
                                                $balance = $finalTotal - $paid;
                                                $track = $row['id'];

                                            ?>
                                                <tr>

                                                    <td><?= $row['sale_date'] ?></td>
                                                    <td><?= number_format($row['amount_paid']) ?></td>
                                                    <td><?= number_format($balance) ?></td>
                                                    <td><?= $row['mode'] ?></td>
                                                    <td>
                                                        <a href="#" onclick="openPrintPreview('receipt.php?track=<?= $track ?>', 'sales_details.php?track=<?= $trackingNo; ?>' )" class="btn btn-success btn-sm">Receipt</a>
                                                        
                                                        <a class='btn btn-danger btn-sm' href='deletes/delete_sale_detail.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>Delete</a>
                                                    </td>
                                                </tr>
                                            <?php
                                            endwhile
                                            ?>
                                        </tbody>

                                        <script>
                                            function openPrintPreview(url, redirectUrl) {
                                                var printWindow = window.open(url, '_blank');

                                                // Set up the onload event to trigger the print dialog
                                                printWindow.onload = function() {
                                                    printWindow.print();
                                                };

                                                // Listen for the afterprint event to redirect
                                                printWindow.onafterprint = function() {
                                                    printWindow.close(); // Close the print window
                                                    // Redirect the parent window
                                                    window.location.href = redirectUrl;
                                                    // history.go(-1)
                                                };
                                            }
                                        </script>
                                    </table>


                                </div>
                            </div>

                        </div>
                        <div class="modal" id="editmodalw">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Transaction</h5>

                                    </div>
                                    <div class="modal-body">

                                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_user">
                                            <input type="hidden" name="id" id="id" value="">
                                            <label class="form-label">Amount Paid<span class="required" style="color: red;">*</span></label>
                                            <div class="form-group">
                                                <input type="text" name="amount_paid" id="amount_paids" class="form-control" value=""
                                                    required>
                                            </div>

                                            <label class="form-label">Payment Mode<span class="required" style="color: red;">*</span></label>
                                            <div class="form-group">
                                                <select name="mode" id="modes" required class="form-control">
                                                    <option value="" disabled selected>Select payment mode</option>
                                                    <?php

                                                    $sqlmode = "SELECT id, mode FROM payment_modes  ";
                                                    $moderesult = $connection->query($sqlmode);
                                                    // Check if there are results
                                                    if ($moderesult->num_rows > 0) {
                                                        // Output data of each row
                                                        while ($row = $moderesult->fetch_assoc()) {
                                                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['mode']) . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No customer available</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="date">Date </label>
                                                <input type="date" class="form-control" id="sale_dates" name="sale_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                                            </div>



                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary btn-sm"
                                                    name="pay">Edit</button>
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-dismiss="modal">Close</button>


                                            </div>
                                        </form>

                                    </div>

                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>





            </section>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div>

    <?php include 'script.php'; ?>

    <script>
        const openeditmodalw = (id, sale_dates, amount_paid, mode) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('sale_dates').value = sale_date;
            document.getElementById('amount_paid').value = amount_paid;
            document.getElementById('modes').value = mode;

        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }

        document.getElementById('edit_user').onsubmit = function() {
            const unitCost = document.getElementById('unit_costs').value;
            const unitQuantity = document.getElementById('unit_quantitys').value;

            if (unitCost < 1 || unitQuantity < 1) {
                alert('Unit cost and quantity must be at least 1.');
                return false; // Prevent form submission
            }
            return true;
        };

        document.getElementById('add_user').onsubmit = function() {
            const unitCost = document.getElementById('unit_cost').value;
            const unitQuantity = document.getElementById('unit_quantity').value;

            if (unitCost < 1 || unitQuantity < 1) {
                alert('Unit cost and quantity must be at least 1.');
                return false; // Prevent form submission
            }
            return true;
        };
    </script>

</body>