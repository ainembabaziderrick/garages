<?php
session_start();

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $sqlCheck = "SELECT * FROM users where email = '$email'";

    $result = $connection->query($sqlCheck);

    $result = $result->fetch_assoc();

    $hashePassword = $result['password'];

    $password = $_POST['password'];

    $comparePassword = password_verify($password, $hashePassword);



    if ($comparePassword === true) {
        $_SESSION['id'] = $result['id'];
        $_SESSION['email'] = $result['email'];
        $_SESSION['role_id'] = $result['role_id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['garage_id'] = $result['garage_id'];
        $_SESSION['business_id'] = $result['business_id'];
        $user_id = $_SESSION['id'];
        $sqlgarage = "SELECT * FROM garages , users where garages.id = users.garage_id and users.id = $user_id";
        $garageResult = $connection->query($sqlgarage);
        $garageResult = $garageResult->fetch_assoc();
        $_SESSION['garage_name'] = $garageResult['name'];
        $_SESSION['garage_address'] = $garageResult['location'];
        $_SESSION['garage_contact'] = $garageResult['contact'];
        $_SESSION['am_logged_in'];
        // var_dump($_SESSION['id']);
        header("location: dashboard.php");
    } else {
        echo "Invalid email or password!";
    }
}
include 'head.php';
?>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="index2.html"><b>GARAGE</b>MONITOR</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="" method="post">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" name="email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <?php include 'script.php'; ?>