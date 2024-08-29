<?php
session_start();


include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$name = "";
$nameErr = "";
$sucessMessage = "";
if (isset($_POST['addrole'])) {
    
    if(empty($_POST['name'])){
        $nameErr = "Role Field is empty";
    }else{
        $name = $_POST['name'];
    }
   
    if(!empty($name)){
    $checkQuery = "SELECT * FROM roles WHERE (name = '$name')";
    $checkResult = $connection->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // If contact or email already exists, set error message
        $_SESSION['errorMessage'] = "Role already exists.";
    } else {

        // add new client to the database

        
        $sql = "INSERT INTO roles (name)
        VALUES ('$name')";
        $result = $connection->query($sql);

        
        if ($result) {
            $_SESSION['successMessage'] = "Role created Successfully";
        }else {
            $_SESSION['errorMessage'] = "Role creation failed: " . $connection->error;
        }
    }
}

        $avoidreload = $_SERVER['PHP_SELF'] . "#add_role";
        header("location: $avoidreload");
        exit();
    
    }

if (isset($_POST['editrole'])) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    
    $sql = "UPDATE roles SET name = '$name'
where id = $id";
    $result = $connection->query($sql);
    if ($result) {
        $_SESSION['successMessage'] = "Role edited Successfully";
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#edit_role";
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
                            <h1 class="m-0">Roles</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="roles.php">Roles</a></li>
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
                                        <i class="fa fa-plus"></i> Add Roles
                                    </button>


                                    <div class="modal" id="myaddmodal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Roles</h5>

                                                </div>

                                                <div class="modal-body">
                                                    <form method="POST" id="add_role" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                       

                                                        <label class="form-label">Role<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="role" class="form-control" value="<?php echo $name; ?>" placeholder="Insert a Role"
                                                             required   ><span style="color:red;"><?php echo $nameErr; ?></span>
                                                        </div>
                                                        
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="addrole">Save</button>
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
                                                <th>Role</th>
                                               
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $garage_id = $_SESSION['garage_id'];
                                            //read all row from database table
                                            $sql = "SELECT * FROM roles ";
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
                                                    

                                                    <td><?= $row['created_at'] ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>,'<?= $row['name']?>')">Edit</button>
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
                                                    <h5 class="modal-title">Edit Roles</h5>

                                                </div>
                                                <div class="modal-body">

                                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_role">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        

                                                        <label class="form-label">Roles<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="roles" class="form-control" value=""
                                                                required>
                                                        </div>
                                                      
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="editrole">Edit</button>
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
        const openeditmodalw = (id, name) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('roles').value = name;
            
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