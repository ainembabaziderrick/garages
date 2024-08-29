<?php
session_start();
include 'head.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if (isset($_POST['pay'])) {
    $sale_date = $_POST['sale_date'];
    $payment_mode_id = $_POST['payment_mode_id'];
    $orig_balance = $_POST['original_amount'];
    $amount = str_replace(",", '', $_POST['amount']);


    // Fetch sales_id for the given sale
    $sale_id = $_POST['id'];
    $sqlsales = "SELECT id FROM sales WHERE id = ?";
    if ($stmt = $connection->prepare($sqlsales)) {
        $stmt->bind_param("i", $sale_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($sales_id);
            $stmt->fetch();
            $stmt->close();

            // Insert the payment into transactions table
            $sql = "INSERT INTO transactions (sales_id, amount_paid, payment_mode_id, sale_date) VALUES (?, ?, ?, ?)";
            if ($stmt = $connection->prepare($sql)) {
                $stmt->bind_param("idss", $sales_id, $amount, $payment_mode_id, $sale_date);
                $result = $stmt->execute();

                if ($result) {
                    $_SESSION['successMessage'] = "Balance Updated Successfully";
                } else {
                    $_SESSION['errorMessage'] = "Error updating balance";
                }
                $stmt->close();
            } else {
                $_SESSION['errorMessage'] = "Error preparing statement";
            }
        } else {
            $_SESSION['errorMessage'] = "Sale ID not found";
        }
    } else {
        $_SESSION['errorMessage'] = "Error preparing statement";
    }

    $salesQuery = "SELECT sales.*, customers.* FROM sales, customers WHERE sales.customer_id = customers.id AND sales.id = $trackingNo ";
    $salesResult = $connection->query($salesQuery);

    if ($salesResult && $salesResult->num_rows > 0) {
        $salesRow = $salesResult->fetch_assoc();
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#edit_user";
    header("location: $avoidreload");
    exit();
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">


        <!-- Navbar -->
        <?php include 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'sidebar.php'; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Sales List</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="view_sales.php">Sales Page</a></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <?php
            if (isset($_SESSION['successMessage'])) {
                echo
                "
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
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <div class="card">
                                <div class="card-header">

                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Customer</th>
                                                    <th>Total Billed</th>
                                                    <th>Amount Paid</th>
                                                    <th>Balance</th>
                                                    <th>Sale Date</th>
                                                    <th>Sold By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch the sales data with the amount paid for each sale
                                                $garage_id = $_SESSION['garage_id'];

                                                $sql = "SELECT *,(SELECT SUM(amount_paid) from transactions where sales_id=sales.id)as paid,(SELECT name FROM customers where id=sales.customer_id) as customer_name,
                                                (SELECT username FROM users where id=sales.user_id) as users_name from sales WHERE garage_id = $garage_id";

                                                $result = $connection->query($sql);

                                                // Read data of each row
                                                $i = 1;
                                                while ($row = $result->fetch_assoc()) :
                                                    $balance = $row['total'] - $row['paid']; ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td><?= $row['customer_name'] ?></td>
                                                        <td>
                                                            <?= number_format($row['total'], 0) ?>
                                                        </td>
                                                        <td><?= number_format($row['paid'], 0) ?></td>
                                                        <td><?= number_format($balance, 0) ?></td>
                                                        <td><?= $row['date'] ?></td>
                                                        <td><?= $row['users_name'] ?></td>
                                                        <td>
                                                            <a href="sales_details.php?track=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Details</a>



                                                            <?php if ($balance > 0): ?>
                                                                <button class="btn btn-info btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>, '<?= number_format($balance) ?>')">Pay Balance</button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>


                                        </table>


                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>



        <div class="modal" id="editmodalw">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pay Balance</h5>

                    </div>
                    <div class="modal-body">

                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_user">
                            <input type="hidden" name="id" id="id" value="">
                            <label class="form-label">Due Amount<span class="required" style="color: red;">*</span></label>
                            <div class="form-group">
                                <input type="hidden" name="original_amount" class="form-control" value="<?= $balance ?>" min="1" max="1"
                                    required>

                                <input type="text" name="amount" id="amounts" class="form-control" value="<?= $balance ?>" min="1" max="1"
                                    required>
                            </div>

                            <label class="form-label">Payment Mode<span class="required" style="color: red;">*</span></label>
                            <div class="form-group">
                                <select name="payment_mode_id" id="payment_mode_id" required class="form-control">
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
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                            </div>



                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm"
                                    name="pay">Pay</button>
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-dismiss="modal">Close</button>


                            </div>
                        </form>

                    </div>



                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.card -->
       
        <!-- Modal Structure -->
        <div class="modal fade" id="detailsmodal" tabindex="-1" role="dialog" aria-labelledby="detailsmodalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ITEMS BOUGHT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Your card and table content here -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Item Details</h3>
                            </div>
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

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" style="text-align:right"><strong>Final Total:</strong></td>
                                            <td><strong></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>



    </div>
    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- modal end -->




    <!-- /.content-wrapper -->
    <?php include 'footer.php'; ?>


    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
        const openeditmodalw = (id, amount) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('amounts').value = amount;

        }

        function openmodaldetails(id) {
            $('#detailsmodal').modal('show');
            document.getElementById('id').value = id;

        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }

        const saledetails = (id, amount) => {

            $('#sale_details').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('amounts').value = amount;

        }
    </script>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>