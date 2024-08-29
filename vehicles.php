<?php
session_start();


include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$sucessMessage = "";
if (isset($_POST['addemployee'])) {

    $customer_id = $_POST['customer_id'];
    $number_plate = $_POST['number_plate'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $color = $_POST['color'];
   
    $checkQuery = "SELECT * FROM vehicles WHERE (number_plate = '$number_plate')";
    $checkResult = $connection->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // If contact or email already exists, set error message
        $_SESSION['errorMessage'] = "Number Plate already exists.";
    } else {

        // add new client to the database

        $garage_id = $_SESSION['garage_id'];
        $business_id = $_SESSION['business_id'];
        $sql = "INSERT INTO vehicles (garage_id,customer_id, number_plate ,model,year,color)
        VALUES ($garage_id, $customer_id, '$number_plate', '$model','$year','$color')";
        $result = $connection->query($sql);

        
        if ($result) {
            $_SESSION['successMessage'] = "Vehicle created Successfully";
        }else {
            $_SESSION['errorMessage'] = "Vehicle creation failed: " . $connection->error;
        }
    }

        $avoidreload = $_SERVER['PHP_SELF'] . "#add_user";
        header("location: $avoidreload");
        exit();
    }

if (isset($_POST['edituser'])) {

    $id = $_POST["id"];
    $customer_id = $_POST["customer_id"];
    $number_plate = $_POST["number_plate"];

    $model = $_POST["model"];
    $year = $_POST["year"];
    $color = $_POST["color"];

    $sql = "UPDATE vehicles SET customer_id = '$customer_id', number_plate = '$number_plate', model = '$model', year = '$year', color = '$color'
where id = $id";
    $result = $connection->query($sql);
    if ($result) {
        $_SESSION['successMessage'] = "Vehicle edited Successfully";
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#edit_user";
    header("location: $avoidreload");
    exit();
}

include 'head.php';

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
                            <h1 class="m-0">Vehicles</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="vehicles.php">Vehicles</a></li>
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
                                        <i class="fa fa-plus"></i> Add Vehicle
                                    </button>


                                    <div class="modal" id="myaddmodal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Vehicle</h5>

                                                </div>

                                                <div class="modal-body">
                                                    <form method="POST" id="add_user" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                        <label class="form-label">Owner<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <select name="customer_id" id="customer_id" required class="form-control">
                                                                <option value="" disabled selected>Select Owner</option>
                                                                <?php
                                                                $garage_id = $_SESSION['garage_id'];
                                                                $sql = "SELECT id, name FROM customers where garage_id = $garage_id";
                                                                $Roleresult = $connection->query($sql);
                                                                // Check if there are results
                                                                if ($Roleresult->num_rows > 0) {
                                                                    // Output data of each row
                                                                    while ($row = $Roleresult->fetch_assoc()) {
                                                                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                                    }
                                                                } else {
                                                                    echo "<option value=''>No role available</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <label class="form-label">Number Plate<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="number_plate" id="number_plate" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        <label class="form-label">Model<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="model" id="model" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Year<span class="required" style="color: red;"></span></label>
                                                        <div class="form-group">
                                                            <input type="year" name="year" id="year" class="form-control" value="" required>
                                                        </div>

                                                        <label class="form-label">Color<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="color" id="color" class="form-control" value=""
                                                                required>
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
                                                <th>Owner</th>
                                                <th>Number Plate</th>
                                                <th>Model</th>
                                                <th>Year</th>
                                                <th>Color</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $garage_id = $_SESSION['garage_id'];
                                            //read all row from database table
                                            $sql = "SELECT vehicles.*,
                                            customers.name as fname 
                                             FROM vehicles,customers where vehicles.customer_id = customers.id and vehicles.garage_id = $garage_id";
                                            $result = $connection->query($sql);

                                            if (!$result) {
                                                die("Invalid query: " . $connection->error);
                                            }

                                            // read data of each row
                                            $i = 1;
                                            while ($row = $result->fetch_assoc()) : ?>

                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $row['fname'] ?></td>
                                                    <td><?= $row['number_plate'] ?></td>
                                                    <td><?= $row['model'] ?></td>
                                                    <td><?= $row['year'] ?></td>
                                                    <td><?= $row['color'] ?></td>

                                                    <td><?= $row['created_at'] ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>, '<?= $row['customer_id'] ?>','<?= $row['number_plate'] ?>', '<?= $row['model'] ?>','<?= $row['year'] ?>','<?= $row['color'] ?>')">Edit</button>
                                                        <a class='btn btn-danger btn-sm' href='/garages/delete_vehicle.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>Delete</a>
                                                    </td>
                                                </tr>


                                            <?php endwhile ?>
                                        </tbody>

                                    </table>

                                    <div class="modal" id="editmodalw">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Vehicle</h5>

                                                </div>
                                                <div class="modal-body">

                                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_user">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <label class="form-label">Owner<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <select name="customer_id" id="customer_ids" required class="form-control">
                                                                <option value="" disabled selected>Select Owner</option>
                                                                <?php
                                                                $garage_id = $_SESSION['garage_id'];
                                                                $sql = "SELECT id, name FROM customers where garage_id = $garage_id";
                                                                $Roleresult = $connection->query($sql);
                                                                // Check if there are results
                                                                if ($Roleresult->num_rows > 0) {
                                                                    // Output data of each row
                                                                    while ($row = $Roleresult->fetch_assoc()) {
                                                                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                                    }
                                                                } else {
                                                                    echo "<option value=''>No role available</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <label class="form-label">Number Plate<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="number_plate" id="number_plates" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        <label class="form-label">Model<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="model" id="models" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Year<span class="required" style="color: red;"></span></label>
                                                        <div class="form-group">
                                                            <input type="year" name="year" id="years" class="form-control" value="" required>
                                                        </div>

                                                        <label class="form-label">Color<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="color" id="colors" class="form-control" value=""
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
        const openeditmodalw = (id, customer_id, number_plate, model, year, color) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('customer_ids').value = customer_id;
            document.getElementById('number_plates').value = number_plate;
            document.getElementById('models').value = model;
            document.getElementById('years').value = year;
            document.getElementById('colors').value = color;
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