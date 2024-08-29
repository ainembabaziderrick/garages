<?php
session_start();
include 'head.php';
include 'db.php';
$successMessage = "";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['addtocart'])) {
    $user_id = $_SESSION['id'];
    $account_id = $_POST['account_id'];

    $unit_cost = str_replace(",", '', $_POST['unit_cost']);
    $unit_quantity = str_replace(",", '', $_POST['unit_quantity']);

    $checkItem = mysqli_query($connection, "SELECT * FROM cart_items where account_id = $account_id and user_id = '$user_id'");
    if (($checkItem->num_rows) > 1) {
        echo "<script>alert('This item already exists'); history.go(-1);</script>";
    } else {

        // Add new cart_items to the database
        $garage_id = $_SESSION['garage_id'];


        $stmt = $connection->prepare("INSERT INTO cart_items (user_id, account_id, unit_cost, unit_quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iidd", $user_id, $account_id, $unit_cost, $unit_quantity);

        if ($stmt->execute()) {
            $_SESSION['successMessage'] = "Cart created Successfully";
        } else {
            $errorMessage = "Invalid query: " . $connection->error;
        }

        $stmt->close();
        $avoidreload = $_SERVER['PHP_SELF'] . "#add_user";
        header("location: $avoidreload");
        exit();
    }
}

if (isset($_POST['edituser'])) {
    $id = $_POST["id"];
    $account_id = $_POST["account_id"];
    $unit_cost = str_replace(",", '', $_POST["unit_cost"]);
    $unit_quantity = str_replace(",", '', $_POST["unit_quantity"]);

    $stmt = $connection->prepare("UPDATE cart_items SET account_id = ?, unit_cost = ?, unit_quantity = ? WHERE id = ?");
    $stmt->bind_param("iddi", $account_id, $unit_cost, $unit_quantity, $id);

    if ($stmt->execute()) {
        $_SESSION['successMessage'] = "Cart edited Successfully";
    } else {
        $errorMessage = "Invalid query: " . $connection->error;
    }

    $stmt->close();
    $avoidreload = $_SERVER['PHP_SELF'] . "#edit_user";
    header("location: $avoidreload");
    exit();
}

if (isset($_POST['addsale'])) {
    $user_id = $_SESSION['id'];
    $sale_date = $_POST['sale_date'];
    $formatted_date = date("Y-m-d", strtotime($sale_date));
    $customer_id = $_POST['customer_id'];
    $payment_mode_id = $_POST['payment_mode_id'];
    $finalTotal = $_POST['finalTotal'];
    $amount_paid = str_replace(",", '', $_POST['amount_paid']);

    // Start transaction
    $connection->begin_transaction();

    try {

        // Insert into sales with the final total
        $stmt = $connection->prepare("INSERT INTO sales (garage_id, user_id, customer_id, total, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiids", $_SESSION['garage_id'], $user_id, $customer_id, $finalTotal, $formatted_date);
        $stmt->execute();
        $sales_id = $stmt->insert_id;
        $stmt->close();

        // Select all cart items for the user and calculate the final total
        $sqlcart = "SELECT * FROM cart_items WHERE user_id = $user_id";
        $cartResult = $connection->query($sqlcart);

        while ($cartRow = $cartResult->fetch_assoc()) {
            $account_id = $cartRow['account_id'];
            $unit_cost = str_replace(",", '', $cartRow['unit_cost']);
            $unit_quantity = str_replace(",", '', $cartRow['unit_quantity']);
            $total = $unit_cost * $unit_quantity;

            // Accumulate total sum
            $finalTotal += $total;

            // Insert each item into the cart table with the correct sales_id
            $stmt = $connection->prepare("INSERT INTO cart (sales_id, account_id, unit_cost, unit_quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iidd", $sales_id, $account_id, $unit_cost, $unit_quantity);
            $stmt->execute();
            $stmt->close();
        }

        // Insert transaction details
        $stmt = $connection->prepare("INSERT INTO transactions (sales_id, amount_paid, payment_mode_id, sale_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idis", $sales_id, $amount_paid, $payment_mode_id, $formatted_date);
        $stmt->execute();
        $stmt->close();

        // Clear cart items after a successful sale
        $stmt = $connection->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $connection->commit();
        $_SESSION['successMessage'] = "Sale created Successfully";
    } catch (Exception $e) {
        $connection->rollback();
        $errorMessage = "Transaction failed: " . $e->getMessage();
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#add_sales";
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

<!-- dropdown -->
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

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
                            <h1 class="m-0">Making a Sale</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="cart.php">Making a Sale</a></li></li>
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
                        <!-- Left Side: Date and Items Table -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        
                                        Add Cart
                                    </h3>

                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <form method="POST" id="add_user" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <label class="form-label">Item<span class="required" style="color: red;">*</span></label>
                                        <div class="form-group">
                                            <select name="account_id" id="account_id" required class="form-control" style="width: 100%; height: 40px; padding-bottom: 110px;">
                                                <option value="" disabled selected class="">Select item</option>
                                                <?php
                                                $sqlitem = "SELECT id, name FROM accounts";
                                                $Roleresult = $connection->query($sqlitem);
                                                // Check if there are results
                                                if ($Roleresult->num_rows > 0) {
                                                    // Output data of each row
                                                    while ($row = $Roleresult->fetch_assoc()) {
                                                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No item available</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label class="form-label">Unit Cost<span class="required" style="color: red;">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="unit_cost" id="unit_cost_id" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                        </div>
                                        <label class="form-label">Quantity<span class="required" style="color: red;">*</span></label>
                                        <div class="form-group">
                                            <input type="text" name="unit_quantity" id="unit_quantity" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                        </div>


                                        <button type="submit" class="btn btn-primary btn-sm"
                                            name="addtocart">Add to Cart</button>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!-- Right Side: Cart Items, Customer, Payment Mode, and Balance -->

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                       
                                    Cart Items
                                    </h3>
                                   
                                </div><!-- /.card-header -->

                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Cart Items</label>
                                        <table class="table table-bordered" id="cartItemsTable">
                                            <thead>
                                                <tr>

                                                    <th>Item</th>
                                                    <th>Unit Cost</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $user_id = $_SESSION['id'];
                                                // Initialize total sum
                                                $finalTotal = 0;

                                                // Read all rows from the database table
                                                $sql = "SELECT cart_items.*, accounts.name as name
                        FROM cart_items
                        JOIN accounts ON cart_items.account_id = accounts.id
                        WHERE cart_items.user_id = $user_id";
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
                                                        <td>
                                                            <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>, '<?= $row['account_id'] ?>','<?= $row['unit_cost'] ?>', '<?= $row['unit_quantity'] ?>')">Edit</button>
                                                            <a class='btn btn-danger btn-sm' href='/garages/delete_cart.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" style="text-align:right"><strong>Final Total:</strong></td>
                                                    <td><strong><?= number_format($finalTotal) ?></strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>


                                    <form method="POST" id="add_sales" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <div class="form-group">
                                            <input type="hidden" value="<?= $finalTotal; ?>" name="finalTotal">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="sale_date" name="sale_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <label class="form-label">Customer<span class="required" style="color: red;">*</span></label>
                                        <div class="form-group">
                                            <select name="customer_id" id="customer_id" required class="form-control">
                                                <option value="" disabled selected>Select customer</option>
                                                <?php
                                                $garage_id = $_SESSION['garage_id'];
                                                $sqlcustomers = "SELECT id, name FROM customers where garage_id = $garage_id ";
                                                $customersresult = $connection->query($sqlcustomers);
                                                // Check if there are results
                                                if ($customersresult->num_rows > 0) {
                                                    // Output data of each row
                                                    while ($row = $customersresult->fetch_assoc()) {
                                                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No customer available</option>";
                                                }
                                                ?>
                                            </select>
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
                                            <label for="amount_paid">Amount Paid</label>
                                            <input type="text" class="form-control" id="amount_paid" name="amount_paid" min="0" step="0.01" required onkeyup="this.value=addCommas(this.value);">
                                        </div>



                                        <button type="submit" class="btn btn-success btn-sm"
                                            name="addsale">Submit</button>
                                </div>
                            </div>

                        </div>
                        </form>

                    </div>
                </div>
                <div class="modal" id="editmodalw">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Cart</h5>

                            </div>
                            <div class="modal-body">

                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_user">
                                    <input type="hidden" name="id" id="id" value="">
                                    <label class="form-label">Item<span class="required" style="color: red;">*</span></label>
                                    <div class="form-group">
                                        <select name="account_id" id="account_ids" required class="form-control">
                                            <option value="" disabled selected>Select item</option>
                                            <?php
                                            $sqlitem = "SELECT id, name FROM accounts";
                                            $Roleresult = $connection->query($sqlitem);
                                            // Check if there are results
                                            if ($Roleresult->num_rows > 0) {
                                                // Output data of each row
                                                while ($row = $Roleresult->fetch_assoc()) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No item available</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <label class="form-label">Unit Cost<span class="required" style="color: red;">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="unit_cost" id="unit_costs" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                    </div>
                                    <label class="form-label">Unit Quantity<span class="required" style="color: red;">*</span></label>
                                    <div class="form-group">
                                        <input type="text" name="unit_quantity" id="unit_quantitys" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                    </div>


                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-sm"
                                            name="edituser">Edit</button>
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            data-dismiss="modal">Close</button>

                                    </div>
                                </form>

                            </div>

            </section>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzm7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    <script>
        $(document).ready(function() {
            $('#account_id').select2({
                placeholder: "select ",
                allowClear: false,
                selectOnClose: true
            });


            $('#account_id').on('change', function() {
                var id = $(this).val();

                // get unit price


                $.ajax({
                    url: "get_unit_price.php",
                    method: "POST",
                    data: {
                        accountid: id
                    },
                    success: function(data) {
                        $('#unit_cost_id').val(data);
                        getvalue = $('#unit_cost_id').val();

                        console.log(getvalue);

                    },
                    error: function(error) {
                        alert('error while getting unit price');
                    }
                })



            });


        });
    </script>

    <script>
        const openeditmodalw = (id, account_id, unit_cost, unit_quantity) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('account_ids').value = account_id;
            document.getElementById('unit_costs').value = unit_cost;
            document.getElementById('unit_quantitys').value = unit_quantity;

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