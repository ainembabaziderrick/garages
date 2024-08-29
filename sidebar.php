<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index3.html" class="brand-link">
    <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">GARAGE MONITOR</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?= $_SESSION['username'] ?? "Not Logged In" ?></a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

               <li class="nav-item">
            <a href="/garages/dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
        <li class="nav-item">
          <a href="customers.php" class="nav-link">
            <i class="nav-icon far fa-plus-square"></i>
            <p>
              Customers
              <span class="right badge badge-danger"></span>
            </p>
          </a>

        <li class="nav-item">
          <a href="vehicles.php" class="nav-link">
            <i class="nav-icon fas fa-book"></i>
            <p>
              Vehicles
              <span class="right badge badge-danger"></span>
            </p>
          </a>
          <li class="nav-item">
            <a href="spares.php" class="nav-link">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>
                Stock Control
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="services.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Services</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="spares.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Spare Parts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/charts/inline.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inline</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/charts/uplot.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>uPlot</p>
                </a>
              </li>
            </ul>
          </li>
        
          <li class="nav-item">
          <a href="cart.php" class="nav-link">
            <i class="nav-icon fas fa-file"></i>
            <p>
              Make a Sale
              <span class="right badge badge-danger"></span>
            </p>
          </a>

          <li class="nav-item">
          <a href="view_sales.php" class="nav-link">
            <i class="nav-icon fas fa-columns"></i>
            <p>
              View Sales
              <span class="right badge badge-danger"></span>
            </p>
          </a>

          <li class="nav-item">
            <a href="users.php" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Manage Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="users.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Users List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="staff.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Staff</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="roles.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Roles</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/charts/inline.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inline</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/charts/uplot.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>uPlot</p>
                </a>
              </li>
            </ul>
          </li>

        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-circle nav-icon"></i>
            <p>
              Logout
              <span class="right badge badge-danger"></span>
            </p>
          </a>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>