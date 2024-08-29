<?php
session_start();
include 'head.php';

include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sucessMessage = "";
if (isset($_POST['addemployee'])) {

    $user_id = $_SESSION['id'];
    $name = $_POST['name'];

    $unit_cost = str_replace(",", '', $_POST['unit_cost']);
    $unit_quantity = str_replace(",", '', $_POST['unit_quantity']);

    $sql_category_id = "SELECT id FROM category WHERE name = 'service'";
    $query = $connection->query($sql_category_id);

    if ($query->num_rows > 0) {
        // Category exists, get the existing category ID
        $row = $query->fetch_assoc();
        $category_id = $row['id'];
    } else {
        // Category does not exist, insert new category
        $sql_insert = "INSERT INTO category (name) VALUES ('service')";
        if ($connection->query($sql_insert) === TRUE) {
            // Get the last inserted ID
            $category_id = $connection->insert_id;
        } else {
            die("Error inserting category: " . $connection->error);
        }
    }

    do {

        // add new client to the database

        $garage_id = $_SESSION['garage_id'];
        $business_id = $_SESSION['business_id'];
        $sql = "INSERT INTO accounts (garage_id, user_id ,category_id,name,unit_cost,unit_quantity)
        VALUES ($garage_id, $user_id, $category_id,'$name',$unit_cost,$unit_quantity)";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        if ($result) {
            $_SESSION['successMessage'] = "Spare created Successfully";
        }

        $avoidreload = $_SERVER['PHP_SELF'] . "#add_user";
        header("location: $avoidreload");
        exit();
    } while (false);
}




if (isset($_POST['edituser'])) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $unit_cost = str_replace(",", '', $_POST['unit_cost']);

    $unit_quantity = $_POST["unit_quantity"];

    $sql = "UPDATE accounts SET name = '$name', unit_cost = '$unit_cost', unit_quantity = '$unit_quantity'
where id = $id";
    $result = $connection->query($sql);
    if ($result) {
        $_SESSION['successMessage'] = "Spare edited Successfully";
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
                            <h1 class="m-0">Services</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="services.php">Services</a></li>
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

                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#myaddmodal">
                                        <i class="fa fa-plus"></i> Add Service
                                    </button>


                                    <div class="modal" id="myaddmodal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Service</h5>

                                                </div>

                                                <div class="modal-body">
                                                    <form method="POST" id="add_user" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                                        <label class="form-label">Service<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="name" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        <label class="form-label">Unit Cost<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="unit_cost" id="unit_cost" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                                        </div>
                                                        <label class="form-label">Quantity<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="unit_quantity" id="unit_quantity" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="addemployee">Save</button>
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
                                                <th>Service</th>
                                                <th>Added By</th>
                                                <th>Unit Cost</th>
                                                <th>Quantity</th>

                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            //working on category
                                            $sql_category = "SELECT id FROM category WHERE name = 'service'";
                                            $query1 = $connection->query($sql_category);

                                            $row = $query1->fetch_assoc();
                                            $category = $row['id'];

                                            $garage_id = $_SESSION['garage_id'];
                                            //read all row from database table
                                            $sql = "SELECT accounts.*,
                                            users.username as username 
                                            
                                             FROM users,accounts where users.id = accounts.user_id and accounts.garage_id = $garage_id and accounts.category_id = $category";
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
                                                    <td><?= $row['username'] ?></td>
                                                    <td><?= number_format($row['unit_cost']) ?></td>
                                                    <td><?= number_format($row['unit_quantity']) ?></td>



                                                    <td>
                                                        <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>, '<?= $row['name'] ?>','<?= number_format($row['unit_cost']) ?>', '<?= $row['unit_quantity'] ?>')">Edit</button>
                                                        <a class='btn btn-danger btn-sm' href='/garages/delete_service.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>Delete</a>
                                                    </td>
                                                </tr>


                                            <?php endwhile ?>
                                        </tbody>

                                    </table>

                                    <div class="modal" id="editmodalw">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit User</h5>

                                                </div>
                                                <div class="modal-body">

                                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_user">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <label class="form-label">Spare Part<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="names" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        <label class="form-label">Unit Cost<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="unit_cost" id="unit_costs" class="form-control" value="" required onkeyup="this.value=addCommas(this.value);">
                                                        </div>
                                                        <label class="form-label">Quantity<span class="required" style="color: red;">*</span></label>
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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
        const openeditmodalw = (id, name, unit_cost, unit_quantity) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('names').value = name;
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