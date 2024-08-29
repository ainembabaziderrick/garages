<?php
session_start();
include 'head.php';

include 'db.php';

$sucessMessage = "";
if (isset($_POST['addemployee'])) {

    $role_id = $_POST['role_id'];
    $username = $_POST['username'];

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    do {

        // add new client to the database

        $garage_id = $_SESSION['garage_id'];
        $business_id = $_SESSION['business_id'];
        $sql = "INSERT INTO users (garage_id, business_id ,role_id,username,email,contact,password)
        VALUES ($garage_id, $business_id, $role_id,'$username','$email','$contact','$password')";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }
        if ($result) {
            $_SESSION['successMessage'] = "User created Successfully";
        }

        $avoidreload = $_SERVER['PHP_SELF'] . "#add_user";
        header("location: $avoidreload");
        exit();
    } while (false);
}



if (isset($_POST['edituser'])) {

    $id = $_POST["id"];
    $role_id = $_POST["role_id"];
    $username = $_POST["username"];

    $contact = $_POST["contact"];
    $email = $_POST["email"];

    $sql = "UPDATE users SET role_id = '$role_id', username = '$username', contact = '$contact', email = '$email'
where id = $id";
    $result = $connection->query($sql);
    if ($result) {
        $_SESSION['successMessage'] = "User edited Successfully";
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
                            <h1 class="m-0">Users</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="users.php">Users</a></li>
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
                                    <button class="btn btn-primary float-right btn-sm" data-toggle="modal" data-target="#myaddmodal">
                                        <i class="fa fa-plus"></i> Add User
                                    </button>


                                    <div class="modal" id="myaddmodal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add User</h5>

                                                </div>

                                                <div class="modal-body">
                                                    <form method="POST" id="add_user" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                        <label class="form-label">Role<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <select name="role_id" id="role_id" required class="form-control">
                                                                <option value="" disabled selected>Select a role</option>
                                                                <?php
                                                                $sql = "SELECT id, name FROM roles";
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

                                                        <label class="form-label">User Name<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="username" id="name" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        <label class="form-label">Email<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="email" id="email" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="phone" name="contact" id="phone" class="form-control" value="" required>
                                                        </div>

                                                        <label class="form-label">Password<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="password" name="password" id="password" class="form-control" value=""
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
                                                <th>Garage</th>
                                                <th>Role</th>
                                                <th>Username</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $garage_id = $_SESSION['garage_id'];
                                            //read all row from database table
                                            $sql = "SELECT users.*,
                                            garages.name as gname ,
                                            roles.name as rname
                                             FROM users,garages,roles where users.garage_id = garages.id and users.role_id = roles.id and users.garage_id = $garage_id";
                                            $result = $connection->query($sql);

                                            if (!$result) {
                                                die("Invalid query: " . $connection->error);
                                            }

                                            // read data of each row
                                            $i = 1;
                                            while ($row = $result->fetch_assoc()) : ?>

                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $row['gname'] ?></td>
                                                    <td><?= $row['rname'] ?></td>
                                                    <td><?= $row['username'] ?></td>
                                                    <td><?= $row['contact'] ?></td>
                                                    <td><?= $row['email'] ?></td>

                                                    <td><?= $row['created_at'] ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>, '<?= $row['role_id'] ?>','<?= $row['username'] ?>', '<?= $row['email'] ?>','<?= $row['contact'] ?>')">Edit</button>
                                                        <a class='btn btn-danger btn-sm' href='/garages/delete_user.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>Delete</a>
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
                                                        <label class="form-label">Role<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <select name="role_id" id="role" required class="form-control">
                                                                <option value="" disabled selected>Select a role</option>
                                                                <?php
                                                                $sql = "SELECT id, name FROM roles";
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

                                                        <label class="form-label">User Name<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="username" id="username" class="form-control" value=""
                                                                required>
                                                        </div>
                                                        <label class="form-label">Email<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="email" id="emails" class="form-control" value="" required>
                                                        </div>
                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="phone" name="contact" id="contact" class="form-control" value="" required>
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="edituser">Save</button>
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
        const openeditmodalw = (id, role_id, username, email, contact) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('role').value = role_id;
            document.getElementById('username').value = username;
            document.getElementById('emails').value = email;
            document.getElementById('contact').value = contact;

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