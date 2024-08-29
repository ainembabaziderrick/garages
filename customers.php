<?php
session_start();

include 'head.php';
include 'db.php';

$sucessMessage = "";
$errorMessage = "";

if (isset($_POST['addcustomer'])) {
    $name = $_POST['name'];
    
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $garage_id = $_SESSION['garage_id'];
    $business_id = $_SESSION['business_id'];

    // Check if contact or email already exists in the database
    $checkQuery = "SELECT * FROM customers WHERE (contact = '$contact' OR email = '$email') AND garage_id = $garage_id";
    $checkResult = $connection->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // If contact or email already exists, set error message
        $_SESSION['errorMessage'] = "Contact number or email already exists.";
    } else {
        // Add new client to the database if contact or email doesn't exist
        $sql = "INSERT INTO customers (garage_id, name, contact, email, address)
                VALUES ($garage_id, '$name', '$contact', '$email', '$address')";
        $result = $connection->query($sql);

        if ($result) {
            $_SESSION['successMessage'] = "Customer created successfully";
        } else {
            $_SESSION['errorMessage'] = "Customer creation failed: " . $connection->error;
        }
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#add_user";
    header("location: $avoidreload");
    exit();
}

if (isset($_POST['edituser'])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];


    $sql = "UPDATE customers SET name = '$name', contact = '$contact', email = '$email', address = '$address' WHERE id = $id";
    $result = $connection->query($sql);

    if ($result) {
        $_SESSION['successMessage'] = "Customer edited successfully";
    } else {
        $_SESSION['errorMessage'] = "Customer edit failed: " . $connection->error;
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#edit_user";
    header("location: $avoidreload");
    exit();
}
?>



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
                            <h1 class="m-0">Customers</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="customers.php">Customers</a></li>
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
            elseif(isset($_SESSION['errorMessage'])) {
                echo
                "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
        <strong>{$_SESSION['errorMessage']}</strong>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button>
    </div>
            ";
                unset($_SESSION['errorMessage']);
            }
            ?>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#myaddmodal">
                                        <i class="fa fa-plus"></i> Add Customer
                                    </button>


                                    <div class="modal" id="myaddmodal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Customer</h5>

                                                </div>

                                                <div class="modal-body">
                                                    <form method="POST" id="add_user" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                                        <label class="form-label">Full Names<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="fir_name" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        
                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="phone" name="contact" id="contact" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Email<span class="required" style="color: red;"></span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="email" id="email" class="form-control" value="" required>
                                                        </div>


                                                        <label class="form-label">Address<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="address" id="address" class="form-control" value=""
                                                                required>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="addcustomer">Save</button>
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-dismiss="modal">Close</button>

                                                        </div>
                                                    </form>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Full Names</th>
                                                
                                                <th>Phone Number</th>
                                                <th>Email</th>
                                                <th>Address</th>

                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $garage_id = $_SESSION['garage_id'];
                                            //read all row from database table
                                            $sql = "SELECT *
                                           FROM customers where customers.garage_id = $garage_id";
                                            $result = $connection->query($sql);

                                            if (!$result) {
                                                die("Invalid query: " . $connection->error);
                                            }

                                            // read data of each row
                                            $i = 1;
                                            while ($row = $result->fetch_assoc()) : ?>

                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $row['name'] ?></td>
                                                    
                                                    <td><?= $row['contact'] ?></td>
                                                    <td><?= $row['email'] ?></td>
                                                    <td><?= $row['address'] ?></td>


                                                    <td>
                                                        <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>, '<?= $row['name'] ?>', '<?= $row['email'] ?>','<?= $row['contact'] ?>','<?= $row['address'] ?>')">Edit</button>
                                                        <a class='btn btn-danger btn-sm' href='delete_customer.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>Delete</a>
                                                    </td>
                                                </tr>


                                            <?php endwhile ?>
                                        </tbody>

                                    </table>

                                    <div class="modal" id="editmodalw">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Customer</h5>

                                                </div>
                                                <div class="modal-body">

                                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_user">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        
                                                        <label class="form-label">Full Names<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="first_name" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        
                                                        <label class="form-label">Email<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="email" id="emai" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="phone" name="contact" id="conta" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Address<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="address" id="addres" class="form-control" value=""
                                                                required>
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="edituser">Edit</button>
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
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>




        <!-- /.content-wrapper -->
        <?php include 'footer.php'; ?>


    </div>
    
    <script>
        const openeditmodalw = (id, name, email, contact, address) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('first_name').value = name;
            
            document.getElementById('emai').value = email;
            document.getElementById('conta').value = contact;
            document.getElementById('addres').value = address;
        }
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }

        
    </script>

 <?php include 'script.php';?>

<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
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