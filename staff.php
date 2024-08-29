<?php
session_start();
include 'head.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

$name = $contact = $address = $gender = $dob = $email = $nin = $position = "";
$nameErr = $contactErr = $addressErr = $genderErr = $dobErr = $emailErr = $ninErr = $positionErr = "";

if (isset($_POST['addstaff'])) {

    if (empty($_POST["name"])) {
        $nameErr = "Name field is required";
    } else {
        $name = $_POST["name"];
    }

    if (empty($_POST["contact"])) {
        $contactErr = "Phone Number is required";
    } else {
        $contact = $_POST["contact"];
    }


    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = $_POST["address"];
    }


    if (empty($_POST["gender"])) {
        $genderErr = "Please select your gender";
    } else {
        $gender = $_POST["gender"];
    }


    if (empty($_POST["dob"])) {
        $dobErr = "Date of Birth is required";
    } else {
        $dob = $_POST["dob"];
    }


    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
    }


    if (empty($_POST["nin"])) {
        $ninErr = "NIN is required";
    } else {
        $nin = $_POST["nin"];
    }

    if (empty($_POST["position"])) {
        $positionErr = "Position is required";
    } else {
        $position = $_POST["position"];
    }


    if (empty($nameErr) && empty($contactErr) && empty($addressErr) && empty($genderErr) && empty($dobErr) && empty($emailErr) && empty($ninErr) && empty($positionErr)) {
        $garage_id = $_SESSION['garage_id'];
        $checkQuery = "SELECT * FROM staff WHERE (contact = '$contact' OR email = '$email') AND garage_id = $garage_id";
        $checkResult = $connection->query($checkQuery);

        if ($checkResult->num_rows > 0) {

            $_SESSION['errorMessage'] = "Staff already exists.";
        } else {

            $status = "active";
            $sql = "INSERT INTO staff (garage_id,name,email,contact,address, gender, dob, nin, position, status)
            VALUES ($garage_id,'$name','$email','$contact','$address', '$gender', '$dob', '$nin','$position', '$status')";
            $result = $connection->query($sql);

            if (!$result) {
                $errorMessage = "Invalid query: " . $connection->error;
            }
            if ($result) {
                $_SESSION['successMessage'] = "Staff created Successfully";
            }
            $avoidreload = $_SERVER['PHP_SELF'] . "#add_staff";
            header("location: $avoidreload");
            exit();
        }
    } else {

        echo "<script>
                    window.onload = function() {
                        $('#myaddmodal').modal('show');
                    }
                  </script>";
    }
}





if (isset($_POST['editstaff'])) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $gender = $_POST["gender"];

    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $nin = $_POST["nin"];
    $dob = $_POST["dob"];
    $address = $_POST["address"];
    $position = $_POST["position"];

    $sql = "UPDATE staff SET name = '$name', gender = '$gender', contact = '$contact', email = '$email', nin = '$nin', dob = '$dob', address = '$address', position = '$position'
where id = $id";
    $result = $connection->query($sql);
    if ($result) {
        $_SESSION['successMessage'] = "Staff edited Successfully";
    }

    $avoidreload = $_SERVER['PHP_SELF'] . "#edit_staff";
    header("location: $avoidreload");
    exit();
}

if (isset($_POST['addemployee'])) {

    $role_id = $_POST['role_id'];
    $username = $_POST['username'];

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $_POST['contact'];
    $email = $_POST['email'];

  

        // add new client to the database

        $garage_id = $_SESSION['garage_id'];
        $business_id = $_SESSION['business_id'];
        $sql = "INSERT INTO users (garage_id, business_id ,role_id,username,email,contact,password)
        VALUES ($garage_id, $business_id, $role_id,'$username','$email','$contact','$password')";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
      
        }
        if ($result) {
            $_SESSION['successMessage'] = "User created Successfully";
        }

        $avoidreload = $_SERVER['PHP_SELF'] . "#add_user";
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
                            <h1 class="m-0">Staff</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active"><a href="staff.php">Staff</a></li>
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
            } elseif (isset($_SESSION['errorMessage'])) {
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
                                        <i class="fa fa-plus"></i> Add Staff
                                    </button>


                                    <div class="modal" id="myaddmodal">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Staff</h5>
                                                </div>

                                                <div class="modal-body">
                                                    <form method="POST" id="add_staff" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                                        <label class="form-label">Name<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="name" class="form-control" value="<?php echo $name; ?>" required>
                                                            <span style="color:red;"><?php echo $nameErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="contact" id="phone" class="form-control" value="<?php echo $contact; ?>" required>
                                                            <span style="color:red;"><?php echo $contactErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Address<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="address" id="address" class="form-control" value="<?php echo $address; ?>" required>
                                                            <span style="color:red;"><?php echo $addressErr; ?></span>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="gender">Gender<span class="required" style="color: red;">*</span></label>
                                                            <select name="gender" id="gender" class="form-control">
                                                                <option value="">--Select Gender--</option>
                                                                <option value="Male" <?php if ($gender == "Male") echo "selected"; ?>>Male</option>
                                                                <option value="Female" <?php if ($gender == "Female") echo "selected"; ?>>Female</option>
                                                                <option value="Other" <?php if ($gender == "Other") echo "selected"; ?>>Other</option>
                                                            </select>
                                                            <span style="color:red;"><?php echo $genderErr; ?></span>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="dob">Date Of Birth<span class="required" style="color: red;">*</span></label>
                                                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $dob; ?>" max="<?= date('Y-m-d') ?>">
                                                            <span style="color:red;"><?php echo $dobErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Email<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $email; ?>" required>
                                                            <span style="color:red;"><?php echo $emailErr; ?></span>
                                                        </div>

                                                        <label class="form-label">NIN<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="nin" id="nin" class="form-control" value="<?php echo $nin; ?>">
                                                            <span style="color:red;"><?php echo $ninErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Position<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="position" id="position" class="form-control" value="<?php echo $position; ?>">
                                                            <span style="color:red;"><?php echo $positionErr; ?></span>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm" name="addstaff">Save</button>
                                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Address</th>
                                                <th>Gender</th>
                                                <th>DOB</th>
                                                <th>Email</th>
                                                <th>NIN</th>
                                                <th>Position</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $garage_id = $_SESSION['garage_id'];
                                            //read all row from database table
                                            $sql = "SELECT * FROM staff
                                            where garage_id = $garage_id and status = 'active'";
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
                                                    <td><?= $row['address'] ?></td>
                                                    <td><?= $row['gender'] ?></td>
                                                    <td><?= $row['dob'] ?></td>
                                                    <td><?= $row['email'] ?></td>
                                                    <td><?= $row['nin'] ?></td>
                                                    <td><?= $row['position'] ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-primary btn-sm" type="button" onclick="openeditmodalw(<?= $row['id'] ?>,'<?= $row['name'] ?>','<?= $row['contact'] ?>','<?= $row['address'] ?>','<?= $row['gender'] ?>','<?= $row['dob'] ?>','<?= $row['email'] ?>','<?= $row['nin'] ?>','<?= $row['position'] ?>')">
                                                                <i class="fas fa-edit fa-xs"></i>
                                                            </button>
                                                            <a class='btn btn-danger btn-sm' href='deletes/delete_staff.php?id=<?= $row['id'] ?>' onclick='return confirmDelete();'>
                                                                <i class="fas fa-trash fa-xs"></i>
                                                            </a>
                                                            <button class="btn btn-success btn-sm" type="button" onclick="enrollStaff('<?= $row['id'] ?>', '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['contact'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['address'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>')">
                                                                <i class="fas fa-user-plus fa-xs"></i>
                                                            </button>

                                                        </div>
                                                    </td>

                                                </tr>


                                            <?php endwhile ?>
                                        </tbody>

                                    </table>

                                    <div class="modal" id="editmodalw">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Staff</h5>

                                                </div>
                                                <div class="modal-body">

                                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_staff">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <label class="form-label">Name<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="name" id="names" class="form-control" value="<?php echo $name; ?>" required>
                                                            <span style="color:red;"><?php echo $nameErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="contact" id="contacts" class="form-control" value="<?php echo $contact; ?>" required>
                                                            <span style="color:red;"><?php echo $contactErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Address<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="address" id="addresss" class="form-control" value="<?php echo $address; ?>" required>
                                                            <span style="color:red;"><?php echo $addressErr; ?></span>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="gender">Gender<span class="required" style="color: red;">*</span></label>
                                                            <select name="gender" id="genders" class="form-control">
                                                                <option value="">--Select Gender--</option>
                                                                <option value="Male" <?php if ($gender == "Male") echo "selected"; ?>>Male</option>
                                                                <option value="Female" <?php if ($gender == "Female") echo "selected"; ?>>Female</option>
                                                                <option value="Other" <?php if ($gender == "Other") echo "selected"; ?>>Other</option>
                                                            </select>
                                                            <span style="color:red;"><?php echo $genderErr; ?></span>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="dob">Date Of Birth<span class="required" style="color: red;">*</span></label>
                                                            <input type="date" class="form-control" id="dobs" name="dob" value="<?php echo $dob; ?>" max="<?= date('Y-m-d') ?>">
                                                            <span style="color:red;"><?php echo $dobErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Email<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="email" id="emails" class="form-control" value="<?php echo $email; ?>" required>
                                                            <span style="color:red;"><?php echo $emailErr; ?></span>
                                                        </div>

                                                        <label class="form-label">NIN<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="nin" id="nins" class="form-control" value="<?php echo $nin; ?>">
                                                            <span style="color:red;"><?php echo $ninErr; ?></span>
                                                        </div>

                                                        <label class="form-label">Position<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="position" id="positions" class="form-control" value="<?php echo $position; ?>">
                                                            <span style="color:red;"><?php echo $positionErr; ?></span>
                                                        </div>


                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm"
                                                                name="editstaff">Edit</button>
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

                                    <!-- Enrol modal -->

                                    <div class="modal" id="editmodalstaff">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Enroll Staff</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="enroll_staff">
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
                                                            <input type="text" name="username" id="namez" class="form-control" required>
                                                        </div>

                                                        <label class="form-label">Email<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="email" name="emails" id="emailz" class="form-control" required>
                                                        </div>


                                                        <label class="form-label">Phone Number<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="contact" id="contactz" class="form-control" required>
                                                        </div>

                                                        <label class="form-label">Address<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="text" name="address" id="addressz" class="form-control" required>
                                                        </div>

                                                        <label class="form-label">Password<span class="required" style="color: red;">*</span></label>
                                                        <div class="form-group">
                                                            <input type="password" name="password" id="password" class="form-control" required>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary btn-sm" name="enrollstaff">Enroll</button>
                                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


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
        const openeditmodalw = (id, name, contact, address, email) => {

            $('#editmodalw').modal('show');
            document.getElementById('id').value = id;
            document.getElementById('names').value = name;
            document.getElementById('contacts').value = contact;
            document.getElementById('addresss').value = address;

            document.getElementById('emails').value = email;


        }

        const enrollStaff = (id, name, contact, address, email) => {
            $('#editmodalstaff').modal('show');
            document.getElementById('namez').value = name;
            document.getElementById('contactz').value = contact;
            document.getElementById('addressz').value = address;
            document.getElementById('emailz').value = email;
        }


        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
    </script>
    <?php include 'script.php'; ?>

    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": [""]
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