<?php
session_start();
include 'db.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$garage_id = $_SESSION['garage_id'];
$user_id = $_SESSION['id'];


$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
if(empty($start_date)){
  $start_date = date('Y-m-d');
}

if(empty($end_date)){
  $end_date = date('Y-m-d');
}

// First, get the category_id for 'services'
$categoryQuery = "SELECT id FROM category WHERE name = 'service'";
$categoryResult = $connection->query($categoryQuery);
$categoryRow = $categoryResult->fetch_assoc();
$category_id = $categoryRow['id'];

// Then, use this category_id in your main query
$sql = "
    SELECT 
    (SELECT COALESCE(SUM(total),0) FROM sales WHERE date BETWEEN '$start_date' AND '$end_date' AND garage_id = $garage_id) AS totalPayable, 
    
    (SELECT COUNT(customer_id) 
        FROM sales 
        WHERE date BETWEEN '$start_date' AND '$end_date' 
        AND garage_id = $garage_id
        AND total > (SELECT COALESCE(SUM(amount_paid), 0) 
                     FROM transactions 
                     WHERE transactions.sales_id = sales.id)
    ) AS debtors,
    
    (SELECT COALESCE(SUM(amount_paid),0) 
        FROM transactions 
        WHERE sale_date BETWEEN '$start_date' AND '$end_date' 
        AND garage_id = $garage_id
    ) AS totalPaid 
    
    FROM sales 
    WHERE garage_id = $garage_id 
    LIMIT 1;
";
$query = $connection->query($sql);
$row = $query->fetch_assoc();

$expectedAmount = number_format($row['totalPayable']);
$paidAmount = number_format($row['totalPaid']);
$pendingBalance = $row['totalPayable'] - $row['totalPaid'];
$totalDebtors = $row['debtors'];



$sqlStatic = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE garage_id = $garage_id) as users,
        (SELECT COUNT(*) FROM accounts WHERE garage_id = $garage_id) as services,
        (SELECT COUNT(*) FROM customers WHERE garage_id = $garage_id) as customers,
        (SELECT COUNT(*) FROM sales WHERE garage_id = $garage_id) as sales
";

$queryStatic = $connection->query($sqlStatic);

if ($queryStatic) {  // Check if the query was successful
  $rowStatic = $queryStatic->fetch_assoc();

  $userCount = $rowStatic['users'];
  $servicesCount = $rowStatic['services'];
  $customerCount = $rowStatic['customers'];
  $salesCount = $rowStatic['sales'];
} else {
  echo "Query failed: " . $connection->error;
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
              <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active"><a href="dashboard.php">Dashboard</a></li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->


          <!-- Filter Form -->
          <div class="row mb-4">
            <div class="col-md-8">
              <form method="GET" action="dashboard.php" class="form-inline">
                <div class="form-group mb-2 mr-3">
                  <label for="start_date" class="mr-2">Start Date:</label>
                  <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="form-group mb-2 mr-3">
                  <label for="end_date" class="mr-2">End Date:</label>
                  <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Filter</button>
              </form>
            </div>
          </div>

        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->

          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $expectedAmount ?></h3>
                  <p>Expected Amount</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="/garage/services/index.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $paidAmount ?><sup style="font-size: 20px"></sup></h3>
                  <p>Paid Amount</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <a href="/garage/customer.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?= $pendingBalance ?></h3>
                  <p>Pending Balance</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="/garage/staff.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $totalDebtors ?></h3>
                  <p>Total Debtors</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <a href="/garage/track_sales.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>


          <!-- /.row -->
          <!-- Main row -->
          <div class="row">
            <!-- Additional Content Can Be Added Here -->
            <div class="col-md-6">
              <script type="text/javascript">
                google.charts.load('current', {
                  'packages': ['bar']
                });
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                  var data = google.visualization.arrayToDataTable([
                    ['Category', 'Count'],

                    <?php
                    $text = ['users', 'services', 'customers', 'sales'];
                    $value = [$userCount, $servicesCount, $customerCount, $salesCount];
                    for ($i = 0; $i < 4; $i++) {
                      echo "['" . $text[$i] . "', " . $value[$i] . "],";
                    }
                    ?>

                  ]);

                  var options = {
                    chart: {
                      title: 'Bar Graph',
                      subtitle: '',
                    }
                  };

                  var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

                  chart.draw(data, google.charts.Bar.convertOptions(options));
                }
              </script>

              <div id="columnchart_material" style="width: auto; height: 500px;"></div>
            </div>
            <div class="col-md-6">
              <script type="text/javascript">
                google.charts.load('current', {
                  'packages': ['corechart']
                });
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {

                  var data = google.visualization.arrayToDataTable([
                    ['Category', 'Count'],
                    <?php
                    $text = ['users', 'services', 'customers', 'sales'];
                    $value = [$userCount, $servicesCount, $customerCount, $salesCount];
                    for ($i = 0; $i < 4; $i++) {
                      echo "['" . $text[$i] . "', " . $value[$i] . "],";
                    }
                    ?>
                  ]);

                  var options = {
                    title: 'Pie Chart'
                  };

                  var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                  chart.draw(data, options);
                }
              </script>
              <div id="piechart" style="width: auto; height: 500px;"></div>
            </div>
          </div>
        </div>
        <!-- /.row (main row) -->

        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $servicesCount ?></h3>
                <p>Cash At Hand</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="/garage/services/index.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $customerCount ?><sup style="font-size: 20px"></sup></h3>
                <p>Cash At Bank</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="/garage/customer.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $userCount ?></h3>
                <p>Cash At MoMo</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="/garage/staff.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $salesCount ?></h3>
                <p>Sales</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="/garage/track_sales.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>

        <?php include 'footer.php'; ?>
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  </div>
  <!-- ./wrapper -->
</body>

</html>